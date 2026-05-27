<template>
  <v-container fluid class="fill-height">
    <v-row align="center" justify="center">
      <v-col cols="12" class="text-center">
        <h1 class="text-h3 mb-4">{{ t('app.title') }}</h1>
        
        <v-card class="mx-auto mb-8" max-width="600" variant="outlined">
          <v-card-text>
            <p class="text-body-1 mb-4">
              {{ t('courses.noCourses') }}
            </p>
            
            <v-btn
              color="primary"
              size="large"
              :to="{ name: 'courses.list' }"
            >
              {{ t('courses.title') }}
            </v-btn>
          </v-card-text>
        </v-card>

        <!-- Quick Stats -->
        <v-row v-if="authStore.isLoggedIn" justify="center">
          <v-col cols="12" sm="4" md="2">
            <v-card variant="tonal" color="primary">
              <v-card-text class="text-center">
                <div class="text-h4">{{ stats.courses }}</div>
                <div class="text-caption">{{ t('courses.title') }}</div>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="12" sm="4" md="2">
            <v-card variant="tonal" color="success">
              <v-card-text class="text-center">
                <div class="text-h4">{{ stats.categories }}</div>
                <div class="text-caption">{{ t('app.menu.categories') }}</div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { useAuthStore } from '@/stores/auth.store';
import api from '@/services/api.service';

const { t } = useI18n();
const authStore = useAuthStore();

const stats = ref({
  courses: 0,
  categories: 0
});

async function fetchStats() {
  try {
    const [coursesRes, categoriesRes] = await Promise.all([
      api.get('/api/courses', { params: { per_page: 1 } }),
      api.get('/api/categories')
    ]);
    
    stats.value.courses = coursesRes.data.meta?.pagination?.total || 0;
    stats.value.categories = categoriesRes.data?.length || 0;
  } catch (error) {
    console.error('Error fetching stats:', error);
  }
}

onMounted(() => {
  if (authStore.isLoggedIn) {
    fetchStats();
  }
});
</script>
