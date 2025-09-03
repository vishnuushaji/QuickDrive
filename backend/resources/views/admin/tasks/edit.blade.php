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
                            Edit Task
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
                            <a class="text-secondary-foreground hover:text-primary" href="{{ route('admin.tasks.show', $task) }}">
                                {{ Str::limit($task->title, 30) }}
                            </a>
                            <span class="text-muted-foreground text-sm">/</span>
                            <span class="text-mono">Edit</span>
                        </div>
                    </div>
                    <div class="flex items-center flex-wrap gap-1.5 lg:gap-3.5">
                        @if(auth()->user()->role === 'super_admin')
                        <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" class="inline" 
                              onsubmit="return confirm('Are you sure you want to delete this task?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="kt-btn kt-btn-danger">
                                <i class="ki-filled ki-trash"></i>
                                Delete Task
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                <!-- End of Container -->
            </div>
            <!-- End of Toolbar -->

            <!-- Container -->
            <div class="kt-container-fixed">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Task Details</h3>
                        <div class="card-toolbar">
                            <span class="badge 
                                @if($task->status == 'pending') badge-warning
                                @elseif($task->status == 'in_progress') badge-info
                                @elseif($task->status == 'completed') badge-primary
                                @elseif($task->status == 'approved') badge-success
                                @elseif($task->status == 'rejected') badge-danger
                                @endif">
                                Current Status: {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.tasks.update', $task) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="space-y-5">
                                <!-- Project -->
                                <div>
                                    <label class="kt-form-label required">Project</label>
                                    <select name="project_id" class="kt-select" data-kt-select="true" required>
                                        <option value="">Select Project</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" 
                                                {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                                                {{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Task Title -->
                                <div>
                                    <label class="kt-form-label required">Task Title</label>
                                    <input type="text" name="title" value="{{ old('title', $task->title) }}" 
                                           class="kt-input @error('title') kt-input-invalid @enderror" 
                                           placeholder="Enter task title" required>
                                    @error('title')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div>
                                    <label class="kt-form-label">Description</label>
                                    <textarea name="description" rows="4" 
                                              class="kt-input @error('description') kt-input-invalid @enderror"
                                              placeholder="Enter task description">{{ old('description', $task->description) }}</textarea>
                                    @error('description')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Assign To -->
                                <div>
                                    <label class="kt-form-label">Assign To</label>
                                    <select name="assigned_user_id" class="kt-select" data-kt-select="true">
                                        <option value="">Unassigned</option>
                                        @foreach($developers as $developer)
                                            <option value="{{ $developer->id }}" 
                                                {{ old('assigned_user_id', $task->assigned_user_id) == $developer->id ? 'selected' : '' }}>
                                                {{ $developer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_user_id')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                    @if($task->assignedUser)
                                        <div class="kt-form-text">Currently assigned to: {{ $task->assignedUser->name }}</div>
                                    @endif
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="kt-form-label required">Status</label>
                                    <select name="status" class="kt-select" data-kt-select="true" required>
                                        <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>
                                            Pending
                                        </option>
                                        <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>
                                            In Progress
                                        </option>
                                        <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>
                                            Completed (Pending Approval)
                                        </option>
                                        <option value="approved" {{ old('status', $task->status) == 'approved' ? 'selected' : '' }}>
                                            Approved
                                        </option>
                                        <option value="rejected" {{ old('status', $task->status) == 'rejected' ? 'selected' : '' }}>
                                            Rejected
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Priority -->
                                <div>
                                    <label class="kt-form-label required">Priority</label>
                                    <select name="priority" class="kt-select" data-kt-select="true" required>
                                        <option value="normal" {{ old('priority', $task->priority) == 'normal' ? 'selected' : '' }}>
                                            Normal
                                        </option>
                                        <option value="urgent" {{ old('priority', $task->priority) == 'urgent' ? 'selected' : '' }}>
                                            Urgent
                                        </option>
                                                                               <option value="top_urgent" {{ old('priority', $task->priority) == 'top_urgent' ? 'selected' : '' }}>
                                            Top Urgent
                                        </option>
                                    </select>
                                    @error('priority')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Dates -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="kt-form-label">Start Date</label>
                                        <input type="date" name="start_date" 
                                               value="{{ old('start_date', $task->start_date ? $task->start_date->format('Y-m-d') : '') }}" 
                                               class="kt-input @error('start_date') kt-input-invalid @enderror">
                                        @error('start_date')
                                            <div class="kt-form-invalid">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="kt-form-label">Due Date</label>
                                        <input type="date" name="due_date" 
                                               value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}" 
                                               class="kt-input @error('due_date') kt-input-invalid @enderror">
                                        @error('due_date')
                                            <div class="kt-form-invalid">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Estimated Hours -->
                                <div>
                                    <label class="kt-form-label">Estimated Hours</label>
                                    <input type="number" name="hours" value="{{ old('hours', $task->hours) }}" 
                                           min="1" class="kt-input @error('hours') kt-input-invalid @enderror" 
                                           placeholder="Enter estimated hours">
                                    @error('hours')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Attachment -->
                                <div>
                                    <label class="kt-form-label">Attachment</label>
                                    @if($task->attachment)
                                        <div class="mb-3">
                                            <p class="text-sm text-secondary-foreground mb-2">Current attachment:</p>
                                            <a href="{{ Storage::url($task->attachment) }}" target="_blank" 
                                               class="flex items-center gap-2 text-sm link">
                                                <i class="ki-filled ki-file"></i>
                                                View current attachment
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file" name="attachment" 
                                           class="kt-input @error('attachment') kt-input-invalid @enderror">
                                    @error('attachment')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                    <div class="kt-form-text">Max file size: 10MB. Leave empty to keep current attachment.</div>
                                </div>

                                <!-- Task Info -->
                                <div class="card bg-secondary">
                                    <div class="card-body">
                                        <h4 class="text-sm font-semibold mb-3">Task Information</h4>
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <span class="text-secondary-foreground">Created:</span>
                                                <p class="font-medium">{{ $task->created_at->format('M d, Y h:i A') }}</p>
                                            </div>
                                            <div>
                                                <span class="text-secondary-foreground">Last Updated:</span>
                                                <p class="font-medium">{{ $task->updated_at->format('M d, Y h:i A') }}</p>
                                            </div>
                                            @if($task->project)
                                            <div>
                                                <span class="text-secondary-foreground">Project:</span>
                                                <p class="font-medium">{{ $task->project->name }}</p>
                                            </div>
                                            @endif
                                            @if($task->assignedUser)
                                            <div>
                                                <span class="text-secondary-foreground">Assigned To:</span>
                                                <p class="font-medium">{{ $task->assignedUser->name }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="flex gap-3 pt-5">
                                    <button type="submit" class="kt-btn kt-btn-primary">
                                        <i class="ki-filled ki-check"></i>
                                        Update Task
                                    </button>
                                    <a href="{{ route('admin.tasks.show', $task) }}" class="kt-btn kt-btn-outline">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Success Message -->
                @if(session('success'))
                <div class="alert alert-success mt-5">
                    <div class="alert-content">
                        <i class="ki-filled ki-check-circle"></i>
                        <div class="alert-text">{{ session('success') }}</div>
                    </div>
                </div>
                @endif

                <!-- Warning for Status Changes -->
                @if($task->status === 'approved')
                <div class="alert alert-info mt-5">
                    <div class="alert-content">
                        <i class="ki-filled ki-information-circle"></i>
                        <div class="alert-text">
                            This task has been approved. Changing its status will affect the project progress calculation.
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <!-- End of Container -->
        </main>
    </div>
</div>
<!-- End of Main -->
@endsection

@push('scripts')
<script>
    // Initialize Select2 or KT Select if needed
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all selects with data-kt-select attribute
        const selects = document.querySelectorAll('[data-kt-select]');
        selects.forEach(select => {
            // If using KT UI components
            if (typeof KTSelect !== 'undefined') {
                new KTSelect(select);
            }
        });
    });
</script>
@endpush