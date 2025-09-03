<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;

class DashboardController extends Controller
{
    public function stats()
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            // Load projects with relationships
            $recentProjects = Project::with(['users', 'tasks'])
                ->latest()
                ->take(8)
                ->get()
                ->map(function ($project) {
                    $project->progress = $project->calculateProgress();
                    return $project;
                });
                
            // Load tasks with relationships
            $recentTasks = Task::with(['project', 'assignedUser', 'developers'])
                ->latest()
                ->take(5)
                ->get();
            
            $data = [
                'totalUsers' => User::count(),
                'totalProjects' => Project::count(),
                'totalTasks' => Task::count(),
                'pendingTasks' => Task::where('status', 'pending')->count(),
                'completedTasks' => Task::where('status', 'approved')->count(),
                'activeUsers' => User::where('updated_at', '>', now()->subMinutes(5))->count(),
                'recentProjects' => $recentProjects,
                'recentTasks' => $recentTasks
            ];
        } elseif ($user->isClient()) {
            $projectIds = $user->projects()->wherePivot('role', 'client')->pluck('projects.id');
            
            $recentProjects = $user->projects()
                ->wherePivot('role', 'client')
                ->with(['users', 'tasks'])
                ->latest()
                ->take(8)
                ->get()
                ->map(function ($project) {
                    $project->progress = $project->calculateProgress();
                    return $project;
                });
                
            $recentTasks = Task::whereIn('project_id', $projectIds)
                ->with(['project', 'assignedUser', 'developers'])
                ->latest()
                ->take(5)
                ->get();
            
            $data = [
                'totalProjects' => $user->projects()->wherePivot('role', 'client')->count(),
                'totalTasks' => Task::whereIn('project_id', $projectIds)->count(),
                'pendingTasks' => Task::whereIn('project_id', $projectIds)->where('status', 'pending')->count(),
                'completedTasks' => Task::whereIn('project_id', $projectIds)->where('status', 'approved')->count(),
                'activeUsers' => 0,
                'recentProjects' => $recentProjects,
                'recentTasks' => $recentTasks
            ];
        } else {
            // Developer - get projects where they are assigned or have tasks
            $assignedTasksProjectIds = $user->assignedTasks()->pluck('project_id')->unique();
            $developerProjectIds = $user->projects()->wherePivot('role', 'developer')->pluck('projects.id');
            $allProjectIds = $assignedTasksProjectIds->merge($developerProjectIds)->unique();
            
            $recentProjects = Project::whereIn('id', $allProjectIds)
                ->with(['users', 'tasks'])
                ->latest()
                ->take(8)
                ->get()
                ->map(function ($project) {
                    $project->progress = $project->calculateProgress();
                    return $project;
                });
                
            $recentTasks = $user->assignedTasks()
                ->with(['project', 'assignedUser', 'developers'])
                ->latest()
                ->take(5)
                ->get();
            
            $data = [
                'totalProjects' => Project::whereIn('id', $allProjectIds)->count(),
                'totalTasks' => $user->assignedTasks()->count(),
                'assignedTasks' => $user->assignedTasks()->count(),
                'pendingTasks' => $user->assignedTasks()->where('status', 'pending')->count(),
                'completedTasks' => $user->assignedTasks()->where('status', 'approved')->count(),
                'inProgressTasks' => $user->assignedTasks()->where('status', 'in_progress')->count(),
                'activeUsers' => 0,
                'recentProjects' => $recentProjects,
                'recentTasks' => $recentTasks
            ];
        }

        return response()->json($data);
    }
}