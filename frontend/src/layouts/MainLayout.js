import React, { useState, useEffect, useRef } from 'react';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import {
  HomeIcon,
  UsersIcon,
  FolderIcon,
  ClipboardDocumentListIcon,
  ArrowRightOnRectangleIcon,
  ChevronDownIcon,
  MagnifyingGlassIcon,
  UserCircleIcon,
  Cog6ToothIcon,
  MoonIcon,
  SunIcon,
  CheckIcon
} from '@heroicons/react/24/outline';
import { HomeIcon as HomeIconSolid } from '@heroicons/react/24/solid';

const MainLayout = ({ children }) => {
  const { user, logout, isSuperAdmin, isClient, isDeveloper } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();
  const [isQuickDriveOpen, setIsQuickDriveOpen] = useState(false);
  const [isProfileOpen, setIsProfileOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [isDarkMode, setIsDarkMode] = useState(() => {
    // Initialize from localStorage or default to light mode
    const savedTheme = localStorage.getItem('theme');
    return savedTheme === 'dark';
  });
  const [pendingApprovals, setPendingApprovals] = useState(0);
  const profileRef = useRef(null);
  const quickDriveRef = useRef(null);

  // Initialize theme on mount
  useEffect(() => {
    const savedTheme = localStorage.getItem('theme') || 'light';
    setIsDarkMode(savedTheme === 'dark');
    document.documentElement.classList.toggle('dark', savedTheme === 'dark');
  }, []);

  // Close dropdowns when clicking outside
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (profileRef.current && !profileRef.current.contains(event.target)) {
        setIsProfileOpen(false);
      }
      if (quickDriveRef.current && !quickDriveRef.current.contains(event.target)) {
        setIsQuickDriveOpen(false);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  // Fetch pending approvals for client
  useEffect(() => {
    if (isClient) {
      // Fetch pending task approvals
      // This would be an API call in real implementation
      setPendingApprovals(0);
    }
  }, [isClient]);

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  const toggleDarkMode = () => {
    const newDarkMode = !isDarkMode;
    setIsDarkMode(newDarkMode);
    document.documentElement.classList.toggle('dark', newDarkMode);
    localStorage.setItem('theme', newDarkMode ? 'dark' : 'light');
  };

  const navigation = [
    { 
      name: 'Dashboard', 
      href: '/dashboard', 
      icon: HomeIcon,
      roles: ['super_admin', 'client', 'developer'] 
    },
    { 
      name: 'Users', 
      href: '/users', 
      icon: UsersIcon,
      roles: ['super_admin'] 
    },
    { 
      name: 'Projects', 
      href: '/projects', 
      icon: FolderIcon,
      roles: ['super_admin', 'client', 'developer'] 
    },
    { 
      name: 'Tasks', 
      href: '/tasks', 
      icon: ClipboardDocumentListIcon,
      roles: ['super_admin', 'client', 'developer'],
      badge: isClient ? pendingApprovals : 0
    },
  ];

  const filteredNavigation = navigation.filter(item => 
    item.roles.includes(user?.role)
  );

  const isActiveRoute = (href) => {
    return location.pathname === href || location.pathname.startsWith(href + '/');
  };

  const getRoleBadgeClass = (role) => {
    switch(role) {
      case 'super_admin':
        return 'bg-red-100 text-red-700 border-red-200';
      case 'client':
        return 'bg-blue-100 text-blue-700 border-blue-200';
      default:
        return 'bg-gray-100 text-gray-700 border-gray-200';
    }
  };

  const getUserAvatar = () => {
    // Generate avatar based on user ID or use default
    const avatarNumber = (user?.id % 5) + 1;
    return `/assets/media/avatars/gray/${avatarNumber}.png`;
  };

  return (
    <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
      {/* Sidebar */}
      <div className="fixed top-0 bottom-0 z-20 hidden lg:flex flex-col shrink-0 w-64 bg-gray-100 dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">
        {/* Sidebar Header */}
        <div id="sidebar_header">
          <div className="flex items-center gap-2.5 px-3.5 h-[70px]">
            <Link to="/dashboard">
              <img 
                className="h-[42px]" 
                src="/assets/media/app/mini-logo-circle.svg" 
                alt="Logo"
              />
            </Link>
            <div className="relative grow" ref={quickDriveRef}>
              <button
                onClick={() => setIsQuickDriveOpen(!isQuickDriveOpen)}
                className="flex items-center justify-between w-full px-2 py-1.5 text-base font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md transition-colors"
              >
                <span>Quick Drive</span>
                <ChevronDownIcon className={`h-4 w-4 transition-transform ${isQuickDriveOpen ? 'rotate-180' : ''}`} />
              </button>
              
              {isQuickDriveOpen && (
                <div className="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg py-2 z-50">
                  <Link
                    to="/profile"
                    className="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                    onClick={() => setIsQuickDriveOpen(false)}
                  >
                    <UserCircleIcon className="h-5 w-5" />
                    <span>My Profile</span>
                  </Link>
                  <Link
                    to="/settings"
                    className="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                    onClick={() => setIsQuickDriveOpen(false)}
                  >
                    <Cog6ToothIcon className="h-5 w-5" />
                    <span>Account Settings</span>
                  </Link>
                </div>
              )}
            </div>
          </div>
          
          <div className="pt-2.5 px-3.5 mb-1">
            <div className="relative">
              <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 dark:text-gray-500" />
              <input
                type="text"
                placeholder="Search"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>
        </div>

        {/* Sidebar Menu */}
        <div className="flex items-stretch grow shrink-0 justify-center my-5" id="sidebar_menu">
          <div className="grow overflow-y-auto px-3.5">
            <div className="border-b border-gray-200 mt-4 mb-1 mx-0"></div>
            
            {/* Navigation Menu */}
            <div className="flex flex-col w-full gap-1.5">
              {filteredNavigation.map((item) => {
                const isActive = isActiveRoute(item.href);
                return (
                  <Link
                    key={item.name}
                    to={item.href}
                    className={`flex items-center py-2 px-2 my-0.5 rounded-md border transition-all ${
                      isActive
                        ? 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 shadow-sm'
                        : 'border-transparent hover:bg-gray-200 dark:hover:bg-gray-600'
                    }`}
                  >
                    <span className="flex items-center justify-center w-7 h-7 mr-2.5">
                      <item.icon className={`h-5 w-5 ${isActive ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400'}`} />
                    </span>
                    <span className={`text-sm font-medium ${isActive ? 'text-gray-900 dark:text-gray-100' : 'text-gray-700 dark:text-gray-300'}`}>
                      {item.name}
                    </span>
                    {item.badge > 0 && (
                      <span className="ml-auto px-2 py-0.5 text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded-full">
                        {item.badge}
                      </span>
                    )}
                  </Link>
                );
              })}
            </div>
          </div>
        </div>

        {/* Sidebar Footer */}
        <div className="flex items-center justify-between shrink-0 px-4 pb-3.5" id="sidebar_footer">
          <div className="relative" ref={profileRef}>
            <button
              onClick={() => setIsProfileOpen(!isProfileOpen)}
              className="shrink-0"
            >
              <img
                alt={user?.name}
                className="w-9 h-9 rounded-full border-2 border-gray-300"
                src={getUserAvatar()}
              />
            </button>

            {isProfileOpen && (
              <div className="absolute bottom-full left-0 mb-2 w-[250px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg z-50">
                <div className="flex items-center justify-between px-2.5 py-1.5 gap-1.5">
                  <div className="flex items-center gap-2">
                    <img
                      alt={user?.name}
                      className="w-9 h-9 shrink-0 rounded-full border-2 border-green-500"
                      src={getUserAvatar()}
                    />
                    <div className="flex flex-col gap-1.5">
                      <span className="text-sm text-gray-900 dark:text-gray-100 font-semibold leading-none">
                        {user?.name}
                      </span>
                      <span className="text-xs text-gray-500 dark:text-gray-400 font-medium leading-none">
                        {user?.email}
                      </span>
                    </div>
                  </div>
                  <span className={`px-2 py-1 text-xs font-medium rounded-full border ${getRoleBadgeClass(user?.role)}`}>
                    {user?.role?.replace('_', ' ').charAt(0).toUpperCase() + user?.role?.replace('_', ' ').slice(1)}
                  </span>
                </div>
                
                <div className="border-t border-gray-200 dark:border-gray-600 my-2"></div>

                <Link
                  to="/profile"
                  className="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                  onClick={() => setIsProfileOpen(false)}
                >
                  <UserCircleIcon className="h-5 w-5" />
                  <span>My Profile</span>
                </Link>

                <div className="border-t border-gray-200 dark:border-gray-600 my-2"></div>

                <div className="px-2.5 pt-1.5 mb-2.5 flex flex-col gap-3.5">
                  <div className="flex items-center gap-2 justify-between">
                    <span className="flex items-center gap-2">
                      {isDarkMode ? (
                        <SunIcon className="h-4 w-4 text-gray-500 dark:text-gray-400" />
                      ) : (
                        <MoonIcon className="h-4 w-4 text-gray-500 dark:text-gray-400" />
                      )}
                      <span className="font-medium text-sm text-gray-900 dark:text-gray-100">Dark Mode</span>
                    </span>
                    <button
                      onClick={toggleDarkMode}
                      className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${
                        isDarkMode ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-600'
                      }`}
                    >
                      <span
                        className={`inline-block h-4 w-4 transform rounded-full bg-white dark:bg-gray-300 transition-transform ${
                          isDarkMode ? 'translate-x-6' : 'translate-x-1'
                        }`}
                      />
                    </button>
                  </div>

                  <button
                    onClick={handleLogout}
                    className="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600"
                  >
                    <ArrowRightOnRectangleIcon className="h-4 w-4 mr-2" />
                    Log out
                  </button>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div className="lg:ml-64">
        {/* Page Content */}
        <main className="p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
          {children}
        </main>
      </div>
    </div>
  );
};

export default MainLayout;