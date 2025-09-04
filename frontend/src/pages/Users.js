import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { userService } from '../services/userService';
import toast from 'react-hot-toast';
import {
  MagnifyingGlassIcon,
  FunnelIcon,
  ChevronDownIcon,
  ChevronUpIcon,
  EllipsisVerticalIcon,
  ArrowUpTrayIcon,
  PencilIcon,
  TrashIcon
} from '@heroicons/react/24/outline';

const Users = () => {
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [sortField, setSortField] = useState('name');
  const [sortDirection, setSortDirection] = useState('asc');
  const [showFilters, setShowFilters] = useState(false);
  const [openMenuId, setOpenMenuId] = useState(null);
  const [currentPage, setCurrentPage] = useState(1);
  const [pagination, setPagination] = useState(null);

  useEffect(() => {
    fetchUsers();
  }, []);

  const fetchUsers = async (page = 1) => {
    try {
      const response = await userService.getAll(page);
      setUsers(response.data.data);
      setPagination(response.data);
      setCurrentPage(page);
    } catch (error) {
      toast.error('Failed to fetch users');
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id) => {
    if (window.confirm('Are you sure you want to delete this user?')) {
      try {
        await userService.delete(id);
        toast.success('User deleted successfully');
        fetchUsers();
      } catch (error) {
        toast.error('Failed to delete user');
      }
    }
  };

  const handleSort = (field) => {
    if (sortField === field) {
      setSortDirection(sortDirection === 'asc' ? 'desc' : 'asc');
    } else {
      setSortField(field);
      setSortDirection('asc');
    }
  };

  const getSortedUsers = () => {
    const filtered = users.filter(user => 
      (user.name || '').toLowerCase().includes(searchQuery.toLowerCase()) ||
      (user.email || '').toLowerCase().includes(searchQuery.toLowerCase())
    );

    return filtered.sort((a, b) => {
      let aVal, bVal;

      switch (sortField) {
        case 'name':
          aVal = (a.name || '').toLowerCase();
          bVal = (b.name || '').toLowerCase();
          break;
        case 'email':
          aVal = (a.email || '').toLowerCase();
          bVal = (b.email || '').toLowerCase();
          break;
        case 'role':
          aVal = (a.role || '').toLowerCase();
          bVal = (b.role || '').toLowerCase();
          break;
        default:
          return 0;
      }

      if (aVal < bVal) return sortDirection === 'asc' ? -1 : 1;
      if (aVal > bVal) return sortDirection === 'asc' ? 1 : -1;
      return 0;
    });
  };

  const getRoleBadge = (role) => {
    const badges = {
      super_admin: { text: 'Super Admin', className: 'bg-purple-100 text-purple-700' },
      client: { text: 'Client', className: 'bg-blue-100 text-blue-700' },
      developer: { text: 'Developer', className: 'bg-green-100 text-green-700' },
    };
    return badges[role] || { text: role, className: 'bg-gray-100 text-gray-700' };
  };

  const getUserAvatar = (user) => {
    return user.name ? user.name.charAt(0).toUpperCase() : '?';
  };

  const renderSortIcon = (field) => {
    if (sortField !== field) {
      return <ChevronDownIcon className="h-3 w-3 opacity-30" />;
    }
    return sortDirection === 'asc' ? 
      <ChevronUpIcon className="h-3 w-3" /> : 
      <ChevronDownIcon className="h-3 w-3" />;
  };

  const sortedUsers = getSortedUsers();

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <nav className="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400 mb-2">
            <Link to="/" className="hover:text-gray-900 dark:hover:text-gray-100">Home</Link>
            <span>/</span>
            <Link to="/users" className="hover:text-gray-900 dark:hover:text-gray-100">Users</Link>
          </nav>
          <h1 className="text-2xl font-semibold text-gray-900 dark:text-gray-100">Users Management</h1>
        </div>
        
        <div className="flex items-center gap-3">
          <Link
            to="/users/create"
            className="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
          >
            <span>New User</span>
          </Link>
          <button className="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
            <ArrowUpTrayIcon className="h-4 w-4" />
            Export
          </button>
        </div>
      </div>

      {/* Stats and Filters */}
      <div className="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div className="p-6 border-b border-gray-200 dark:border-gray-700">
          <div className="flex items-center justify-between mb-4">
            <p className="text-sm text-gray-600 dark:text-gray-400">
              Showing <span className="font-semibold text-gray-900 dark:text-gray-100">{sortedUsers.length}</span> of{' '}
              <span className="font-semibold text-gray-900 dark:text-gray-100">{pagination ? pagination.total : users.length}</span> users
            </p>
          </div>
          
          <div className="flex items-center gap-4">
            <div className="relative flex-1 max-w-md">
              <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 dark:text-gray-500" />
              <input
                type="text"
                placeholder="Search users"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            
            <button
              onClick={() => setShowFilters(!showFilters)}
              className="flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <FunnelIcon className="h-4 w-4" />
              Filters
            </button>
          </div>
        </div>

        {/* Table */}
        <div className="overflow-x-auto">
          <table className="min-w-full">
            <thead>
              <tr className="border-b border-gray-200 dark:border-gray-700">
                <th className="text-left py-3 px-6">
                  <div className="flex items-center gap-1 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    <input type="checkbox" className="rounded border-gray-300 dark:border-gray-600" />
                  </div>
                </th>
                <th className="text-left py-3 px-6">
                  <button
                    onClick={() => handleSort('name')}
                    className="flex items-center gap-1 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-300 cursor-pointer"
                  >
                    User
                    {renderSortIcon('name')}
                  </button>
                </th>
                <th className="text-left py-3 px-6">
                  <button
                    onClick={() => handleSort('email')}
                    className="flex items-center gap-1 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-300 cursor-pointer"
                  >
                    Email
                    {renderSortIcon('email')}
                  </button>
                </th>
                <th className="text-left py-3 px-6">
                  <button
                    onClick={() => handleSort('role')}
                    className="flex items-center gap-1 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-300 cursor-pointer"
                  >
                    Role
                    {renderSortIcon('role')}
                  </button>
                </th>
                <th className="text-right py-3 px-6">
                  <div className="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Actions
                  </div>
                </th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200 dark:divide-gray-700">
              {sortedUsers.map((user) => {
                const roleBadge = getRoleBadge(user.role);
                return (
                  <tr key={user.id} className="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td className="py-4 px-6">
                      <input type="checkbox" className="rounded border-gray-300 dark:border-gray-600" />
                    </td>
                    <td className="py-4 px-6">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                          {getUserAvatar(user)}
                        </div>
                        <div className="font-medium text-gray-900 dark:text-gray-100">{user.name}</div>
                      </div>
                    </td>
                    <td className="py-4 px-6">
                      <span className="text-gray-600 dark:text-gray-400">{user.email}</span>
                    </td>
                    <td className="py-4 px-6">
                      <span className={`px-2.5 py-1 text-xs font-medium rounded-md ${roleBadge.className}`}>
                        {roleBadge.text}
                      </span>
                    </td>
                    <td className="py-4 px-6">
                      <div className="flex items-center justify-end gap-2 relative">
                        <Link
                          to={`/users/${user.id}/edit`}
                          className="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors"
                          title="Edit"
                        >
                          <PencilIcon className="h-4 w-4" />
                        </Link>
                        <button
                          onClick={() => handleDelete(user.id)}
                          className="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors"
                          title="Delete"
                        >
                          <TrashIcon className="h-4 w-4" />
                        </button>
                        <button
                          className="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors"
                          onClick={() => setOpenMenuId(openMenuId === user.id ? null : user.id)}
                        >
                          <EllipsisVerticalIcon className="h-4 w-4" />
                        </button>
                        {/* Dropdown menu */}
                        {openMenuId === user.id && (
                          <div className="absolute right-0 top-full mt-1 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <Link
                              to={`/users/${user.id}`}
                              className="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                              onClick={() => setOpenMenuId(null)}
                            >
                              View Details
                            </Link>
                            <Link
                              to={`/users/${user.id}/permissions`}
                              className="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                              onClick={() => setOpenMenuId(null)}
                            >
                              Manage Permissions
                            </Link>
                            <button
                              className="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                              onClick={() => {
                                // Handle deactivate
                                setOpenMenuId(null);
                              }}
                            >
                              Deactivate User
                            </button>
                          </div>
                        )}
                      </div>
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>

        {/* Pagination */}
        {pagination && pagination.last_page > 1 && (
          <div className="flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            <div className="flex items-center text-sm text-gray-700 dark:text-gray-300">
              <p>
                Showing <span className="font-medium">{pagination.from}</span> to{' '}
                <span className="font-medium">{pagination.to}</span> of{' '}
                <span className="font-medium">{pagination.total}</span> results
              </p>
            </div>
            <div className="flex items-center space-x-2">
              <button
                onClick={() => fetchUsers(currentPage - 1)}
                disabled={currentPage === 1}
                className="px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Previous
              </button>
              <div className="flex items-center space-x-1">
                {Array.from({ length: Math.min(5, pagination.last_page) }, (_, i) => {
                  const page = i + 1;
                  return (
                    <button
                      key={page}
                      onClick={() => fetchUsers(page)}
                      className={`px-3 py-1 text-sm font-medium rounded-md ${
                        currentPage === page
                          ? 'text-blue-600 bg-blue-50 border border-blue-500'
                          : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50'
                      }`}
                    >
                      {page}
                    </button>
                  );
                })}
              </div>
              <button
                onClick={() => fetchUsers(currentPage + 1)}
                disabled={currentPage === pagination.last_page}
                className="px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Next
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default Users;