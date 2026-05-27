<template>
  <v-container class="fill-height" fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="4">
        <v-card elevation="12">
          <v-toolbar color="primary" dark>
            <v-toolbar-title>{{ t('auth.login') }}</v-toolbar-title>
          </v-toolbar>

          <v-card-text>
            <v-form @submit.prevent="handleLogin" ref="formRef">
              <v-text-field
                v-model="credentials.fio"
                :label="t('auth.fio')"
                prepend-icon="mdi-account"
                :rules="[rules.required]"
                variant="outlined"
              />

              <v-text-field
                v-model="credentials.password"
                :label="t('auth.password')"
                prepend-icon="mdi-lock"
                :type="showPassword ? 'text' : 'password'"
                :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                @click:append-inner="showPassword = !showPassword"
                :rules="[rules.required]"
                variant="outlined"
              />

              <v-alert
                v-if="authStore.error"
                type="error"
                variant="tonal"
                class="mt-4"
                closable
                @click:close="authStore.clearError()"
              >
                {{ authStore.error }}
              </v-alert>
            </v-form>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn
              color="primary"
              :loading="authStore.isLoading"
              :disabled="authStore.isLoading"
              @click="handleLogin"
            >
              {{ t('auth.login') }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { useAuthStore } from '@/stores/auth.store';

const router = useRouter();
const { t } = useI18n();
const authStore = useAuthStore();

const formRef = ref(null);
const showPassword = ref(false);

const credentials = reactive({
  fio: '',
  password: ''
});

const rules = {
  required: value => !!value || t('app.validation.required')
};

async function handleLogin() {
  const { valid } = await formRef.value.validate();
  
  if (!valid) return;

  try {
    await authStore.login(credentials);
    router.push('/my-account');
  } catch (error) {
    // Error handled in store
  }
}
</script>
