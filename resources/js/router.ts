import {createRouter, createWebHistory, RouteRecordRaw} from "vue-router";

const routes: Array<RouteRecordRaw> = [
  {
    name: 'HomePage',
    path: '/',
    meta: {
      requiresAuth: true,
    },
    component: () => import('@/views/HomePage.vue')
  },
  {
    name: 'Investments',
    path: '/investments',
    meta: {
      requiresAuth: true,
    },
    component: () => import('@/views/InvestmentsPage.vue')
  },
  {
    name: 'Expenses',
    path: '/expenses',
    meta: {
      requiresAuth: true,
    },
    component: () => import('@/views/ExpensesPage.vue')
  },
  {
    name: 'Login',
    path: '/login',
    meta: {
      onlyGuests: true,
    },
    component: () => import('@/views/LoginPage.vue')
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

export default router
