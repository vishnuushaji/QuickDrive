import React, { useState, useEffect } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { Menu, Search, ChevronDown } from 'lucide-react';
import { useAuth } from '../contexts/AuthContext';

const Header = ({ onMenuToggle }) => {
  const location = useLocation();
  const { user } = useAuth();
  const [isSheetOpen, setIsSheetOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');

  // Close sheet when route changes
  useEffect(() => {
    setIsSheetOpen(false);
  }, [location]);

  const handleInputChange = () => {};

  const navigation = [
    { name: 'Dashboard', href: '/dashboard', roles: ['super_admin', 'client', 'developer'] },
    { name: 'Users', href: '/users', roles: ['super_admin'] },
    { name: 'Projects', href: '/projects', roles: ['super_admin', 'client', 'developer'] },
    { name: 'Tasks', href: '/tasks', roles: ['super_admin', 'client', 'developer'] },
  ];

  const filteredNavigation = navigation.filter(item =>
    item.roles.includes(user?.role)
  );

  return (
    <>
      <header className="flex lg:hidden items-center fixed z-10 top-0 start-0 end-0 shrink-0 bg-muted h-[--header-height]">
        <div className="flex items-center justify-between flex-wrap gap-3 px-5">
          <Link to="/" className="flex items-center">
            <img
              className="dark:hidden min-h-[30px]"
              src="/assets/media/app/mini-logo-gray.svg"
              alt="Logo"
            />
            <img
              className="hidden dark:block min-h-[30px]"
              src="/assets/media/app/mini-logo-gray-dark.svg"
              alt="Logo Dark"
            />
          </Link>

          <button
            className="flex items-center justify-center w-10 h-10 rounded-md hover:bg-background"
            onClick={() => setIsSheetOpen(true)}
          >
            <Menu className="h-6 w-6" />
          </button>
        </div>
      </header>

      {/* Mobile Sheet */}
      {isSheetOpen && (
        <div className="fixed inset-0 z-50 lg:hidden">
          <div
            className="fixed inset-0 bg-black/50"
            onClick={() => setIsSheetOpen(false)}
          />
          <div className="fixed top-0 left-0 bottom-0 w-[275px] bg-background shadow-lg">
            {/* Sheet Header */}
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
                  <ChevronDown className="size-3.5 text-muted-foreground" />
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

            {/* Sheet Menu */}
            <div className="kt-scrollable-y-auto grow max-h-[calc(100vh-11.5rem)]">
              <div className="border-b border-input my-4 mx-5"></div>
              <div className="flex flex-col w-full gap-1.5 px-3.5">
                {filteredNavigation.map((item) => (
                  <Link
                    key={item.name}
                    to={item.href}
                    className={`flex items-center gap-2 h-9 px-2 rounded-md border border-transparent text-accent-foreground hover:text-primary hover:bg-background hover:border-border ${location.pathname === item.href ? 'text-primary bg-background border-border font-medium' : ''}`}
                    onClick={() => setIsSheetOpen(false)}
                  >
                    <span className="text-sm font-medium">{item.name}</span>
                  </Link>
                ))}
              </div>
            </div>

            {/* Sheet Footer */}
            <div className="flex items-center justify-between shrink-0 ps-4 pe-3.5 h-14">
              <div className="size-9 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 border-2 border-secondary shrink-0 cursor-pointer flex items-center justify-center text-white font-semibold text-sm">
                {user?.name ? user.name.charAt(0).toUpperCase() : '?'}
              </div>

              <div className="flex items-center gap-1.5">
                <button className="flex items-center justify-center w-10 h-10 rounded-md hover:bg-background">
                  <span className="text-sm">🔔</span>
                </button>
                <button className="flex items-center justify-center w-10 h-10 rounded-md hover:bg-background">
                  <span className="text-sm">💬</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </>
  );
};

export default Header;
