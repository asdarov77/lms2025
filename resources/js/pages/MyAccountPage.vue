<template>
  <v-container fluid>
    <v-row class="mb-4">
      <v-col cols="12">
        <h1 class="text-h4">{{ t('app.menu.profile') }}</h1>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12" md="6">
        <v-card variant="outlined">
          <v-card-item>
            <template v-slot:prepend>
              <v-avatar color="primary">
                <v-icon color="white">mdi-account</v-icon>
              </v-avatar>
            </template>
            <v-card-title>{{ authStore.user?.fio || authStore.user?.name }}</v-card-title>
            <v-card-subtitle>{{ authStore.user?.role }}</v-card-subtitle>
          </v-card-item>

          <v-card-text>
            <v-list density="compact">
              <v-list-item
                prepend-icon="mdi-email"
                :title="authStore.user?.email"
                subtitle="Email"
              />
              <v-list-item
                prepend-icon="mdi-phone"
                :title="authStore.user?.phonenumber"
                subtitle="Телефон"
              />
              <v-list-item
                prepend-icon="mdi-building"
                :title="authStore.user?.organization"
                subtitle="Организация"
              />
              <v-list-item
                prepend-icon="mdi-briefcase"
                :title="authStore.user?.position"
                subtitle="Должность"
              />
            </v-list>
          </v-card-text>

          <v-card-actions>
            <v-btn
              color="primary"
              variant="tonal"
              :to="{ name: 'users.edit', params: { id: authStore.user?.id } }"
            >
              {{ t('app.common.edit') }}
            </v-btn>
            <v-btn
              color="secondary"
              variant="tonal"
              :to="{ name: 'users.change-password', params: { id: authStore.user?.id } }"
            >
              Сменить пароль
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>

      <v-col cols="12" md="6">
        <v-card variant="outlined">
          <v-card-item>
            <v-card-title>{{ t('groups.enroll') }}</v-card-title>
          </v-card-item>

          <v-card-text>
            <v-alert
              v-if="!userGroups.length"
              type="info"
              variant="tonal"
            >
              Вы не записаны ни на один курс
            </v-alert>

            <v-list v-else>
              <v-list-item
                v-for="group in userGroups"
                :key="group.id"
                :title="group.groupname"
                :subtitle="group.groupdescription"
              >
                <template v-slot:append>
                  <v-chip color="primary" size="small">
                    {{ group.courses_count || 0 }} курсов
                  </v-chip>
                </template>
              </v-list-item>
            </v-list>
          </v-card-text>

          <v-card-actions>
            <v-btn
              color="primary"
              variant="tonal"
              :to="{ name: 'user.learning' }"
            >
              Мои курсы
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <!-- Permissions -->
    <v-row v-if="authStore.user?.permissions?.length" class="mt-4">
      <v-col cols="12">
        <v-card variant="outlined">
          <v-card-item>
            <v-card-title>{{ t('users.permissions') }}</v-card-title>
          </v-card-item>

          <v-card-text>
            <div class="d-flex flex-wrap gap-2">
              <v-chip
                v-for="permission in authStore.user.permissions"
                :key="permission.id"
                color="success"
                variant="tonal"
                class="mr-2 mb-2"
              >
                {{ permission.name }}
              </v-chip>
            </div>
          </v-card-text>
        </v-card>
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

const userGroups = ref([]);

async function fetchUserGroups() {
  try {
    const response = await api.get(`/api/users/${authStore.user?.id}/groups`);
    userGroups.value = response.data || [];
  } catch (error) {
    console.error('Error fetching user groups:', error);
  }
}

onMounted(() => {
  if (authStore.isLoggedIn) {
    fetchUserGroups();
  }
});
</script>

<style scoped>
.gap-2 {
  gap: 8px;
}
</style>
