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
                            Task Details
                        </h1>
                        <div class="flex items-center gap-1 text-sm font-normal">
                            <a class="text-secondary-foreground hover:text-primary" href="{{ route('dashboard') }}">
                                Home
                            </a>
                            <span class="text-muted-foreground text-sm">/</span>
                            <a class="text-secondary-foreground hover:text-primary" href="{{ route('admin.tasks.index') }}">
                                Tasks
                            </a>
                            <span class="text-muted-foreground text-sm">/</span>
                            <span class="text-mono">{{ $task->title }}</span>
                        </div>
                    </div>
                    <div class="flex items-center flex-wrap gap-1.5 lg:gap-3.5">
                        <a class="kt-btn kt-btn-outline" href="{{ route('admin.tasks.index') }}">
                            <i class="ki-filled ki-black-left"></i>
                            Back to Tasks
                        </a>
                        @if(auth()->user()->role === 'super_admin' || auth()->user()->id === $task->assigned_user_id)
                        <a class="kt-btn kt-btn-primary" href="{{ route('admin.tasks.edit', $task) }}">
                            <i class="ki-filled ki-edit"></i>
                            Edit Task
                        </a>
                        @endif
                    </div>
                </div>
                <!-- End of Container -->
            </div>
            <!-- End of Toolbar -->
            
            <!-- Container -->
            <div class="kt-container-fixed">
                <div class="max-w-4xl mx-auto">
                    <!-- Task Header Card -->
                    <div class="kt-card mb-6">
                        <div class="kt-card-header">
                            <div class="flex items-center justify-between">
                                <h3 class="kt-card-title text-xl font-semibold">{{ $task->title }}</h3>
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
                                    <span class="kt-badge kt-badge-outline
                                        @if($task->priority == 'normal') kt-badge-secondary
                                        @elseif($task->priority == 'urgent') kt-badge-warning
                                        @elseif($task->priority == 'top_urgent') kt-badge-destructive
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $task->priority)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Task Details Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Main Task Information -->
                        <div class="lg:col-span-2">
                            <div class="kt-card">
                                <div class="kt-card-header">
                                    <h3 class="kt-card-title">Task Information</h3>
                                </div>
                                <div class="kt-card-body space-y-6">
                                    <!-- Description -->
                                    <div>
                                        <label class="form-label text-sm font-medium text-secondary-foreground mb-2.5">Description</label>
                                        <div class="kt-input bg-muted/50">
                                            {{ $task->description ?: 'No description provided' }}
                                        </div>
                                    </div>

                                    <!-- Project Information -->
                                    <div>
                                        <label class="form-label text-sm font-medium text-secondary-foreground mb-2.5">Project</label>
                                        <div class="kt-input bg-muted/50">
                                            <a href="{{ route('admin.projects.show', $task->project) }}" class="text-primary hover:underline">
                                                {{ $task->project->name }}
                                            </a>
                                        </div>
                                    </div>
                                    <!-- Client Approval Section -->
@if(auth()->user()->role === 'client' && $task->status === 'completed')
<div class="border-t border-border pt-4 mt-4">
    <h4 class="text-sm font-semibold mb-3">Task Approval</h4>
    <p class="text-sm text-secondary-foreground mb-4">
        This task has been marked as completed. Please review and approve or reject it.
    </p>
    <div class="flex gap-2">
        <!-- Approve Form -->
        <form method="POST" action="{{ route('admin.tasks.update', $task) }}" class="inline">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="approved">
            <button type="submit" class="kt-btn kt-btn-success" 
                    onclick="return confirm('Are you sure you want to approve this task?')">
                <i class="ki-filled ki-check-circle"></i>
                Approve Task
            </button>
        </form>
        
        <!-- Reject Form -->
        <form method="POST" action="{{ route('admin.tasks.update', $task) }}" class="inline">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="rejected">
            <button type="submit" class="kt-btn kt-btn-danger" 
                    onclick="return confirm('Are you sure you want to reject this task? The developer will need to rework it.')">
                <i class="ki-filled ki-cross-circle"></i>
                Reject Task
            </button>
        </form>
    </div>
</div>
@endif

<!-- Show Approval/Rejection Status -->
@if($task->status === 'approved')
<div class="alert alert-success mt-4">
    <div class="alert-content">
        <i class="ki-filled ki-check-circle"></i>
        <div class="alert-text">This task has been approved by the client.</div>
    </div>
</div>
@elseif($task->status === 'rejected')
<div class="alert alert-danger mt-4">
    <div class="alert-content">
        <i class="ki-filled ki-cross-circle"></i>
        <div class="alert-text">This task has been rejected by the client and requires rework.</div>
    </div>
