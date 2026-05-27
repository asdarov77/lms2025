<template>
  <v-app>
    <v-navigation-drawer
      v-if="authStore.isLoggedIn"
      v-model="drawer"
      app
      permanent
    >
      <v-list-item
        prepend-avatar="https://avataaars.io/?avatarStyle=Transparent&topType=ShortHairShortCurly&accessoriesType=Prescription02&hairColor=Black&facialHairType=Blank&clotheType=Hoodie&clotheColor=White&eyeType=Default&eyebrowType=DefaultNatural&mouthType=Default&skinColor=Light"
        :title="authStore.user?.fio || authStore.user?.name"
        :subtitle="authStore.user?.role"
      />

      <v-divider />

      <v-list density="compact" nav>
        <v-list-item
          v-for="item in menuItems"
          :key="item.name"
          :to="item.to"
          :prepend-icon="item.icon"
          :title="item.title"
          :value="item.name"
        />
      </v-list>
    </v-navigation-drawer>

    <v-app-bar v-if="authStore.isLoggedIn" app color="primary" dark>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-app-bar-title>{{ t('app.title') }}</v-app-bar-title>

      <v-spacer />

      <!-- Language Switcher -->
      <v-select
        v-model="currentLocale"
        :items="locales"
        item-title="name"
        item-value="code"
        variant="outlined"
        density="compact"
        hide-details
        class="mr-4"
        style="max-width: 100px;"
        @update:model-value="changeLocale"
      />

      <!-- User Menu -->
      <v-menu>
        <template v-slot:activator="{ props }">
          <v-btn v-bind="props" icon>
            <v-icon>mdi-account-circle</v-icon>
          </v-btn>
        </template>

        <v-list>
          <v-list-item
            :to="{ name: 'my-account' }"
            prepend-icon="mdi-account"
            :title="t('app.menu.profile')"
          />
          <v-list-item
            prepend-icon="mdi-logout"
            :title="t('app.menu.logout')"
            @click="handleLogout"
          />
        </v-list>
      </v-menu>
    </v-app-bar>

    <v-main>
      <router-view />
    </v-main>
  </v-app>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { useAuthStore } from '@/stores/auth.store';

const router = useRouter();
const { t, locale } = useI18n();
const authStore = useAuthStore();

const drawer = ref(true);
const currentLocale = ref(locale.value);

const locales = [
  { code: 'ru', name: 'Русский' },
  { code: 'en', name: 'English' }
];

const menuItems = computed(() => [
  { name: 'home', title: t('app.menu.home'), to: '/', icon: 'mdi-home' },
  { name: 'courses', title: t('app.menu.courses'), to: '/courses', icon: 'mdi-school' },
  { name: 'categories', title: t('app.menu.categories'), to: '/categories', icon: 'mdi-folder' },
  ...(authStore.hasPermission('manage-users') ? [
    { name: 'users', title: t('app.menu.users'), to: '/users', icon: 'mdi-account-group' },
    { name: 'groups', title: t('app.menu.groups'), to: '/groups', icon: 'mdi-account-multiple' },
    { name: 'questions-bank', title: t('app.menu.questionsBank'), to: '/questions-bank', icon: 'mdi-book-open-page-variant' },
    { name: 'settings', title: t('app.menu.settings'), to: '/settings', icon: 'mdi-cog' }
  ] : []),
  { name: 'user-learning', title: t('app.menu.calendar'), to: '/user/learning', icon: 'mdi-calendar' }
]);

function changeLocale(code) {
  locale.value = code;
  localStorage.setItem('locale', code);
}

async function handleLogout() {
  await authStore.logout();
  router.push('/login');
}

// Watch for locale changes
watch(locale, (newLocale) => {
  currentLocale.value = newLocale;
});
</script>
