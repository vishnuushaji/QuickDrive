import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { projectService } from '../services/projectService';
import { userService } from '../services/userService';
import toast from 'react-hot-toast';

const EditProject = () => {
  const navigate = useNavigate();
  const { id } = useParams();
  const [loading, setLoading] = useState(false);
  const [users, setUsers] = useState([]);
  const [errors, setErrors] = useState({});
  const [touched, setTouched] = useState({});
  const [formData, setFormData] = useState({
    name: '',
    description: '',
    client_ids: [],
    developer_ids: [],
    status: 'active',
  });

  useEffect(() => {
    fetchProject();
    fetchUsers();
  }, [id]);

  const fetchProject = async () => {
    try {
      const response = await projectService.getById(id);
      const project = response.data;

      // Extract client and developer IDs from users
      const clientIds = project.users
        .filter(user => user.pivot.role === 'client')
        .map(user => user.id.toString());
      const developerIds = project.users
        .filter(user => user.pivot.role === 'developer')
        .map(user => user.id.toString());

      setFormData({
        name: project.name,
        description: project.description || '',
        client_ids: clientIds,
        developer_ids: developerIds,
        status: project.status,
      });
    } catch (error) {
      toast.error('Failed to fetch project');
      navigate('/projects');
    }
  };

  const fetchUsers = async () => {
    try {
      const response = await userService.getAllWithoutPagination();
      setUsers(response.data.data);
    } catch (error) {
      toast.error('Failed to fetch users');
    }
  };

  // Validation function
  const validateForm = () => {
    const newErrors = {};

    // Project name validation
    if (!formData.name.trim()) {
      newErrors.name = 'Project name is required';
    } else if (formData.name.trim().length < 3) {
      newErrors.name = 'Project name must be at least 3 characters';
    } else if (formData.name.trim().length > 100) {
      newErrors.name = 'Project name must not exceed 100 characters';
    }

    // Description validation
    if (formData.description.trim().length > 1000) {
      newErrors.description = 'Description must not exceed 1000 characters';
    }

    // Client validation (optional but if selected, must have at least one)
    if (formData.client_ids.length === 0) {
      newErrors.client_ids = 'At least one client must be assigned';
    }

    // Developer validation
    if (formData.developer_ids.length === 0) {
      newErrors.developer_ids = 'At least one developer must be assigned';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  // Real-time validation for individual fields
  const validateField = (name, value) => {
    const fieldErrors = { ...errors };

    switch (name) {
      case 'name':
        if (!value.trim()) {
          fieldErrors.name = 'Project name is required';
        } else if (value.trim().length < 3) {
          fieldErrors.name = 'Project name must be at least 3 characters';
        } else if (value.trim().length > 100) {
          fieldErrors.name = 'Project name must not exceed 100 characters';
        } else {
          delete fieldErrors.name;
        }
        break;
      case 'description':
        if (value.trim().length > 1000) {
          fieldErrors.description = 'Description must not exceed 1000 characters';
        } else {
          delete fieldErrors.description;
        }
        break;
      default:
        break;
    }

    setErrors(fieldErrors);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    // Mark all fields as touched
    setTouched({
      name: true,
      description: true,
      client_ids: true,
      developer_ids: true,
      status: true
    });

    if (!validateForm()) {
      toast.error('Please fix the errors before submitting');
      return;
    }

    setLoading(true);
    try {
      await projectService.update(id, formData);
      toast.success('Project updated successfully');
      navigate('/projects');
    } catch (error) {
      toast.error(error.response?.data?.message || 'Failed to update project');
    } finally {
      setLoading(false);
    }
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));

    // Real-time validation
    if (touched[name]) {
      validateField(name, value);
    }
  };

  const handleBlur = (e) => {
    const { name, value } = e.target;
    setTouched(prev => ({ ...prev, [name]: true }));
    validateField(name, value);
  };

  const handleMultiSelect = (e, field) => {
    const selectedOptions = Array.from(e.target.selectedOptions).map(option => option.value);
    setFormData(prev => ({
      ...prev,
      [field]: selectedOptions
    }));

    // Clear errors for multi-select fields
    if (selectedOptions.length > 0) {
      const newErrors = { ...errors };
      delete newErrors[field];
      setErrors(newErrors);
    } else if (touched[field]) {
      setErrors(prev => ({
        ...prev,
        [field]: field === 'client_ids' ? 'At least one client must be assigned' : 'At least one developer must be assigned'
      }));
    }
  };

  const handleMultiSelectBlur = (field) => {
    setTouched(prev => ({ ...prev, [field]: true }));
    if (formData[field].length === 0) {
      setErrors(prev => ({
        ...prev,
        [field]: field === 'client_ids' ? 'At least one client must be assigned' : 'At least one developer must be assigned'
      }));
    }
  };

  const clients = users.filter(user => user.role === 'client');
  const developers = users.filter(user => user.role === 'developer');

  return (
    <div className="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
          {/* Header */}
          <div className="bg-gradient-to-r from-blue-600 to-blue-600 px-6 py-8">
            <h1 className="text-3xl font-bold text-white mb-2">Edit Project</h1>
            <p className="text-blue-100">Update your project details and team members</p>
          </div>

          {/* Form */}
          <form onSubmit={handleSubmit} className="p-8 space-y-8">
            {/* Project Name */}
            <div className="space-y-2">
              <label className="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                Project Name *
              </label>
              <input
                type="text"
                name="name"
                value={formData.name}
                onChange={handleChange}
                onBlur={handleBlur}
                className={`w-full px-4 py-3 rounded-lg border-2 transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 ${
                  errors.name && touched.name
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-200'
                    : 'border-gray-200 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-200'
                }`}
                placeholder="Enter project name"
              />
              {errors.name && touched.name && (
                <p className="text-red-600 text-sm flex items-center mt-1">
                  <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                  </svg>
                  {errors.name}
                </p>
              )}
              <p className="text-xs text-gray-500 dark:text-gray-400">
                {formData.name.length}/100 characters
              </p>
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
                    : 'border-gray-200 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-200'
                }`}
                placeholder="Describe your project goals, requirements, and key details..."
              />
              {errors.description && touched.description && (
                <p className="text-red-600 text-sm flex items-center mt-1">
                  <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                  </svg>
                  {errors.description}
                </p>
              )}
              <p className="text-xs text-gray-500 dark:text-gray-400">
                {formData.description.length}/1000 characters
              </p>
            </div>

            {/* Team Assignment Section */}
            <div className="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 space-y-6">
              <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                <svg className="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z" />
                </svg>
                Team Assignment
              </h3>

              {/* Clients */}
              <div className="space-y-2">
                <label className="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                  Assign Clients *
                </label>
                <select
                  multiple
                  name="client_ids"
                  value={formData.client_ids}
                  onChange={(e) => handleMultiSelect(e, 'client_ids')}
                  onBlur={() => handleMultiSelectBlur('client_ids')}
                  className={`w-full px-4 py-3 rounded-lg border-2 transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 ${
                    errors.client_ids && touched.client_ids
                      ? 'border-red-300 focus:border-red-500 focus:ring-red-200'
                      : 'border-gray-200 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-200'
                  }`}
                  size={Math.min(clients.length, 4)}
                >
                  {clients.map(client => (
                    <option key={client.id} value={client.id} className="py-2">
                      {client.name} ({client.email})
                    </option>
                  ))}
                </select>
                {errors.client_ids && touched.client_ids && (
                  <p className="text-red-600 text-sm flex items-center mt-1">
                    <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                    </svg>
                    {errors.client_ids}
                  </p>
                )}
                <p className="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                  <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd" />
                  </svg>
                  Hold Ctrl/Cmd to select multiple clients ({formData.client_ids.length} selected)
                </p>
              </div>

              {/* Developers */}
              <div className="space-y-2">
                <label className="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                  Assign Developers *
                </label>
                <select
                  multiple
                  name="developer_ids"
                  value={formData.developer_ids}
                  onChange={(e) => handleMultiSelect(e, 'developer_ids')}
                  onBlur={() => handleMultiSelectBlur('developer_ids')}
                  className={`w-full px-4 py-3 rounded-lg border-2 transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 ${
                    errors.developer_ids && touched.developer_ids
                      ? 'border-red-300 focus:border-red-500 focus:ring-red-200'
                      : 'border-gray-200 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-200'
                  }`}
                  size={Math.min(developers.length, 4)}
                >
                  {developers.map(developer => (
                    <option key={developer.id} value={developer.id} className="py-2">
                      {developer.name} ({developer.email})
                    </option>
                  ))}
                </select>
                {errors.developer_ids && touched.developer_ids && (
                  <p className="text-red-600 text-sm flex items-center mt-1">
                    <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                    </svg>
                    {errors.developer_ids}
                  </p>
                )}
                <p className="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                  <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd" />
                  </svg>
                  Hold Ctrl/Cmd to select multiple developers ({formData.developer_ids.length} selected)
                </p>
              </div>
            </div>

            {/* Status */}
            <div className="space-y-2">
              <label className="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                Project Status
              </label>
              <select
                name="status"
                value={formData.status}
                onChange={handleChange}
                className="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-gray-600 transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:border-blue-500 focus:ring-blue-200"
              >
                <option value="active">🟢 Active</option>
                <option value="completed">✅ Completed</option>
                <option value="on_hold">⏸️ On Hold</option>
              </select>
            </div>

            {/* Action Buttons */}
            <div className="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200 dark:border-gray-600">
              <button
                type="button"
                onClick={() => navigate('/projects')}
                className="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300"
              >
                Cancel
              </button>
              <button
                type="submit"
                disabled={loading || Object.keys(errors).length > 0}
                className="px-6 py-3 border-2 border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-600 hover:from-blue-700 hover:to-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-300 flex items-center justify-center"
              >
                {loading ? (
                  <>
                    <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                      <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Updating Project...
                  </>
                ) : (
                  <>
                    <svg className="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                      <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                    </svg>
                    Update Project
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

export default EditProject;