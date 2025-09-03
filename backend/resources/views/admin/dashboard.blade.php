@extends('layouts.admin')
@section('content')
<!-- Main -->
<div class="flex flex-col grow items-stretch rounded-xl bg-background border border-input lg:ms-(--sidebar-width) mt-0 lg:mt-[15px] m-[15px]">
    <div class="flex flex-col grow kt-scrollable-y-auto [--kt-scrollbar-width:auto] pt-5" id="scrollable_content">
        <main class="grow" role="content">
            <!-- Toolbar -->
            <div class="pb-5">
                <!-- Container -->
                 
                <div class="kt-container-fixed flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center flex-wrap gap-1 lg:gap-5">
                        
                        <h1 class="font-medium text-lg text-mono">
                            Dashboard
                        </h1>
                        <div class="flex items-center gap-1 text-sm font-normal">
                            <span class="text-mono">Home</span>
                        </div>
                    </div>
                    <div class="flex items-center flex-wrap gap-1.5 lg:gap-3.5">
                        <a class="kt-btn kt-btn-outline" href="#">
                            <i class="ki-filled ki-exit-down"></i>
                            Export Report
                        </a>
                        @if(auth()->user()->role === 'super_admin')
                        <a class="kt-btn kt-btn-primary" href="{{ route('admin.projects.create') }}">
                            <i class="ki-filled ki-plus"></i>
                            New Project
                        </a>
                        @endif
                    </div>
                </div>
                <!-- End of Container -->
            </div>
            <!-- End of Toolbar -->
            
            <!-- Container -->
            <div class="max-w-none px-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-7.5 mb-5 lg:mb-10">
                    <!-- Total Projects -->
                    <div class="kt-card">
                        <div class="kt-card-body p-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h5 class="text-sm font-medium text-secondary-foreground mb-2">Total Projects</h5>
                                    <h2 class="text-2xl font-bold text-mono">{{ $totalProjects }}</h2>
                                </div>
                                <div class="flex items-center justify-center size-12 shrink-0 rounded-full bg-primary/10">
                                    <i class="ki-filled ki-element-11 text-primary text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-border">
                                <div class="flex items-center gap-1 text-xs">
                                    <i class="ki-filled ki-arrow-up text-success"></i>
                                    <span class="text-success font-medium">Active Projects</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Total Tasks -->
                    <div class="kt-card">
                        <div class="kt-card-body p-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h5 class="text-sm font-medium text-secondary-foreground mb-2">Total Tasks</h5>
                                    <h2 class="text-2xl font-bold text-mono">{{ $totalTasks ?? 0 }}</h2>
                                </div>
                                <div class="flex items-center justify-center size-12 shrink-0 rounded-full bg-info/10">
                                    <i class="ki-filled ki-file-sheet text-info text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-border">
                                <div class="flex items-center gap-1 text-xs">
                                    <i class="ki-filled ki-time text-warning"></i>
                                    <span class="text-warning font-medium">In Progress</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Completed Tasks -->
                    <div class="kt-card">
                        <div class="kt-card-body p-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h5 class="text-sm font-medium text-secondary-foreground mb-2">Completed Tasks</h5>
                                    <h2 class="text-2xl font-bold text-mono">{{ $completedTasks ?? 0 }}</h2>
                                </div>
                                <div class="flex items-center justify-center size-12 shrink-0 rounded-full bg-success/10">
                                    <i class="ki-filled ki-check-circle text-success text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-border">
                                <div class="flex items-center gap-1 text-xs">
                                    <i class="ki-filled ki-arrow-up text-success"></i>
                                    <span class="text-success font-medium">This Month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Active Users -->
                    <div class="kt-card">
                        <div class="kt-card-body p-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h5 class="text-sm font-medium text-secondary-foreground mb-2">Active Users</h5>
                                    <h2 class="text-2xl font-bold text-mono">{{ $activeUsers ?? 0 }}</h2>
                                </div>
                                <div class="flex items-center justify-center size-12 shrink-0 rounded-full bg-warning/10">
                                    <i class="ki-filled ki-users text-warning text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-border">
                                <div class="flex items-center gap-1 text-xs">
                                    <i class="ki-filled ki-user text-info"></i>
                                    <span class="text-info font-medium">Online Now</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Projects (Larger) and Tasks -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-7.5">
                    <!-- Recent Projects - Takes 2/3 of the width -->
                    <div class="lg:col-span-2 kt-card">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title text-xl font-semibold">Recent Projects</h3>
                            <a href="{{ route('admin.projects.index') }}" class="kt-btn kt-btn-sm kt-btn-outline">
                                View All
                            </a>
                        </div>
                        <div class="kt-card-body p-0">
                            @if($recentProjects && $recentProjects->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-auto table-border">
                                    <thead>
                                        <tr>
                                            <th class="min-w-[250px]">
                                                <span class="text-secondary-foreground text-sm font-normal">Project</span>
                                            </th>
                                            <th class="min-w-[120px]">
                                                <span class="text-secondary-foreground text-sm font-normal">Status</span>
                                            </th>
                                            <th class="min-w-[150px]">
                                                <span class="text-secondary-foreground text-sm font-normal">Progress</span>
                                            </th>
                                            <th class="min-w-[100px]">
                                                <span class="text-secondary-foreground text-sm font-normal">Team</span>
                                            </th>
                                            <th class="w-[80px]"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentProjects as $project)
                                        <tr>
                                            <td>
                                                <div class="flex items-center gap-4">
                                                    <div class="flex items-center justify-center size-10 shrink-0 rounded-full bg-primary/10">
                                                        <i class="ki-filled ki-element-11 text-primary text-lg"></i>
                                                    </div>
                                                    <div class="flex flex-col gap-1">
                                                        <a class="text-base font-semibold text-mono hover:text-primary" href="{{ route('admin.projects.show', $project) }}">
                                                            {{ $project->name }}
                                                        </a>
                                                        <span class="text-sm text-secondary-foreground">
                                                            {{ $project->tasks->count() }} tasks • Created {{ $project->created_at->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="kt-badge kt-badge-outline {{ $project->status === 'active' ? 'kt-badge-success' : ($project->status === 'completed' ? 'kt-badge-primary' : 'kt-badge-warning') }}">
                                                    {{ ucfirst($project->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="flex items-center gap-3">
                                                    <div class="flex-1 bg-secondary rounded-full h-2.5">
                                                        <div class="bg-primary h-2.5 rounded-full" style="width: {{ $project->progress }}%"></div>
                                                    </div>
                                                    <span class="text-sm font-medium text-mono min-w-[40px]">{{ $project->progress }}%</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="flex -space-x-2">
                                                    @foreach($project->users->take(3) as $user)
                                                    <img class="size-8 rounded-full ring-2 ring-background" 
                                                         src="{{ asset('assets/media/avatars/gray/' . (($loop->index % 5) + 1) . '.png') }}" 
                                                         alt="{{ $user->name }}" 
                                                         data-tooltip="{{ $user->name }}"/>
                                                    @endforeach
                                                    @if($project->users->count() > 3)
                                                    <div class="size-8 rounded-full bg-muted flex items-center justify-center ring-2 ring-background">
                                                        <span class="text-xs font-medium">+{{ $project->users->count() - 3 }}</span>
                                                    </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.projects.show', $project) }}" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-outline">
                                                    <i class="ki-filled ki-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-12">
                                <i class="ki-filled ki-folder-open text-5xl text-muted-foreground mb-4"></i>
                                <h4 class="text-lg font-semibold mb-2">No Projects Yet</h4>
                                <p class="text-sm text-secondary-foreground mb-6">Create your first project to get started</p>
                                @if(auth()->user()->role === 'super_admin')
                                <a href="{{ route('admin.projects.create') }}" class="kt-btn kt-btn-primary">
                                    <i class="ki-filled ki-plus"></i>
                                    Create Project
                                </a>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Recent Tasks - Takes 1/3 of the width -->
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title text-lg font-semibold">Recent Tasks</h3>
                            <a href="{{ route('admin.tasks.index') }}" class="kt-btn kt-btn-sm kt-btn-outline">
                                View All
                            </a>
                        </div>
                        <div class="kt-card-body p-0">
                            @if(isset($recentTasks) && $recentTasks->count() > 0)
                            <div class="flex flex-col gap-0">
                                @foreach($recentTasks as $task)
                                <div class="flex items-center justify-between p-4 border-b border-border last:border-b-0">
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center gap-1.5">
                                            @if($task->priority == 'top_urgent')
                                            <i class="ki-filled ki-arrow-up text-destructive"></i>
                                            @elseif($task->priority == 'urgent')
                                            <i class="ki-filled ki-minus text-warning"></i>
                                            @else
                                            <i class="ki-filled ki-arrow-down text-success"></i>
                                            @endif
                                        </div>
                                        <div class="flex flex-col gap-0.5">
                                            <a class="text-sm font-semibold text-mono hover:text-primary" href="{{ route('admin.tasks.show', $task) }}">
                                                {{ Str::limit($task->title, 25) }}
                                            </a>
                                            <div class="flex items-center gap-2 text-xs text-secondary-foreground">
                                                @if($task->project)
                                                <span>{{ Str::limit($task->project->name, 15) }}</span>
                                                <span>•</span>
                                                @endif
                                                @if($task->due_date)
                                                <span>Due {{ $task->due_date->format('M d') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        <span class="kt-badge kt-badge-outline kt-badge-sm
                                            @if($task->status == 'pending') kt-badge-warning
                                            @elseif($task->status == 'in_progress') kt-badge-info
                                            @elseif($task->status == 'completed') kt-badge-primary
                                            @elseif($task->status == 'approved') kt-badge-success
                                            @elseif($task->status == 'rejected') kt-badge-destructive
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                        @if($task->developers->count() > 0)
                                        <div class="flex -space-x-1">
                                            @foreach($task->developers->take(2) as $developer)
                                            <img class="size-5 rounded-full ring-1 ring-background" 
                                                 src="{{ asset('assets/media/avatars/gray/' . (($loop->index % 5) + 1) . '.png') }}" 
                                                 alt="{{ $developer->name }}" 
                                                 data-tooltip="{{ $developer->name }}"/>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-10">
                                <i class="ki-filled ki-file-sheet text-4xl text-muted-foreground mb-3"></i>
                                <h4 class="text-base font-semibold mb-2">No Tasks Yet</h4>
                                <p class="text-sm text-secondary-foreground mb-4">Create your first task to get started</p>
                                @if(auth()->user()->role === 'super_admin')
                                <a href="{{ route('admin.tasks.create') }}" class="kt-btn kt-btn-primary">
                                    <i class="ki-filled ki-plus"></i>
                                    Create Task
                                </a>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Project Progress Overview -->
                @if(isset($recentProjects) && $recentProjects->count() > 0)
                <div class="kt-card mb-5 lg:mb-7.5">
                    <div class="kt-card-header">
                        <h3 class="kt-card-title text-lg font-semibold">Project Progress Overview</h3>
                        <a href="{{ route('admin.projects.index') }}" class="kt-btn kt-btn-sm kt-btn-outline">
                            View All Projects
                        </a>
                    </div>
                    <div class="kt-card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                            @foreach($recentProjects->take(8) as $project)
                            <div class="flex items-center gap-3 p-4 border border-border rounded-lg hover:border-primary hover:bg-primary/5 transition-colors">
                                <div class="flex items-center justify-center size-10 shrink-0 rounded-full bg-primary/10">
                                    <i class="ki-filled ki-element-11 text-primary"></i>
                                </div>
                                <div class="flex-1">
                                    <a class="text-sm font-medium text-mono hover:text-primary block" href="{{ route('admin.projects.show', $project) }}">
                                        {{ Str::limit($project->name, 20) }}
                                    </a>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="flex-1 bg-secondary rounded-full h-1.5">
                                            <div class="bg-primary h-1.5 rounded-full" style="width: {{ $project->progress }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-mono">{{ $project->progress }}%</span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1 text-xs text-secondary-foreground">
                                        <span>{{ $project->tasks->count() }} tasks</span>
                                        <span>•</span>
                                        <span>{{ $project->tasks->where('status', 'approved')->count() }} completed</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Quick Actions -->
                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="kt-card-title text-lg font-semibold">Quick Actions</h3>
                    </div>
                    <div class="kt-card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            @if(auth()->user()->role === 'super_admin')
                            <a href="{{ route('admin.projects.create') }}" class="flex items-center gap-3 p-4 border border-border rounded-lg hover:border-primary hover:bg-primary/5 transition-colors">
                                <div class="flex items-center justify-center size-10 shrink-0 rounded-full bg-primary/10">
                                    <i class="ki-filled ki-element-11 text-primary"></i>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-mono block">New Project</span>
                                    <span class="text-xs text-secondary-foreground">Create a new project</span>
                                </div>
                            </a>
                            
                            <a href="{{ route('admin.tasks.create') }}" class="flex items-center gap-3 p-4 border border-border rounded-lg hover:border-primary hover:bg-primary/5 transition-colors">
                                <div class="flex items-center justify-center size-10 shrink-0 rounded-full bg-info/10">
                                    <i class="ki-filled ki-file-sheet text-info"></i>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-mono block">New Task</span>
                                    <span class="text-xs text-secondary-foreground">Create a new task</span>
                                </div>
                            </a>
                            @endif
                            
                            <a href="{{ route('admin.projects.index') }}" class="flex items-center gap-3 p-4 border border-border rounded-lg hover:border-primary hover:bg-primary/5 transition-colors">
                                <div class="flex items-center justify-center size-10 shrink-0 rounded-full bg-success/10">
                                    <i class="ki-filled ki-chart-line text-success"></i>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-mono block">View Projects</span>
                                    <span class="text-xs text-secondary-foreground">Browse all projects</span>
                                </div>
                            </a>
                            
                            <a href="{{ route('admin.tasks.index') }}" class="flex items-center gap-3 p-4 border border-border rounded-lg hover:border-primary hover:bg-primary/5 transition-colors">
                                <div class="flex items-center justify-center size-10 shrink-0 rounded-full bg-warning/10">
                                    <i class="ki-filled ki-note text-warning"></i>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-mono block">View Tasks</span>
                                    <span class="text-xs text-secondary-foreground">Browse all tasks</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                
            </div>
            <!-- End of Container -->
        </main>
    </div>
</div>
<!-- End of Main -->
@endsection