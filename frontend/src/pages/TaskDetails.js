import React, { useState, useEffect, useCallback } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { taskService } from '../services/taskService';
import { useAuth } from '../contexts/AuthContext';
import toast from 'react-hot-toast';
import {
  PencilIcon,
  TrashIcon,
  CheckCircleIcon,
  XCircleIcon,
  PaperClipIcon,
  CalendarDaysIcon as CalendarIcon,
  UserIcon,
  FolderIcon,
  ClipboardDocumentListIcon as ClipboardListIcon,
  ArrowLeftIcon,
  ChartBarIcon,
  ClockIcon,
  ExclamationTriangleIcon
} from '@heroicons/react/24/outline';

const TaskDetails = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const { user, isSuperAdmin, isClient, isDeveloper } = useAuth();
  const [task, setTask] = useState(null);
  const [loading, setLoading] = useState(true);

  const BACKEND_BASE_URL = process.env.REACT_APP_API_BASE_URL?.replace('/api', '') || 'http://localhost:8000';

  const fetchTask = useCallback(async () => {
    try {
      const response = await taskService.getById(id);
      setTask(response.data);
    } catch (error) {
      toast.error('Failed to fetch task details');
      navigate('/tasks');
    } finally {
      setLoading(false);
    }
  }, [id, navigate]);

  useEffect(() => {
    fetchTask();
  }, [fetchTask]);

  const handleStatusUpdate = async (newStatus) => {
    try {
      await taskService.updateStatus(id, newStatus);
      toast.success('Status updated successfully');
      navigate('/tasks', { replace: true });
    } catch (error) {
      toast.error('Failed to update status');
    }
  };

  const handleApprove = async () => {
    try {
      await taskService.approve(id);
      toast.success('Task approved successfully');
      navigate('/tasks', { replace: true });
    } catch (error) {
      toast.error('Failed to approve task');
    }
  };

  const handleReject = async () => {
    try {
      await taskService.reject(id);
      toast.success('Task rejected');
      navigate('/tasks', { replace: true });
    } catch (error) {
      toast.error('Failed to reject task');
    }
  };

  const handleDelete = async () => {
    if (window.confirm('Are you sure you want to delete this task?')) {
      try {
        await taskService.delete(id);
        toast.success('Task deleted successfully');
        navigate('/tasks');
      } catch (error) {
        toast.error('Failed to delete task');
      }
    }
  };

  const getStatusColor = (status) => {
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

  const getPriorityIcon = (priority) => {
    switch (priority) {
      case 'urgent':
        return <ClockIcon className="h-4 w-4" />;
      case 'top_urgent':
        return <ExclamationTriangleIcon className="h-4 w-4" />;
      default:
        return <ClipboardListIcon className="h-4 w-4" />;
    }
  };

  if (loading) {
    return <div className="text-center py-4 text-gray-600 dark:text-gray-400">Loading...</div>;
  }

  if (!task) {
    return <div className="text-center py-4 text-gray-600 dark:text-gray-400">Task not found</div>;
  }

  const canEdit = isSuperAdmin;
  const canDelete = isSuperAdmin; // Only super admins can delete tasks
  const canApprove = isClient && task.status === 'completed';
  const canUpdateStatus = isDeveloper && task.assigned_user_id === user?.id;

  // Calculate days since creation
  const daysSinceCreation = Math.floor((new Date() - new Date(task.created_at)) / (1000 * 60 * 60 * 24));
  const daysSinceUpdate = Math.floor((new Date() - new Date(task.updated_at)) / (1000 * 60 * 60 * 24));

  return (
    <div className="max-w-4xl mx-auto space-y-6">
      {/* Main Task Card */}
      <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div className="flex justify-between items-start mb-6">
          <div>
            <div className="flex items-center mb-2">
              <Link
                to="/tasks"
                className="mr-3 p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
              >
                <ArrowLeftIcon className="h-5 w-5" />
              </Link>
              <ClipboardListIcon className="h-6 w-6 text-indigo-600 mr-2" />
              <h1 className="text-2xl font-semibold text-gray-900 dark:text-gray-100">{task.title}</h1>
            </div>
            <div className="flex items-center mt-2 space-x-3">
              <span className={`px-3 py-1 text-sm rounded-full ${getStatusColor(task.status)}`}>
                {task.status.replace('_', ' ').toUpperCase()}
              </span>
              <span className={`px-3 py-1 text-sm rounded-full ${getPriorityColor(task.priority)}`}>
                {task.priority.replace('_', ' ').toUpperCase()}
              </span>
            </div>
          </div>
          <div className="flex items-center space-x-2">
            {canEdit && (
              <Link
                to={`/tasks/${task.id}/edit`}
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

        {/* Task Stats Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
          <div className="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div className="flex items-center justify-center mb-2">
              {getPriorityIcon(task.priority)}
            </div>
            <div className="text-2xl font-bold text-gray-900 dark:text-gray-100">{task.priority?.replace('_', ' ').toUpperCase()}</div>
            <div className="text-sm text-gray-600 dark:text-gray-400">Priority Level</div>
          </div>

          <div className="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div className="flex items-center justify-center mb-2">
              <ClockIcon className="h-6 w-6 text-blue-600" />
            </div>
            <div className="text-2xl font-bold text-gray-900 dark:text-gray-100">{daysSinceCreation}</div>
            <div className="text-sm text-gray-600 dark:text-gray-400">Days Since Created</div>
            <div className="text-xs text-gray-500 dark:text-gray-500 mt-1">
              Updated {daysSinceUpdate} days ago
            </div>
          </div>

          <div className="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div className="flex items-center justify-center mb-2">
              <ChartBarIcon className="h-6 w-6 text-indigo-600" />
            </div>
            <div className="text-2xl font-bold text-gray-900 dark:text-gray-100">
              {task.status === 'approved' ? '100%' : task.status === 'completed' ? '90%' : task.status === 'in_progress' ? '50%' : '0%'}
            </div>
            <div className="text-sm text-gray-600 dark:text-gray-400">Progress</div>
            <div className="w-full bg-gray-300 dark:bg-gray-600 rounded-full h-2 mt-2">
              <div
                className={`h-2 rounded-full transition-all duration-300 ${
                  task.status === 'approved' ? 'bg-green-600' :
                  task.status === 'completed' ? 'bg-yellow-600' :
                  task.status === 'in_progress' ? 'bg-blue-600' : 'bg-gray-400'
                }`}
                style={{
                  width: task.status === 'approved' ? '100%' :
                         task.status === 'completed' ? '90%' :
                         task.status === 'in_progress' ? '50%' : '0%'
                }}
              ></div>
            </div>
          </div>

          <div className="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div className="flex items-center justify-center mb-2">
              <svg className="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div className="text-2xl font-bold text-gray-900 dark:text-gray-100">{task.hours || 'N/A'}</div>
            <div className="text-sm text-gray-600 dark:text-gray-400">Estimated Hours</div>
          </div>
        </div>

        {/* Task Dates Section */}
        {(task.start_date || task.due_date) && (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {task.start_date && (
              <div>
                <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2 flex items-center">
                  <CalendarIcon className="h-4 w-4 mr-1" />
                  Start Date
                </h3>
                <div className="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                  <p className="text-gray-900 dark:text-gray-100 font-medium">
                    {new Date(task.start_date).toLocaleDateString()}
                  </p>
                </div>
              </div>
            )}

            {task.due_date && (
              <div>
                <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2 flex items-center">
                  <CalendarIcon className="h-4 w-4 mr-1" />
                  Due Date
                </h3>
                <div className="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                  <p className={`font-medium ${
                    new Date(task.due_date) < new Date() && task.status !== 'approved' && task.status !== 'completed'
                      ? 'text-red-600 dark:text-red-400'
                      : 'text-gray-900 dark:text-gray-100'
                  }`}>
                    {new Date(task.due_date).toLocaleDateString()}
                    {new Date(task.due_date) < new Date() && task.status !== 'approved' && task.status !== 'completed' && (
                      <span className="ml-2 text-xs text-red-600 dark:text-red-400">(Overdue)</span>
                    )}
                  </p>
                </div>
              </div>
            )}
          </div>
        )}

        {/* Task Description */}
        {task.description && (
          <div className="mb-6">
            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</h3>
            <p className="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{task.description}</p>
          </div>
        )}

        {/* Project & Assignment Section */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div>
            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2 flex items-center">
              <FolderIcon className="h-4 w-4 mr-1" />
              Project
            </h3>
            <div className="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
              <Link
                to={`/projects/${task.project?.id}`}
                className="text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-400 font-medium"
              >
                {task.project?.name || 'No project assigned'}
              </Link>
              {task.project?.status && (
                <div className="mt-1">
                  <span className="text-xs text-gray-500 dark:text-gray-400">
                    Project Status: {task.project.status.replace('_', ' ').toUpperCase()}
                  </span>
                </div>
              )}
            </div>
          </div>

          <div>
            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2 flex items-center">
              <UserIcon className="h-4 w-4 mr-1" />
              Assigned Developer
            </h3>
            <div className="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
              {task.assigned_user ? (
                <div className="flex items-center space-x-3">
                  <div className="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                    <span className="text-sm font-medium text-blue-600 dark:text-blue-400">
                      {task.assigned_user.name.charAt(0).toUpperCase()}
                    </span>
                  </div>
                  <div className="flex-1">
                    <p className="text-sm font-medium text-gray-900 dark:text-gray-100">{task.assigned_user.name}</p>
                    <p className="text-xs text-gray-500 dark:text-gray-400">{task.assigned_user.email}</p>
                  </div>
                </div>
              ) : (
                <p className="text-sm text-gray-500 dark:text-gray-400">No developer assigned</p>
              )}
            </div>
          </div>
        </div>

        {/* Attachment Section */}
        {task.attachment && (
          <div className="mb-6">
            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2 flex items-center">
              <PaperClipIcon className="h-4 w-4 mr-1" />
              Attachment
            </h3>
            <div className="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
              <a
                href={`${BACKEND_BASE_URL}/storage/${task.attachment}`}
                target="_blank"
                rel="noopener noreferrer"
                className="text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-400 flex items-center"
              >
                <PaperClipIcon className="h-4 w-4 mr-2" />
                View Attachment
              </a>
            </div>
          </div>
        )}

        {/* Actions Section */}
        <div className="border-t border-gray-200 dark:border-gray-700 pt-6">
          <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">Available Actions</h3>
          
          {(canUpdateStatus || canApprove) ? (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {/* Status Update Actions */}
              {canUpdateStatus && task.status !== 'approved' && task.status !== 'rejected' && (
                <div>
                  <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Update Status</h4>
                  <div className="space-y-2">
                    {task.status !== 'pending' && (
                      <button
                        onClick={() => handleStatusUpdate('pending')}
                        className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                      >
                        Mark as Pending
                      </button>
                    )}
                    {task.status !== 'in_progress' && (
                      <button
                        onClick={() => handleStatusUpdate('in_progress')}
                        className="w-full px-4 py-2 border border-blue-300 dark:border-blue-600 rounded-md text-sm font-medium text-blue-700 dark:text-blue-300 bg-white dark:bg-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900"
                      >
                        Mark as In Progress
                      </button>
                    )}
                    {task.status !== 'completed' && (
                      <button
                        onClick={() => handleStatusUpdate('completed')}
                        className="w-full px-4 py-2 border border-green-300 dark:border-green-600 rounded-md text-sm font-medium text-green-700 dark:text-green-300 bg-white dark:bg-gray-700 hover:bg-green-50 dark:hover:bg-green-900"
                      >
                        Mark as Completed
                      </button>
                    )}
                  </div>
                </div>
              )}
              
              {/* Client Approval Actions */}
              {canApprove && (
                <div>
                  <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Client Actions</h4>
                  <div className="space-y-2">
                    <button
                      onClick={handleApprove}
                      className="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700"
                    >
                      <CheckCircleIcon className="h-5 w-5 mr-2" />
                      Approve Task
                    </button>
                    <button
                      onClick={handleReject}
                      className="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700"
                    >
                      <XCircleIcon className="h-5 w-5 mr-2" />
                      Reject Task
                    </button>
                  </div>
                </div>
              )}
            </div>
          ) : (
            <div className="text-center py-8 border border-gray-200 dark:border-gray-600 rounded-lg">
              <ClipboardListIcon className="h-12 w-12 text-gray-400 mx-auto mb-4" />
              <h4 className="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Actions Available</h4>
              <p className="text-gray-500 dark:text-gray-400">You don't have permission to modify this task</p>
            </div>
          )}
        </div>

        {/* Task Timeline */}
        <div className="mt-6 text-sm text-gray-500 dark:text-gray-400">
          <p className="flex items-center">
            <CalendarIcon className="h-4 w-4 mr-1" />
            Created: {new Date(task.created_at).toLocaleDateString()}
          </p>
          <p className="flex items-center mt-1">
            <CalendarIcon className="h-4 w-4 mr-1" />
            Last Updated: {new Date(task.updated_at).toLocaleDateString()}
          </p>
        </div>
      </div>
    </div>
  );
};

export default TaskDetails;