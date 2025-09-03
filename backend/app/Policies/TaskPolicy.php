<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TaskPolicy
{
    public function viewAny(User $user)
    {
        return true; // All authenticated users can view tasks
    }

    public function view(User $user, Task $task)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Developer can view their assigned tasks
        if ($user->isDeveloper() && $task->assigned_user_id == $user->id) {
            return true;
        }

        // Client can view tasks in their projects
        if ($user->isClient()) {
            $task->load('project.users');
            return $task->project->users()
                ->wherePivot('role', 'client')
                ->where('users.id', $user->id)
                ->exists();
        }

        return false;
    }

    public function create(User $user)
    {
        return $user->isSuperAdmin() || $user->isDeveloper();
    }

    public function update(User $user, Task $task)
    {
        return $user->isSuperAdmin() || 
               ($user->isDeveloper() && $task->assigned_user_id == $user->id);
    }

    public function delete(User $user, Task $task)
    {
        return $user->isSuperAdmin();
    }

    public function approve(User $user, Task $task)
    {
        Log::info('Approve policy check', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'task_id' => $task->id,
            'task_status' => $task->status
        ]);

        // Task must be completed to be approved
        if ($task->status !== 'completed') {
            return false;
        }

        // Only clients can approve
        if (!$user->isClient()) {
            return false;
        }

        // Client must be associated with the project
        $task->load('project.users');
        return $task->project->users()
            ->wherePivot('role', 'client')
            ->where('users.id', $user->id)
            ->exists();
    }

    public function reject(User $user, Task $task)
    {
        // Same logic as approve
        return $this->approve($user, $task);
    }
}