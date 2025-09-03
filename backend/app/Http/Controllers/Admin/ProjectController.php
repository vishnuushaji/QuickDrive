<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Project::with(['clients', 'developers', 'tasks']);
        
        // Role-based filtering
        if ($user->role === 'super_admin') {
            // Super admin sees all projects
        } elseif ($user->role === 'client') {
            // Clients see only their projects
            $query->whereHas('users', function($q) use ($user) {
                $q->where('users.id', $user->id)->where('project_user.role', 'client');
            });
        } else {
            // Developers see projects they're assigned to
            $query->whereHas('users', function($q) use ($user) {
                $q->where('users.id', $user->id)->where('project_user.role', 'developer');
            });
        }
        
        // Status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        $projects = $query->get();
        
        // Calculate progress for each project
        foreach ($projects as $project) {
            $project->progress = $project->calculateProgress();
        }
        $projects = $query->paginate(10);
        
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        $clients = User::where('role', 'client')->get();
        $developers = User::where('role', 'developer')->get();
        return view('admin.projects.create', compact('clients', 'developers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,completed,on_hold',
            'clients' => 'required|array',
            'developers' => 'required|array'
        ]);

        $project = Project::create($validated);

        // Attach clients
        foreach ($request->clients as $clientId) {
            $project->users()->attach($clientId, ['role' => 'client']);
        }

        // Attach developers
        foreach ($request->developers as $developerId) {
            $project->users()->attach($developerId, ['role' => 'developer']);
        }

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully');
    }

    public function show(Project $project)
    {
        $project->load(['clients', 'developers', 'tasks.assignedUser']);
        $project->progress = $project->calculateProgress();
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $clients = User::where('role', 'client')->get();
        $developers = User::where('role', 'developer')->get();
        $project->load(['clients', 'developers']);
        return view('admin.projects.edit', compact('project', 'clients', 'developers'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,completed,on_hold',
            'clients' => 'required|array',
            'developers' => 'required|array'
        ]);

        $project->update($validated);

        // Sync clients and developers
        $project->users()->detach();
        
        foreach ($request->clients as $clientId) {
            $project->users()->attach($clientId, ['role' => 'client']);
        }

        foreach ($request->developers as $developerId) {
            $project->users()->attach($developerId, ['role' => 'developer']);
        }

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully');
    }
}