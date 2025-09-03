import React, { useState, useEffect } from 'react';
import { useAuth } from '../contexts/AuthContext';
import { Link } from 'react-router-dom';
import api from '../services/api';
import {
  UsersIcon,
  FolderIcon,
  ClipboardDocumentListIcon,
  CheckCircleIcon,
  PlusIcon,
  ArrowDownTrayIcon,
  ArrowUpIcon,
  ClockIcon,
  EyeIcon,
  ChartBarIcon,
  DocumentTextIcon,
  MinusIcon
} from '@heroicons/react/24/outline';

const Dashboard = () => {
  const { user, isSuperAdmin, isDeveloper } = useAuth();
  const [stats, setStats] = useState({
    totalUsers: 0,
    totalProjects: 0,
    totalTasks: 0,
    completedTasks: 0,
    activeUsers: 0
  });
  const [recentProjects, setRecentProjects] = useState([]);
  const [recentTasks, setRecentTasks] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchDashboardData();
  }, []);

  const fetchDashboardData = async () => {
    try {
      setLoading(true);
      const statsRes = await api.get('/dashboard/stats');

      // Use filtered data from stats endpoint
      const statsData = statsRes.data;
      setStats({
        totalUsers: statsData.totalUsers || 0,
        totalProjects: statsData.totalProjects || 0,
        totalTasks: statsData.totalTasks || 0,
        completedTasks: statsData.completedTasks || 0,
        activeUsers: statsData.activeUsers || 0
      });

      // Use the already filtered recent projects and tasks from stats
      setRecentProjects(Array.isArray(statsData.recentProjects) ? statsData.recentProjects.slice(0, 8) : []);
      setRecentTasks(Array.isArray(statsData.recentTasks) ? statsData.recentTasks.slice(0, 5) : []);
    } catch (error) {
      console.error('Failed to fetch dashboard data:', error);
    } finally {
      setLoading(false);
    }
  };

  const StatCard = ({ title, value, icon: Icon, color, trend, trendText }) => (
    <div className="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
      <div className="p-5">
        <div className="flex items-center justify-between">
          <div>
            <h5 className="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">{title}</h5>
            <h2 className="text-2xl font-bold text-gray-900 dark:text-gray-100">{value}</h2>
          </div>
          <div className={`flex items-center justify-center w-12 h-12 rounded-full ${color} bg-opacity-10`}>
            <Icon className={`h-6 w-6 ${color.replace('bg-', 'text-')}`} />
          </div>
        </div>
        <div className="mt-3 pt-3 border-t border-gray-100 dark:border-gray-600">
          <div className="flex items-center gap-1 text-xs">
            {trend === 'up' && <ArrowUpIcon className="h-3 w-3 text-green-500" />}
            {trend === 'warning' && <ClockIcon className="h-3 w-3 text-yellow-500" />}
            {trend === 'info' && <UsersIcon className="h-3 w-3 text-blue-500" />}
            <span className={`font-medium ${
              trend === 'up' ? 'text-green-500' :
              trend === 'warning' ? 'text-yellow-500' :
              'text-blue-500'
            }`}>
              {trendText}
            </span>
          </div>
        </div>
      </div>
    </div>
  );

  const getPriorityIcon = (priority) => {
    switch(priority) {
      case 'top_urgent':
        return <ArrowUpIcon className="h-4 w-4 text-red-500" />;
      case 'urgent':
        return <MinusIcon className="h-4 w-4 text-yellow-500" />;
      default:
        return <ArrowDownTrayIcon className="h-4 w-4 text-green-500" />;
    }
  };

  const getStatusBadgeClass = (status) => {
    const baseClass = "px-2 py-1 text-xs font-medium rounded-full border";
    switch(status) {
      case 'active':
      case 'approved':
        return `${baseClass} bg-green-50 text-green-700 border-green-200`;
      case 'completed':
        return `${baseClass} bg-blue-50 text-blue-700 border-blue-200`;
      case 'pending':
        return `${baseClass} bg-yellow-50 text-yellow-700 border-yellow-200`;
      case 'in_progress':
        return `${baseClass} bg-cyan-50 text-cyan-700 border-cyan-200`;
      case 'rejected':
        return `${baseClass} bg-red-50 text-red-700 border-red-200`;
      default:
        return `${baseClass} bg-gray-50 text-gray-700 border-gray-200`;
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 dark:border-blue-400"></div>
      </div>
    );
  }

  return (
    <div className="flex flex-col grow items-stretch">
      {/* Toolbar */}
      <div className="pb-5">
        <div className="flex items-center justify-between flex-wrap gap-3">
          <div className="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 className="font-medium text-lg text-gray-900 dark:text-gray-100">Dashboard</h1>
            <div className="flex items-center gap-1 text-sm font-normal">
              <span className="text-gray-600 dark:text-gray-400">Welcome back, {user?.name}</span>
            </div>
          </div>
          <div className="flex items-center flex-wrap gap-1.5 lg:gap-3.5">
            <button className="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
              <ArrowDownTrayIcon className="h-4 w-4 mr-2" />
              Export Report
            </button>
            {isSuperAdmin && (
              <Link 
                to="/projects/create" 
                className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
              >
                <PlusIcon className="h-4 w-4 mr-2" />
                New Project
              </Link>
            )}
          </div>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-7.5 mb-5 lg:mb-10">
        {isSuperAdmin && (
          <StatCard
            title="Total Users"
            value={stats.totalUsers}
            icon={UsersIcon}
            color="bg-blue-500"
            trend="info"
            trendText="All Users"
          />
        )}
        <StatCard
          title="Total Projects"
          value={stats.totalProjects}
          icon={FolderIcon}
          color="bg-green-500"
          trend="up"
          trendText="Active Projects"
        />
        <StatCard
          title="Total Tasks"
          value={stats.totalTasks}
          icon={ClipboardDocumentListIcon}
          color="bg-yellow-500"
          trend="warning"
          trendText="In Progress"
        />
        <StatCard
          title="Completed Tasks"
          value={stats.completedTasks}
          icon={CheckCircleIcon}
          color="bg-purple-500"
          trend="up"
          trendText="This Month"
        />
        {isDeveloper && stats.inProgressTasks !== undefined && (
          <StatCard
            title="In Progress Tasks"
            value={stats.inProgressTasks}
            icon={ClockIcon}
            color="bg-cyan-500"
            trend="warning"
            trendText="Your Tasks"
          />
        )}
      </div>

      {/* Recent Projects and Tasks */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-7.5">
        {/* Recent Projects - Takes 2/3 of the width */}
        <div className="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
          <div className="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
            <h3 className="text-xl font-semibold text-gray-900 dark:text-gray-100">Recent Projects</h3>
            <Link to="/projects" className="text-sm font-medium text-blue-600 hover:text-blue-700">
              View All
            </Link>
          </div>
          <div className="p-0">
            {recentProjects.length > 0 ? (
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead className="bg-gray-50 dark:bg-gray-700">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Project
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Status
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Progress
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Team
                      </th>
                      <th className="relative px-6 py-3">
                        <span className="sr-only">Actions</span>
                      </th>
                    </tr>
                  </thead>
                  <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    {recentProjects.map((project) => (
                      <tr key={project.id}>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="flex items-center">
                            <div className="flex-shrink-0 h-10 w-10">
                              <div className="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                <FolderIcon className="h-5 w-5 text-blue-600 dark:text-blue-400" />
                              </div>
                            </div>
                            <div className="ml-4">
                              <Link to={`/projects/${project.id}`} className="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600">
                                {project.name}
                              </Link>
                              <div className="text-sm text-gray-500 dark:text-gray-400">
                                {project.tasks?.length || 0} tasks • Created {new Date(project.created_at).toLocaleDateString()}
                              </div>
                            </div>
                          </div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <span className={getStatusBadgeClass(project.status)}>
                            {project.status.charAt(0).toUpperCase() + project.status.slice(1).replace('_', ' ')}
                          </span>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="flex items-center">
                            <div className="flex-1 bg-gray-200 dark:bg-gray-600 rounded-full h-2.5 mr-2">
                              <div
                                className="bg-blue-600 dark:bg-blue-400 h-2.5 rounded-full"
                                style={{ width: `${project.progress || 0}%` }}
                              ></div>
                            </div>
                            <span className="text-sm font-medium text-gray-900 dark:text-gray-100">{project.progress || 0}%</span>
                          </div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="flex -space-x-2">
                            {project.users?.slice(0, 3).map((user, index) => (
                              <div
                                key={user.id}
                                className="h-8 w-8 rounded-full bg-gray-400 ring-2 ring-white flex items-center justify-center text-xs font-medium text-white"
                                title={user.name}
                              >
                                {user.name.charAt(0).toUpperCase()}
                              </div>
                            ))}
                            {project.users?.length > 3 && (
                              <div className="h-8 w-8 rounded-full bg-gray-300 ring-2 ring-white flex items-center justify-center text-xs font-medium text-gray-600">
                                +{project.users.length - 3}
                              </div>
                            )}
                          </div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                          <Link
                            to={`/projects/${project.id}`}
                            className="text-gray-400 hover:text-gray-500"
                          >
                            <EyeIcon className="h-5 w-5" />
                          </Link>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            ) : (
              <div className="text-center py-12">
                <FolderIcon className="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" />
                <h4 className="mt-2 text-lg font-semibold text-gray-900 dark:text-gray-100">No Projects Yet</h4>
                <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">Create your first project to get started</p>
                {isSuperAdmin && (
                  <Link
                    to="/projects/create"
                    className="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                  >
                    <PlusIcon className="h-4 w-4 mr-2" />
                    Create Project
                  </Link>
                )}
              </div>
            )}
          </div>
        </div>

        {/* Recent Tasks - Takes 1/3 of the width */}
        <div className="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
          <div className="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Tasks</h3>
            <Link to="/tasks" className="text-sm font-medium text-blue-600 hover:text-blue-700">
              View All
            </Link>
          </div>
          <div className="p-0">
            {recentTasks.length > 0 ? (
              <div className="divide-y divide-gray-200 dark:divide-gray-700">
                {recentTasks.map((task) => (
                  <div key={task.id} className="p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-3">
                        <div className="flex items-center gap-1.5">
                          {getPriorityIcon(task.priority)}
                        </div>
                        <div className="flex flex-col gap-0.5">
                          <Link
                            to={`/tasks/${task.id}`}
                            className="text-sm font-semibold text-gray-900 dark:text-gray-100 hover:text-blue-600"
                          >
                            {task.title.length > 25 ? task.title.substring(0, 25) + '...' : task.title}
                          </Link>
                          <div className="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                            {task.project && (
                              <>
                                <span>{task.project.name.length > 15 ? task.project.name.substring(0, 15) + '...' : task.project.name}</span>
                                <span>•</span>
                              </>
                            )}
                            <span>Created {new Date(task.created_at).toLocaleDateString()}</span>
                          </div>
                        </div>
                      </div>
                      <div className="flex flex-col items-end gap-2">
                        <span className={`${getStatusBadgeClass(task.status)} text-xs`}>
                          {task.status.replace('_', ' ').charAt(0).toUpperCase() + task.status.replace('_', ' ').slice(1)}
                        </span>
                        {task.assigned_user && (
                          <div className="flex -space-x-1">
                            <div
                              className="h-5 w-5 rounded-full bg-gray-400 ring-1 ring-white flex items-center justify-center text-xs font-medium text-white"
                              title={task.assigned_user.name}
                            >
                              {task.assigned_user.name.charAt(0).toUpperCase()}
                            </div>
                          </div>
                        )}
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <div className="text-center py-10">
                <ClipboardDocumentListIcon className="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500" />
                <h4 className="mt-2 text-base font-semibold text-gray-900 dark:text-gray-100">No Tasks Yet</h4>
                <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">Create your first task to get started</p>
                {(isSuperAdmin || isDeveloper) && (
                  <Link
                    to="/tasks/create"
                    className="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                  >
                    <PlusIcon className="h-4 w-4 mr-2" />
                    Create Task
                  </Link>
                )}
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Project Progress Overview */}
      {recentProjects.length > 0 && (
        <div className="mt-5 lg:mt-7.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
          <div className="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100">Project Progress Overview</h3>
            <Link to="/projects" className="text-sm font-medium text-blue-600 hover:text-blue-700">
              View All Projects
            </Link>
          </div>
          <div className="p-5">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
              {recentProjects.slice(0, 8).map((project) => (
                <div
                  key={project.id}
                  className="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                >
                  <div className="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100">
                    <FolderIcon className="h-5 w-5 text-blue-600" />
                  </div>
                  <div className="flex-1">
                    <Link
                      to={`/projects/${project.id}`}
                      className="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600 block"
                    >
                      {project.name.length > 20 ? project.name.substring(0, 20) + '...' : project.name}
                    </Link>
                    <div className="flex items-center gap-2 mt-1">
                      <div className="flex-1 bg-gray-200 dark:bg-gray-600 rounded-full h-1.5">
                        <div
                          className="bg-blue-600 dark:bg-blue-400 h-1.5 rounded-full"
                          style={{ width: `${project.progress || 0}%` }}
                        ></div>
                      </div>
                      <span className="text-xs font-medium text-gray-900 dark:text-gray-100">{project.progress || 0}%</span>
                    </div>
                    <div className="flex items-center gap-2 mt-1 text-xs text-gray-500 dark:text-gray-400">
                      <span>{project.tasks?.length || 0} tasks</span>
                      <span>•</span>
                      <span>{project.tasks?.filter(t => t.status === 'approved').length || 0} completed</span>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {/* Quick Actions */}
      <div className="mt-5 lg:mt-7.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <div className="p-5 border-b border-gray-200 dark:border-gray-700">
          <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100">Quick Actions</h3>
        </div>
        <div className="p-5">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {isSuperAdmin && (
              <>
                <Link
                  to="/projects/create"
                  className="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                >
                  <div className="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900">
                    <FolderIcon className="h-5 w-5 text-blue-600 dark:text-blue-400" />
                  </div>
                  <div>
                    <span className="text-sm font-medium text-gray-900 dark:text-gray-100 block">New Project</span>
                    <span className="text-xs text-gray-500 dark:text-gray-400">Create a new project</span>
                  </div>
                </Link>
              </>
            )}

            {(isSuperAdmin || isDeveloper) && (
              <Link
                to="/tasks/create"
                className="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
              >
                <div className="flex items-center justify-center w-10 h-10 rounded-full bg-cyan-100 dark:bg-cyan-900">
                  <ClipboardDocumentListIcon className="h-5 w-5 text-cyan-600 dark:text-cyan-400" />
                </div>
                <div>
                  <span className="text-sm font-medium text-gray-900 dark:text-gray-100 block">New Task</span>
                  <span className="text-xs text-gray-500 dark:text-gray-400">Create a new task</span>
                </div>
              </Link>
            )}

            <Link
              to="/projects"
              className="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
            >
              <div className="flex items-center justify-center w-10 h-10 rounded-full bg-green-100 dark:bg-green-900">
                <ChartBarIcon className="h-5 w-5 text-green-600 dark:text-green-400" />
              </div>
              <div>
                <span className="text-sm font-medium text-gray-900 dark:text-gray-100 block">View Projects</span>
                <span className="text-xs text-gray-500 dark:text-gray-400">Browse all projects</span>
              </div>
            </Link>

            <Link
              to="/tasks"
              className="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
            >
              <div className="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900">
                <DocumentTextIcon className="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
              </div>
              <div>
                <span className="text-sm font-medium text-gray-900 dark:text-gray-100 block">View Tasks</span>
                <span className="text-xs text-gray-500 dark:text-gray-400">Browse all tasks</span>
              </div>
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;