</div>
@endif

                                    <!-- Assigned Developer -->
                                    <div>
                                        <label class="form-label text-sm font-medium text-secondary-foreground mb-2.5">Assigned To</label>
                                        <div class="kt-input bg-muted/50">
                                            @if($task->assignedUser)
                                                {{ $task->assignedUser->name }} ({{ $task->assignedUser->email }})
                                            @else
                                                <span class="text-muted-foreground">Not assigned</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Attachment -->
                                    @if($task->attachment)
                                    <div>
                                        <label class="form-label text-sm font-medium text-secondary-foreground mb-2.5">Attachment</label>
                                        <div class="kt-input bg-muted/50">
                                            <a href="{{ asset('storage/' . $task->attachment) }}" target="_blank" class="text-primary hover:underline flex items-center gap-2">
                                                <i class="ki-filled ki-document"></i>
                                                View Attachment
                                            </a>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Task Metadata -->
                        <div class="lg:col-span-1">
                            <div class="kt-card">
                                <div class="kt-card-header">
                                    <h3 class="kt-card-title">Task Details</h3>
                                </div>
                                <div class="kt-card-body space-y-4">
                                    <!-- Status -->
                                    <div>
                                        <label class="form-label text-sm font-medium text-secondary-foreground mb-2.5">Status</label>
                                        <div class="kt-input bg-muted/50">
                                            <span class="kt-badge kt-badge-outline
                                                @if($task->status == 'pending') kt-badge-warning
                                                @elseif($task->status == 'in_progress') kt-badge-info
                                                @elseif($task->status == 'completed') kt-badge-primary
                                                @elseif($task->status == 'approved') kt-badge-success
                                                @elseif($task->status == 'rejected') kt-badge-destructive
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Priority -->
                                    <div>
                                        <label class="form-label text-sm font-medium text-secondary-foreground mb-2.5">Priority</label>
                                        <div class="kt-input bg-muted/50">
                                            <span class="kt-badge kt-badge-outline
                                                @if($task->priority == 'normal') kt-badge-secondary
                                                @elseif($task->priority == 'urgent') kt-badge-warning
                                                @elseif($task->priority == 'top_urgent') kt-badge-destructive
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $task->priority)) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Start Date -->
                                    @if($task->start_date)
                                    <div>
                                        <label class="form-label text-sm font-medium text-secondary-foreground mb-2.5">Start Date</label>
                                        <div class="kt-input bg-muted/50">
                                            {{ $task->start_date->format('M d, Y') }}
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Due Date -->
                                    @if($task->due_date)
                                    <div>
                                        <label class="form-label text-sm font-medium text-secondary-foreground mb-2.5">Due Date</label>
                                        <div class="kt-input bg-muted/50">
                                            {{ $task->due_date->format('M d, Y') }}
                                            @if($task->due_date->isPast() && !in_array($task->status, ['completed', 'approved']))
                                                <span class="kt-badge kt-badge-destructive ml-2">Overdue</span>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Hours -->
                                    @if($task->hours)
                                    <div>
                                        <label class="form-label text-sm font-medium text-secondary-foreground mb-2.5">Estimated Hours</label>
                                        <div class="kt-input bg-muted/50">
                                            {{ $task->hours }} hours
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Created Date -->
                                    <div>
                                        <label class="form-label text-sm font-medium text-secondary-foreground mb-2.5">Created</label>
                                        <div class="kt-input bg-muted/50">
                                            {{ $task->created_at->format('M d, Y \a\t g:i A') }}
                                        </div>
                                    </div>

                                    <!-- Last Updated -->
                                    @if($task->updated_at != $task->created_at)
                                    <div>
                                        <label class="form-label text-sm font-medium text-secondary-foreground mb-2.5">Last Updated</label>
                                        <div class="kt-input bg-muted/50">
                                            {{ $task->updated_at->format('M d, Y \a\t g:i A') }}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 mt-6">
                        <a class="kt-btn kt-btn-outline" href="{{ route('admin.tasks.index') }}">
                            <i class="ki-filled ki-black-left"></i>
                            Back to Tasks
                        </a>
                        @if(auth()->user()->role === 'super_admin' || auth()->user()->id === $task->assigned_user_id)
                        <a class="kt-btn kt-btn-primary" href="{{ route('admin.tasks.edit', $task) }}">
                            <i class="ki-filled ki-edit"></i>
                            Edit Task
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            <!-- End of Container -->
        </main>
    </div>
</div>
@endsection
