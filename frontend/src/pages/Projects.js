import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { projectService } from '../services/projectService';
import toast from 'react-hot-toast';
import { 
  PlusIcon, 
  EllipsisVerticalIcon,
  Squares2X2Icon,
  ListBulletIcon,
  CalendarDaysIcon,
  MapPinIcon,
  CurrencyDollarIcon,
  UserIcon,
  ClipboardDocumentListIcon as ClipboardListIcon,
  CheckCircleIcon,
  UserGroupIcon
} from '@heroicons/react/24/outline';

const Projects = () => {
  const { isSuperAdmin } = useAuth();
  const [projects, setProjects] = useState([]);
  const [loading, setLoading] = useState(true);
  const [viewMode, setViewMode] = useState('grid');
  const [openMenuId, setOpenMenuId] = useState(null);
  const [currentPage, setCurrentPage] = useState(1);
  const [pagination, setPagination] = useState(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');

  useEffect(() => {
    fetchProjects();
  }, []);

  // Refetch projects when search or filter changes
  useEffect(() => {
    fetchProjects(1); // Reset to page 1 when filters change
  }, [searchQuery, statusFilter]);

  const fetchProjects = async (page = 1) => {
    try {
      const params = { page };
      if (searchQuery && searchQuery.trim() !== '') {
        params.search = searchQuery.trim();
      }
      if (statusFilter && statusFilter !== 'all') {
        params.status = statusFilter;
      }

      const response = await projectService.getAll(page, params);
      console.log('Projects response:', response); // Debug log
      setProjects(response.data.data);
      setPagination(response.data);
      setCurrentPage(page);
    } catch (error) {
      console.error('Project fetch error:', error); // Debug log
      if (error.response?.status === 401) {
        toast.error('Please login to view projects');
      } else if (error.response?.data?.error) {
        toast.error(error.response.data.error);
      } else {
        toast.error('Failed to fetch projects');
      }
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id) => {
    if (window.confirm('Are you sure you want to delete this project?')) {
      try {
        await projectService.delete(id);
        toast.success('Project deleted successfully');
        fetchProjects(currentPage); // Refetch current page with current filters
      } catch (error) {
        toast.error('Failed to delete project');
      }
    }
  };

  const getProjectIcon = (project) => {
    const icons = ['📊', '🚀', '💼', '🎯', '📱', '🌐'];
    return icons[project.id % icons.length];
  };

  const getStatusColor = (status) => {
    const colors = {
      active: 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300',
      completed: 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300',
      on_hold: 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300',
      cancelled: 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300',
    };
    return colors[status] || 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
  };


  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 dark:border-blue-400"></div>
      </div>
    );
  }


  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
        <h1 className="text-2xl font-semibold text-gray-900 dark:text-gray-100">
          {pagination ? pagination.total : projects.length} Projects
        </h1>
        <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
          {/* Search and Filter */}
          <div className="flex items-center gap-3">
            {/* Search Bar */}
            <div className="relative">
              <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg className="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
              <input
                type="text"
                placeholder="Search projects..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            {/* Status Filter */}
            <select
              value={statusFilter}
              onChange={(e) => setStatusFilter(e.target.value)}
              className="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="all">All Status</option>
              <option value="active">Active</option>
              <option value="completed">Completed</option>
              <option value="on_hold">On Hold</option>
            </select>
          </div>

          {/* Action Buttons */}
          <div className="flex items-center gap-3">
            {isSuperAdmin && (
              <Link
                to="/projects/create"
                className="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 whitespace-nowrap"
              >
                <PlusIcon className="h-4 w-4" />
                New Project
              </Link>
            )}
            <div className="flex items-center border border-gray-300 dark:border-gray-600 rounded-lg">
              <button
                onClick={() => setViewMode('grid')}
                className={`p-2 ${viewMode === 'grid' ? 'bg-gray-100 dark:bg-gray-700' : ''}`}
              >
                <Squares2X2Icon className="h-5 w-5 text-gray-600 dark:text-gray-400" />
              </button>
              <button
                onClick={() => setViewMode('list')}
                className={`p-2 ${viewMode === 'list' ? 'bg-gray-100 dark:bg-gray-700' : ''}`}
              >
                <ListBulletIcon className="h-5 w-5 text-gray-600 dark:text-gray-400" />
              </button>
            </div>
          </div>
        </div>
      </div>

      {/* Projects List */}
      <div className="space-y-4">
        {viewMode === 'grid' ? (
          // Grid View
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {projects.map((project) => (
              <div key={project.id} className="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow relative">
                {/* Status Badge - Top Left Corner */}
                <div className="absolute top-4 left-4 z-10">
                  <span className={`px-3 py-1 text-sm rounded-full ${getStatusColor(project.status)}`}>
                    {project.status?.replace('_', ' ').toUpperCase()}
                  </span>
                </div>

                {/* Project Header - Centered */}
                <div className="flex flex-col items-center text-center mb-6">
                  <div className="flex items-center justify-between w-full mb-4">
                    <div className="w-8 h-8" /> {/* Spacer for balance */}
                    <div className="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-2xl">
                      {getProjectIcon(project)}
                    </div>
                    <div className="relative">
                      <button
                        onClick={() => setOpenMenuId(openMenuId === project.id ? null : project.id)}
                        className="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                      >
                        <EllipsisVerticalIcon className="h-5 w-5 text-gray-400 dark:text-gray-500" />
                      </button>

                      {openMenuId === project.id && (
                        <div className="absolute right-0 top-full mt-1 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg z-10">
                          <Link
                            to={`/projects/${project.id}`}
                            className="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                            onClick={() => setOpenMenuId(null)}
                          >
                            View Details
                          </Link>
                          {isSuperAdmin && (
                            <>
                              <Link
                                to={`/projects/${project.id}/edit`}
                                className="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                onClick={() => setOpenMenuId(null)}
                              >
                                Edit Project
                              </Link>
                              <button
                                onClick={() => {
                                  handleDelete(project.id);
                                  setOpenMenuId(null);
                                }}
                                className="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20"
                              >
                                Delete Project
                              </button>
                            </>
                          )}
                        </div>
                      )}
                    </div>
                  </div>
                  <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {project.name}
                  </h3>
                  <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {project.description}
                  </p>
                </div>


                {/* Project Stats - Real Data */}
                <div className="grid grid-cols-3 gap-4 text-sm">
                  <div>
                    <div className="flex items-center gap-1 text-gray-500 dark:text-gray-400 mb-1">
                      <ClipboardListIcon className="h-4 w-4" />
                      <span className="text-xs">Total Tasks</span>
                    </div>
                    <p className="font-medium text-gray-900 dark:text-gray-100">
                      {project.tasks?.length || 0}
                    </p>
                  </div>
                  <div>
                    <div className="flex items-center gap-1 text-gray-500 dark:text-gray-400 mb-1">
                      <CheckCircleIcon className="h-4 w-4" />
                      <span className="text-xs">Completed</span>
                    </div>
                    <p className="font-medium text-gray-900 dark:text-gray-100">
                      {project.tasks?.filter(task => task.status === 'completed').length || 0}
                    </p>
                  </div>
                  <div>
                    <div className="flex items-center gap-1 text-gray-500 dark:text-gray-400 mb-1">
                      <UserGroupIcon className="h-4 w-4" />
                      <span className="text-xs">Members</span>
                    </div>
                    <p className="font-medium text-gray-900 dark:text-gray-100">
                      {project.users?.length || 0}
                    </p>
                  </div>
                </div>

                {/* Progress Bar */}
                <div className="mt-6">
                  <div className="flex justify-between items-center mb-2">
                    <span className="text-sm text-gray-600 dark:text-gray-400">Progress</span>
                    <span className="text-sm font-medium text-gray-900 dark:text-gray-100">{project.progress || 0}%</span>
                  </div>
                  <div className="relative w-full">
                    <div className="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                      <div
                        className={`h-2 rounded-full transition-all ${
                          project.status === 'completed' ? 'bg-green-500' :
                          project.status === 'active' ? 'bg-blue-600 dark:bg-blue-400' :
                          'bg-gray-400 dark:bg-gray-500'
                        }`}
                        style={{ width: `${project.progress || 0}%` }}
                      />
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : (
          // List View
          <div className="space-y-3">
            {projects.map((project) => (
              <div key={project.id} className="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-4 flex-1">
                    {/* Project Icon & Name */}
                    <div className="flex items-center space-x-3">
                      <div className="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-lg">
                        {getProjectIcon(project)}
                      </div>
                      <div>
                        <Link
                          to={`/projects/${project.id}`}
                          className="text-lg font-semibold text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400"
                        >
                          {project.name}
                        </Link>
                        {project.description && (
                          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">
                            {project.description}
                          </p>
                        )}
                      </div>
                    </div>

                    {/* Status Badge */}
                    <span className={`px-3 py-1 text-sm rounded-full ${getStatusColor(project.status)}`}>
                      {project.status?.replace('_', ' ').toUpperCase()}
                    </span>

                    {/* Project Stats */}
                    <div className="hidden md:flex items-center space-x-6 text-sm text-gray-600 dark:text-gray-400">
                      <div className="flex items-center space-x-1">
                        <ClipboardListIcon className="h-4 w-4" />
                        <span>{project.tasks?.length || 0} tasks</span>
                      </div>
                      <div className="flex items-center space-x-1">
                        <UserIcon className="h-4 w-4" />
                        <span>{project.users?.length || 0} members</span>
                      </div>
                      <div className="flex items-center space-x-1">
                        <CalendarDaysIcon className="h-4 w-4" />
                        <span>{new Date(project.created_at).toLocaleDateString()}</span>
                      </div>
                    </div>

                    {/* Progress */}
                    <div className="hidden lg:block min-w-[120px]">
                      <div className="flex items-center space-x-2">
                        <div className="flex-1 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                          <div
                            className={`h-2 rounded-full ${
                              project.status === 'completed' ? 'bg-green-500' :
                              project.status === 'active' ? 'bg-blue-600' :
                              'bg-gray-400'
                            }`}
                            style={{ width: `${project.progress || 0}%` }}
                          />
                        </div>
                        <span className="text-sm font-medium text-gray-900 dark:text-gray-100 min-w-[35px]">
                          {project.progress || 0}%
                        </span>
                      </div>
                    </div>
                  </div>

                  {/* Actions Menu */}
                  <div className="relative ml-4">
                    <button
                      onClick={() => setOpenMenuId(openMenuId === project.id ? null : project.id)}
                      className="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full"
                    >
                      <EllipsisVerticalIcon className="h-5 w-5 text-gray-400 dark:text-gray-500" />
                    </button>

                    {openMenuId === project.id && (
                      <div className="absolute right-0 top-full mt-1 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg z-10">
                        <Link
                          to={`/projects/${project.id}`}
                          className="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                          onClick={() => setOpenMenuId(null)}
                        >
                          View Details
                        </Link>
                        {isSuperAdmin && (
                          <>
                            <Link
                              to={`/projects/${project.id}/edit`}
                              className="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                              onClick={() => setOpenMenuId(null)}
                            >
                              Edit Project
                            </Link>
                            <button
                              onClick={() => {
                                handleDelete(project.id);
                                setOpenMenuId(null);
                              }}
                              className="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20"
                            >
                              Delete Project
                            </button>
                          </>
                        )}
                      </div>
                    )}
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
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
              onClick={() => fetchProjects(currentPage - 1)}
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
                    onClick={() => fetchProjects(page)}
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
              onClick={() => fetchProjects(currentPage + 1)}
              disabled={currentPage === pagination.last_page}
              className="px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next
            </button>
          </div>
        </div>
      )}

      {/* Empty State */}
      {(pagination ? pagination.total === 0 : projects.length === 0) ? (
        <div className="text-center py-12">
          <div className="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
            <Squares2X2Icon className="h-8 w-8 text-gray-400 dark:text-gray-500" />
          </div>
          <h3 className="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No projects yet</h3>
          <p className="text-gray-500 dark:text-gray-400 mb-6">Get started by creating your first project</p>
          {isSuperAdmin && (
            <Link
              to="/projects/create"
              className="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
            >
              <PlusIcon className="h-4 w-4" />
              Create Project
            </Link>
          )}
        </div>
      ) : projects.length === 0 ? (
        <div className="text-center py-12">
          <div className="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg className="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <h3 className="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No projects found</h3>
          <p className="text-gray-500 dark:text-gray-400 mb-6">
            No projects match your search criteria. Try adjusting your search or filter.
          </p>
          <button
            onClick={() => {
              setSearchQuery('');
              setStatusFilter('all');
            }}
            className="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30"
          >
            Clear Filters
          </button>
        </div>
      ) : null}
    </div>
  );
};

export default Projects;