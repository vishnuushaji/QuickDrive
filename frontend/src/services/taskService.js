import api from './api';

export const taskService = {
  getAll: (page = 1, additionalParams = {}) => api.get('/tasks', { params: { page, ...additionalParams } }),
  getById: (id) => api.get(`/tasks/${id}`),
  
  create: (data) => {
    // Check if data is FormData (for file uploads)
    if (data instanceof FormData) {
      return api.post('/tasks', data, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });
    }
    return api.post('/tasks', data);
  },
  
  update: (id, data) => {
    // Check if data is FormData (for file uploads)
    if (data instanceof FormData) {
      // Laravel doesn't support PUT with FormData, use POST with _method override
      data.append('_method', 'PUT');
      return api.post(`/tasks/${id}`, data, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });
    }
    return api.put(`/tasks/${id}`, data);
  },
  
  delete: (id) => api.delete(`/tasks/${id}`),
  updateStatus: (id, status) => api.patch(`/tasks/${id}/status`, { status }),
  approve: (id) => api.patch(`/tasks/${id}/approve`),
  reject: (id) => api.patch(`/tasks/${id}/reject`),
};