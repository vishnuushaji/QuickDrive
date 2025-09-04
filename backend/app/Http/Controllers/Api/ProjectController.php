<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $perPage = $request->get('per_page', 10);
            $search = $request->get('search', '');
            $statusFilter = $request->get('status', '');

            $query = Project::with(['users', 'tasks']);

            // Apply user-based filtering
            if (!$user->isSuperAdmin()) {
                $query->whereHas('users', function ($userQuery) use ($user) {
                    $userQuery->where('users.id', $user->id);
                });
            }

            // Apply search filter
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%')
                      ->orWhereHas('users', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', '%' . $search . '%');
                      });
                });
            }

            // Apply status filter
            if (!empty($statusFilter) && $statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }

            $projects = $query->paginate($perPage);

            // Add progress to each project
            $projects->each(function ($project) {
                $project->progress = $project->calculateProgress();
            });

            return response()->json($projects);
        } catch (\Exception $e) {
            Log::error('Error fetching projects: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch projects'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Remove authorize check temporarily or handle it differently
            $user = auth()->user();
            
            if (!$user->isSuperAdmin() && !$user->isDeveloper()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|in:active,completed,on_hold',
                'client_ids' => 'nullable|array',
                'client_ids.*' => 'exists:users,id',
                'developer_ids' => 'nullable|array',
                'developer_ids.*' => 'exists:users,id',
            ]);

            $project = Project::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]);

            // Attach the authenticated user as a developer (project creator)
            $project->users()->attach($user->id, ['role' => 'developer']);

            // Attach clients
            if (!empty($request->client_ids)) {
                foreach ($request->client_ids as $clientId) {
                    $project->users()->attach($clientId, ['role' => 'client']);
                }
            }

            // Attach additional developers
            if (!empty($request->developer_ids)) {
                foreach ($request->developer_ids as $developerId) {
                    // Skip if already attached as creator
                    if ($developerId != $user->id) {
                        $project->users()->attach($developerId, ['role' => 'developer']);
                    }
                }
            }

            return response()->json($project->load('users'), 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error creating project: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create project'], 500);
        }
    }

    public function show($id)
    {
        try {
            $project = Project::with(['users', 'tasks.assignedUser'])->findOrFail($id);
            
            $user = auth()->user();
            if (!$user->isSuperAdmin() && !$project->users->contains($user)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $project->progress = $project->calculateProgress();
            return response()->json($project);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Project not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error loading project: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load project details'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $project = Project::findOrFail($id);
            $user = auth()->user();
            
            if (!$user->isSuperAdmin()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|in:active,completed,on_hold',
                'client_ids' => 'nullable|array',
                'client_ids.*' => 'exists:users,id',
                'developer_ids' => 'nullable|array',
                'developer_ids.*' => 'exists:users,id',
            ]);

            $project->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]);

            // Sync users
            $project->users()->detach();

            // Re-attach the authenticated user as a developer
            $project->users()->attach($user->id, ['role' => 'developer']);

            // Attach clients
            if (!empty($request->client_ids)) {
                foreach ($request->client_ids as $clientId) {
                    $project->users()->attach($clientId, ['role' => 'client']);
                }
            }

            // Attach developers
            if (!empty($request->developer_ids)) {
                foreach ($request->developer_ids as $developerId) {
                    if ($developerId != $user->id) {
                        $project->users()->attach($developerId, ['role' => 'developer']);
                    }
                }
            }

            return response()->json($project->load('users'));
        } catch (\Exception $e) {
            Log::error('Error updating project: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update project'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $project = Project::findOrFail($id);
            $user = auth()->user();
            
            if (!$user->isSuperAdmin()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $project->delete();
            
            return response()->json(['message' => 'Project deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting project: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete project'], 500);
        }
    }
}