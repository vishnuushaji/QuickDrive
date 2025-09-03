@extends('layouts.admin')
@section('content')
<!-- Main -->
<div class="flex flex-col grow items-stretch rounded-xl bg-background border border-input lg:ms-(--sidebar-width) mt-0 lg:mt-[15px] m-[15px]">
    <div class="flex flex-col grow overflow-y-auto pt-5" id="scrollable_content">
        <main class="grow" role="content">
            <!-- Toolbar -->
            <div class="pb-5">
                <!-- Container -->
                <div class="kt-container-fixed flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center flex-wrap gap-1 lg:gap-5">
                        <h1 class="font-medium text-lg text-mono">
                            Tasks
                        </h1>
                        <div class="flex items-center gap-1 text-sm font-normal">
                            <a class="text-secondary-foreground hover:text-primary" href="{{ route('dashboard') }}">
                                Home
                            </a>
                            <span class="text-muted-foreground text-sm">/</span>
                            <span class="text-mono">Tasks</span>
                        </div>
                    </div>
                    <div class="flex items-center flex-wrap gap-1.5 lg:gap-3.5">
                        <a class="kt-btn kt-btn-outline" href="#">
                            <i class="ki-filled ki-exit-down"></i>
                            Export
                        </a>
                        @if(auth()->user()->role === 'super_admin')
                        <a class="kt-btn kt-btn-primary" href="{{ route('admin.tasks.create') }}">
                            <i class="ki-filled ki-plus"></i>
                            Add Task
                        </a>
                        @endif
                    </div>
                </div>
                <!-- End of Container -->
            </div>
            <!-- End of Toolbar -->
            
            <style>
                .hero-bg {
                    background-image: url('{{ asset('assets/media/images/2600x1200/bg-1.png') }}');
                }
                .dark .hero-bg {
                    background-image: url('{{ asset('assets/media/images/2600x1200/bg-1-dark.png') }}');
                }
            </style>
            
            <!-- Container -->
            <div class="kt-container-fixed">
                <div class="flex items-center flex-wrap md:flex-nowrap lg:items-end justify-between border-b border-b-border gap-3 lg:gap-6 mb-5 lg:mb-10">
                    <div class="grid">
                        <div class="kt-scrollable-x-auto">
                            <div class="kt-menu gap-3" data-kt-menu="true">
                                <div class="kt-menu-item border-b-2 border-b-transparent kt-menu-item-active:border-b-primary kt-menu-item-here:border-b-primary here">
                                    <div class="kt-menu-link gap-1.5 pb-2 lg:pb-4 px-2">
                                        <span class="kt-menu-title text-nowrap text-sm text-secondary-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-medium kt-menu-item-here:text-primary kt-menu-item-here:font-medium kt-menu-item-show:text-primary kt-menu-link-hover:text-primary">
                                            All Tasks ({{ $tasks->count() }})
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end grow lg:grow-0 lg:pb-4 gap-2.5 mb-3 lg:mb-0">
                        <!-- Filter by status -->
                        <select class="kt-input kt-input-sm" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        
                       <!-- Filter by priority -->
                        <select class="kt-input kt-input-sm" id="priorityFilter">
                            <option value="">All Priority</option>
                            <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="top_urgent" {{ request('priority') == 'top_urgent' ? 'selected' : '' }}>Top Urgent</option>
                        </select>
                        
                        <!-- View Toggle -->
                        <div class="kt-toggle-group" data-kt-tabs="true">
                            <a class="kt-btn kt-btn-icon active" data-kt-tab-toggle="#tasks_cards" href="#">
                                <i class="ki-filled ki-category"></i>
                            </a>
                            <a class="kt-btn kt-btn-icon" data-kt-tab-toggle="#tasks_list" href="#">
                                <i class="ki-filled ki-row-horizontal"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Container -->
            
            <!-- Container -->
            <div class="kt-container-fixed">
                <!-- begin: tasks -->
                <div class="flex flex-col items-stretch gap-5 lg:gap-7.5">
                    
                    <!-- begin: cards view -->
                    <div id="tasks_cards">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:gap-7.5">
                            @forelse($tasks as $task)
                            <div class="kt-card overflow-hidden grow justify-between">
                                <div class="p-5 mb-5">
                                    <!-- Status and Actions -->
                                    <div class="flex items-center justify-between mb-5">
                                        <div class="flex items-center gap-2">
                                            <span class="kt-badge kt-badge-outline
                                                @if($task->status == 'pending') kt-badge-warning
                                                @elseif($task->status == 'in_progress') kt-badge-info
                                                @elseif($task->status == 'completed') kt-badge-primary
                                                @elseif($task->status == 'approved') kt-badge-success
                                                @elseif($task->status == 'rejected') kt-badge-destructive
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                            <!-- Quick Approval for Clients -->
                                            @if(auth()->user()->role === 'client' && $task->status === 'completed')
                                            <div class="flex gap-2 mt-3">
                                                <form method="POST" action="{{ route('admin.tasks.update', $task) }}" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="kt-btn kt-btn-sm kt-btn-success">
                                                        <i class="ki-filled ki-check"></i>
                                                        Approve
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.tasks.update', $task) }}" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="kt-btn kt-btn-sm kt-btn-danger">
                                                        <i class="ki-filled ki-cross"></i>
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                           @endif
                                           <div class="flex items-center gap-1">
                                                @if($task->priority == 'top_urgent')
                                                <i class="ki-filled ki-arrow-up text-destructive text-xs"></i>
                                                @elseif($task->priority == 'urgent')
                                                <i class="ki-filled ki-minus text-warning text-xs"></i>
                                                @else
                                                <i class="ki-filled ki-arrow-down text-success text-xs"></i>
                                                @endif
                                                <span class="text-xs font-medium text-secondary-foreground">{{ ucfirst(str_replace('_', ' ', $task->priority)) }}</span>
                                            </div>
                                        </div>
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
                                                                <i class="ki-filled ki-setting-3"></i>
                                                            </span>
                                                            <span class="kt-menu-title">Edit</span>
                                                        </a>
                                                    </div>
                                                    <div class="kt-menu-item">
                                                        <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="kt-menu-link w-full text-left">
                                                                <span class="kt-menu-icon">
                                                                    <i class="ki-filled ki-trash"></i>
                                                                </span>
                                                                <span class="kt-menu-title">Delete</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Task Icon/Logo -->
                                    <div class="flex justify-center mb-2">
                                        <div class="flex items-center justify-center size-12 shrink-0 rounded-full bg-info/10">
                                            <i class="ki-filled ki-check-circle text-info text-2xl"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- Task Title and Description -->
                                    <div class="text-center mb-7">
                                        <a class="text-lg font-medium text-mono hover:text-primary" href="{{ route('admin.tasks.show', $task) }}">
                                            {{ $task->title }}
                                        </a>
                                        <div class="text-sm text-secondary-foreground">
                                            {{ Str::limit($task->description, 60) }}
                                        </div>
                                    </div>
                                    
                                    <!-- Assigned Developers -->
                                    <div class="grid justify-center gap-1.5 mb-7.5">
                                        <span class="text-xs uppercase text-secondary-foreground text-center">developers</span>
                                        <div class="flex -space-x-2 justify-center">
                                            @php 
                                                $developers = $task->developers->take(4);
                                                $remainingCount = $task->developers->count() - 4;
                                            @endphp
                                            @if($developers->count() > 0)
                                                @foreach($developers as $developer)
                                                <div class="flex" data-tooltip="{{ $developer->name }}">
                                                    <img class="hover:z-5 relative shrink-0 rounded-full ring-1 ring-background size-7" 
                                                         src="{{ asset('assets/media/avatars/gray/' . (($loop->index % 5) + 1) . '.png') }}" 
                                                         alt="{{ $developer->name }}"/>
                                                </div>
                                                @endforeach
                                                @if($remainingCount > 0)
                                                <div class="flex">
                                                    <span class="hover:z-5 relative inline-flex items-center justify-center shrink-0 rounded-full ring-1 font-semibold leading-none text-2xs size-7 text-primary-foreground ring-background bg-primary">
                                                        +{{ $remainingCount }}
                                                    </span>
                                                </div>
                                                @endif
                                            @else
                                                <span class="text-xs text-muted-foreground">Unassigned</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Task Stats -->
                                    <div class="flex items-center justify-center flex-wrap gap-2 lg:gap-5">
                                        <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-input shrink-0 rounded-md px-2.5 py-2 min-w-24 max-w-auto">
                                            <span class="text-mono text-sm leading-none font-medium">
                                                {{ $task->start_date ? $task->start_date->format('M d') : 'Not set' }}
                                            </span>
                                            <span class="text-secondary-foreground text-xs">Start Date</span>
                                        </div>
                                        <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-input shrink-0 rounded-md px-2.5 py-2 min-w-24 max-w-auto">
                                            <span class="text-mono text-sm leading-none font-medium">
                                                {{ $task->due_date ? $task->due_date->format('M d') : 'No due' }}
                                            </span>
                                            <span class="text-secondary-foreground text-xs">Due Date</span>
                                        </div>
                                        <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-input shrink-0 rounded-md px-2.5 py-2 min-w-24 max-w-auto">
                                            <span class="text-mono text-sm leading-none font-medium">
                                                {{ $task->hours ?? 0 }}h
                                            </span>
                                            <span class="text-secondary-foreground text-xs">Hours</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Project Info -->
                                @if($task->project)
                                <div class="px-5 py-3 bg-accent/30 border-t border-border">
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center justify-center size-6 shrink-0 rounded-full bg-primary/20">
                                        <i class="ki-filled ki-element-11 text-primary text-xs"></i>
                                        </div>
                                        <span class="text-sm font-medium">{{ $task->project->name }}</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @empty
                            <div class="col-span-2">
                                <div class="kt-card p-10 text-center">
                                    <i class="ki-filled ki-check-circle text-5xl text-muted-foreground mb-4"></i>
                                    <h3 class="text-lg font-semibold mb-2">No Tasks Found</h3>
                                    <p class="text-sm text-secondary-foreground mb-4">
                                        @if(auth()->user()->role === 'super_admin')
                                            Get started by creating your first task.
                                        @else
                                            You haven't been assigned any tasks yet.
                                        @endif
                                    </p>
                                    @if(auth()->user()->role === 'super_admin')
                                    <a href="{{ route('admin.tasks.create') }}" class="kt-btn kt-btn-primary">
                                        <i class="ki-filled ki-plus"></i>
                                        Create First Task
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    <!-- end: cards view -->
                     <div class="mt-6">
                    {{ $tasks->withQueryString()->links('pagination::tailwind') }}
                    </div>

                    
                    <!-- begin: list view -->
                    <div class="hidden" id="tasks_list">
                        <div class="flex flex-col gap-5 lg:gap-7.5">
                            @forelse($tasks as $task)
                            <div class="kt-card p-7.5">
                                <div class="flex items-center flex-wrap justify-between gap-5">
                                    <!-- Task Info -->
                                    <div class="flex items-center gap-3.5">
                                        <div class="flex items-center justify-center min-w-12">
                                            <div class="flex items-center justify-center size-12 shrink-0 rounded-full bg-info/10">
                                                <i class="ki-filled ki-check-circle text-info text-xl"></i>
                                            </div>
                                        </div>
                                        <div class="flex flex-col">
                                            <a class="text-lg font-medium text-mono hover:text-primary" href="{{ route('admin.tasks.show', $task) }}">
                                                {{ $task->title }}
                                            </a>
                                            <div class="text-sm text-secondary-foreground">
                                                {{ Str::limit($task->description, 80) }}
                                            </div>
                                            @if($task->project)
                                            <div class="flex items-center gap-1 mt-1">
                                                <i class="ki-filled ki-element-11 text-primary text-xs"></i>
                                                <span class="text-xs text-primary">{{ $task->project->name }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Task Details -->
                                    <div class="flex items-center flex-wrap gap-5 lg:gap-12">
                                        <div class="flex items-center flex-wrap gap-5 lg:gap-14">
                                            <div class="flex items-center lg:justify-center flex-wrap gap-2 lg:gap-5">
                                                <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-input shrink-0 rounded-md px-2.5 py-2 min-w-24 max-w-auto">
                                                    <span class="text-mono text-sm leading-none font-medium">
                                                        {{ $task->start_date ? $task->start_date->format('M d') : 'Not set' }}
                                                    </span>
                                                    <span class="text-secondary-foreground text-xs">Start</span>
                                                </div>
                                                <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-input shrink-0 rounded-md px-2.5 py-2 min-w-24 max-w-auto">
                                                    <span class="text-mono text-sm leading-none font-medium">
                                                        {{ $task->due_date ? $task->due_date->format('M d') : 'No due' }}
                                                    </span>
                                                    <span class="text-secondary-foreground text-xs">Due Date</span>
                                                </div>
                                                <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-input shrink-0 rounded-md px-2.5 py-2 min-w-24 max-w-auto">
                                                    <span class="text-mono text-sm leading-none font-medium">
                                                        {{ $task->hours ?? 0 }}h
                                                    </span>
                                                    <span class="text-secondary-foreground text-xs">Hours</span>
                                                </div>
                                            </div>
                                            <div class="w-[125px] shrink-0 flex items-center gap-2">
                                                @if($task->priority == 'high')
                                                <i class="ki-filled ki-arrow-up text-destructive"></i>
                                                @elseif($task->priority == 'medium')
                                                <i class="ki-filled ki-minus text-warning"></i>
                                                @else
                                                <i class="ki-filled ki-arrow-down text-success"></i>
                                                @endif
                                                <span class="text-sm font-medium">{{ ucfirst($task->priority) }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Developers and Actions -->
                                        <div class="flex items-center gap-5 lg:gap-14">
                                            <!-- Developers -->
                                            <div class="grid justify-end min-w-24">
                                                <div class="flex -space-x-2">
                                                    @php 
                                                        $developers = $task->developers->take(3);
                                                        $remainingCount = $task->developers->count() - 3;
                                                    @endphp
                                                    @if($developers->count() > 0)
                                                        @foreach($developers as $developer)
                                                        <div class="flex" data-tooltip="{{ $developer->name }}">
                                                            <img class="hover:z-5 relative shrink-0 rounded-full ring-1 ring-background size-7" 
                                                                 src="{{ asset('assets/media/avatars/gray/' . (($loop->index % 5) + 1) . '.png') }}" 
                                                                 alt="{{ $developer->name }}"/>
                                                        </div>
                                                        @endforeach
                                                        @if($remainingCount > 0)
                                                        <div class="flex">
                                                            <span class="hover:z-5 relative inline-flex items-center justify-center shrink-0 rounded-full ring-1 font-semibold leading-none text-2xs size-7 text-primary-foreground ring-background bg-primary">
                                                                +{{ $remainingCount }}
                                                            </span>
                                                        </div>
                                                        @endif
                                                    @else
                                                        <span class="text-xs text-muted-foreground">Unassigned</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Actions Menu -->
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
                                                        <div class="kt-menu-item">
                                                            <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="kt-menu-link w-full text-left">
                                                                    <span class="kt-menu-icon">
                                                                        <i class="ki-filled ki-trash"></i>
                                                                    </span>
                                                                    <span class="kt-menu-title">Delete</span>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                            <a href="{{ route('admin.tasks.show', $task) }}" class="kt-btn kt-btn-sm kt-btn-outline">
                                                View Details
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-span-2">
                                <div class="kt-card p-10 text-center">
                                    <i class="ki-filled ki-check-circle text-5xl text-muted-foreground mb-4"></i>
                                    <h3 class="text-lg font-semibold mb-2">No Tasks Found</h3>
                                    <p class="text-sm text-secondary-foreground mb-4">
                                        @if(auth()->user()->role === 'super_admin')
                                            Get started by creating your first task.
                                        @else
                                            You haven't been assigned any tasks yet.
                                        @endif
                                    </p>
                                    @if(auth()->user()->role === 'super_admin')
                                    <a href="{{ route('admin.tasks.create') }}" class="kt-btn kt-btn-primary">
                                        <i class="ki-filled ki-plus"></i>
                                        Create First Task
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    <!-- end: cards view -->
                    
                </div>
                <!-- end: tasks -->
            </div>
            <!-- End of Container -->
        </main>
    </div>
</div>
<!-- End of Main -->

<!-- JavaScript for view toggle and filters -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // View toggle functionality
    const toggleButtons = document.querySelectorAll('[data-kt-tab-toggle]');
    const views = document.querySelectorAll('#tasks_cards, #tasks_list');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all buttons
            toggleButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get target view
            const target = this.getAttribute('data-kt-tab-toggle');
            
            // Hide all views
            views.forEach(view => {
                view.classList.add('hidden');
            });
            
            // Show target view
            const targetView = document.querySelector(target);
            if (targetView) {
                targetView.classList.remove('hidden');
            }
        });
    });

    // Filter functionality
    const statusFilter = document.getElementById('statusFilter');
    const priorityFilter = document.getElementById('priorityFilter');
    
    function updateFilters() {
        const params = new URLSearchParams(window.location.search);
        
        // Update status parameter
        if (statusFilter.value) {
            params.set('status', statusFilter.value);
        } else {
            params.delete('status');
        }
        
        // Update priority parameter
        if (priorityFilter.value) {
            params.set('priority', priorityFilter.value);
        } else {
            params.delete('priority');
        }
        
        // Get the current path without query parameters
        const currentPath = window.location.pathname;
        
        // Construct the new URL
        const newUrl = params.toString() ? `${currentPath}?${params.toString()}` : currentPath;
        
        // Navigate to the new URL
        window.location.href = newUrl;
    }
    
    // Add event listeners to filters
    if (statusFilter) {
        statusFilter.addEventListener('change', updateFilters);
    }
    
    if (priorityFilter) {
        priorityFilter.addEventListener('change', updateFilters);
    }
});
</script>
@endsection