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
                            Create User
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
                        <h3 class="card-title">New User Details</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.store') }}">
                            @csrf
                            <div class="space-y-5">
                                <div>
                                    <label class="kt-form-label required">Name</label>
                                    <input type="text" name="name" value="{{ old('name') }}" 
                                           class="kt-input @error('name') kt-input-invalid @enderror" 
                                           placeholder="Enter user name" required>
                                    @error('name')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label class="kt-form-label required">Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}" 
                                           class="kt-input @error('email') kt-input-invalid @enderror" 
                                           placeholder="Enter email address" required>
                                    @error('email')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label class="kt-form-label required">Password</label>
                                    <input type="password" name="password" 
                                           class="kt-input @error('password') kt-input-invalid @enderror" 
                                           placeholder="Enter password" required>
                                    @error('password')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label class="kt-form-label required">Role</label>
                                    <select name="role" class="kt-select @error('role') kt-input-invalid @enderror" 
                                            data-kt-select="true" data-placeholder="Select role" required>
                                        <option value="">Select Role</option>
                                        <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                        <option value="client" {{ old('role') == 'client' ? 'selected' : '' }}>Client</option>
                                        <option value="developer" {{ old('role') == 'developer' ? 'selected' : '' }}>Developer</option>
                                    </select>
                                    @error('role')
                                        <div class="kt-form-invalid">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="flex gap-3 pt-5">
                                    <button type="submit" class="kt-btn kt-btn-primary">
                                        <i class="ki-filled ki-check"></i>
                                        Create User
                                    </button>
                                    <a href="{{ route('admin.users.index') }}" class="kt-btn kt-btn-outline">
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