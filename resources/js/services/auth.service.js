import api from './api.service';

const API_URL = import.meta.env.VITE_APP_API_URL || '/api';

export const authService = {
  async login(credentials) {
    return api.post(`${API_URL}/login`, credentials);
  },

  async register(userData) {
    return api.post(`${API_URL}/register`, userData);
  },

  async logout() {
    return api.post(`${API_URL}/logout`);
  },

  async getMe() {
    return api.get(`${API_URL}/user/me`);
  },

  async updateUser(id, data) {
    return api.put(`${API_URL}/users/${id}`, data);
  },

  async changePassword(id, data) {
    return api.post(`${API_URL}/users/${id}/change-password`, data);
  }
};
