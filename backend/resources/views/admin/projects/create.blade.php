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
                            Create Project
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
                        <h3 class="card-title">New Project Details</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.projects.store') }}">
                            @csrf
                            <div class="space-y-5">
                                <div>
                                    <label class="kt-form-label required">Project Name</label>
                                    <input type="text" name="name" value="{{ old('name') }}" 
                                           class="kt-input @error('name') kt-input-invalid @enderror" 
                                           placeholder="Enter project name" required>
                                    @error('name')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label class="kt-form-label">Description</label>
                                    <textarea name="description" rows="4" 
                                              class="kt-input @error('description') kt-input-invalid @enderror"
                                              placeholder="Enter project description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

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
                                        <label class="kt-form-label">End Date</label>
                                        <input type="date" name="end_date" value="{{ old('end_date') }}" 
                                               class="kt-input @error('end_date') kt-input-invalid @enderror">
                                        @error('end_date')
                                            <div class="kt-form-invalid">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="kt-form-label required">Status</label>
                                    <select name="status" class="kt-select" data-kt-select="true" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    @error('status')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label class="kt-form-label required">Assign Clients</label>
                                    <select name="clients[]" multiple class="kt-select" data-kt-select="true" data-placeholder="Select clients" required>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ collect(old('clients'))->contains($client->id) ? 'selected' : '' }}>
                                                {{ $client->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('clients')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label class="kt-form-label required">Assign Developers</label>
                                    <select name="developers[]" multiple class="kt-select" data-kt-select="true" data-placeholder="Select developers" required>
                                        @foreach($developers as $developer)
                                            <option value="{{ $developer->id }}" {{ collect(old('developers'))->contains($developer->id) ? 'selected' : '' }}>
                                                {{ $developer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('developers')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="flex gap-3 pt-5">
                                    <button type="submit" class="kt-btn kt-btn-primary">
                                        <i class="ki-filled ki-check"></i>
                                        Create Project
                                    </button>
                                    <a href="{{ route('admin.projects.index') }}" class="kt-btn kt-btn-outline">
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
    @endsection