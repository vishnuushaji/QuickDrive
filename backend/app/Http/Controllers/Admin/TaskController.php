<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TaskAssigned;
use App\Mail\TaskCompleted;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Start building the query
        $query = Task::with(['project', 'assignedUser']);
        
        // Apply role-based filtering
        if ($user->isSuperAdmin()) {
            // Super admin can see all tasks
        } elseif ($user->isDeveloper()) {
            $query->where('assigned_user_id', $user->id);
        } else {
            $projectIds = $user->projects()->wherePivot('role', 'client')->pluck('projects.id');
            $query->whereIn('project_id', $projectIds);
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Apply priority filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        $tasks = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.tasks.index', compact('tasks'));
    }

    public function show(Task $task)
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            // Super admin can see all tasks
        } elseif ($user->isDeveloper()) {
            if ($task->assigned_user_id !== $user->id) {
                abort(403, 'You do not have permission to view this task.');
            }
        } else {
            if (!$user->projects->contains($task->project_id)) {
                abort(403, 'You do not have permission to view this task.');
            }
        }

        // Load relationships for the show view
        $task->load(['project', 'assignedUser']);

        return view('admin.tasks.show', compact('task'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            $projects = Project::all();
        } else {
            $projects = $user->projects;
        }
        
        $developers = User::where('role', 'developer')->get();
        return view('admin.tasks.create', compact('projects', 'developers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'assigned_user_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:normal,urgent,top_urgent',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'hours' => 'nullable|integer|min:1',
            'attachment' => 'nullable|file|max:10240'
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('attachments', 'public');
        }

        $task = Task::create($validated);
        $task->load(['project', 'assignedUser']);

        // Send email notifications when task is assigned
        if ($task->assigned_user_id) {
            $this->sendTaskAssignedEmails($task);
        }

        return redirect()->route('admin.tasks.index')->with('success', 'Task created successfully and notifications sent');
    }

    public function edit(Task $task)
    {
        $user = auth()->user();
        
        // Clients cannot access the edit form - they can only approve/reject through the show page
        if ($user->role === 'client') {
            return redirect()->route('admin.tasks.show', $task)
                ->with('info', 'As a client, you can approve or reject tasks from the task details page.');
        }
        
        // Only super admins and the assigned developer can edit
        if (!$user->isSuperAdmin() && $task->assigned_user_id !== $user->id) {
            abort(403, 'You do not have permission to edit this task.');
        }
        
        if ($user->isSuperAdmin()) {
            $projects = Project::all();
        } else {
            $projects = $user->projects;
        }
        
        $developers = User::where('role', 'developer')->get();
        return view('admin.tasks.edit', compact('task', 'projects', 'developers'));
    }

    public function update(Request $request, Task $task)
    {
        $user = auth()->user();
    
        // Special handling for clients approving/rejecting tasks
        if ($user->role === 'client') {
            // Check if client has access to this project
            $hasAccess = $user->projects()
                ->wherePivot('role', 'client')
                ->where('projects.id', $task->project_id)
                ->exists();
    
            if (!$hasAccess) {
                abort(403, 'You do not have permission to update this task.');
            }
    
            // Clients can only change status from 'completed' → 'approved' or 'rejected'
            if ($task->status !== 'completed') {
                abort(403, 'You can only approve or reject completed tasks.');
            }
    
            // Validate only status field for clients
            $validated = $request->validate([
                'status' => 'required|in:approved,rejected'
            ]);
    
            $task->update(['status' => $validated['status']]);
    
            // Update project progress if approved
            if ($validated['status'] === 'approved') {
                $task->project->update(['progress' => $task->project->calculateProgress()]);
            }
    
            return redirect()->route('admin.tasks.show', $task)
                ->with('success', 'Task ' . $validated['status'] . ' successfully');
        }
    
        // ========== Admins & Developers ========== //
        
        // For developers, limit what they can update
        if ($user->isDeveloper() && $task->assigned_user_id === $user->id) {
            $validated = $request->validate([
                'status' => 'required|in:pending,in_progress,completed',
                'description' => 'nullable|string',
                'attachment' => 'nullable|file|max:10240'
            ]);
        } else {
            // Full validation for super admins
            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'assigned_user_id' => 'nullable|exists:users,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|in:pending,in_progress,completed,approved,rejected',
                'priority' => 'required|in:normal,urgent,top_urgent',
                'start_date' => 'nullable|date',
                'due_date' => 'nullable|date|after_or_equal:start_date',
                'hours' => 'nullable|integer|min:1',
                'attachment' => 'nullable|file|max:10240'
            ]);
        }
    
        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('attachments', 'public');
        }
    
        $oldStatus = $task->status;
        $oldAssignedTo = $task->assigned_user_id;
    
        $task->update($validated);
        $task->load(['project', 'assignedUser']);
    
        // Notify if reassigned
        if (isset($validated['assigned_user_id']) && $oldAssignedTo != $task->assigned_user_id && $task->assigned_user_id) {
            $this->sendTaskAssignedEmails($task);
        }
    
        // Notify if newly completed
        if ($oldStatus !== 'completed' && $task->status === 'completed') {
            $this->sendTaskCompletedEmails($task);
        }
    
        // Update project progress if approved
        if ($task->status === 'approved') {
            $task->project->update(['progress' => $task->project->calculateProgress()]);
        }
    
        return redirect()->route('admin.tasks.index')->with('success', 'Task updated successfully');
    }

    public function destroy(Task $task)
    {
        $user = auth()->user();
        
        // Only super admins can delete tasks
        if (!$user->isSuperAdmin()) {
            abort(403, 'You do not have permission to delete this task.');
        }
        
        $task->delete();
        return redirect()->route('admin.tasks.index')->with('success', 'Task deleted successfully');
    }

    /**
     * Send task assigned emails to admin, clients, and developer
     */
    private function sendTaskAssignedEmails(Task $task)
    {
        $recipients = collect();
        
        // Add all super admins
        $admins = User::where('role', 'super_admin')->get();
        $recipients = $recipients->merge($admins);
        
        // Add assigned developer
        if ($task->assignedUser) {
            $recipients->push($task->assignedUser);
        }
        
        // Add all project clients
        if ($task->project) {
            $clients = $task->project->clients;
            $recipients = $recipients->merge($clients);
        }
        
        // Remove duplicates and send emails
        $recipients->unique('id')->each(function ($user) use ($task) {
            try {
                Mail::to($user->email)->send(new TaskAssigned($task));
            } catch (\Exception $e) {
                \Log::error('Failed to send task assigned email to ' . $user->email . ': ' . $e->getMessage());
            }
        });
    }

    /**
     * Send task completed emails to admin, clients, and developer
     */
    private function sendTaskCompletedEmails(Task $task)
    {
        $recipients = collect();
        
        // Add all super admins
        $admins = User::where('role', 'super_admin')->get();
        $recipients = $recipients->merge($admins);
        
        // Add assigned developer
        if ($task->assignedUser) {
            $recipients->push($task->assignedUser);
        }
        
        // Add all project clients
        if ($task->project) {
            $clients = $task->project->clients;
            $recipients = $recipients->merge($clients);
        }
        
        // Remove duplicates and send emails
        $recipients->unique('id')->each(function ($user) use ($task) {
            try {
                Mail::to($user->email)->send(new TaskCompleted($task));
            } catch (\Exception $e) {
                \Log::error('Failed to send task completed email to ' . $user->email . ': ' . $e->getMessage());
            }
        });
    }
}