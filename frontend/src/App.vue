<template>
  <div class="app">
    <header class="header">
      <div class="brand">
        <h1>Nhà Việt</h1>
      </div>

      <nav class="navigation" aria-label="Điều hướng chính">
        <button
          type="button"
          :class="{ active: route.name === 'home' }"
          @click="router.push('/')"
        >
          Trang chủ
        </button>

        <button
          type="button"
          :class="{ active: route.name === 'list' || route.name === 'detail' }"
          @click="router.push('/san-pham')"
        >
          Sản phẩm
        </button>

        <button
          v-if="coQuyenQuanTri"
          type="button"
          :class="{ active: route.path.startsWith('/quan-tri') }"
          @click="router.push('/quan-tri/san-pham')"
        >
          Quản lý sản phẩm
        </button>

        <template v-if="user">
          <span class="nguoi-dung" :title="user.ten_dang_nhap">
            {{ user.ho_ten }}
          </span>
          <button type="button" @click="dangXuat">
            Đăng xuất
          </button>
        </template>

        <button
          v-else
          type="button"
          :class="{ active: route.name === 'login' }"
          @click="router.push('/dang-nhap')"
        >
          Đăng nhập
        </button>
      </nav>

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
    </header>

    <main>
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { authService } from './lib/authService'

const route = useRoute()
const router = useRouter()
const isDark = ref(false)
const user = ref(null)

const coQuyenQuanTri = computed(() =>
  !!user.value && ['quan_tri', 'nha_cung_cap'].includes(user.value.vai_tro)
)

async function taiNguoiDung() {
  user.value = await authService.layNguoiDungHienTai()
}

async function dangXuat() {
  await authService.dangXuat()
  user.value = null
  router.push('/dang-nhap')
}

function toggleTheme() {
  isDark.value = !isDark.value
  document.documentElement.setAttribute(
    'data-theme',
    isDark.value ? 'dark' : 'light'
  )
}

onMounted(() => {
  document.documentElement.setAttribute('data-theme', 'light')
  taiNguoiDung()
})

watch(() => route.fullPath, taiNguoiDung)
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
  align-items: center;
}

.navigation button,
.theme-button {
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
.theme-button:hover {
  background: var(--color-surface-2);
}

.navigation button:focus-visible,
.theme-button:focus-visible {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
}

.navigation button.active {
  background: var(--color-primary);
  border-color: var(--color-primary);
  color: var(--color-bg);
}

.nguoi-dung {
  font-size: var(--font-size-small);
  font-weight: var(--font-weight-semibold);
  padding: 0 var(--space-2);
}

.theme-button {
  white-space: nowrap;
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
  .theme-button {
    padding: 0 var(--space-3);
  }
}
</style>