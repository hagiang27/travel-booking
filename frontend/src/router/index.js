import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  { path: '/', name: 'home', component: () => import('../pages/Home.vue') },
  { path: '/dang-nhap', name: 'login', component: () => import('../pages/Login.vue') },
  { path: '/san-pham', name: 'list', component: () => import('../pages/ProductList.vue') },
  { path: '/san-pham/:slug', name: 'detail', component: () => import('../pages/ProductDetail.vue'), props: true },
  { path: '/dat-cho/:code', name: 'booking', component: () => import('../views/BookingView.vue'), props: true, meta: { requiresAuth: true } },
  { path: '/:pathMatch(.*)*', name: '404', component: () => import('../views/NotFound.vue') },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 }),
})

export default router
