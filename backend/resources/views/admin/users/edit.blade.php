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
                            Edit User
                        </h1>
                        <div class="flex items-center gap-1 text-sm font-normal">
                            <a class="text-secondary-foreground hover:text-primary" href="{{ route('dashboard') }}">
                                Home
                            </a>
                            <span class="text-muted-foreground text-sm">/</span>
                            <a class="text-secondary-foreground hover:text-primary" href="{{ route('admin.users.index') }}">
                                Users
                            </a>
                            <span class="text-muted-foreground text-sm">/</span>
                            <span class="text-mono">{{ $user->name }}</span>
                            <span class="text-muted-foreground text-sm">/</span>
                            <span class="text-mono">Edit</span>
                        </div>
                    </div>
                    <div class="flex items-center flex-wrap gap-1.5 lg:gap-3.5">
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" 
                              onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="kt-btn kt-btn-danger">
                                <i class="ki-filled ki-trash"></i>
                                Delete User
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
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-7.5">
                    <!-- Left Column - User Info Card -->
                    <div class="lg:col-span-1">
                        <div class="card sticky top-5">
                            <div class="card-body p-5">
                                <!-- User Avatar & Basic Info -->
                                <div class="flex flex-col items-center text-center mb-5">
                                    <div class="relative mb-4">
                                        <img alt="{{ $user->name }}" 
                                             class="size-24 rounded-full" 
                                             src="{{ asset('assets/media/avatars/gray/' . (($user->id % 5) + 1) . '.png') }}"/>
                                        <div class="absolute bottom-0 end-0 size-6 bg-success rounded-full border-2 border-background"></div>
                                    </div>
                                    <h3 class="text-lg font-semibold mb-1">{{ $user->name }}</h3>
                                    <span class="text-sm text-secondary-foreground">{{ $user->email }}</span>
                                    <div class="mt-3">
                                        @if($user->role === 'super_admin')
                                            <span class="badge badge-danger">Super Admin</span>
                                        @elseif($user->role === 'client')
                                            <span class="badge badge-primary">Client</span>
                                        @else
                                            <span class="badge badge-secondary">Developer</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- User Stats -->
                                <div class="border-t border-border pt-5">
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-secondary-foreground">Projects</span>
                                            <span class="text-sm font-semibold">{{ $user->projects->count() }}</span>
                                        </div>
                                        @if($user->role === 'developer')
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-secondary-foreground">Assigned Tasks</span>
                                            <span class="text-sm font-semibold">{{ $user->assignedTasks->count() }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-secondary-foreground">Completed Tasks</span>
                                            <span class="text-sm font-semibold text-success">
                                                {{ $user->assignedTasks->where('status', 'approved')->count() }}
                                            </span>
                                        </div>
                                        @endif
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-secondary-foreground">Member Since</span>
                                            <span class="text-sm font-semibold">{{ $user->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Edit Form -->
                    <div class="lg:col-span-2">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Account Details</h3>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="space-y-5">
                                        <!-- Name -->
                                        <div>
                                            <label class="kt-form-label required">Full Name</label>
                                            <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                                   class="kt-input @error('name') kt-input-invalid @enderror" 
                                                   placeholder="Enter full name" required>
                                            @error('name')
                                                <div class="kt-form-invalid">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Email -->
                                        <div>
                                            <label class="kt-form-label required">Email Address</label>
                                            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                                   class="kt-input @error('email') kt-input-invalid @enderror" 
                                                   placeholder="Enter email address" required>
                                            @error('email')
                                                <div class="kt-form-invalid">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Role -->
                                        <div>
                                            <label class="kt-form-label required">Role</label>
                                            <select name="role" class="kt-select" data-kt-select="true" required>
                                                <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>
                                                    Super Admin
                                                </option>
                                                <option value="client" {{ old('role', $user->role) == 'client' ? 'selected' : '' }}>
                                                    Client
                                                </option>
                                                <option value="developer" {{ old('role', $user->role) == 'developer' ? 'selected' : '' }}>
                                                    Developer
                                                </option>
                                            </select>
                                            @error('role')
                                                <div class="kt-form-invalid">{{ $message }}</div>
                                            @enderror
                                            <div class="kt-form-text">
                                                @if($user->id === auth()->id())
                                                    <span class="text-warning">Warning: You are editing your own role.</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Password Section -->
                                        <div class="border-t border-border pt-5">
                                            <h4 class="text-sm font-semibold mb-4">Change Password</h4>
                                            <div class="space-y-4">
                                                <!-- New Password -->
                                                <div>
                                                    <label class="kt-form-label">New Password</label>
                                                    <input type="password" name="password" 
                                                           class="kt-input @error('password') kt-input-invalid @enderror" 
                                                           placeholder="Leave blank to keep current password">
                                                    @error('password')
                                                        <div class="kt-form-invalid">{{ $message }}</div>
                                                    @enderror
                                                    <div class="kt-form-text">Minimum 8 characters</div>
                                                </div>

                                                <!-- Confirm Password -->
                                                <div>
                                                    <label class="kt-form-label">Confirm New Password</label>
                                                    <input type="password" name="password_confirmation" 
                                                           class="kt-input" 
                                                           placeholder="Confirm new password">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Form Actions -->
                                        <div class="flex gap-3 pt-5">
                                            <button type="submit" class="kt-btn kt-btn-primary">
                                                <i class="ki-filled ki-check"></i>
                                                Update User
                                            </button>
                                            <a href="{{ route('admin.users.index') }}" class="kt-btn kt-btn-outline">
                                                Cancel
                                            </a>
                                        </div>
                                    </div>
                                </form>
                                                       </div>
                        </div>

                        <!-- Activity/Projects Card (Optional) -->
                        @if($user->projects->count() > 0)
                        <div class="card mt-5">
                            <div class="card-header">
                                <h3 class="card-title">Assigned Projects</h3>
                            </div>
                            <div class="card-body">
                                <div class="space-y-3">
                                    @foreach($user->projects->take(5) as $project)
                                    <div class="flex items-center justify-between py-2 border-b border-border last:border-0">
                                        <div class="flex items-center gap-3">
                                            <div class="size-10 rounded-lg bg-secondary flex items-center justify-center">
                                                <i class="ki-filled ki-folder text-secondary-foreground"></i>
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.projects.show', $project) }}" 
                                                   class="text-sm font-medium hover:text-primary">
                                                    {{ $project->name }}
                                                </a>
                                                <p class="text-xs text-secondary-foreground">
                                                    Role: {{ ucfirst($project->pivot->role) }}
                                                </p>
                                            </div>
                                        </div>
                                        <span class="badge {{ $project->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                            {{ ucfirst($project->status) }}
                                        </span>
                                    </div>
                                    @endforeach
                                    
                                    @if($user->projects->count() > 5)
                                    <div class="text-center pt-2">
                                        <a href="{{ route('admin.projects.index', ['user' => $user->id]) }}" 
                                           class="text-sm link">
                                            View all {{ $user->projects->count() }} projects
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                <div class="alert alert-success mt-5">
                    <div class="alert-content">
                        <i class="ki-filled ki-check-circle"></i>
                        <div class="alert-text">{{ session('success') }}</div>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger mt-5">
                    <div class="alert-content">
                        <i class="ki-filled ki-cross-circle"></i>
                        <div class="alert-text">{{ session('error') }}</div>
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
    // Initialize Select2 or KT Select if available
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize select
        const selectElement = document.querySelector('[data-kt-select]');
        if (selectElement && typeof KTSelect !== 'undefined') {
            new KTSelect(selectElement);
        }
    });
</script>
@endpush