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
  },
  {
    path: '/so-sanh/:slug',
    name: 'compare',
    component: () => import('../pages/CompareView.vue'),
  },
  {
    path: '/quan-tri/san-pham',
    name: 'admin-products',
    component: () => import('../pages/AdminProducts.vue'),
    meta: { roles: ['quan_tri', 'nha_cung_cap'] },
  },
  {
    path: '/khong-du-quyen',
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
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

// Kiểm tra đăng nhập và quyền trước khi vào trang
router.beforeEach(async (to) => {
  const cacVaiTro = to.meta.roles
  const cancDangNhap = to.meta.requiresAuth

  if (!cacVaiTro && !cancDangNhap) return true

  const nguoiDung = await authService.layNguoiDungHienTai()

  if (!nguoiDung) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  if (cacVaiTro && !cacVaiTro.includes(nguoiDung.vai_tro)) {
    return { name: 'forbidden' }
  }

  return true
})

export default router