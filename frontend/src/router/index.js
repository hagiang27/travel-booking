import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  { path: '/', name: 'home', component: () => import('../views/HomeView.vue') },
  { path: '/san-pham', name: 'list', component: () => import('../views/ProductList.vue') },
  { path: '/san-pham/:slug', name: 'detail', component: () => import('../views/ProductDetail.vue'), props: true },
  { path: '/dat-cho/:code', name: 'booking', component: () => import('../views/BookingView.vue'), props: true, meta: { requiresAuth: true } },
  { path: '/:pathMatch(.*)*', name: '404', component: () => import('../views/NotFound.vue') },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 }),
})

export default router