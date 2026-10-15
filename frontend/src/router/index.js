import { createRouter, createWebHistory } from 'vue-router'
import { authService } from '../lib/authService'

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('../views/HomeView.vue'),
  },
  {
    path: '/dang-nhap',
    name: 'login',
    component: () => import('../pages/Login.vue'),
  },
  {
    path: '/san-pham',
    name: 'list',
    component: () => import('../pages/ProductList.vue'),
  },
  {
    path: '/san-pham/:slug',
    name: 'detail',
    component: () => import('../pages/ProductDetail.vue'),
    props: false,
  },
  {
    path: '/so-sanh/:slug',
    name: 'compare',
    component: () => import('../pages/CompareView.vue'),
  },
  {
    path: '/dat-cho/:code',
    name: 'booking',
    component: () => import('../views/BookingView.vue'),
    props: true,
    meta: { requiresAuth: true },
  },
  {
    path: '/quan-tri/san-pham',
    name: 'admin-products',
    component: () => import('../pages/AdminProducts.vue'),
    meta: { requiresAuth: true, roles: ['quan_tri', 'nha_cung_cap'] },
  },
  {
    path: '/khong-co-quyen',
    name: 'forbidden',
    component: () => import('../views/ForbiddenView.vue'),
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('../views/NotFound.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to) => {
  const cанCheck = to.meta.requiresAuth || to.meta.roles
  if (!cанCheck) return true

  const nguoiDung = await authService.layNguoiDungHienTai()

  if (!nguoiDung) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  if (to.meta.roles && !to.meta.roles.includes(nguoiDung.vai_tro)) {
    return { name: 'forbidden' }
  }

  return true
})

export default router
