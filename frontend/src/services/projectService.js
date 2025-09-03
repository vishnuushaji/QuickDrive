import api from './api';

export const projectService = {
  getAll: (page = 1) => api.get('/projects', { params: { page } }),
  getAllWithoutPagination: () => api.get('/projects', { params: { per_page: 1000 } }),
  getById: (id) => api.get(`/projects/${id}`),
  create: (data) => api.post('/projects', data),
  update: (id, data) => api.put(`/projects/${id}`, data),
  delete: (id) => api.delete(`/projects/${id}`),
};