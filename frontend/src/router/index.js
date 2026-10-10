// frontend/src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import { authService } from '../lib/authService'

const routes = [
  { path: '/', name: 'home', component: () => import('../pages/Home.vue') },
  { path: '/dang-nhap', name: 'login', component: () => import('../pages/Login.vue') },
  { path: '/san-pham', name: 'list', component: () => import('../pages/ProductList.vue') },
  { path: '/san-pham/:slug', name: 'detail', component: () => import('../pages/ProductDetail.vue'), props: true },
  { path: '/dat-cho/:code', name: 'booking', component: () => import('../views/BookingView.vue'), props: true, meta: { requiresAuth: true } },

  // Trang quản trị: chỉ admin và nhà cung cấp.
  // Tạm dùng ProductList để kiểm tra guard. Khi có AdminProducts.vue thì đổi component.
  {
    path: '/quan-tri/san-pham',
    name: 'admin-products',
    component: () => import('../pages/ProductList.vue'),
    meta: { roles: ['admin', 'supplier'] },
  },

  { path: '/khong-du-quyen', name: 'forbidden', component: () => import('../views/ForbiddenView.vue') },
  { path: '/:pathMatch(.*)*', name: '404', component: () => import('../views/NotFound.vue') },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 }),
})

// Chặn truy cập theo vai trò ở phía giao diện.
// Máy chủ vẫn phải kiểm tra lại khi nhận yêu cầu (V3 phụ trách).
router.beforeEach(async (to) => {
  const canKiemTra = to.meta.roles || to.meta.requiresAuth
  if (!canKiemTra) return true

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