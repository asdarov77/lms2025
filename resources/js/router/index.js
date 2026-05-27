import { createRouter, createWebHashHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth.store';

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('@/pages/HomePage.vue')
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/pages/LoginPage.vue'),
    meta: { guest: true }
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('@/pages/RegisterPage.vue'),
    meta: { guest: true }
  },
  {
    path: '/my-account',
    name: 'my-account',
    component: () => import('@/pages/MyAccountPage.vue'),
    meta: { requiresAuth: true }
  },
  // Users
  {
    path: '/users',
    name: 'users.list',
    component: () => import('@/pages/users/UserListPage.vue'),
    meta: { requiresAuth: true, permission: 'manage-users' }
  },
  {
    path: '/users/:id/edit',
    name: 'users.edit',
    component: () => import('@/pages/users/UserEditPage.vue'),
    props: true,
    meta: { requiresAuth: true, permission: 'manage-users' }
  },
  {
    path: '/users/:id/change-password',
    name: 'users.change-password',
    component: () => import('@/pages/users/UserChangePasswordPage.vue'),
    props: true,
    meta: { requiresAuth: true, permission: 'manage-users' }
  },
  // Groups
  {
    path: '/groups',
    name: 'groups.list',
    component: () => import('@/pages/groups/GroupListPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/groups/create',
    name: 'groups.create',
    component: () => import('@/pages/groups/GroupCreatePage.vue'),
    meta: { requiresAuth: true, permission: 'manage-users' }
  },
  {
    path: '/groups/:id/edit',
    name: 'groups.edit',
    component: () => import('@/pages/groups/GroupEditPage.vue'),
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/groups/:id/learning',
    name: 'groups.learning',
    component: () => import('@/pages/groups/GroupLearningPage.vue'),
    props: true,
    meta: { requiresAuth: true, permission: 'manage-users' }
  },
  // Courses
  {
    path: '/courses',
    name: 'courses.list',
    component: () => import('@/pages/courses/CourseListPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/courses/:id',
    name: 'courses.show',
    component: () => import('@/pages/courses/CourseShowPage.vue'),
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/courses/:id/manifest',
    name: 'courses.manifest',
    component: () => import('@/pages/courses/CourseManifestPage.vue'),
    props: route => ({
      idEdit: parseInt(route.params.id),
      idCategory: parseInt(route.query.categoryId)
    }),
    meta: { requiresAuth: true }
  },
  {
    path: '/aircrafts',
    name: 'aircrafts.index',
    component: () => import('@/pages/aircrafts/AircraftIndexPage.vue'),
    meta: { requiresAuth: true, permission: 'manage-users' }
  },
  // Categories
  {
    path: '/categories',
    name: 'categories.index',
    component: () => import('@/pages/categories/CategoryListPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/categories/create',
    name: 'categories.create',
    component: () => import('@/pages/categories/CategoryCreatePage.vue'),
    meta: { requiresAuth: true, permission: 'manage-users' }
  },
  {
    path: '/categories/:id/edit',
    name: 'categories.edit',
    component: () => import('@/pages/categories/CategoryEditPage.vue'),
    props: true,
    meta: { requiresAuth: true }
  },
  // Questions & Tests
  {
    path: '/questions',
    name: 'questions.exam',
    component: () => import('@/pages/questions/ExaminePage.vue'),
    props: route => ({
      idEdit: parseInt(route.query.courseId),
      idCategory: parseInt(route.query.categoryId)
    }),
    meta: { requiresAuth: true }
  },
  {
    path: '/questions-bank',
    name: 'questions.bank',
    component: () => import('@/pages/questions/QuestionBankPage.vue'),
    meta: { requiresAuth: true, permission: 'manage-users' }
  },
  {
    path: '/questions/create/:categoryId/:aukstructureId',
    name: 'questions.create',
    component: () => import('@/pages/questions/QuestionCreatePage.vue'),
    props: true,
    meta: { requiresAuth: true, permission: 'manage-users' }
  },
  {
    path: '/questions/:id/edit',
    name: 'questions.edit',
    component: () => import('@/pages/questions/QuestionEditPage.vue'),
    props: true,
    meta: { requiresAuth: true, permission: 'manage-users' }
  },
  {
    path: '/gift/import',
    name: 'gift.import',
    component: () => import('@/pages/gift/GiftImportPage.vue'),
    meta: { requiresAuth: true, permission: 'manage-users' }
  },
  // Settings
  {
    path: '/settings/grade-boundaries',
    name: 'settings.grade-boundaries',
    component: () => import('@/pages/settings/GradeBoundarySettingsPage.vue'),
    meta: { requiresAuth: true, permission: 'manage-users' }
  },
  {
    path: '/settings',
    name: 'settings.index',
    component: () => import('@/pages/settings/SettingsIndexPage.vue'),
    meta: { requiresAuth: true, permission: 'manage-users' }
  },
  // User Learning
  {
    path: '/user/learning',
    name: 'user.learning',
    component: () => import('@/pages/user/UserLearningPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/user/course/:id',
    name: 'user.course',
    component: () => import('@/pages/user/UserCoursePage.vue'),
    props: true,
    meta: { requiresAuth: true }
  },
  // Errors
  {
    path: '/403',
    name: 'error.403',
    component: () => import('@/pages/errors/Error403Page.vue')
  },
  {
    path: '/404',
    name: 'error.404',
    component: () => import('@/pages/errors/Error404Page.vue')
  },
  {
    path: '/500',
    name: 'error.500',
    component: () => import('@/pages/errors/Error500Page.vue')
  },
  // Catch all
  {
    path: '/:pathMatch(.*)*',
    redirect: '/404'
  }
];

const router = createRouter({
  history: createWebHashHistory(),
  routes
});

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  const isLoggedIn = authStore.isLoggedIn;

  if (to.meta.requiresAuth && !isLoggedIn) {
    next('/login');
  } else if (to.meta.guest && isLoggedIn) {
    next('/my-account');
  } else if (to.meta.permission && !authStore.hasPermission(to.meta.permission)) {
    next('/403');
  } else {
    next();
  }
});

export default router;
