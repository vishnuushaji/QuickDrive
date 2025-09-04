import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { taskService } from '../services/taskService';
import { projectService } from '../services/projectService';
import { userService } from '../services/userService';
import toast from 'react-hot-toast';

const CreateTask = () => {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [projects, setProjects] = useState([]);
  const [users, setUsers] = useState([]);

  const [errors, setErrors] = useState({});
  const [touched, setTouched] = useState({});

  const [formData, setFormData] = useState({
    title: '',
    description: '',
    project_id: '',
    assigned_user_id: '',
    priority: 'normal',
    status: 'pending',
    start_date: '',
    due_date: '',
    hours: '',
    attachment: null,
  });

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      const [projectsRes, usersRes] = await Promise.all([
        projectService.getAllWithoutPagination(),
        userService.getAllWithoutPagination(),
      ]);
      setProjects(projectsRes.data.data || []);
      setUsers((usersRes.data.data || []).filter((u) => u.role === 'developer'));
    } catch (error) {
      toast.error('Failed to fetch data');
    }
  };

  const maxTitle = 150;
  const maxDescription = 2000;
  const maxFileSize = 10 * 1024 * 1024; // 10MB
  const allowedExt = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'png', 'jpg', 'jpeg'];

  const validateForm = () => {
    const newErrors = {};

    // Title
    if (!formData.title.trim()) {
      newErrors.title = 'Title is required';
    } else if (formData.title.trim().length < 3) {
      newErrors.title = 'Title must be at least 3 characters';
    } else if (formData.title.trim().length > maxTitle) {
      newErrors.title = `Title must not exceed ${maxTitle} characters`;
    }

    // Description (optional)
    if (formData.description.trim().length > maxDescription) {
      newErrors.description = `Description must not exceed ${maxDescription} characters`;
    }

    // Project
    if (!formData.project_id) {
      newErrors.project_id = 'Project is required';
    }

    // Priority
    if (!['normal', 'urgent', 'top_urgent'].includes(formData.priority)) {
      newErrors.priority = 'Invalid priority selected';
    }

    // Status
    if (!['pending', 'in_progress', 'completed'].includes(formData.status)) {
      newErrors.status = 'Invalid status selected';
    }

    // Start Date (optional)
    if (formData.start_date && !/^\d{4}-\d{2}-\d{2}$/.test(formData.start_date)) {
      newErrors.start_date = 'Start date must be in YYYY-MM-DD format';
    }

    // Due Date (optional)
    if (formData.due_date && !/^\d{4}-\d{2}-\d{2}$/.test(formData.due_date)) {
      newErrors.due_date = 'Due date must be in YYYY-MM-DD format';
    }

    // Due Date after Start Date
    if (formData.start_date && formData.due_date && formData.start_date > formData.due_date) {
      newErrors.due_date = 'Due date must be after or equal to start date';
    }

    // Hours (optional)
    if (formData.hours && (isNaN(formData.hours) || parseInt(formData.hours) < 1)) {
      newErrors.hours = 'Hours must be a positive number';
    }

    // Attachment (optional)
    if (formData.attachment) {
      const file = formData.attachment;
      const ext = file.name.split('.').pop()?.toLowerCase();
      if (!allowedExt.includes(ext)) {
        newErrors.attachment = 'Unsupported file type';
      } else if (file.size > maxFileSize) {
        newErrors.attachment = 'File size exceeds 10MB';
      }
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const validateField = (name, value) => {
    const fieldErrors = { ...errors };

    switch (name) {
      case 'title': {
        const v = value.trim();
        if (!v) fieldErrors.title = 'Title is required';
        else if (v.length < 3) fieldErrors.title = 'Title must be at least 3 characters';
        else if (v.length > maxTitle) fieldErrors.title = `Title must not exceed ${maxTitle} characters`;
        else delete fieldErrors.title;
        break;
      }

      case 'description': {
        const v = value.trim();
        if (v.length > maxDescription) fieldErrors.description = `Description must not exceed ${maxDescription} characters`;
        else delete fieldErrors.description;
        break;
      }

      case 'project_id':
        if (!value) fieldErrors.project_id = 'Project is required';
        else delete fieldErrors.project_id;
        break;

      case 'priority':
        if (!['normal', 'urgent', 'top_urgent'].includes(value)) fieldErrors.priority = 'Invalid priority selected';
        else delete fieldErrors.priority;
        break;

      case 'status':
        if (!['pending', 'in_progress', 'completed'].includes(value)) fieldErrors.status = 'Invalid status selected';
        else delete fieldErrors.status;
        break;

      case 'start_date': {
        const v = value.trim();
        if (v && !/^\d{4}-\d{2}-\d{2}$/.test(v)) fieldErrors.start_date = 'Start date must be in YYYY-MM-DD format';
        else delete fieldErrors.start_date;
        break;
      }

      case 'due_date': {
        const v = value.trim();
        if (v && !/^\d{4}-\d{2}-\d{2}$/.test(v)) fieldErrors.due_date = 'Due date must be in YYYY-MM-DD format';
        else if (v && formData.start_date && v < formData.start_date) fieldErrors.due_date = 'Due date must be after or equal to start date';
        else delete fieldErrors.due_date;
        break;
      }

      case 'hours': {
        const v = value.trim();
        if (v && (isNaN(v) || parseInt(v) < 1)) fieldErrors.hours = 'Hours must be a positive number';
        else delete fieldErrors.hours;
        break;
      }

      case 'attachment': {
        const file = value;
        if (file) {
          const ext = file.name.split('.').pop()?.toLowerCase();
          if (!allowedExt.includes(ext)) fieldErrors.attachment = 'Unsupported file type';
          else if (file.size > maxFileSize) fieldErrors.attachment = 'File size exceeds 10MB';
          else delete fieldErrors.attachment;
        } else {
          delete fieldErrors.attachment;
        }
        break;
      }

      default:
        break;
    }

    setErrors(fieldErrors);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    setTouched({
      title: true,
      description: true,
      project_id: true,
      assigned_user_id: true,
      priority: true,
      status: true,
      start_date: true,
      due_date: true,
      hours: true,
      attachment: true,
    });

    if (!validateForm()) {
      toast.error('Please fix the errors before submitting');
      return;
    }

    setLoading(true);
    try {
      const submitData = new FormData();
      Object.entries(formData).forEach(([key, val]) => {
        if (val !== null && val !== '') {
          submitData.append(key, val);
        }
      });

      await taskService.create(submitData);
      toast.success('Task created successfully');
      navigate('/tasks');
    } catch (error) {
      toast.error(error.response?.data?.message || 'Failed to create task');
    } finally {
      setLoading(false);
    }
  };

  const handleChange = (e) => {
    const { name, value, files } = e.target;

    if (name === 'attachment') {
      const file = files?.[0] || null;
      setFormData((prev) => ({ ...prev, attachment: file }));
      if (touched.attachment) validateField('attachment', file);
      return;
    }

    setFormData((prev) => ({ ...prev, [name]: value }));

    if (touched[name]) {
      validateField(name, value);
    }
  };

  const handleBlur = (e) => {
    const { name, value } = e.target;
    setTouched((prev) => ({ ...prev, [name]: true }));
    if (name === 'attachment') {
      validateField(name, formData.attachment);
    } else {
      validateField(name, value);
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
          {/* Header */}
          <div className="bg-gradient-to-r from-indigo-600 to-blue-800 px-6 py-8">
            <h1 className="text-3xl font-bold text-white mb-2">Create New Task</h1>
            <p className="text-indigo-100">Define task details, assign a developer, and set priority</p>
          </div>

          {/* Form */}
          <form onSubmit={handleSubmit} className="p-8 space-y-8">
            {/* Title */}
            <div className="space-y-2">
              <label className="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                Task Title *
              </label>
              <input
                type="text"
                name="title"
                value={formData.title}
                onChange={handleChange}
                onBlur={handleBlur}
                className={`w-full px-4 py-3 rounded-lg border-2 transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 ${
                  errors.title && touched.title
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-200'
                    : 'border-gray-200 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-200'
                }`}
                placeholder="e.g., Implement user authentication"
              />
              {errors.title && touched.title && (
                <p className="text-red-600 text-sm flex items-center mt-1">
                  <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                  </svg>
                  {errors.title}
                </p>
              )}
              <p className="text-xs text-gray-500 dark:text-gray-400">{formData.title.length}/{maxTitle} characters</p>
            </div>

            {/* Description */}
            <div className="space-y-2">
              <label className="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                Description
              </label>
              <textarea
                name="description"
                value={formData.description}
                onChange={handleChange}
                onBlur={handleBlur}
                rows={4}
                className={`w-full px-4 py-3 rounded-lg border-2 transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 resize-vertical ${
                  errors.description && touched.description
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-200'
                    : 'border-gray-200 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-200'
                }`}
                placeholder="Add more context, acceptance criteria, or dependencies..."
              />
              {errors.description && touched.description && (
                <p className="text-red-600 text-sm flex items-center mt-1">
                  <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                  </svg>
                  {errors.description}
                </p>
              )}
              <p className="text-xs text-gray-500 dark:text-gray-400">{formData.description.length}/{maxDescription} characters</p>
            </div>

            {/* Assignment & Settings */}
            <div className="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 space-y-6">
              <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                <svg className="w-5 h-5 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 2a1 1 0 00-1 1v1H6.5A1.5 1.5 0 005 5.5V7H4a2 2 0 00-2 2v1h16V9a2 2 0 00-2-2h-1V5.5A1.5 1.5 0 0013.5 4H12V3a1 1 0 00-1-1H9zM2 12v3a2 2 0 002 2h3v-5H2zm7 0v5h2v-5H9zm5 0v5h3a2 2 0 002-2v-3h-5z" />
                </svg>
                Assignment & Settings
              </h3>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {/* Project */}
                <div className="space-y-2">
                  <label className="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Project *
                  </label>
                  <select
                    name="project_id"
                    value={formData.project_id}
                    onChange={handleChange}
                    onBlur={handleBlur}
                    className={`w-full px-4 py-3 rounded-lg border-2 transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 ${
                      errors.project_id && touched.project_id
                        ? 'border-red-300 focus:border-red-500 focus:ring-red-200'
                        : 'border-gray-200 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-200'
                    }`}
                  >
                    <option value="">Select a project</option>
                    {projects.map((project) => (
                      <option key={project.id} value={project.id}>
                        {project.name}
                      </option>
                    ))}
                  </select>
                  {errors.project_id && touched.project_id && (
                    <p className="text-red-600 text-sm flex items-center mt-1">
                      <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                      </svg>
                      {errors.project_id}
                    </p>
                  )}
                </div>

                {/* Assign To */}
                <div className="space-y-2">
                  <label className="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Assign To
                  </label>
                  <select
                    name="assigned_user_id"
                    value={formData.assigned_user_id}
                    onChange={handleChange}
                    onBlur={handleBlur}
                    className="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-gray-600 transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:border-indigo-500 focus:ring-indigo-200"
                  >
                    <option value="">Select a developer</option>
                    {users.map((user) => (
                      <option key={user.id} value={user.id}>
                        {user.name}
                      </option>
                    ))}
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {/* Priority */}
                <div className="space-y-2">
                  <label className="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Priority
                  </label>
                  <select
                    name="priority"
                    value={formData.priority}
                    onChange={handleChange}
                    onBlur={handleBlur}
                    className={`w-full px-4 py-3 rounded-lg border-2 transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 ${
                      errors.priority && touched.priority
                        ? 'border-red-300 focus:border-red-500 focus:ring-red-200'
                        : 'border-gray-200 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-200'
                    }`}
                  >
                    <option value="normal">Normal</option>
                    <option value="urgent">Urgent</option>
                    <option value="top_urgent">Top Urgent</option>
                  </select>
                  {errors.priority && touched.priority && (
                    <p className="text-red-600 text-sm flex items-center mt-1">
                      <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                      </svg>
                      {errors.priority}
                    </p>
                  )}
                </div>

                {/* Status */}
                <div className="space-y-2">
                  <label className="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Status
                  </label>
                  <select
                    name="status"
                    value={formData.status}
                    onChange={handleChange}
                    onBlur={handleBlur}
                    className={`w-full px-4 py-3 rounded-lg border-2 transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 ${
                      errors.status && touched.status
                        ? 'border-red-300 focus:border-red-500 focus:ring-red-200'
                        : 'border-gray-200 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-200'
                    }`}
                  >
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                  </select>
                  {errors.status && touched.status && (
                    <p className="text-red-600 text-sm flex items-center mt-1">
                      <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                      </svg>
                      {errors.status}
                    </p>
                  )}
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {/* Start Date */}
                <div className="space-y-2">
                  <label className="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Start Date
                  </label>
                  <input
                    type="date"
                    name="start_date"
                    value={formData.start_date}
                    onChange={handleChange}
                    onBlur={handleBlur}
                    className={`w-full px-4 py-3 rounded-lg border-2 transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 ${
                      errors.start_date && touched.start_date
                        ? 'border-red-300 focus:border-red-500 focus:ring-red-200'
                        : 'border-gray-200 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-200'
                    }`}
                  />
                  {errors.start_date && touched.start_date && (
                    <p className="text-red-600 text-sm flex items-center mt-1">
                      <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                      </svg>
                      {errors.start_date}
                    </p>
                  )}
                </div>

                {/* Due Date */}
                <div className="space-y-2">
                  <label className="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Due Date
                  </label>
                  <input
                    type="date"
                    name="due_date"
                    value={formData.due_date}
                    onChange={handleChange}
                    onBlur={handleBlur}
                    min={formData.start_date || undefined}
                    className={`w-full px-4 py-3 rounded-lg border-2 transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 ${
                      errors.due_date && touched.due_date
                        ? 'border-red-300 focus:border-red-500 focus:ring-red-200'
                        : 'border-gray-200 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-200'
                    }`}
                  />
                  {errors.due_date && touched.due_date && (
                    <p className="text-red-600 text-sm flex items-center mt-1">
                      <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                      </svg>
                      {errors.due_date}
                    </p>
                  )}
                </div>

                {/* Hours */}
                <div className="space-y-2">
                  <label className="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Estimated Hours
                  </label>
                  <input
                    type="number"
                    name="hours"
                    value={formData.hours}
                    onChange={handleChange}
                    onBlur={handleBlur}
                    min="1"
                    className={`w-full px-4 py-3 rounded-lg border-2 transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 ${
                      errors.hours && touched.hours
                        ? 'border-red-300 focus:border-red-500 focus:ring-red-200'
                        : 'border-gray-200 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-200'
                    }`}
                    placeholder="e.g., 8"
                  />
                  {errors.hours && touched.hours && (
                    <p className="text-red-600 text-sm flex items-center mt-1">
                      <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                      </svg>
                      {errors.hours}
                    </p>
                  )}
                </div>
              </div>
            </div>

            {/* Attachment */}
            <div className="space-y-2">
              <label className="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                Attachment (Optional)
              </label>
              <input
                type="file"
                name="attachment"
                onChange={handleChange}
                onBlur={handleBlur}
                accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg"
                className="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-blue-900 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-800"
              />
              {errors.attachment && touched.attachment && (
                <p className="text-red-600 text-sm flex items-center mt-1">
                  <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                  </svg>
                  {errors.attachment}
                </p>
              )}
              <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">Max file size: 10MB</p>
            </div>

            {/* Action Buttons */}
            <div className="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200 dark:border-gray-600">
              <button
                type="button"
                onClick={() => navigate('/tasks')}
                className="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300"
              >
                Cancel
              </button>
              <button
                type="submit"
                disabled={loading || Object.keys(errors).length > 0}
                className="px-6 py-3 border-2 border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-blue-800 hover:from-indigo-700 hover:to-blue-900 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-300 flex items-center justify-center"
              >
                {loading ? (
                  <>
                    <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                      <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Creating Task...
                  </>
                ) : (
                  <>
                    <svg className="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                      <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                    </svg>
                    Create Task
                  </>
                )}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
};

export default CreateTask;