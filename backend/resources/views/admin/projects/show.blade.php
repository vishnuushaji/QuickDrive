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
                            {{ $project->name }}
                        </h1>
                        <div class="flex items-center gap-1 text-sm font-normal">
                            <a class="text-secondary-foreground hover:text-primary" href="{{ route('dashboard') }}">
                                Home
                            </a>
                            <span class="text-muted-foreground text-sm">/</span>
                            <a class="text-secondary-foreground hover:text-primary" href="{{ route('admin.projects.index') }}">
                                Projects
                            </a>
                            <span class="text-muted-foreground text-sm">/</span>
                            <span class="text-mono">{{ Str::limit($project->name, 20) }}</span>
                        </div>
                    </div>
                    <div class="flex items-center flex-wrap gap-1.5 lg:gap-3.5">
                        <a class="kt-btn kt-btn-outline" href="{{ route('admin.projects.index') }}">
                            <i class="ki-filled ki-black-left"></i>
                            Back to Projects
                        </a>
                        @if(auth()->user()->role === 'super_admin')
                        <a class="kt-btn kt-btn-outline" href="{{ route('admin.projects.edit', $project) }}">
                            <i class="ki-filled ki-edit"></i>
                            Edit Project
                        </a>
                        @endif
                        <a class="kt-btn kt-btn-primary" href="{{ route('admin.tasks.create', ['project_id' => $project->id]) }}">
                            <i class="ki-filled ki-plus"></i>
                            Add Task
                        </a>
                    </div>
                </div>
                <!-- End of Container -->
            </div>
            <!-- End of Toolbar -->
            
            <!-- Container -->
            <div class="kt-container-fixed">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-7.5">
                    
                    <!-- Left Column - Project Details -->
                    <div class="lg:col-span-2 space-y-5 lg:space-y-7.5">
                        
                        <!-- Project Overview Card -->
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center size-10 shrink-0 rounded-full bg-primary/10">
                                        <i class="ki-filled ki-element-11 text-primary text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="kt-card-title text-lg font-semibold">Project Overview</h3>
                                        <span class="text-sm text-secondary-foreground">Complete project information</span>
                                    </div>
                                </div>
                                <span class="kt-badge kt-badge-outline {{ $project->status === 'active' ? 'kt-badge-success' : ($project->status === 'completed' ? 'kt-badge-primary' : 'kt-badge-warning') }}">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </div>
                            <div class="kt-card-body">
                                <!-- Description -->
                                <div class="mb-6">
                                    <h4 class="text-sm font-medium text-secondary-foreground mb-3">Description</h4>
                                    <p class="text-sm leading-relaxed">{{ $project->description ?: 'No description provided for this project.' }}</p>
                                </div>
                                
                                <!-- Progress -->
                                <div class="mb-6">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="text-sm font-medium text-secondary-foreground">Overall Progress</h4>
                                        <span class="text-lg font-bold text-mono">{{ $project->progress }}%</span>
                                    </div>
                                    <div class="kt-progress kt-progress-primary h-2">
                                        <div class="kt-progress-indicator" style="width: {{ $project->progress }}%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs text-secondary-foreground mt-2">
                                        <span>Started</span>
                                        <span>{{ $project->progress < 100 ? 'In Progress' : 'Completed' }}</span>
                                    </div>
                                </div>
                                
                                <!-- Project Dates -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex items-center gap-3 p-3 border border-border rounded-lg">
                                        <div class="flex items-center justify-center size-8 shrink-0 rounded-full bg-success/10">
                                            <i class="ki-filled ki-calendar text-success"></i>
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-medium text-secondary-foreground">Start Date</h5>
                                            <p class="text-sm font-medium">{{ $project->start_date ? $project->start_date->format('M d, Y') : 'Not set' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 p-3 border border-border rounded-lg">
                                        <div class="flex items-center justify-center size-8 shrink-0 rounded-full bg-warning/10">
                                            <i class="ki-filled ki-time text-warning"></i>
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-medium text-secondary-foreground">Due Date</h5>
                                            <p class="text-sm font-medium">{{ $project->end_date ? $project->end_date->format('M d, Y') : 'Not set' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tasks Card -->
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h3 class="kt-card-title text-lg font-semibold">Project Tasks ({{ $project->tasks->count() }})</h3>
                                <a href="{{ route('admin.tasks.create', ['project_id' => $project->id]) }}" class="kt-btn kt-btn-sm kt-btn-primary">
                                    <i class="ki-filled ki-plus"></i>
                                    Add Task
                                </a>
                            </div>
                            <div class="kt-card-body p-0">
                                @if($project->tasks->count() > 0)
                                <div class="kt-scrollable-x-auto">
                                    <table class="table table-auto table-border">
                                        <thead>
                                            <tr>
                                                <th class="min-w-[200px]">
                                                    <span class="text-secondary-foreground text-sm font-normal">Task</span>
                                                </th>
                                                <th class="min-w-[120px]">
                                                    <span class="text-secondary-foreground text-sm font-normal">Assigned To</span>
                                                </th>
                                                <th class="min-w-[100px]">
                                                    <span class="text-secondary-foreground text-sm font-normal">Priority</span>
                                                </th>
                                                <th class="min-w-[100px]">
                                                    <span class="text-secondary-foreground text-sm font-normal">Status</span>
                                                </th>
                                                <th class="min-w-[100px]">
                                                    <span class="text-secondary-foreground text-sm font-normal">Due Date</span>
                                                </th>
                                                <th class="w-[60px]"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($project->tasks as $task)
                                            <tr>
                                                <td>
                                                    <div class="flex flex-col gap-0.5">
                                                        <a class="text-sm font-semibold text-mono hover:text-primary" href="{{ route('admin.tasks.show', $task) }}">
                                                            {{ $task->title }}
                                                        </a>
                                                        <span class="text-xs text-secondary-foreground">
                                                            {{ Str::limit($task->description, 40) }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($task->developers->count() > 0)
                                                    <div class="flex -space-x-2">
                                                        @foreach($task->developers->take(2) as $developer)
                                                        <img class="size-6 rounded-full ring-1 ring-background" 
                                                             src="{{ asset('assets/media/avatars/gray/' . (($loop->index % 5) + 1) . '.png') }}" 
                                                             alt="{{ $developer->name }}" 
                                                             data-tooltip="{{ $developer->name }}"/>
                                                        @endforeach
                                                        @if($task->developers->count() > 2)
                                                        <span class="size-6 rounded-full ring-1 ring-background bg-primary text-white text-xs flex items-center justify-center">
                                                            +{{ $task->developers->count() - 2 }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                    @else
                                                    <span class="text-xs text-muted-foreground">Unassigned</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-1.5">
                                                        @if($task->priority == 'top_urgent')
                                                        <i class="ki-filled ki-arrow-up text-destructive"></i>
                                                        @elseif($task->priority == 'urgent')
                                                        <i class="ki-filled ki-minus text-warning"></i>
                                                        @else
                                                        <i class="ki-filled ki-arrow-down text-success"></i>
                                                        @endif
                                                        <span class="text-sm">{{ ucfirst(str_replace('_', ' ', $task->priority)) }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="kt-badge kt-badge-outline kt-badge-sm
                                                        @if($task->status == 'pending') kt-badge-warning
                                                        @elseif($task->status == 'in_progress') kt-badge-info
                                                        @elseif($task->status == 'completed') kt-badge-primary
                                                        @elseif($task->status == 'approved') kt-badge-success
                                                        @elseif($task->status == 'rejected') kt-badge-destructive
                                                        @endif">
                                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($task->due_date)
                                                    <div class="flex flex-col gap-0.5">
                                                        <span class="text-sm font-medium">{{ $task->due_date->format('M d') }}</span>
                                                        <span class="text-xs text-secondary-foreground">{{ $task->due_date->diffForHumans() }}</span>
                                                    </div>
                                                    @else
                                                    <span class="text-sm text-muted-foreground">No due date</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(auth()->user()->role === 'super_admin')
                                                    <div class="kt-menu" data-kt-menu="true">
                                                        <div class="kt-menu-item" data-kt-menu-item-offset="0, 10px" data-kt-menu-item-placement="bottom-end" data-kt-menu-item-toggle="dropdown" data-kt-menu-item-trigger="click">
                                                            <button class="kt-menu-toggle kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost">
                                                                <i class="ki-filled ki-dots-vertical text-lg"></i>
                                                            </button>
                                                            <div class="kt-menu-dropdown kt-menu-default w-full max-w-[200px]" data-kt-menu-dismiss="true">
                                                                <div class="kt-menu-item">
                                                                    <a class="kt-menu-link" href="{{ route('admin.tasks.show', $task) }}">
                                                                        <span class="kt-menu-icon">
                                                                            <i class="ki-filled ki-eye"></i>
                                                                        </span>
                                                                        <span class="kt-menu-title">View</span>
                                                                    </a>
                                                                </div>
                                                                <div class="kt-menu-item">
                                                                    <a class="kt-menu-link" href="{{ route('admin.tasks.edit', $task) }}">
                                                                        <span class="kt-menu-icon">
                                                                            <i class="ki-filled ki-pencil"></i>
                                                                        </span>
                                                                        <span class="kt-menu-title">Edit</span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <a href="{{ route('admin.tasks.show', $task) }}" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-outline">
                                                        <i class="ki-filled ki-eye"></i>
                                                    </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-10">
                                    <i class="ki-filled ki-file-sheet text-4xl text-muted-foreground mb-3"></i>
                                    <h4 class="text-base font-semibold mb-2">No Tasks Yet</h4>
                                    <p class="text-sm text-secondary-foreground mb-4">Add your first task to get started</p>
                                    <a href="{{ route('admin.tasks.create', ['project_id' => $project->id]) }}" class="kt-btn kt-btn-primary">
                                        <i class="ki-filled ki-plus"></i>
                                        Create First Task
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                    </div>
                    
                    <!-- Right Column - Team & Stats -->
                    <div class="space-y-5 lg:space-y-7.5">
                        
                        <!-- Project Stats Card -->
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h3 class="kt-card-title text-lg font-semibold">Project Statistics</h3>
                            </div>
                            <div class="kt-card-body">
                                <div class="space-y-4">
                                    <!-- Total Tasks -->
                                    <div class="flex items-center justify-between p-3 bg-accent/30 rounded-lg">
                                        <div class="flex items-center gap-2">
                                            <i class="ki-filled ki-file-sheet text-primary"></i>
                                            <span class="text-sm font-medium">Total Tasks</span>
                                        </div>
                                        <span class="text-lg font-bold text-mono">{{ $project->tasks->count() }}</span>
                                    </div>
                                    
                                    <!-- Completed Tasks -->
                                    <div class="flex items-center justify-between p-3 bg-success/10 rounded-lg">
                                        <div class="flex items-center gap-2">
                                            <i class="ki-filled ki-check-circle text-success"></i>
                                            <span class="text-sm font-medium">Completed</span>
                                        </div>
                                        <span class="text-lg font-bold text-mono text-success">{{ $project->tasks->where('status', 'approved')->count() }}</span>
                                    </div>
                                    
                                    <!-- In Progress Tasks -->
                                    <div class="flex items-center justify-between p-3 bg-info/10 rounded-lg">
                                        <div class="flex items-center gap-2">
                                            <i class="ki-filled ki-time text-info"></i>
                                            <span class="text-sm font-medium">In Progress</span>
                                        </div>
                                        <span class="text-lg font-bold text-mono text-info">{{ $project->tasks->where('status', 'in_progress')->count() }}</span>
                                    </div>
                                    
                                    <!-- Pending Tasks -->
                                    <div class="flex items-center justify-between p-3 bg-warning/10 rounded-lg">
                                        <div class="flex items-center gap-2">
                                            <i class="ki-filled ki-timer text-warning"></i>
                                            <span class="text-sm font-medium">Pending</span>
                                        </div>
                                        <span class="text-lg font-bold text-mono text-warning">{{ $project->tasks->where('status', 'pending')->count() }}</span>
                                    </div>
                                    
                                    @if($project->tasks->where('status', 'rejected')->count() > 0)
                                    <!-- Rejected Tasks -->
                                    <div class="flex items-center justify-between p-3 bg-destructive/10 rounded-lg">
                                        <div class="flex items-center gap-2">
                                            <i class="ki-filled ki-cross-circle text-destructive"></i>
                                            <span class="text-sm font-medium">Rejected</span>
                                        </div>
                                        <span class="text-lg font-bold text-mono text-destructive">{{ $project->tasks->where('status', 'rejected')->count() }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Team Members Card -->
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h3 class="kt-card-title text-lg font-semibold">Team Members</h3>
                                <span class="kt-badge kt-badge-outline">{{ $project->clients->count() + $project->developers->count() }} Members</span>
                            </div>
                            <div class="kt-card-body">
                                <!-- Clients Section -->
                                @if($project->clients->count() > 0)
                                <div class="mb-6">
                                    <h4 class="text-xs font-semibold text-secondary-foreground mb-3 uppercase tracking-wider">Clients</h4>
                                    <div class="space-y-3">
                                        @foreach($project->clients as $client)
                                        <div class="flex items-center gap-3 p-2 hover:bg-accent/20 rounded-lg transition-colors">
                                            <img src="{{ asset('assets/media/avatars/gray/' . (($loop->index % 5) + 1) . '.png') }}" 
                                                 class="size-9 rounded-full ring-2 ring-primary/20" alt="{{ $client->name }}">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium">{{ $client->name }}</p>
                                                <p class="text-xs text-secondary-foreground">{{ $client->email }}</p>
                                            </div>
                                            <span class="kt-badge kt-badge-sm kt-badge-outline kt-badge-primary">Client</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                
                                <!-- Developers Section -->
                                @if($project->developers->count() > 0)
                                <div>
                                    <h4 class="text-xs font-semibold text-secondary-foreground mb-3 uppercase tracking-wider">Developers</h4>
                                    <div class="space-y-3">
                                        @foreach($project->developers as $developer)
                                        <div class="flex items-center gap-3 p-2 hover:bg-accent/20 rounded-lg transition-colors">
                                            <img src="{{ asset('assets/media/avatars/gray/' . (($loop->index % 5) + 1) . '.png') }}" 
                                                 class="size-9 rounded-full ring-2 ring-success/20" alt="{{ $developer->name }}">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium">{{ $developer->name }}</p>
                                                <p class="text-xs text-secondary-foreground">{{ $developer->email }}</p>
                                            </div>
                                            <span class="kt-badge kt-badge-sm kt-badge-outline kt-badge-success">Developer</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                
                                @if($project->clients->count() == 0 && $project->developers->count() == 0)
                                <div class="text-center py-6">
                                    <i class="ki-filled ki-users text-3xl text-muted-foreground mb-2"></i>
                                    <p class="text-sm text-secondary-foreground">No team members assigned yet</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Project Actions Card -->
                        @if(auth()->user()->role === 'super_admin')
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h3 class="kt-card-title text-lg font-semibold">Project Actions</h3>
                            </div>
                            <div class="kt-card-body">
                                <div class="space-y-3">
                                    <a href="{{ route('admin.projects.edit', $project) }}" class="flex items-center gap-3 p-3 border border-border rounded-lg hover:border-primary hover:bg-primary/5 transition-colors">
                                        <div class="flex items-center justify-center size-8 shrink-0 rounded-full bg-primary/10">
                                            <i class="ki-filled ki-edit text-primary"></i>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium block">Edit Project</span>
                                            <span class="text-xs text-secondary-foreground">Update project details</span>
                                        </div>
                                    </a>
                                    
                                    <a href="{{ route('admin.tasks.create', ['project_id' => $project->id]) }}" class="flex items-center gap-3 p-3 border border-border rounded-lg hover:border-primary hover:bg-primary/5 transition-colors">
                                        <div class="flex items-center justify-center size-8 shrink-0 rounded-full bg-info/10">
                                            <i class="ki-filled ki-plus text-info"></i>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium block">Add Task</span>
                                            <span class="text-xs text-secondary-foreground">Create new task</span>
                                        </div>
                                    </a>
                                    
                                    <div class="kt-separator"></div>
                                    
                                    <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this project and all its tasks?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex items-center gap-3 p-3 border border-destructive/20 rounded-lg hover:border-destructive hover:bg-destructive/5 transition-colors w-full text-left">
                                            <div class="flex items-center justify-center size-8 shrink-0 rounded-full bg-destructive/10">
                                                <i class="ki-filled ki-trash text-destructive"></i>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium block text-destructive">Delete Project</span>
                                                <span class="text-xs text-destructive/70">Permanently remove project</span>
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                    </div>
                </div>
            </div>
            <!-- End of Container -->
        </main>
    </div>
</div>
<!-- End of Main -->
@endsection