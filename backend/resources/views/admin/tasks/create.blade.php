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
                            Create Task
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
                            <span class="text-mono">Create</span>
                        </div>
                    </div>
                </div>
                <!-- End of Container -->
            </div>
            <!-- End of Toolbar -->

            <!-- Container -->
            <div class="kt-container-fixed">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">New Task Details</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.tasks.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-5">
                                <!-- Project Selection -->
                                <div>
                                    <label class="kt-form-label required">Project</label>
                                    <select name="project_id" class="kt-select @error('project_id') kt-input-invalid @enderror" 
                                            data-kt-select="true" data-placeholder="Select project" required>
                                        <option value="">Select Project</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
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
                                    <input type="text" name="title" value="{{ old('title') }}" 
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
                                              placeholder="Describe the task details...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Developer Assignment -->
                                <div>
                                    <label class="kt-form-label">Assign To Developer</label>
                                    <select name="assigned_user_id" class="kt-select @error('assigned_user_id') kt-input-invalid @enderror" 
                                            data-kt-select="true" data-placeholder="Select developer">
                                        <option value="">Select Developer</option>
                                        @foreach($developers as $developer)
                                            <option value="{{ $developer->id }}" {{ old('assigned_user_id') == $developer->id ? 'selected' : '' }}>
                                                {{ $developer->name }} - {{ $developer->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_user_id')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                    <div class="kt-form-help">Leave empty to assign later</div>
                                </div>

                                <!-- Priority -->
                                <div>
                                    <label class="kt-form-label required">Priority Level</label>
                                    <select name="priority" class="kt-select @error('priority') kt-input-invalid @enderror" 
                                            data-kt-select="true" data-placeholder="Select priority" required>
                                        <option value="">Select Priority</option>
                                        <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>
                                            Normal Priority
                                        </option>
                                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>
                                            Urgent Priority
                                        </option>
                                        <option value="top_urgent" {{ old('priority') == 'top_urgent' ? 'selected' : '' }}>
                                            Top Urgent Priority
                                        </option>
                                    </select>
                                    @error('priority')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Date Fields -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="kt-form-label">Start Date</label>
                                        <input type="date" name="start_date" value="{{ old('start_date') }}" 
                                               class="kt-input @error('start_date') kt-input-invalid @enderror">
                                        @error('start_date')
                                            <div class="kt-form-invalid">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="kt-form-label">Due Date</label>
                                        <input type="date" name="due_date" value="{{ old('due_date') }}" 
                                               class="kt-input @error('due_date') kt-input-invalid @enderror">
                                        @error('due_date')
                                            <div class="kt-form-invalid">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Estimated Hours -->
                                <div>
                                    <label class="kt-form-label">Estimated Hours</label>
                                    <input type="number" name="hours" value="{{ old('hours') }}" min="0.5" step="0.5"
                                           class="kt-input @error('hours') kt-input-invalid @enderror"
                                           placeholder="e.g., 8.5">
                                    @error('hours')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                    <div class="kt-form-help">Enter estimated hours for this task</div>
                                </div>

                                <!-- File Attachment -->
                                <div>
                                    <label class="kt-form-label">Attachment</label>
                                    <input type="file" name="attachment" 
                                           class="kt-input @error('attachment') kt-input-invalid @enderror"
                                           accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.zip">
                                    @error('attachment')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                    <div class="kt-form-help">Max file size: 10MB. Supported formats: PDF, DOC, TXT, Images, ZIP</div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-3 pt-5">
                                    <button type="submit" class="kt-btn kt-btn-primary">
                                        <i class="ki-filled ki-check"></i>
                                        Create Task
                                    </button>
                                    <button type="submit" name="action" value="draft" class="kt-btn kt-btn-secondary">
                                        <i class="ki-filled ki-file-down"></i>
                                        Save as Draft
                                    </button>
                                    <a href="{{ route('admin.tasks.index') }}" class="kt-btn kt-btn-outline">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End of Container -->
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-set start date to today if not set
    const startDateInput = document.querySelector('input[name="start_date"]');
    if (startDateInput && !startDateInput.value) {
        const today = new Date().toISOString().split('T')[0];
        startDateInput.value = today;
    }
    
    // Initialize Select2 if kt-select is available
    if (typeof KTSelect !== 'undefined') {
        const selects = document.querySelectorAll('[data-kt-select="true"]');
        selects.forEach(select => {
            new KTSelect(select);
        });
    }
    
    // Date validation - ensure due date is after start date
    const dueDateInput = document.querySelector('input[name="due_date"]');
    if (startDateInput && dueDateInput) {
        startDateInput.addEventListener('change', function() {
            if (dueDateInput.value && dueDateInput.value < startDateInput.value) {
                dueDateInput.value = startDateInput.value;
            }
            dueDateInput.min = startDateInput.value;
        });
        
        dueDateInput.addEventListener('change', function() {
            if (startDateInput.value && dueDateInput.value < startDateInput.value) {
                alert('Due date cannot be before start date');
                dueDateInput.value = startDateInput.value;
            }
        });
    }
    
    // File upload preview
    const fileInput = document.querySelector('input[type="file"]');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
                const maxSize = 10; // 10MB
                
                if (fileSize > maxSize) {
                    alert('File size exceeds 10MB limit');
                    fileInput.value = '';
                    return;
                }
                
                // Show file info
                let fileInfo = fileInput.parentNode.querySelector('.file-info');
                if (!fileInfo) {
                    fileInfo = document.createElement('div');
                    fileInfo.className = 'file-info kt-form-help mt-2';
                    fileInput.parentNode.appendChild(fileInfo);
                }
                fileInfo.innerHTML = `<i class="ki-filled ki-file"></i> ${file.name} (${fileSize}MB)`;
            }
        });
    }
    
    // Priority visual enhancement
    const prioritySelect = document.querySelector('select[name="priority"]');
    if (prioritySelect) {
        function updatePriorityVisual() {
            const value = prioritySelect.value;
            const selectWrapper = prioritySelect.closest('.kt-select-wrapper') || prioritySelect.parentNode;
            
            // Remove existing classes
            selectWrapper.classList.remove('priority-normal', 'priority-urgent', 'priority-top-urgent');
            
            // Add new class based on priority
            if (value) {
                selectWrapper.classList.add(`priority-${value.replace('_', '-')}`);
            }
        }
        
        prioritySelect.addEventListener('change', updatePriorityVisual);
        updatePriorityVisual(); // Initial call
    }
    
    // Form validation before submit
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitButton = e.submitter;
            const isDraft = submitButton && submitButton.getAttribute('value') === 'draft';
            
            // Skip validation for draft saves
            if (isDraft) {
                return true;
            }
            
            // Validate required fields
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('kt-input-invalid');
                    isValid = false;
                    
                    // Show error message if not already shown
                    let errorMsg = field.parentNode.querySelector('.kt-form-invalid');
                    if (!errorMsg) {
                        errorMsg = document.createElement('div');
                        errorMsg.className = 'kt-form-invalid';
                        errorMsg.textContent = 'This field is required';
                        field.parentNode.appendChild(errorMsg);
                    }
                } else {
                    field.classList.remove('kt-input-invalid');
                    const errorMsg = field.parentNode.querySelector('.kt-form-invalid:not(.server-error)');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                // Focus on first invalid field
                const firstInvalid = form.querySelector('.kt-input-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
        
        // Remove validation errors on input
        const inputs = form.querySelectorAll('.kt-input, .kt-select');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('kt-input-invalid');
                    const errorMsg = this.parentNode.querySelector('.kt-form-invalid:not(.server-error)');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }
            });
        });
    }
});
</script>

<style>
/* Priority visual styles */
.priority-normal {
    border-left: 3px solid var(--kt-success) !important;
}
.priority-urgent {
    border-left: 3px solid var(--kt-warning) !important;
}
.priority-top-urgent {
    border-left: 3px solid var(--kt-danger) !important;
}

/* File info styling */
.file-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--kt-muted-foreground);
}

.file-info i {
    color: var(--kt-primary);
}
</style>
@endpush