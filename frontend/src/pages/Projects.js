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

  useEffect(() => {
    fetchProjects();
  }, []);

  const fetchProjects = async () => {
    try {
      const response = await projectService.getAll();
      console.log('Projects response:', response); // Debug log
      setProjects(response.data);
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
        fetchProjects();
      } catch (error) {
        toast.error('Failed to delete project');
      }
    }
  };

  const getProjectIcon = (project) => {
    const icons = ['📊', '🚀', '💼', '🎯', '📱', '🌐'];
    return icons[project.id % icons.length];
  };

  const getStatusSection = (status) => {
    const sections = {
      active: 'In Progress',
      completed: 'Completed',
      on_hold: 'On Hold'
    };
    return sections[status] || 'In Progress';
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

  const getTeamAvatars = () => {
    // Generate sample team avatars
    const avatarCount = Math.floor(Math.random() * 3) + 2;
    return Array.from({ length: avatarCount }, (_, i) => ({
      id: i,
      src: `/assets/media/avatars/300-${i + 1}.jpg`
    }));
  };

  const groupProjectsByStatus = () => {
    const grouped = {
      'In Progress': [],
      'Completed': [],
      'On Hold': []
    };
    projects.forEach(project => {
      const section = getStatusSection(project.status);
      if (grouped[section]) {
        grouped[section].push(project);
      }
    });
    return Object.entries(grouped).filter(([_, projects]) => projects.length > 0);
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 dark:border-blue-400"></div>
      </div>
    );
  }

  const groupedProjects = groupProjectsByStatus();

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-semibold text-gray-900 dark:text-gray-100">
          {projects.length} Projects
        </h1>
        <div className="flex items-center gap-3">
          {isSuperAdmin && (
            <Link
              to="/projects/create"
              className="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
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

      {/* Projects by Status */}
      <div className="space-y-8">
        {groupedProjects.map(([status, statusProjects]) => (
          <div key={status}>
            {/* Status Header */}
            <div className="flex items-center justify-between mb-4">
              <h2 className={`text-sm font-medium ${
                status === 'In Progress' ? 'text-blue-600 dark:text-blue-400' :
                status === 'Completed' ? 'text-green-600 dark:text-green-400' :
                status === 'On Hold' ? 'text-yellow-600 dark:text-yellow-400' :
                'text-gray-600 dark:text-gray-400'
              }`}>
                {status}
              </h2>
              <button className="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                <EllipsisVerticalIcon className="h-5 w-5 text-gray-400 dark:text-gray-500" />
              </button>
            </div>

{/* Project Cards Grid/List */}
{viewMode === 'grid' ? (
  // Grid View
  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
    {statusProjects.map((project) => (
      <div key={project.id} className="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
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

        {/* Team - Centered */}
        <div className="mb-6 text-center">
          <p className="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">TEAM</p>
          <div className="flex justify-center -space-x-2">
            {getTeamAvatars().map((avatar) => (
              <img
                key={avatar.id}
                src={avatar.src}
                alt="Team member"
                className="w-8 h-8 rounded-full border-2 border-white dark:border-gray-800"
              />
            ))}
            <div className="w-8 h-8 rounded-full bg-blue-600 border-2 border-white dark:border-gray-800 flex items-center justify-center text-white text-xs font-medium">
              S
            </div>
          </div>
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
                  status === 'Completed' ? 'bg-green-500' :
                  status === 'In Progress' ? 'bg-blue-600 dark:bg-blue-400' :
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
                {statusProjects.map((project) => (
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
                                  status === 'Completed' ? 'bg-green-500' :
                                  status === 'In Progress' ? 'bg-blue-600' :
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

            {/* Section Progress Line */}
            {status === 'In Progress' && (
              <div className="mt-6">
                <div className="h-1 bg-blue-600 rounded-full" style={{ width: '70%' }}></div>
              </div>
            )}
            {status === 'Upcoming' && (
              <div className="mt-6">
                <div className="h-1 bg-gray-300 rounded-full" style={{ width: '30%' }}></div>
              </div>
            )}
            {status === 'Completed' && (
              <div className="mt-6">
                <div className="h-1 bg-green-500 rounded-full" style={{ width: '100%' }}></div>
              </div>
            )}
          </div>
        ))}
      </div>

      {/* Empty State */}
      {projects.length === 0 && (
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
      )}
    </div>
  );
};

export default Projects;