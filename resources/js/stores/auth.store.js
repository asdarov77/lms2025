import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { authService } from '@/services/auth.service';
import { TokenService } from '@/services/token.service';

export const useAuthStore = defineStore('auth', () => {
  const accessToken = ref(TokenService.getToken());
  const user = ref(TokenService.getUser());
  const isLoading = ref(false);
  const error = ref(null);

  const isLoggedIn = computed(() => !!accessToken.value);
  const currentUser = computed(() => user.value);
  
  const hasPermission = computed(() => {
    return (slug) => {
      if (!user.value?.permissions) return false;
      return user.value.permissions.some(p => p.slug === slug || p.name === slug);
    };
  });

  const hasRole = computed(() => {
    return (role) => {
      if (!user.value) return false;
      return user.value.role === role || user.value.roles?.some(r => r.name === role);
    };
  });

  async function login(credentials) {
    isLoading.value = true;
    error.value = null;
    
    try {
      const response = await authService.login(credentials);
      const { token, user: userData, permissions } = response.data;
      
      accessToken.value = token;
      user.value = { ...userData, permissions };
      
      TokenService.saveToken(token);
      TokenService.saveUser(user.value);
      
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Ошибка входа';
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  async function register(userData) {
    isLoading.value = true;
    error.value = null;
    
    try {
      const response = await authService.register(userData);
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Ошибка регистрации';
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  function logout() {
    accessToken.value = null;
    user.value = null;
    error.value = null;
    
    TokenService.removeToken();
    TokenService.removeUser();
  }

  function clearError() {
    error.value = null;
  }

  return {
    accessToken,
    user,
    isLoading,
    error,
    isLoggedIn,
    currentUser,
    hasPermission,
    hasRole,
    login,
    register,
    logout,
    clearError
  };
});
