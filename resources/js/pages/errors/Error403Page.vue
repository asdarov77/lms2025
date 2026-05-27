<template>
  <v-container class="fill-height" fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" class="text-center">
        <h1 class="text-h1 text-error mb-4">403</h1>
        <h2 class="text-h4 mb-4">{{ t('app.errors.forbidden') }}</h2>
        <p class="text-body-1 mb-8">
          {{ t('app.errors.unauthorized') }}
        </p>
        <v-btn color="primary" :to="{ name: 'home' }">
          {{ t('app.menu.home') }}
        </v-btn>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>
