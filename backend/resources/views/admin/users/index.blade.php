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
                        <h1 class="font-medium text-lg text-mono dark:text-gray-100">
                            Users Management
                        </h1>
                        <div class="flex items-center gap-1 text-sm font-normal">
                            <a class="text-secondary-foreground hover:text-primary dark:text-gray-400 dark:hover:text-blue-400" href="{{ route('dashboard') }}">
                                Home
                            </a>
                            <span class="text-muted-foreground text-sm dark:text-gray-500">/</span>
                            <span class="text-mono dark:text-gray-100">Users</span>
                        </div>
                    </div>
                    <div class="flex items-center flex-wrap gap-1.5 lg:gap-3.5">
                        <a class="kt-btn kt-btn-primary" href="{{ route('admin.users.create') }}">
                            <i class="ki-filled ki-plus"></i>
                            Add User
                        </a>
                    </div>
                </div>
                <!-- End of Container -->
            </div>
            <!-- End of Toolbar -->
            
            <!-- Container -->
            <div class="kt-container-fixed">
                <div class="grid gap-5 lg:gap-7.5">
                    <div class="kt-card kt-card-grid min-w-full">
                        <div class="kt-card-header flex-wrap gap-2">
                            <h3 class="kt-card-title text-sm">
                                Showing {{ $users->count() }} users
                            </h3>
                            <div class="flex flex-wrap gap-2 lg:gap-5">
                                <div class="flex">
                                    <label class="kt-input">
                                        <i class="ki-filled ki-magnifier"></i>
                                        <input placeholder="Search users" type="text" id="searchUsers" value=""/>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="kt-card-content">
                            <div class="kt-scrollable-x-auto">
                                <table class="kt-table table-auto kt-table-border">
                                    <thead>
                                        <tr>
                                            <th class="w-[60px] text-center">
                                                <input class="kt-checkbox kt-checkbox-sm" type="checkbox"/>
                                            </th>
                                            <th class="min-w-[200px]">
                                                <span class="kt-table-col">
                                                    <span class="kt-table-col-label">User</span>
                                                </span>
                                            </th>
                                            <th class="min-w-[165px]">
                                                <span class="kt-table-col">
                                                    <span class="kt-table-col-label">Email</span>
                                                </span>
                                            </th>
                                            <th class="min-w-[100px]">
                                                <span class="kt-table-col">
                                                    <span class="kt-table-col-label">Role</span>
                                                </span>
                                            </th>
                                            <th class="min-w-[125px]">
                                                <span class="kt-table-col">
                                                    <span class="kt-table-col-label">Projects</span>
                                                </span>
                                            </th>
                                            <th class="min-w-[125px]">
                                                <span class="kt-table-col">
                                                    <span class="kt-table-col-label">Tasks</span>
                                                </span>
                                            </th>
                                            <th class="min-w-[125px]">
                                                <span class="kt-table-col">
                                                    <span class="kt-table-col-label">Joined Date</span>
                                                </span>
                                            </th>
                                            <th class="w-[100px]">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                        <tr>
                                            <td class="text-center">
                                                <input class="kt-checkbox kt-checkbox-sm" type="checkbox" value="{{ $user->id }}"/>
                                            </td>
                                            <td>
                                                <div class="flex items-center gap-2.5">
                                                    <div>
                                                        <img alt="{{ $user->name }}" class="size-9 rounded-full" 
                                                             src="{{ asset('assets/media/avatars/gray/' . (($loop->index % 5) + 1) . '.png') }}"/>
                                                    </div>
                                                    <div class="flex flex-col gap-0.5">
                                                        <a class="leading-none font-semibold text-sm link" 
                                                           href="{{ route('admin.users.edit', $user) }}">
                                                            {{ $user->name }}
                                                        </a>
                                                        <span class="text-xs text-secondary-foreground">
                                                            ID: {{ $user->id }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-sm text-secondary-foreground">
                                                {{ $user->email }}
                                            </td>
                                            <td>
                                                @if($user->role === 'super_admin')
                                                    <span class="badge badge-danger">Super Admin</span>
                                                @elseif($user->role === 'client')
                                                    <span class="badge badge-primary">Client</span>
                                                @else
                                                    <span class="badge badge-secondary">Developer</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-sm text-secondary-foreground">
                                                    {{ $user->projects->count() }} projects
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-sm text-secondary-foreground">
                                                    @if($user->role === 'developer')
                                                        {{ $user->assignedTasks->count() }} tasks
                                                    @else
                                                        -
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="text-sm text-secondary-foreground">
                                                {{ $user->created_at->format('M d, Y') }}
                                            </td>
                                            <td>
                                                <div class="kt-menu" data-kt-menu="true">
                                                    <div class="kt-menu-item" data-kt-menu-item-offset="0, 10px" 
                                                         data-kt-menu-item-placement="bottom-end" 
                                                         data-kt-menu-item-toggle="dropdown" 
                                                         data-kt-menu-item-trigger="click|lg:click">
                                                        <button class="kt-menu-toggle kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost kt-btn-rounded">
                                                            <i class="ki-filled ki-dots-vertical"></i>
                                                        </button>
                                                        <div class="kt-menu-dropdown kt-menu-default w-[175px] py-2">
                                                            <div class="kt-menu-item">
                                                                <a class="kt-menu-link" href="{{ route('admin.users.edit', $user) }}">
                                                                    <span class="kt-menu-icon">
                                                                        <i class="ki-filled ki-edit"></i>
                                                                    </span>
                                                                    <span class="kt-menu-title">Edit</span>
                                                                </a>
                                                            </div>
                                                            <div class="kt-menu-separator"></div>
                                                            <div class="kt-menu-item">
                                                                <form action="{{ route('admin.users.destroy', $user) }}" 
                                                                      method="POST" 
                                                                      onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="kt-menu-link w-full">
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
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            @if($users->isEmpty())
                            <div class="text-center py-10">
                                <i class="ki-filled ki-users text-5xl text-muted-foreground mb-4"></i>
                                <p class="text-secondary-foreground">No users found</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Container -->
        </main>
    </div>
</div>
<!-- End of Main -->
@endsection

@push('scripts')
<script>
    // Simple search functionality
    document.getElementById('searchUsers').addEventListener('keyup', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const tableRows = document.querySelectorAll('tbody tr');
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Select all checkboxes
    document.querySelector('thead input[type="checkbox"]').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>
@endpush