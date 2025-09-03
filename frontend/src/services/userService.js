import api from './api';

export const userService = {
  getAll: (page = 1) => api.get('/users', { params: { page } }),
  getAllWithoutPagination: () => api.get('/users', { params: { per_page: 1000 } }),
  getById: (id) => api.get(`/users/${id}`),
  create: (data) => api.post('/users', data),
  update: (id, data) => api.put(`/users/${id}`, data),
  updateProfile: (data) => api.put('/profile', data),
  delete: (id) => api.delete(`/users/${id}`),
};