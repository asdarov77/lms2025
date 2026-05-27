import axios from 'axios';
import { TokenService } from './token.service';
import router from '@/router';

const api = axios.create({
  baseURL: import.meta.env.VITE_APP_API_URL || '/',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Request interceptor
api.interceptors.request.use(
  config => {
    const token = TokenService.getToken();
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  error => Promise.reject(error)
);

// Response interceptor
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      TokenService.clear();
      router.push('/login');
    }
    
    if (error.response?.status === 403) {
      router.push('/403');
    }

    if (error.response?.status === 500) {
      console.error('Server error:', error.response.data);
    }

    return Promise.reject(error);
  }
);

export default api;
