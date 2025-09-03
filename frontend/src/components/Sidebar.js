import React, { useState, useEffect } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import {
  HomeIcon,
  UsersIcon,
  FolderIcon,
  ClipboardDocumentListIcon as ClipboardListIcon,
  MagnifyingGlassIcon,
  ChevronDownIcon,
  ArrowRightOnRectangleIcon,
  MoonIcon,
  MessageSquareDot,
  MessageCircleMore,
  Star,
  FileText,
  Lock
} from '@heroicons/react/24/outline';
import { Search } from 'lucide-react';

const Sidebar = ({ isOpen, onClose }) => {
  const { user, logout } = useAuth();
  const location = useLocation();
  const navigate = useNavigate();
  const [searchQuery, setSearchQuery] = useState('');
  const [isUserMenuOpen, setIsUserMenuOpen] = useState(false);
  const [selectedMenuItem, setSelectedMenuItem] = useState('dashboard');

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  const handleInputChange = () => {};

  // Update selected menu item based on current path
  useEffect(() => {
    const path = location.pathname;
    if (path.includes('/dashboard')) setSelectedMenuItem('dashboard');
    else if (path.includes('/users')) setSelectedMenuItem('users');
    else if (path.includes('/projects')) setSelectedMenuItem('projects');
    else if (path.includes('/tasks')) setSelectedMenuItem('tasks');
  }, [location.pathname]);

  const navigation = [
    { name: 'Dashboard', href: '/dashboard', icon: HomeIcon, roles: ['super_admin', 'client', 'developer'], key: 'dashboard' },
    { name: 'Users', href: '/users', icon: UsersIcon, roles: ['super_admin'], key: 'users' },
    { name: 'Projects', href: '/projects', icon: FolderIcon, roles: ['super_admin', 'client', 'developer'], key: 'projects' },
    { name: 'Tasks', href: '/tasks', icon: ClipboardListIcon, roles: ['super_admin', 'client', 'developer'], key: 'tasks' },
  ];

  const filteredNavigation = navigation.filter(item =>
    item.roles.includes(user?.role)
  );

  const secondaryItems = [
    {
      title: 'Favorites',
      value: 'favorites',
      plus: false,
      children: [
        {
          icon: Star,
          title: 'Recent Projects',
          path: '/projects',
        },
        {
          icon: FileText,
          title: 'Task Templates',
          path: '/tasks',
        },
        { icon: Lock, title: 'Private Docs', path: '/docs' },
      ],
    },
  ];

  const getRoleBadgeColor = (role) => {
    switch (role) {
      case 'super_admin': return 'bg-red-100 text-red-800';
      case 'client': return 'bg-blue-100 text-blue-800';
      case 'developer': return 'bg-gray-100 text-gray-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <div className={`fixed top-0 bottom-0 z-20 hidden lg:flex flex-col shrink-0 w-[270px] bg-background ${isOpen ? 'block' : ''}`}>

      {/* Sidebar Header */}
      <div className="mb-3.5">
        <div className="flex items-center justify-between gap-2.5 px-3.5 h-[70px]">
          <Link to="/" className="flex items-center">
            <img
              className="dark:hidden h-[42px]"
              src="/assets/media/app/mini-logo-circle.svg"
              alt="Logo"
            />
            <img
              className="hidden dark:inline-block h-[42px]"
              src="/assets/media/app/mini-logo-circle-dark.svg"
              alt="Logo Dark"
            />
          </Link>

          <div className="cursor-pointer text-mono font-medium flex items-center justify-between gap-2 w-[150px]">
            <span>Quick Drive</span>
            <ChevronDownIcon className="size-3.5 text-muted-foreground" />
          </div>
        </div>

        <div className="pt-2.5 px-3.5 mb-1">
          <div className="relative">
            <Search className="text-muted-foreground absolute top-1/2 start-3.5 -translate-y-1/2 size-4" />
            <input
              placeholder="Search"
              onChange={handleInputChange}
              className="w-full px-9 py-2 border border-input rounded-md bg-background text-sm"
              value={searchQuery}
            />
            <span className="text-xs text-muted-foreground absolute end-3.5 top-1/2 -translate-y-1/2">
              cmd + /
            </span>
          </div>
        </div>
      </div>

      {/* Sidebar Menu */}
      <div className="kt-scrollable-y-auto grow max-h-[calc(100vh-11.5rem)]">
        {/* Primary Menu */}
        <div className="space-y-2.5 px-3.5">
          {filteredNavigation.map((item) => (
            <Link
              key={item.key}
              to={item.href}
              className={`flex items-center gap-2 h-9 px-2 rounded-md border border-transparent text-accent-foreground hover:text-primary hover:bg-background hover:border-border ${selectedMenuItem === item.key ? 'text-primary bg-background border-border font-medium' : ''}`}
              onClick={onClose}
            >
              <item.icon className="size-4" />
              <span className="text-sm font-medium">{item.name}</span>
            </Link>
          ))}
        </div>

        <div className="border-b border-input my-4 mx-5"></div>

        {/* Secondary Menu */}
        <div className="flex flex-col w-full gap-1.5 px-3.5">
          {secondaryItems.map((item, index) => (
            <div key={index}>
              <div className="flex items-center gap-2 h-9 px-2">
                <ChevronDownIcon className="size-3.5 text-muted-foreground" />
                <span className="text-sm font-medium">{item.title}</span>
              </div>
              <div className="ml-4 space-y-1">
                {item.children.map((child, childIndex) => (
                  <Link
                    key={childIndex}
                    to={child.path}
                    className="flex items-center gap-2 h-9 px-2 rounded-md border border-transparent text-accent-foreground hover:text-primary hover:bg-background hover:border-border"
                  >
                    <span className="rounded-md size-7 flex items-center justify-center border border-border text-foreground">
                      <child.icon className="size-4" />
                    </span>
                    <span className="text-sm">{child.title}</span>
                  </Link>
                ))}
              </div>
              {index !== secondaryItems.length - 1 && (
                <div className="border-b border-input my-2 mx-1.5"></div>
              )}
            </div>
          ))}
        </div>
      </div>

      {/* Sidebar Footer */}
      <div className="flex items-center justify-between shrink-0 ps-4 pe-3.5 h-14">
        {/* User Profile Dropdown */}
        <div className="relative">
          <button
            onClick={() => setIsUserMenuOpen(!isUserMenuOpen)}
            className="cursor-pointer shrink-0"
          >
            <img
              alt={user?.name}
              className="size-9 rounded-full border-2 border-secondary shrink-0"
              src={`/assets/media/avatars/gray/${((user?.id % 5) + 1)}.png`}
            />
          </button>

          {isUserMenuOpen && (
            <div className="absolute bottom-full left-0 mb-2 w-[250px] bg-background border border-border rounded-md shadow-lg">
              <div className="flex items-center justify-between px-2.5 py-1.5 gap-1.5">
                <div className="flex items-center gap-2">
                  <img
                    alt={user?.name}
                    className="size-9 shrink-0 rounded-full border-2 border-green-500"
                    src={`/assets/media/avatars/gray/${((user?.id % 5) + 1)}.png`}
                  />
                  <div className="flex flex-col gap-1.5">
                    <span className="text-sm text-foreground font-semibold leading-none">
                      {user?.name}
                    </span>
                    <span className="text-xs text-secondary-foreground font-medium leading-none">
                      {user?.email}
                    </span>
                  </div>
                </div>
                <span className={`px-2 py-1 text-xs rounded-full ${getRoleBadgeColor(user?.role)}`}>
                  {user?.role?.replace('_', ' ')}
                </span>
              </div>

              <div className="px-2.5 pt-1.5 mb-2.5 flex flex-col gap-3.5">
                <div className="flex items-center gap-2 justify-between">
                  <span className="flex items-center gap-2">
                    <MoonIcon className="text-base text-muted-foreground h-4 w-4" />
                    <span className="font-medium text-sm">Dark Mode</span>
                  </span>
                  <input
                    className="w-4 h-4"
                    type="checkbox"
                  />
                </div>
                <button
                  onClick={handleLogout}
                  className="flex items-center justify-center gap-2 w-full px-3 py-2 border border-border rounded-md hover:bg-accent"
                >
                  <ArrowRightOnRectangleIcon className="h-4 w-4" />
                  Log out
                </button>
              </div>
            </div>
          )}
        </div>

        <div className="flex items-center gap-1.5">
          <button className="flex items-center justify-center w-10 h-10 rounded-md hover:bg-background">
            <MessageSquareDot className="size-4.5" />
          </button>
          <button className="flex items-center justify-center w-10 h-10 rounded-md hover:bg-background">
            <MessageCircleMore className="size-4.5" />
          </button>
        </div>
      </div>
    </div>
  );
};

export default Sidebar;