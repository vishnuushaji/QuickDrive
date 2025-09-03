<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            $data = [
                'totalUsers' => User::count(),
                'totalProjects' => Project::count(),
                'totalTasks' => Task::count(),
                'pendingTasks' => Task::where('status', 'pending')->count(),
                'completedTasks' => Task::where('status', 'approved')->count(),
                'recentProjects' => Project::latest()->take(5)->get(),
                'recentTasks' => Task::latest()->take(5)->get()
            ];
        } elseif ($user->isClient()) {
            $projectIds = $user->projects()->wherePivot('role', 'client')->pluck('projects.id');
            $data = [
                'totalProjects' => $user->projects()->wherePivot('role', 'client')->count(),
                'totalTasks' => Task::whereIn('project_id', $projectIds)->count(),
                'pendingTasks' => Task::whereIn('project_id', $projectIds)->where('status', 'pending')->count(),
                'completedTasks' => Task::whereIn('project_id', $projectIds)->where('status', 'approved')->count(),
                'recentProjects' => $user->projects()->wherePivot('role', 'client')->latest()->take(5)->get(),
                'recentTasks' => Task::whereIn('project_id', $projectIds)->latest()->take(5)->get()
            ];
        } else {
            $data = [
                'totalProjects' => $user->projects()->wherePivot('role', 'developer')->count(),
                'assignedTasks' => $user->assignedTasks()->count(),
                'pendingTasks' => $user->assignedTasks()->where('status', 'pending')->count(),
                'completedTasks' => $user->assignedTasks()->where('status', 'approved')->count(),
                'inProgressTasks' => $user->assignedTasks()->where('status', 'in_progress')->count(),
                'recentProjects' => $user->projects()->wherePivot('role', 'developer')->latest()->take(5)->get(),
                'recentTasks' => $user->assignedTasks()->latest()->take(5)->get()
            ];
        }

        return view('admin.dashboard', $data);
    }
}