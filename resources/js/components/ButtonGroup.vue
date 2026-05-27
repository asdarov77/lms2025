<template>
  <div class="d-flex justify-space-between">
    <v-btn
      color="success"
      :loading="isLoading"
      @click="$emit('submitForm')"
    >
      {{ t('app.common.save') }}
    </v-btn>
    
    <v-btn
      color="error"
      variant="outlined"
      :disabled="isLoading"
      @click="$emit('cancelBtn')"
    >
      {{ t('app.common.cancel') }}
    </v-btn>
  </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
  isLoading: {
    type: Boolean,
    default: false
  }
});

defineEmits(['submitForm', 'cancelBtn']);
</script>
