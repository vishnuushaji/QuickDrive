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
                            Edit Project
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
                            <a class="text-secondary-foreground hover:text-primary" href="{{ route('admin.projects.show', $project) }}">
                                {{ $project->name }}
                            </a>
                            <span class="text-muted-foreground text-sm">/</span>
                            <span class="text-mono">Edit</span>
                        </div>
                    </div>
                    <div class="flex items-center flex-wrap gap-1.5 lg:gap-3.5">
                        <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline" 
                              onsubmit="return confirm('Are you sure you want to delete this project? This will also delete all associated tasks.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="kt-btn kt-btn-danger">
                                <i class="ki-filled ki-trash"></i>
                                Delete Project
                            </button>
                        </form>
                    </div>
                </div>
                <!-- End of Container -->
            </div>
            <!-- End of Toolbar -->

            <!-- Container -->
            <div class="kt-container-fixed">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Project Details</h3>
                        <div class="card-toolbar">
                            <span class="badge {{ $project->status === 'active' ? 'badge-success' : ($project->status === 'completed' ? 'badge-primary' : 'badge-warning') }}">
                                Current Status: {{ ucfirst($project->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.projects.update', $project) }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="space-y-5">
                                <!-- Project Name -->
                                <div>
                                    <label class="kt-form-label required">Project Name</label>
                                    <input type="text" name="name" value="{{ old('name', $project->name) }}" 
                                           class="kt-input @error('name') kt-input-invalid @enderror" 
                                           placeholder="Enter project name" required>
                                    @error('name')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div>
                                    <label class="kt-form-label">Description</label>
                                    <textarea name="description" rows="4" 
                                              class="kt-input @error('description') kt-input-invalid @enderror"
                                              placeholder="Enter project description">{{ old('description', $project->description) }}</textarea>
                                    @error('description')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Dates -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="kt-form-label">Start Date</label>
                                        <input type="date" name="start_date" 
                                               value="{{ old('start_date', $project->start_date ? $project->start_date->format('Y-m-d') : '') }}" 
                                               class="kt-input @error('start_date') kt-input-invalid @enderror">
                                        @error('start_date')
                                            <div class="kt-form-invalid">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="kt-form-label">End Date</label>
                                        <input type="date" name="end_date" 
                                               value="{{ old('end_date', $project->end_date ? $project->end_date->format('Y-m-d') : '') }}" 
                                               class="kt-input @error('end_date') kt-input-invalid @enderror">
                                        @error('end_date')
                                            <div class="kt-form-invalid">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="kt-form-label required">Status</label>
                                    <select name="status" class="kt-select" data-kt-select="true" required>
                                        <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                        <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    @error('status')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Assign Clients -->
                                <div>
                                    <label class="kt-form-label required">Assign Clients</label>
                                    <select name="clients[]" multiple class="kt-select" data-kt-select="true" data-placeholder="Select clients" required>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" 
                                                {{ collect(old('clients', $project->clients->pluck('id')->toArray()))->contains($client->id) ? 'selected' : '' }}>
                                                {{ $client->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('clients')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                    <div class="kt-form-text">Currently assigned: {{ $project->clients->pluck('name')->join(', ') ?: 'None' }}</div>
                                </div>

                                <!-- Assign Developers -->
                                <div>
                                    <label class="kt-form-label required">Assign Developers</label>
                                    <select name="developers[]" multiple class="kt-select" data-kt-select="true" data-placeholder="Select developers" required>
                                        @foreach($developers as $developer)
                                            <option value="{{ $developer->id }}" 
                                                {{ collect(old('developers', $project->developers->pluck('id')->toArray()))->contains($developer->id) ? 'selected' : '' }}>
                                                {{ $developer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('developers')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                    <div class="kt-form-text">Currently assigned: {{ $project->developers->pluck('name')->join(', ') ?: 'None' }}</div>
                                </div>

                                <!-- Project Statistics Info -->
                                <div class="card bg-secondary">
                                    <div class="card-body">
                                        <h4 class="text-sm font-semibold mb-3">Project Statistics</h4>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                            <div>
                                                <span class="text-secondary-foreground">Total Tasks:</span>
                                                <p class="font-semibold">{{ $project->tasks->count() }}</p>
                                            </div>
                                            <div>
                                                <span class="text-secondary-foreground">Completed:</span>
                                                <p class="font-semibold text-success">{{ $project->tasks->where('status', 'approved')->count() }}</p>
                                            </div>
                                            <div>
                                                <span class="text-secondary-foreground">In Progress:</span>
                                                <p class="font-semibold text-warning">{{ $project->tasks->where('status', 'in_progress')->count() }}</p>
                                            </div>
                                            <div>
                                                <span class="text-secondary-foreground">Progress:</span>
                                                <p class="font-semibold">{{ $project->progress }}%</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="flex gap-3 pt-5">
                                    <button type="submit" class="kt-btn kt-btn-primary">
                                        <i class="ki-filled ki-check"></i>
                                        Update Project
                                    </button>
                                    <a href="{{ route('admin.projects.show', $project) }}" class="kt-btn kt-btn-outline">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Recent Changes Alert (Optional) -->
                @if(session('success'))
                <div class="alert alert-success mt-5">
                    <div class="alert-content">
                        <i class="ki-filled ki-check-circle"></i>
                        <div class="alert-text">{{ session('success') }}</div>
                    </div>
                </div>
                @endif

                <!-- Warning Alert for Completed Projects -->
                @if($project->status === 'completed')
                <div class="alert alert-warning mt-5">
                    <div class="alert-content">
                        <i class="ki-filled ki-information-circle"></i>
                        <div class="alert-text">
                            This project is marked as completed. Changing the status to active will allow new tasks to be added.
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