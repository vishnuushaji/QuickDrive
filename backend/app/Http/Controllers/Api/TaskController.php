<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Mail\TaskAssigned;
use App\Mail\TaskCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            $tasks = Task::with(['project', 'assignedUser'])->get();
        } elseif ($user->isClient()) {
            $projectIds = $user->projects()->wherePivot('role', 'client')->pluck('projects.id');
            $tasks = Task::whereIn('project_id', $projectIds)
                        ->with(['project', 'assignedUser'])
                        ->get();
        } else {
            $tasks = $user->assignedTasks()->with(['project', 'assignedUser'])->get();
        }

        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_user_id' => 'nullable|exists:users,id',
            'priority' => 'required|in:normal,urgent,top_urgent',
            'status' => 'required|in:pending,in_progress,completed,approved,rejected',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $task = new Task($validated);
        
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('task-attachments', 'public');
            $task->attachment = $path;
        }

        $task->save();
        $task->load(['project', 'assignedUser']);

        // Send email notifications to Admin, Client, and Developer
        $this->sendTaskAssignedNotifications($task);

        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        $task->load(['project', 'assignedUser']);
        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_user_id' => 'nullable|exists:users,id',
            'priority' => 'required|in:normal,urgent,top_urgent',
            'status' => 'required|in:pending,in_progress,completed,approved,rejected',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $oldAssignedUserId = $task->assigned_user_id;
        $oldStatus = $task->status;

        $task->fill($validated);

        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($task->attachment) {
                Storage::disk('public')->delete($task->attachment);
            }
            $path = $request->file('attachment')->store('task-attachments', 'public');
            $task->attachment = $path;
        }

        $task->save();
        $task->load(['project', 'assignedUser']);

        // Send notification if task is newly assigned or reassigned
        if ($task->assigned_user_id && $task->assigned_user_id != $oldAssignedUserId) {
            $this->sendTaskAssignedNotifications($task);
        }

        // Send notification if task is completed
        if ($oldStatus != 'completed' && $task->status == 'completed') {
            $this->sendTaskCompletedNotifications($task);
        }

        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        
        // Delete attachment if exists
        if ($task->attachment) {
            Storage::disk('public')->delete($task->attachment);
        }
        
        $task->delete();
        
        return response()->json(['message' => 'Task deleted successfully']);
    }

    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $oldStatus = $task->status;
        $task->status = $validated['status'];
        $task->save();
        $task->load(['project', 'assignedUser']);

        // Send notification if task is completed
        if ($oldStatus != 'completed' && $task->status == 'completed') {
            $this->sendTaskCompletedNotifications($task);
        }

        return response()->json($task);
    }

    public function approve(Task $task)
    {
        try {
            // Load relationships for authorization
            $task->load('project.users');
            
            // Check authorization
            if (!auth()->user()->can('approve', $task)) {
                return response()->json([
                    'error' => 'You are not authorized to approve this task',
                    'details' => 'Only clients associated with this project can approve completed tasks'
                ], 403);
            }
            
            $task->status = 'approved';
            $task->save();
            
            // Reload with relationships for response
            $task->load(['project', 'assignedUser']);
            
            return response()->json($task);
        } catch (\Exception $e) {
            Log::error('Error approving task: ' . $e->getMessage(), [
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to approve task',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function reject(Task $task)
    {
        try {
            // Load relationships for authorization
            $task->load('project.users');
            
            // Check authorization
            if (!auth()->user()->can('reject', $task)) {
                return response()->json([
                    'error' => 'You are not authorized to reject this task',
                    'details' => 'Only clients associated with this project can reject completed tasks'
                ], 403);
            }
            
            $task->status = 'rejected';
            $task->save();
            
            // Reload with relationships for response
            $task->load(['project', 'assignedUser']);
            
            return response()->json($task);
        } catch (\Exception $e) {
            Log::error('Error rejecting task: ' . $e->getMessage(), [
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to reject task',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send task assigned notifications to Admin, Client, and Developer
     */
    private function sendTaskAssignedNotifications(Task $task)
    {
        try {
            $recipients = collect();

            // 1. Add all Super Admins
            $superAdmins = User::where('role', 'super_admin')->get();
            $recipients = $recipients->merge($superAdmins);

            // 2. Add assigned developer
            if ($task->assigned_user_id && $task->assignedUser) {
                $recipients->push($task->assignedUser);
            }

            // 3. Add all clients associated with the project
            $task->load('project.users');
            $projectClients = $task->project->users()
                ->wherePivot('role', 'client')
                ->get();
            $recipients = $recipients->merge($projectClients);

            // 4. Add all developers associated with the project (optional)
            $projectDevelopers = $task->project->users()
                ->wherePivot('role', 'developer')
                ->get();
            $recipients = $recipients->merge($projectDevelopers);

            // Remove duplicates and send emails
            $recipients->unique('id')->each(function ($user) use ($task) {
                try {
                    Mail::to($user->email)->send(new TaskAssigned($task));
                    Log::info("Task assigned email sent to: {$user->email} (Role: {$user->role})");
                } catch (\Exception $e) {
                    Log::error("Failed to send task assigned email to {$user->email}: " . $e->getMessage());
                }
            });

            Log::info("Task assigned notifications sent for task ID: {$task->id}");
        } catch (\Exception $e) {
            Log::error("Error sending task assigned notifications: " . $e->getMessage());
        }
    }

    /**
     * Send task completed notifications to Admin, Client, and Developer
     */
    private function sendTaskCompletedNotifications(Task $task)
    {
        try {
            $recipients = collect();

            // 1. Add all Super Admins
            $superAdmins = User::where('role', 'super_admin')->get();
            $recipients = $recipients->merge($superAdmins);

            // 2. Add all clients associated with the project (they need to approve)
            $task->load('project.users');
            $projectClients = $task->project->users()
                ->wherePivot('role', 'client')
                ->get();
            $recipients = $recipients->merge($projectClients);

            // 3. Add the developer who completed the task
            if ($task->assigned_user_id && $task->assignedUser) {
                $recipients->push($task->assignedUser);
            }

            // 4. Add all other developers on the project (optional)
            $projectDevelopers = $task->project->users()
                ->wherePivot('role', 'developer')
                ->get();
            $recipients = $recipients->merge($projectDevelopers);

            // Remove duplicates and send emails
            $recipients->unique('id')->each(function ($user) use ($task) {
                try {
                    Mail::to($user->email)->send(new TaskCompleted($task));
                    Log::info("Task completed email sent to: {$user->email} (Role: {$user->role})");
                } catch (\Exception $e) {
                    Log::error("Failed to send task completed email to {$user->email}: " . $e->getMessage());
                }
            });

            Log::info("Task completed notifications sent for task ID: {$task->id}");
        } catch (\Exception $e) {
            Log::error("Error sending task completed notifications: " . $e->getMessage());
        }
    }
}