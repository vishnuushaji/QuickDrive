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
  UserIcon
} from '@heroicons/react/24/outline';

const TaskDetails = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const { user, isSuperAdmin, isClient, isDeveloper } = useAuth();
  const [task, setTask] = useState(null);
  const [loading, setLoading] = useState(true);

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
      fetchTask();
    } catch (error) {
      toast.error('Failed to update status');
    }
  };

  const handleApprove = async () => {
    try {
      await taskService.approve(id);
      toast.success('Task approved successfully');
      fetchTask();
    } catch (error) {
      toast.error('Failed to approve task');
    }
  };

  const handleReject = async () => {
    try {
      await taskService.reject(id);
      toast.success('Task rejected');
      fetchTask();
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

  if (loading) {
    return <div className="text-center py-4 text-gray-600 dark:text-gray-400">Loading...</div>;
  }

  if (!task) {
    return <div className="text-center py-4 text-gray-600 dark:text-gray-400">Task not found</div>;
  }

  const canEdit = isSuperAdmin || (isDeveloper && task.assigned_user_id === user?.id);
  const canDelete = isSuperAdmin;
  const canApprove = isClient && task.status === 'completed';
  const canUpdateStatus = isDeveloper && task.assigned_user_id === user?.id;

  return (
    <div className="max-w-4xl mx-auto space-y-6">
      <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div className="flex justify-between items-start mb-6">
          <div>
            <h1 className="text-2xl font-semibold text-gray-900 dark:text-gray-100">{task.title}</h1>
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

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div>
            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Project</h3>
            <Link
              to={`/projects/${task.project?.id}`}
              className="text-indigo-600 hover:text-indigo-900"
            >
              {task.project?.name}
            </Link>
          </div>
          <div>
            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2 flex items-center">
              <UserIcon className="h-4 w-4 mr-1" />
              Assigned To
            </h3>
            <p className="text-gray-900 dark:text-gray-100">
              {task.assigned_user?.name || 'Unassigned'}
            </p>
          </div>
        </div>

        {task.description && (
          <div className="mb-6">
            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</h3>
            <p className="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{task.description}</p>
          </div>
        )}

        {task.attachment && (
          <div className="mb-6">
            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2 flex items-center">
              <PaperClipIcon className="h-4 w-4 mr-1" />
              Attachment
            </h3>
            <a
              href={`/storage/${task.attachment}`}
              target="_blank"
              rel="noopener noreferrer"
              className="text-indigo-600 hover:text-indigo-900"
            >
              View Attachment
            </a>
          </div>
        )}

        <div className="border-t border-gray-200 dark:border-gray-700 pt-6">
          <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">Actions</h3>
          <div className="flex flex-wrap gap-3">
            {canUpdateStatus && task.status !== 'approved' && task.status !== 'rejected' && (
              <>
                {task.status !== 'pending' && (
                  <button
                    onClick={() => handleStatusUpdate('pending')}
                    className="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                  >
                    Mark as Pending
                  </button>
                )}
                {task.status !== 'in_progress' && (
                  <button
                    onClick={() => handleStatusUpdate('in_progress')}
                    className="px-4 py-2 border border-blue-300 dark:border-blue-600 rounded-md text-sm font-medium text-blue-700 dark:text-blue-300 bg-white dark:bg-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900"
                  >
                    Mark as In Progress
                  </button>
                )}
                {task.status !== 'completed' && (
                  <button
                    onClick={() => handleStatusUpdate('completed')}
                    className="px-4 py-2 border border-green-300 dark:border-green-600 rounded-md text-sm font-medium text-green-700 dark:text-green-300 bg-white dark:bg-gray-700 hover:bg-green-50 dark:hover:bg-green-900"
                  >
                    Mark as Completed
                  </button>
                )}
              </>
            )}
            
            {canApprove && (
              <>
                <button
                  onClick={handleApprove}
                  className="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700"
                >
                  <CheckCircleIcon className="h-5 w-5 mr-2" />
                  Approve
                </button>
                <button
                  onClick={handleReject}
                  className="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700"
                >
                  <XCircleIcon className="h-5 w-5 mr-2" />
                  Reject
                </button>
              </>
            )}
          </div>
        </div>

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