<template>
  <div class="app">
    <header class="header">
      <div class="brand">
        <h1>Nhà Việt</h1>
      </div>

      <nav class="navigation" aria-label="Điều hướng chính">
        <button
          type="button"
          :class="{ active: currentPage === 'home' }"
          @click="navigate('home')"
        >
          Trang chủ
        </button>

        <button
          type="button"
          :class="{ active: currentPage === 'products' }"
          @click="navigate('products')"
        >
          Sản phẩm
        </button>

        <button
          type="button"
          :class="{ active: currentPage === 'detail' }"
          @click="navigate('detail')"
        >
          Chi tiết
        </button>

        <button
          v-if="userRole === 'quan_tri'"
          type="button"
          :class="{ active: currentPage === 'admin' }"
          @click="navigate('admin')"
        >
          Quản trị
        </button>

        <button
          v-if="!isLoggedIn"
          type="button"
          :class="{ active: currentPage === 'login' }"
          @click="navigate('login')"
        >
          Đăng nhập
        </button>
      </nav>

      <div class="header-actions">
        <template v-if="isLoggedIn">
          <span class="user-info">
            {{ currentUser.ho_ten }}
            <small>({{ roleLabel }})</small>
          </span>
          <button type="button" class="logout-button" @click="onLogout">
            Đăng xuất
          </button>
        </template>

        <button
          type="button"
          class="theme-button"
          :aria-label="
            isDark
              ? 'Chuyển sang giao diện sáng'
              : 'Chuyển sang giao diện tối'
          "
          @click="toggleTheme"
        >
          {{ isDark ? '☀️ Light' : '🌙 Dark' }}
        </button>
      </div>
    </header>

    <main v-if="authReady">
      <Home v-if="currentPage === 'home'" />
      <ProductList v-else-if="currentPage === 'products'" />
      <ProductDetail v-else-if="currentPage === 'detail'" />
      <Admin v-else-if="currentPage === 'admin'" />
      <Forbidden v-else-if="currentPage === 'forbidden'" />
      <Login v-else @success="onLoginSuccess" />
    </main>
    <p v-else class="loading" role="status">Đang tải...</p>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'

import Home from './pages/Home.vue'
import ProductList from './pages/ProductList.vue'
import ProductDetail from './pages/ProductDetail.vue'
import Login from './pages/Login.vue'
import Admin from './pages/Admin.vue'
import Forbidden from './pages/Forbidden.vue'

import {
  authReady,
  currentUser,
  initAuth,
  isLoggedIn,
  signOut,
  userRole,
} from './services/auth'

/* ---------- Giao diện sáng / tối ---------- */
const isDark = ref(false)

function toggleTheme() {
  isDark.value = !isDark.value
  document.documentElement.setAttribute(
    'data-theme',
    isDark.value ? 'dark' : 'light'
  )
}

/* ---------- Điều hướng theo địa chỉ dạng #/trang ---------- */
const PAGES = ['home', 'products', 'detail', 'login', 'admin', 'forbidden']

// Trang nào cần vai trò nào. Thêm trang quản trị khác thì khai báo ở đây.
const ROLE_RULES = {
  admin: ['quan_tri'],
}

const ROLE_LABELS = {
  khach_hang: 'Khách hàng',
  nha_cung_cap: 'Nhà cung cấp',
  quan_tri: 'Quản trị',
}

const currentPage = ref('home')
const redirectAfterLogin = ref(null)

const roleLabel = computed(() => ROLE_LABELS[userRole.value] ?? userRole.value)

function resolvePage(wanted) {
  if (!PAGES.includes(wanted)) return 'home'
  if (wanted === 'login' && isLoggedIn.value) return 'home'

  const roles = ROLE_RULES[wanted]
  if (roles) {
    if (!isLoggedIn.value) {
      redirectAfterLogin.value = wanted // đăng nhập xong quay lại trang này
      return 'login'
    }
    if (!roles.includes(userRole.value)) return 'forbidden'
  }
  return wanted
}

function syncFromHash() {
  const wanted = window.location.hash.replace('#/', '') || 'home'
  const page = resolvePage(wanted)
  currentPage.value = page
  if (page !== wanted) {
    history.replaceState(null, '', `#/${page}`)
  }
}

function navigate(page) {
  if (window.location.hash === `#/${page}`) {
    syncFromHash()
  } else {
    window.location.hash = `/${page}`
  }
}

function onLoginSuccess() {
  const target = redirectAfterLogin.value || 'home'
  redirectAfterLogin.value = null
  navigate(target)
}

async function onLogout() {
  try {
    await signOut()
  } finally {
    navigate('login')
  }
}

onMounted(async () => {
  document.documentElement.setAttribute('data-theme', 'light')
  window.addEventListener('hashchange', syncFromHash)
  await initAuth() // khôi phục phiên (F5 vẫn giữ đăng nhập)
  syncFromHash()
})

onBeforeUnmount(() => {
  window.removeEventListener('hashchange', syncFromHash)
})
</script>

<style scoped>
.app {
  min-height: 100vh;
  background: var(--color-bg);
  color: var(--color-text);
  font-family: var(--font-family);
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-6);
  padding: var(--space-4) var(--space-6);
  border-bottom: 1px solid var(--color-border);
  background: var(--color-surface);
}

.brand h1 {
  margin: 0;
  font-size: var(--font-size-h3);
  font-weight: var(--font-weight-bold);
}

.navigation {
  display: flex;
  gap: var(--space-2);
  flex-wrap: wrap;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  flex-wrap: wrap;
}

.user-info {
  font-size: var(--font-size-small);
  font-weight: var(--font-weight-semibold);
}

.user-info small {
  color: var(--color-text-secondary);
  font-weight: normal;
}

.navigation button,
.theme-button,
.logout-button {
  min-height: 40px;
  padding: 0 var(--space-4);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background: var(--color-surface);
  color: var(--color-text);
  font-size: var(--font-size-small);
  cursor: pointer;
}

.navigation button:hover,
.theme-button:hover,
.logout-button:hover {
  background: var(--color-surface-2);
}

.navigation button:focus-visible,
.theme-button:focus-visible,
.logout-button:focus-visible {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
}

.navigation button.active {
  background: var(--color-primary);
  border-color: var(--color-primary);
  color: var(--color-bg);
}

.theme-button {
  white-space: nowrap;
}

.loading {
  padding: var(--space-8);
  text-align: center;
  color: var(--color-text-secondary);
}

@media (max-width: 768px) {
  .header {
    flex-wrap: wrap;
  }

  .navigation {
    width: 100%;
    justify-content: center;
    order: 3;
  }
}

@media (max-width: 480px) {
  .header {
    padding: var(--space-3) var(--space-4);
  }

  .navigation button,
  .theme-button,
  .logout-button {
    padding: 0 var(--space-3);
  }
}
</style>