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
                            Projects
                        </h1>
                        <div class="flex items-center gap-1 text-sm font-normal">
                            <a class="text-secondary-foreground hover:text-primary" href="{{ route('dashboard') }}">
                                Home
                            </a>
                            <span class="text-muted-foreground text-sm">/</span>
                            <span class="text-mono">Projects</span>
                        </div>
                    </div>
                    <div class="flex items-center flex-wrap gap-1.5 lg:gap-3.5">
                        <a class="kt-btn kt-btn-outline" href="#">
                            <i class="ki-filled ki-exit-down"></i>
                            Export
                        </a>
                        @if(auth()->user()->role === 'super_admin')
                        <a class="kt-btn kt-btn-primary" href="{{ route('admin.projects.create') }}">
                            <i class="ki-filled ki-plus"></i>
                            Add Project
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
                                            All Projects ({{ $projects->count() }})
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end grow lg:grow-0 lg:pb-4 gap-2.5 mb-3 lg:mb-0">
                        <!-- Filter by status -->
                        <select class="kt-input kt-input-sm" onchange="window.location.href='?status=' + this.value">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        
                        <!-- View Toggle -->
                        <div class="kt-toggle-group" data-kt-tabs="true">
                            <a class="kt-btn kt-btn-icon active" data-kt-tab-toggle="#projects_cards" href="#">
                                <i class="ki-filled ki-category"></i>
                            </a>
                            <a class="kt-btn kt-btn-icon" data-kt-tab-toggle="#projects_list" href="#">
                                <i class="ki-filled ki-row-horizontal"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Container -->
            
            <!-- Container -->
            <div class="kt-container-fixed">
                <!-- begin: projects -->
                <div class="flex flex-col items-stretch gap-5 lg:gap-7.5">
                    
                    <!-- begin: cards view -->
                    <div id="projects_cards">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:gap-7.5">
                            @forelse($projects as $project)
                            <div class="kt-card overflow-hidden grow justify-between">
                                <div class="p-5 mb-5">
                                    <!-- Status and Actions -->
                                    <div class="flex items-center justify-between mb-5">
                                        <span class="kt-badge {{ $project->status === 'active' ? 'kt-badge-success kt-badge-outline' : ($project->status === 'completed' ? 'kt-badge-primary kt-badge-outline' : 'kt-badge-warning kt-badge-outline') }}">
                                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                        </span>
                                        @if(auth()->user()->role === 'super_admin')
                                        <div class="kt-menu" data-kt-menu="true">
                                            <div class="kt-menu-item" data-kt-menu-item-offset="0, 10px" data-kt-menu-item-placement="bottom-end" data-kt-menu-item-toggle="dropdown" data-kt-menu-item-trigger="click">
                                                <button class="kt-menu-toggle kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost">
                                                    <i class="ki-filled ki-dots-vertical text-lg"></i>
                                                </button>
                                                <div class="kt-menu-dropdown kt-menu-default w-full max-w-[200px]" data-kt-menu-dismiss="true">
                                                    <div class="kt-menu-item">
                                                        <a class="kt-menu-link" href="{{ route('admin.projects.show', $project) }}">
                                                            <span class="kt-menu-icon">
                                                                <i class="ki-filled ki-eye"></i>
                                                            </span>
                                                            <span class="kt-menu-title">View</span>
                                                        </a>
                                                    </div>
                                                    <div class="kt-menu-item">
                                                        <a class="kt-menu-link" href="{{ route('admin.projects.edit', $project) }}">
                                                            <span class="kt-menu-icon">
                                                                <i class="ki-filled ki-setting-3"></i>
                                                            </span>
                                                            <span class="kt-menu-title">Edit</span>
                                                        </a>
                                                    </div>
                                                    <div class="kt-menu-item">
                                                        <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
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
                                    
                                    <!-- Project Icon/Logo -->
                                    <div class="flex justify-center mb-2">
                                        <div class="flex items-center justify-center size-12 shrink-0 rounded-full bg-primary/10">
                                            <i class="ki-filled ki-element-11 text-primary text-2xl"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- Project Title and Description -->
                                    <div class="text-center mb-7">
                                        <a class="text-lg font-medium text-mono hover:text-primary" href="{{ route('admin.projects.show', $project) }}">
                                            {{ $project->name }}
                                        </a>
                                        <div class="text-sm text-secondary-foreground">
                                            {{ Str::limit($project->description, 60) }}
                                        </div>
                                    </div>
                                    
                                    <!-- Team Members -->
                                    <div class="grid justify-center gap-1.5 mb-7.5">
                                        <span class="text-xs uppercase text-secondary-foreground text-center">team</span>
                                        <div class="flex -space-x-2 justify-center">
                                            @php 
                                                $allUsers = $project->clients->merge($project->developers)->take(4);
                                                $remainingCount = ($project->clients->count() + $project->developers->count()) - 4;
                                            @endphp
                                            @foreach($allUsers as $user)
                                            <div class="flex" data-tooltip="{{ $user->name }} ({{ $user->pivot->role }})">
                                                <img class="hover:z-5 relative shrink-0 rounded-full ring-1 ring-background size-7" 
                                                     src="{{ asset('assets/media/avatars/gray/' . (($loop->index % 5) + 1) . '.png') }}" 
                                                     alt="{{ $user->name }}"/>
                                            </div>
                                            @endforeach
                                            @if($remainingCount > 0)
                                            <div class="flex">
                                                <span class="hover:z-5 relative inline-flex items-center justify-center shrink-0 rounded-full ring-1 font-semibold leading-none text-2xs size-7 text-primary-foreground ring-background bg-primary">
                                                    +{{ $remainingCount }}
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Project Stats -->
                                    <div class="flex items-center justify-center flex-wrap gap-2 lg:gap-5">
                                        <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-input shrink-0 rounded-md px-2.5 py-2 min-w-24 max-w-auto">
                                            <span class="text-mono text-sm leading-none font-medium">
                                                {{ $project->start_date ? $project->start_date->format('M d') : 'Not set' }}
                                            </span>
                                            <span class="text-secondary-foreground text-xs">Start Date</span>
                                        </div>
                                        <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-input shrink-0 rounded-md px-2.5 py-2 min-w-24 max-w-auto">
                                            <span class="text-mono text-sm leading-none font-medium">
                                                {{ $project->tasks->count() }}
                                            </span>
                                            <span class="text-secondary-foreground text-xs">Tasks</span>
                                        </div>
                                        <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-input shrink-0 rounded-md px-2.5 py-2 min-w-24 max-w-auto">
                                            <span class="text-mono text-sm leading-none font-medium">
                                                {{ $project->tasks->where('status', 'approved')->count() }}
                                            </span>
                                            <span class="text-secondary-foreground text-xs">Completed</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Progress Bar -->
                                <div class="kt-progress kt-progress-primary h-1">
                                    <div class="kt-progress-indicator" style="width: {{ $project->progress }}%"></div>
                                </div>
                            </div>
                            @empty
                            <div class="col-span-2">
                                <div class="kt-card p-10 text-center">
                                    <i class="ki-filled ki-folder-open text-5xl text-muted-foreground mb-4"></i>
                                    <h3 class="text-lg font-semibold mb-2">No Projects Found</h3>
                                    <p class="text-sm text-secondary-foreground mb-4">
                                        @if(auth()->user()->role === 'super_admin')
                                            Get started by creating your first project.
                                        @else
                                            You haven't been assigned to any projects yet.
                                        @endif
                                    </p>
                                    @if(auth()->user()->role === 'super_admin')
                                    <a href="{{ route('admin.projects.create') }}" class="kt-btn kt-btn-primary">
                                        <i class="ki-filled ki-plus"></i>
                                        Create First Project
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    <!-- end: cards view -->
                    
                    <!-- begin: list view -->
                    <div class="hidden" id="projects_list">
                        <div class="flex flex-col gap-5 lg:gap-7.5">
                            @forelse($projects as $project)
                            <div class="kt-card p-7.5">
                                <div class="flex items-center flex-wrap justify-between gap-5">
                                    <!-- Project Info -->
                                    <div class="flex items-center gap-3.5">
                                        <div class="flex items-center justify-center min-w-12">
                                            <div class="flex items-center justify-center size-12 shrink-0 rounded-full bg-primary/10">
                                                <i class="ki-filled ki-element-11 text-primary text-xl"></i>
                                            </div>
                                        </div>
                                        <div class="flex flex-col">
                                            <a class="text-lg font-medium text-mono hover:text-primary" href="{{ route('admin.projects.show', $project) }}">
                                                {{ $project->name }}
                                            </a>
                                            <div class="text-sm text-secondary-foreground">
                                                {{ Str::limit($project->description, 80) }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Project Details -->
                                    <div class="flex items-center flex-wrap gap-5 lg:gap-12">
                                        <div class="flex items-center flex-wrap gap-5 lg:gap-14">
                                            <div class="flex items-center lg:justify-center flex-wrap gap-2 lg:gap-5">
                                                <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-input shrink-0 rounded-md px-2.5 py-2 min-w-24 max-w-auto">
                                                    <span class="text-mono text-sm leading-none font-medium">
                                                        {{ $project->start_date ? $project->start_date->format('M d') : 'Not set' }}
                                                    </span>
                                                    <span class="text-secondary-foreground text-xs">Start Date</span>
                                                </div>
                                                <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-input shrink-0 rounded-md px-2.5 py-2 min-w-24 max-w-auto">
                                                    <span class="text-mono text-sm leading-none font-medium">
                                                        {{ $project->tasks->count() }}
                                                    </span>
                                                    <span class="text-secondary-foreground text-xs">Tasks</span>
                                                </div>
                                                <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-input shrink-0 rounded-md px-2.5 py-2 min-w-24 max-w-auto">
                                                    <span class="text-mono text-sm leading-none font-medium">
                                                        {{ $project->progress }}%
                                                    </span>
                                                    <span class="text-secondary-foreground text-xs">Progress</span>
                                                </div>
                                            </div>
                                            <div class="w-[125px] shrink-0">
                                                <span class="kt-badge {{ $project->status === 'active' ? 'kt-badge-success kt-badge-outline' : ($project->status === 'completed' ? 'kt-badge-primary kt-badge-outline' : 'kt-badge-warning kt-badge-outline') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Team and Actions -->
                                        <div class="flex items-center gap-5 lg:gap-14">
                                            <!-- Team Members -->
                                            <div class="grid justify-end min-w-24">
                                                <div class="flex -space-x-2">
                                                    @php 
                                                        $allUsers = $project->clients->merge($project->developers)->take(3);
                                                        $remainingCount = ($project->clients->count() + $project->developers->count()) - 3;
                                                    @endphp
                                                    @foreach($allUsers as $user)
                                                    <div class="flex" data-tooltip="{{ $user->name }} ({{ $user->pivot->role }})">
                                                        <img class="hover:z-5 relative shrink-0 rounded-full ring-1 ring-background size-7" 
                                                             src="{{ asset('assets/media/avatars/gray/' . (($loop->index % 5) + 1) . '.png') }}" 
                                                             alt="{{ $user->name }}"/>
                                                    </div>
                                                    @endforeach
                                                    @if($remainingCount > 0)
                                                    <div class="flex">
                                                        <span class="hover:z-5 relative inline-flex items-center justify-center shrink-0 rounded-full ring-1 font-semibold leading-none text-2xs size-7 text-primary-foreground ring-background bg-primary">
                                                            +{{ $remainingCount }}
                                                        </span>
                                                    </div>
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
                                                            <a class="kt-menu-link" href="{{ route('admin.projects.show', $project) }}">
                                                                <span class="kt-menu-icon">
                                                                    <i class="ki-filled ki-eye"></i>
                                                                </span>
                                                                <span class="kt-menu-title">View</span>
                                                            </a>
                                                        </div>
                                                        <div class="kt-menu-item">
                                                            <a class="kt-menu-link" href="{{ route('admin.projects.edit', $project) }}">
                                                                <span class="kt-menu-icon">
                                                                    <i class="ki-filled ki-pencil"></i>
                                                                </span>
                                                                <span class="kt-menu-title">Edit</span>
                                                            </a>
                                                        </div>
                                                        <div class="kt-menu-item">
                                                            <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
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
                                            <a href="{{ route('admin.projects.show', $project) }}" class="kt-btn kt-btn-sm kt-btn-outline">
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
                                    <i class="ki-filled ki-folder-open text-5xl text-muted-foreground mb-4"></i>
                                    <h3 class="text-lg font-semibold mb-2">No Projects Found</h3>
                                    <p class="text-sm text-secondary-foreground mb-4">
                                        @if(auth()->user()->role === 'super_admin')
                                            Get started by creating your first project.
                                        @else
                                            You haven't been assigned to any projects yet.
                                        @endif
                                    </p>
                                    @if(auth()->user()->role === 'super_admin')
                                    <a href="{{ route('admin.projects.create') }}" class="kt-btn kt-btn-primary">
                                        <i class="ki-filled ki-plus"></i>
                                        Create First Project
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    <!-- end: cards view -->
                    
                </div>
                <!-- end: projects -->
            </div>
            <!-- End of Container -->
        </main>
    </div>
</div>
<!-- End of Main -->

<!-- JavaScript for view toggle -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // View toggle functionality
    const toggleButtons = document.querySelectorAll('[data-kt-tab-toggle]');
    const views = document.querySelectorAll('#projects_cards, #projects_list');
    
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
});
</script>
@endsection