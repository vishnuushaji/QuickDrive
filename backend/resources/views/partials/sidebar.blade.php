<div class="fixed top-0 bottom-0 z-20 hidden lg:flex flex-col shrink-0 w-(--sidebar-width) bg-muted [--kt-drawer-enable:true] lg:[--kt-drawer-enable:false]" data-kt-drawer="true" data-kt-drawer-class="kt-drawer kt-drawer-start flex top-0 bottom-0" id="sidebar">
    <!-- Sidebar Header -->
    <div id="sidebar_header">
        <div class="flex items-center gap-2.5 px-3.5 h-[70px]">
            <a href="{{ route('dashboard') }}">
                <img class="dark:hidden h-[42px]" src="{{ asset('assets/media/app/mini-logo-circle.svg') }}"/>
                <img class="hidden dark:inline-block h-[42px]" src="{{ asset('assets/media/app/mini-logo-circle-dark.svg') }}"/>
            </a>
            <div class="kt-menu kt-menu-default grow" data-kt-menu="true">
                <div class="kt-menu-item grow" data-kt-menu-item-offset="0px,0px" data-kt-menu-item-placement="bottom-start" data-kt-menu-item-toggle="dropdown" data-kt-menu-item-trigger="hover">
                    <div class="kt-menu-label cursor-pointer text-mono font-medium grow justify-between">
                        <span class="text-base font-medium text-mono grow justify-start">
                            Quick Drive
                        </span>
                        <span class="kt-menu-arrow">
                            <i class="ki-filled ki-down"></i>
                        </span>
                    </div>
                    <div class="kt-menu-dropdown w-48 py-2">
                        <div class="kt-menu-item">
                            <a class="kt-menu-link" href="{{ route('profile.edit') }}" tabindex="0">
                                <span class="kt-menu-icon">
                                    <i class="ki-filled ki-profile-circle"></i>
                                </span>
                                <span class="kt-menu-title">My Profile</span>
                            </a>
                        </div>
                        <div class="kt-menu-item">
                            <a class="kt-menu-link" href="{{ route('dashboard') }}" tabindex="0">
                                <span class="kt-menu-icon">
                                    <i class="ki-filled ki-setting-2"></i>
                                </span>
                                <span class="kt-menu-title">Account Settings</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pt-2.5 px-3.5 mb-1">
            <div class="kt-input">
                <i class="ki-filled ki-magnifier"></i>
                <input class="min-w-0" placeholder="Search" type="text" value=""/>
                <span class="text-xs text-secondary-foreground text-nowrap"> </span>
            </div>
        </div>
    </div>
    <!-- End of Sidebar Header -->
    
    <!-- Sidebar menu -->
    <div class="flex items-stretch grow shrink-0 justify-center my-5" id="sidebar_menu">
        <div class="kt-scrollable-y-auto grow" data-kt-scrollable="true" data-kt-scrollable-dependencies="#sidebar_header, #sidebar_footer" data-kt-scrollable-height="auto" data-kt-scrollable-offset="0px" data-kt-scrollable-wrappers="#sidebar_menu">
            <div class="border-b border-input mt-4 mb-1 mx-3.5"></div>
            
            <!-- Secondary Menu -->
            <div class="kt-menu flex flex-col w-full gap-1.5 px-3.5" data-kt-menu="true" data-kt-menu-accordion-expand-all="true" id="sidebar_secondary_menu">
                <!-- Dashboard -->
                <div class="kt-menu-item">
                    <a href="{{ route('dashboard') }}"
                       class="kt-menu-link py-1 px-2 my-0.5 rounded-md border border-transparent {{ request()->routeIs('dashboard') ? 'border-border bg-background' : '' }}">
                        <span class="kt-menu-icon flex place-content-center size-7 me-2.5">
                            <i class="ki-filled ki-home"></i>
                        </span>
                        <span class="kt-menu-title text-sm font-medium">Dashboard</span>
                    </a>
                </div>

                @if(auth()->user()->role === 'super_admin')
                <!-- Users -->
                <div class="kt-menu-item">
                    <a href="{{ route('admin.users.index') }}"
                       class="kt-menu-link py-1 px-2 my-0.5 rounded-md border border-transparent {{ request()->routeIs('admin.users.*') ? 'border-border bg-background' : '' }}">
                        <span class="kt-menu-icon flex place-content-center size-7 me-2.5">
                            <i class="ki-filled ki-users"></i>
                        </span>
                        <span class="kt-menu-title text-sm font-medium">Users</span>
                    </a>
                </div>
                @endif

                <!-- Projects -->
                <div class="kt-menu-item">
                    <a href="{{ route('admin.projects.index') }}"
                       class="kt-menu-link py-1 px-2 my-0.5 rounded-md border border-transparent {{ request()->routeIs('admin.projects.*') ? 'border-border bg-background' : '' }}">
                        <span class="kt-menu-icon flex place-content-center size-7 me-2.5">
                            <i class="ki-filled ki-abstract-43"></i>
                        </span>
                        <span class="kt-menu-title text-sm font-medium">Projects</span>
                    </a>
                </div>

                <!-- Tasks -->
                <div class="kt-menu-item">
                    <a href="{{ route('admin.tasks.index') }}"
                       class="kt-menu-link py-1 px-2 my-0.5 rounded-md border border-transparent {{ request()->routeIs('admin.tasks.*') ? 'border-border bg-background' : '' }}">
                        <span class="kt-menu-icon flex place-content-center size-7 me-2.5">
                            <i class="ki-filled ki-check-square"></i>
                        </span>
                        <span class="kt-menu-title text-sm font-medium">Tasks</span>
                        @if(auth()->user()->role === 'client')
                            @php
                                $pendingApprovals = \App\Models\Task::whereIn('project_id', 
                                    auth()->user()->projects()->wherePivot('role', 'client')->pluck('projects.id')
                                )->where('status', 'completed')->count();
                            @endphp
                            @if($pendingApprovals > 0)
                                <span class="badge badge-warning ms-auto">{{ $pendingApprovals }}</span>
                            @endif
                        @endif
                    </a>
                </div>
            </div>
            <!-- End of Secondary Menu -->
        </div>
    </div>
    <!-- End of Sidebar menu-->
    
    <!-- Footer -->
    <div class="flex flex-center justify-between shrink-0 ps-4 pe-3.5 mb-3.5" id="sidebar_footer">
        <!-- User Profile Dropdown -->
        <div data-kt-dropdown="true" data-kt-dropdown-offset="10px, 10px" data-kt-dropdown-placement="bottom-start" data-kt-dropdown-trigger="click">
            <div class="cursor-pointer shrink-0" data-kt-dropdown-toggle="true">
                <img alt="{{ auth()->user()->name }}" 
                     class="size-9 rounded-full border-2 border-mono/25 shrink-0" 
                     src="{{ asset('assets/media/avatars/gray/' . ((auth()->user()->id % 5) + 1) . '.png') }}"/>
            </div>
            <div class="kt-dropdown-menu w-[250px]" data-kt-dropdown-menu="true">
                <div class="flex items-center justify-between px-2.5 py-1.5 gap-1.5">
                    <div class="flex items-center gap-2">
                        <img alt="{{ auth()->user()->name }}" 
                             class="size-9 shrink-0 rounded-full border-2 border-green-500" 
                             src="{{ asset('assets/media/avatars/gray/' . ((auth()->user()->id % 5) + 1) . '.png') }}"/>
                        <div class="flex flex-col gap-1.5">
                            <span class="text-sm text-foreground font-semibold leading-none">
                                {{ auth()->user()->name }}
                            </span>
                            <span class="text-xs text-secondary-foreground font-medium leading-none">
                                {{ auth()->user()->email }}
                            </span>
                        </div>
                    </div>
                    <span class="kt-badge kt-badge-sm kt-badge-{{ auth()->user()->role === 'super_admin' ? 'danger' : (auth()->user()->role === 'client' ? 'primary' : 'secondary') }} kt-badge-outline">
                        {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                    </span>
                </div>
                <ul class="kt-dropdown-menu-sub">
                    <li>
                        <div class="kt-dropdown-menu-separator"></div>
                    </li>
                    <li>
                        <a class="kt-dropdown-menu-item" href="{{ route('profile.edit') }}">
                            <div class="kt-dropdown-menu-link">
                                <span class="kt-dropdown-menu-icon">
                                    <i class="ki-filled ki-profile-circle"></i>
                                </span>
                                <span class="kt-dropdown-menu-title">My Profile</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="kt-dropdown-menu-separator"></div>
                    </li>
                    </ul>
                <div class="px-2.5 pt-1.5 mb-2.5 flex flex-col gap-3.5">
                    <div class="flex items-center gap-2 justify-between">
                        <span class="flex items-center gap-2">
                            <i class="ki-filled ki-moon text-base text-muted-foreground"></i>
                            <span class="font-medium text-2sm">Dark Mode</span>
                        </span>
                        <input class="kt-switch" id="theme-toggle" name="check" type="checkbox" value="1"/>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="kt-btn kt-btn-outline justify-center w-full">
                            <i class="ki-filled ki-exit-right me-2"></i>
                            Log out
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- End of User Profile Dropdown -->
    </div>
    <!-- End of Footer -->
</div>