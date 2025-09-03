import React, { useState, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { projectService } from '../services/projectService';
import { useAuth } from '../contexts/AuthContext';
import toast from 'react-hot-toast';
import {
  PencilIcon,
  TrashIcon,
  ClipboardDocumentListIcon as ClipboardListIcon,
  UserIcon,
  FolderIcon,
  UsersIcon,
  CheckCircleIcon,
  ClockIcon,
  ArrowLeftIcon,
  CalendarDaysIcon as CalendarIcon,
  ChartBarIcon
} from '@heroicons/react/24/outline';

const ProjectDetails = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const { user, isSuperAdmin } = useAuth();
  const [project, setProject] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchProject();
  }, [id]);

  const fetchProject = async () => {
    try {
      const response = await projectService.getById(id);
      setProject(response.data);
    } catch (error) {
      toast.error('Failed to fetch project details');
      navigate('/projects');
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async () => {
    if (window.confirm('Are you sure you want to delete this project?')) {
      try {
        await projectService.delete(id);
        toast.success('Project deleted successfully');
        navigate('/projects');
      } catch (error) {
        toast.error('Failed to delete project');
      }
    }
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

  const getTaskStatusColor = (status) => {
    const colors = {
      pending: 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
      in_progress: 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300',
      completed: 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300',
      approved: 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300',
      rejected: 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300',
    };
    return colors[status] || 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
  };

  const getPriorityColor = (priority) => {
    const colors = {
      normal: 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
      urgent: 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-300',
      top_urgent: 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300',
    };
    return colors[priority] || 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
  };

  if (loading) {
    return <div className="text-center py-4 text-gray-600 dark:text-gray-400">Loading...</div>;
  }

  if (!project) {
    return <div className="text-center py-4 text-gray-600 dark:text-gray-400">Project not found</div>;
  }

  const clients = project.users?.filter(user => user.pivot.role === 'client') || [];
  const developers = project.users?.filter(user => user.pivot.role === 'developer') || [];
  const completedTasks = project.tasks?.filter(task => task.status === 'approved') || [];
  const inProgressTasks = project.tasks?.filter(task => task.status === 'in_progress') || [];
  const completionRate = project.tasks?.length ? Math.round((completedTasks.length / project.tasks.length) * 100) : 0;

  const canEdit = isSuperAdmin;
  const canDelete = isSuperAdmin;

  return (
    <div className="max-w-4xl mx-auto space-y-6">
      {/* Main Project Card */}
      <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div className="flex justify-between items-start mb-6">
          <div>
            <div className="flex items-center mb-2">
              <Link
                to="/projects"
                className="mr-3 p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
              >
                <ArrowLeftIcon className="h-5 w-5" />
              </Link>
              <FolderIcon className="h-6 w-6 text-indigo-600 mr-2" />
              <h1 className="text-2xl font-semibold text-gray-900 dark:text-gray-100">{project.name}</h1>
            </div>
            <div className="flex items-center mt-2 space-x-3">
              <span className={`px-3 py-1 text-sm rounded-full ${getStatusColor(project.status)}`}>
                {project.status?.replace('_', ' ').toUpperCase()}
              </span>
              {project.progress !== undefined && (
                <span className="px-3 py-1 text-sm rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-300">
                  {project.progress}% Complete
                </span>
              )}
            </div>
          </div>
          <div className="flex items-center space-x-2">
            {canEdit && (
              <Link
                to={`/projects/${project.id}/edit`}
                className="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
              >
                <PencilIcon className="h-5 w-5" />
              </Link>
            )}
            {canDelete && (
              <button
                onClick={handleDelete}
                className="p-2 text-red-600 hover:text-red-900"
              >
                <TrashIcon className="h-5 w-5" />
              </button>
            )}
          </div>
        </div>

        {/* Project Stats Grid */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <div className="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div className="flex items-center justify-center mb-2">
              <ClipboardListIcon className="h-6 w-6 text-blue-600" />
            </div>
            <div className="text-2xl font-bold text-gray-900 dark:text-gray-100">{project.tasks?.length || 0}</div>
            <div className="text-sm text-gray-600 dark:text-gray-400">Total Tasks</div>
            <div className="text-xs text-gray-500 dark:text-gray-500 mt-1">
              {completedTasks.length} completed • {inProgressTasks.length} in progress
            </div>
          </div>
          
          <div className="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div className="flex items-center justify-center mb-2">
              <UsersIcon className="h-6 w-6 text-green-600" />
            </div>
            <div className="text-2xl font-bold text-gray-900 dark:text-gray-100">{project.users?.length || 0}</div>
            <div className="text-sm text-gray-600 dark:text-gray-400">Team Members</div>
            <div className="text-xs text-gray-500 dark:text-gray-500 mt-1">
              {clients.length} clients • {developers.length} developers
            </div>
          </div>

          <div className="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div className="flex items-center justify-center mb-2">
              <ChartBarIcon className="h-6 w-6 text-indigo-600" />
            </div>
            <div className="text-2xl font-bold text-gray-900 dark:text-gray-100">{completionRate}%</div>
            <div className="text-sm text-gray-600 dark:text-gray-400">Completion Rate</div>
            <div className="w-full bg-gray-300 dark:bg-gray-600 rounded-full h-2 mt-2">
              <div 
                className="bg-indigo-600 h-2 rounded-full transition-all duration-300" 
                style={{ width: `${completionRate}%` }}
              ></div>
            </div>
          </div>
        </div>

        {/* Project Description */}
        {project.description && (
          <div className="mb-6">
            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</h3>
            <p className="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{project.description}</p>
          </div>
        )}

        {/* Team Members Section */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div>
            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2 flex items-center">
              <UserIcon className="h-4 w-4 mr-1" />
              Clients ({clients.length})
            </h3>
            {clients.length > 0 ? (
              <div className="space-y-2">
                {clients.map(client => (
                  <div key={client.id} className="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div className="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                      <span className="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                        {client.name.charAt(0).toUpperCase()}
                      </span>
                    </div>
                    <div className="flex-1">
                      <p className="text-sm font-medium text-gray-900 dark:text-gray-100">{client.name}</p>
                      <p className="text-xs text-gray-500 dark:text-gray-400">{client.email}</p>
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <p className="text-sm text-gray-500 dark:text-gray-400">No clients assigned</p>
            )}
          </div>

          <div>
            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2 flex items-center">
              <UserIcon className="h-4 w-4 mr-1" />
              Developers ({developers.length})
            </h3>
            {developers.length > 0 ? (
              <div className="space-y-2">
                {developers.map(developer => (
                  <div key={developer.id} className="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div className="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                      <span className="text-sm font-medium text-blue-600 dark:text-blue-400">
                        {developer.name.charAt(0).toUpperCase()}
                      </span>
                    </div>
                    <div className="flex-1">
                      <p className="text-sm font-medium text-gray-900 dark:text-gray-100">{developer.name}</p>
                      <p className="text-xs text-gray-500 dark:text-gray-400">{developer.email}</p>
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <p className="text-sm text-gray-500 dark:text-gray-400">No developers assigned</p>
            )}
          </div>
        </div>

        {/* Tasks Section */}
        <div className="border-t border-gray-200 dark:border-gray-700 pt-6">
          <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4 flex items-center">
            <ClipboardListIcon className="h-4 w-4 mr-1" />
            Project Tasks ({project.tasks?.length || 0})
          </h3>
          
          {project.tasks && project.tasks.length > 0 ? (
            <div className="space-y-3">
              {project.tasks.map(task => (
                <div key={task.id} className="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                  <div className="flex justify-between items-start">
                    <div className="flex-1">
                      <div className="flex items-center space-x-3 mb-2">
                        <Link
                          to={`/tasks/${task.id}`}
                          className="text-base font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400"
                        >
                          {task.title}
                        </Link>
                        <span className={`px-2 py-1 text-xs rounded-full ${getTaskStatusColor(task.status)}`}>
                          {task.status?.replace('_', ' ').toUpperCase()}
                        </span>
                        {task.priority && task.priority !== 'normal' && (
                          <span className={`px-2 py-1 text-xs rounded-full ${getPriorityColor(task.priority)}`}>
                            {task.priority.replace('_', ' ').toUpperCase()}
                          </span>
                        )}
                      </div>
                      
                      {task.description && (
                        <p className="text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">{task.description}</p>
                      )}
                      
                      <div className="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                        {task.assigned_user && (
                          <div className="flex items-center space-x-1">
                            <UserIcon className="h-3 w-3" />
                            <span>{task.assigned_user.name}</span>
                          </div>
                        )}
                        <div className="flex items-center space-x-1">
                          <CalendarIcon className="h-3 w-3" />
                          <span>Created: {new Date(task.created_at).toLocaleDateString()}</span>
                        </div>
                      </div>
                    </div>
                    
                    <Link
                      to={`/tasks/${task.id}`}
                      className="ml-4 px-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                      View
                    </Link>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="text-center py-8 border border-gray-200 dark:border-gray-600 rounded-lg">
              <ClipboardListIcon className="h-12 w-12 text-gray-400 mx-auto mb-4" />
              <h4 className="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Tasks Yet</h4>
              <p className="text-gray-500 dark:text-gray-400 mb-4">Create your first task for this project</p>
              {isSuperAdmin && (
                <Link 
                  to="/tasks/create" 
                  className="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                >
                  Create Task
                </Link>
              )}
            </div>
          )}
        </div>

        {/* Project Timeline */}
        <div className="mt-6 text-sm text-gray-500 dark:text-gray-400">
          <p className="flex items-center">
            <CalendarIcon className="h-4 w-4 mr-1" />
            Created: {new Date(project.created_at).toLocaleDateString()}
          </p>
          <p className="flex items-center mt-1">
            <CalendarIcon className="h-4 w-4 mr-1" />
            Last Updated: {new Date(project.updated_at).toLocaleDateString()}
          </p>
        </div>
      </div>
    </div>
  );
};

export default ProjectDetails;