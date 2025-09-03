import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { taskService } from '../services/taskService';
import toast from 'react-hot-toast';
import { 
  PlusIcon, 
  PencilIcon, 
  TrashIcon, 
  EyeIcon,
  EllipsisVerticalIcon,
  Squares2X2Icon,
  ListBulletIcon,
  CalendarDaysIcon,
  UserIcon,
  ClipboardDocumentListIcon as ClipboardListIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon,
  ClockIcon
} from '@heroicons/react/24/outline';

const Tasks = () => {
  const { isSuperAdmin, isDeveloper, user } = useAuth();
  const [tasks, setTasks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState('all');
  const [viewMode, setViewMode] = useState('grid');
  const [openMenuId, setOpenMenuId] = useState(null);

  useEffect(() => {
    fetchTasks();
  }, []);

  const fetchTasks = async () => {
    try {
      const response = await taskService.getAll();
      setTasks(response.data);
    } catch (error) {
      toast.error('Failed to fetch tasks');
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id) => {
    if (window.confirm('Are you sure you want to delete this task?')) {
      try {
        await taskService.delete(id);
        toast.success('Task deleted successfully');
        fetchTasks();
      } catch (error) {
        toast.error('Failed to delete task');
      }
    }
  };

  const handleStatusUpdate = async (id, newStatus) => {
    try {
      await taskService.updateStatus(id, newStatus);
      toast.success('Status updated successfully');
      fetchTasks();
    } catch (error) {
      toast.error('Failed to update status');
    }
  };

  const getTaskIcon = (task) => {
    const icons = ['📝', '⚡', '🎯', '💻', '🔧', '📊'];
    return icons[task.id % icons.length];
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

  const getStatusSection = (status) => {
    const sections = {
      pending: 'Pending Tasks',
      in_progress: 'In Progress',
      completed: 'Completed Tasks',
      approved: 'Approved',
      rejected: 'Rejected'
    };
    return sections[status] || 'Other Tasks';
  };

  const groupTasksByStatus = () => {
    const grouped = {
      'Pending Tasks': [],
      'In Progress': [],
      'Completed Tasks': [],
      'Approved': [],
      'Rejected': []
    };
    
    tasks.forEach(task => {
      const section = getStatusSection(task.status);
      if (grouped[section]) {
        grouped[section].push(task);
      }
    });
    
    return Object.entries(grouped).filter(([_, tasks]) => tasks.length > 0);
  };

  const filteredTasks = tasks.filter(task => {
    if (filter === 'all') return true;
    return task.status === filter;
  });

  const groupedTasks = groupTasksByStatus();
  const displayTasks = filter === 'all' ? groupedTasks : [['Filtered Tasks', filteredTasks]];

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
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-semibold text-gray-900 dark:text-gray-100">
          {tasks.length} Tasks
        </h1>
        <div className="flex items-center gap-3">
          {(isSuperAdmin || isDeveloper) && (
            <Link
              to="/tasks/create"
              className="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
            >
              <PlusIcon className="h-4 w-4" />
              New Task
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

      {/* Filter buttons */}
      <div className="flex flex-wrap gap-2">
        <button
          onClick={() => setFilter('all')}
          className={`px-4 py-2 rounded-md text-sm font-medium ${
            filter === 'all'
              ? 'bg-blue-600 text-white'
              : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'
          }`}
        >
          All
        </button>
        <button
          onClick={() => setFilter('pending')}
          className={`px-4 py-2 rounded-md text-sm font-medium ${
            filter === 'pending'
              ? 'bg-blue-600 text-white'
              : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'
          }`}
        >
          Pending
        </button>
        <button
          onClick={() => setFilter('in_progress')}
          className={`px-4 py-2 rounded-md text-sm font-medium ${
            filter === 'in_progress'
              ? 'bg-blue-600 text-white'
              : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'
          }`}
        >
          In Progress
        </button>
        <button
          onClick={() => setFilter('completed')}
          className={`px-4 py-2 rounded-md text-sm font-medium ${
            filter === 'completed'
              ? 'bg-blue-600 text-white'
              : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'
          }`}
        >
          Completed
        </button>
        <button
          onClick={() => setFilter('approved')}
          className={`px-4 py-2 rounded-md text-sm font-medium ${
            filter === 'approved'
              ? 'bg-blue-600 text-white'
              : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'
          }`}
        >
          Approved
        </button>
      </div>

      {/* Tasks by Status */}
      <div className="space-y-8">
        {displayTasks.map(([status, statusTasks]) => (
          <div key={status}>
            {/* Status Header */}
            <div className="flex items-center justify-between mb-4">
              <h2 className={`text-sm font-medium ${
                status === 'In Progress' ? 'text-blue-600 dark:text-blue-400' :
                status === 'Completed Tasks' ? 'text-yellow-600 dark:text-yellow-400' :
                status === 'Approved' ? 'text-green-600 dark:text-green-400' :
                status === 'Rejected' ? 'text-red-600 dark:text-red-400' :
                'text-gray-600 dark:text-gray-400'
              }`}>
                {status}
              </h2>
              <button className="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                <EllipsisVerticalIcon className="h-5 w-5 text-gray-400 dark:text-gray-500" />
              </button>
            </div>

            {/* Task Cards Grid/List */}
            {viewMode === 'grid' ? (
              // Grid View
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {statusTasks.map((task) => (
                  <div key={task.id} className="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                    {/* Task Header */}
                    <div className="flex items-start justify-between mb-4">
                      <div className="flex items-center gap-3">
                        <div className="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-xl">
                          {getTaskIcon(task)}
                        </div>
                        <div>
                          <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {task.title}
                          </h3>
                          <div className="flex items-center gap-2 mt-1">
                            <span className={`px-2 py-1 text-xs rounded-full ${getStatusColor(task.status)}`}>
                              {task.status?.replace('_', ' ').toUpperCase()}
                            </span>
                            {task.priority && task.priority !== 'normal' && (
                              <span className={`px-2 py-1 text-xs rounded-full ${getPriorityColor(task.priority)}`}>
                                {task.priority.replace('_', ' ').toUpperCase()}
                              </span>
                            )}
                          </div>
                        </div>
                      </div>
                      <div className="relative">
                        <button
                          onClick={() => setOpenMenuId(openMenuId === task.id ? null : task.id)}
                          className="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                        >
                          <EllipsisVerticalIcon className="h-5 w-5 text-gray-400 dark:text-gray-500" />
                        </button>
                        
                        {openMenuId === task.id && (
                          <div className="absolute right-0 top-full mt-1 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <Link
                              to={`/tasks/${task.id}`}
                              className="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                              onClick={() => setOpenMenuId(null)}
                            >
                              View Details
                            </Link>
                            {isSuperAdmin && (
                              <>
                                <Link
                                  to={`/tasks/${task.id}/edit`}
                                  className="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                  onClick={() => setOpenMenuId(null)}
                                >
                                  Edit Task
                                </Link>
                                <button
                                  onClick={() => {
                                    handleDelete(task.id);
                                    setOpenMenuId(null);
                                  }}
                                  className="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20"
                                >
                                  Delete Task
                                </button>
                              </>
                            )}
                          </div>
                        )}
                      </div>
                    </div>

                    {/* Task Description */}
                    {task.description && (
                      <p className="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                        {task.description}
                      </p>
                    )}

                    {/* Task Info */}
                    <div className="space-y-2 text-sm mb-4">
                      <div className="flex items-center gap-2">
                        <ClipboardListIcon className="h-4 w-4 text-gray-400" />
                        <span className="text-gray-600 dark:text-gray-400">
                          Project: <Link to={`/projects/${task.project?.id}`} className="text-blue-600 hover:text-blue-800">{task.project?.name}</Link>
                        </span>
                      </div>
                      {task.assigned_user && (
                        <div className="flex items-center gap-2">
                          <UserIcon className="h-4 w-4 text-gray-400" />
                          <span className="text-gray-600 dark:text-gray-400">
                            Assigned to: {task.assigned_user.name}
                          </span>
                        </div>
                      )}
                      <div className="flex items-center gap-2">
                        <CalendarDaysIcon className="h-4 w-4 text-gray-400" />
                        <span className="text-gray-600 dark:text-gray-400">
                          Created: {new Date(task.created_at).toLocaleDateString()}
                        </span>
                      </div>
                    </div>

                    {/* Status Update for Developer */}
                    {isDeveloper && task.assigned_user_id === user?.id && task.status !== 'approved' && task.status !== 'rejected' && (
                      <div className="mb-4">
                        <select
                          value={task.status}
                          onChange={(e) => handleStatusUpdate(task.id, e.target.value)}
                          className="w-full text-sm border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                        >
                          <option value="pending">Pending</option>
                          <option value="in_progress">In Progress</option>
                          <option value="completed">Completed</option>
                        </select>
                      </div>
                    )}
                  </div>
                ))}
              </div>
            ) : (
              // List View
              <div className="space-y-3">
                {statusTasks.map((task) => (
                  <div key={task.id} className="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div className="flex items-center justify-between">
                      <div className="flex items-center space-x-4 flex-1">
                        {/* Task Icon & Name */}
                        <div className="flex items-center space-x-3">
                          <div className="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-lg">
                            {getTaskIcon(task)}
                          </div>
                          <div>
                            <Link
                              to={`/tasks/${task.id}`}
                              className="text-base font-semibold text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400"
                            >
                              {task.title}
                            </Link>
                            {task.description && (
                              <p className="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">
                                {task.description}
                              </p>
                            )}
                          </div>
                        </div>

                        {/* Status & Priority Badges */}
                        <div className="flex items-center space-x-2">
                          <span className={`px-3 py-1 text-sm rounded-full ${getStatusColor(task.status)}`}>
                            {task.status?.replace('_', ' ').toUpperCase()}
                          </span>
                          {task.priority && task.priority !== 'normal' && (
                            <span className={`px-2 py-1 text-xs rounded-full ${getPriorityColor(task.priority)}`}>
                              {task.priority.replace('_', ' ').toUpperCase()}
                            </span>
                          )}
                        </div>

                        {/* Task Info */}
                        <div className="hidden md:flex items-center space-x-6 text-sm text-gray-600 dark:text-gray-400">
                          <div className="flex items-center space-x-1">
                            <ClipboardListIcon className="h-4 w-4" />
                            <Link to={`/projects/${task.project?.id}`} className="hover:text-blue-600">
                              {task.project?.name}
                            </Link>
                          </div>
                          {task.assigned_user && (
                            <div className="flex items-center space-x-1">
                              <UserIcon className="h-4 w-4" />
                              <span>{task.assigned_user.name}</span>
                            </div>
                          )}
                          <div className="flex items-center space-x-1">
                            <CalendarDaysIcon className="h-4 w-4" />
                            <span>{new Date(task.created_at).toLocaleDateString()}</span>
                          </div>
                        </div>

                        {/* Status Update for Developer */}
                        {isDeveloper && task.assigned_user_id === user?.id && task.status !== 'approved' && task.status !== 'rejected' && (
                          <div className="hidden lg:block min-w-[140px]">
                            <select
                              value={task.status}
                              onChange={(e) => handleStatusUpdate(task.id, e.target.value)}
                              className="w-full text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                            >
                              <option value="pending">Pending</option>
                              <option value="in_progress">In Progress</option>
                              <option value="completed">Completed</option>
                            </select>
                          </div>
                        )}
                      </div>

                      {/* Actions Menu */}
                      <div className="relative ml-4">
                        <button
                          onClick={() => setOpenMenuId(openMenuId === task.id ? null : task.id)}
                          className="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full"
                        >
                          <EllipsisVerticalIcon className="h-5 w-5 text-gray-400 dark:text-gray-500" />
                        </button>
                        
                        {openMenuId === task.id && (
                          <div className="absolute right-0 top-full mt-1 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <Link
                              to={`/tasks/${task.id}`}
                              className="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                              onClick={() => setOpenMenuId(null)}
                            >
                              View Details
                            </Link>
                            {isSuperAdmin && (
                              <>
                                <Link
                                  to={`/tasks/${task.id}/edit`}
                                  className="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                  onClick={() => setOpenMenuId(null)}
                                >
                                  Edit Task
                                </Link>
                                <button
                                  onClick={() => {
                                    handleDelete(task.id);
                                    setOpenMenuId(null);
                                  }}
                                  className="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20"
                                >
                                  Delete Task
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
                <div className="h-1 bg-blue-600 rounded-full" style={{ width: '60%' }}></div>
              </div>
            )}
            {status === 'Completed Tasks' && (
              <div className="mt-6">
                <div className="h-1 bg-yellow-500 rounded-full" style={{ width: '80%' }}></div>
              </div>
            )}
            {status === 'Approved' && (
              <div className="mt-6">
                <div className="h-1 bg-green-500 rounded-full" style={{ width: '100%' }}></div>
              </div>
            )}
          </div>
        ))}
      </div>

      {/* Empty State */}
      {tasks.length === 0 && (
        <div className="text-center py-12">
          <div className="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
            <ClipboardListIcon className="h-8 w-8 text-gray-400 dark:text-gray-500" />
          </div>
          <h3 className="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No tasks yet</h3>
          <p className="text-gray-500 dark:text-gray-400 mb-6">Get started by creating your first task</p>
          {(isSuperAdmin || isDeveloper) && (
            <Link
              to="/tasks/create"
              className="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
            >
              <PlusIcon className="h-4 w-4" />
              Create Task
            </Link>
          )}
        </div>
      )}
    </div>
  );
};

export default Tasks;