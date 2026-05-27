import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '@/services/api.service';

export const useCourseStore = defineStore('course', () => {
  // State
  const courses = ref([]);
  const currentCourse = ref(null);
  const categories = ref([]);
  const aircrafts = ref([]);
  const isLoading = ref(false);
  const error = ref(null);
  
  const pagination = ref({
    page: 1,
    perPage: 12,
    total: 0,
    totalPages: 0
  });

  // Getters
  const courseList = computed(() => 
    courses.value.map(course => ({
      id: course.id,
      title: course.title,
      short_description: course.short_description,
      aircraft: course.aircraft?.title,
      categories: course.categories || []
    }))
  );

  const categoryList = computed(() => 
    categories.value.map(cat => ({
      id: cat.id,
      title: cat.title,
      description: cat.description,
      parent_id: cat.parent_id
    }))
  );

  // Actions
  async function fetchCourses(params = {}) {
    isLoading.value = true;
    error.value = null;
    
    try {
      const response = await api.get('/api/courses', { params });
      courses.value = response.data.data || [];
      
      if (response.data.meta?.pagination) {
        pagination.value = response.data.meta.pagination;
      }
      
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Ошибка загрузки курсов';
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  async function fetchCourse(id) {
    isLoading.value = true;
    error.value = null;
    
    try {
      const response = await api.get(`/api/courses/${id}`);
      currentCourse.value = response.data;
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Ошибка загрузки курса';
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  async function fetchCategories() {
    isLoading.value = true;
    error.value = null;
    
    try {
      const response = await api.get('/api/categories');
      categories.value = response.data || [];
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Ошибка загрузки категорий';
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  async function fetchAircrafts() {
    isLoading.value = true;
    error.value = null;
    
    try {
      const response = await api.get('/api/aircrafts');
      aircrafts.value = response.data || [];
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Ошибка загрузки классов ВС';
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  async function createCourse(data) {
    isLoading.value = true;
    error.value = null;
    
    try {
      const response = await api.post('/api/courses', data);
      courses.value.unshift(response.data);
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Ошибка создания курса';
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  async function updateCourse(id, data) {
    isLoading.value = true;
    error.value = null;
    
    try {
      const response = await api.put(`/api/courses/${id}`, data);
      const index = courses.value.findIndex(c => c.id === id);
      if (index !== -1) {
        courses.value[index] = response.data;
      }
      if (currentCourse.value?.id === id) {
        currentCourse.value = response.data;
      }
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Ошибка обновления курса';
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  async function deleteCourse(id) {
    isLoading.value = true;
    error.value = null;
    
    try {
      await api.delete(`/api/courses/${id}`);
      courses.value = courses.value.filter(c => c.id !== id);
    } catch (err) {
      error.value = err.response?.data?.message || 'Ошибка удаления курса';
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  function clearCurrentCourse() {
    currentCourse.value = null;
  }

  function clearError() {
    error.value = null;
  }

  return {
    // State
    courses,
    currentCourse,
    categories,
    aircrafts,
    isLoading,
    error,
    pagination,
    // Getters
    courseList,
    categoryList,
    // Actions
    fetchCourses,
    fetchCourse,
    fetchCategories,
    fetchAircrafts,
    createCourse,
    updateCourse,
    deleteCourse,
    clearCurrentCourse,
    clearError
  };
});
