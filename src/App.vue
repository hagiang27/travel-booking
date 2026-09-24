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
          @click="currentPage = 'home'"
        >
          Trang chủ
        </button>

        <button
          type="button"
          :class="{ active: currentPage === 'products' }"
          @click="currentPage = 'products'"
        >
          Sản phẩm
        </button>

        <button
          type="button"
          :class="{ active: currentPage === 'detail' }"
          @click="currentPage = 'detail'"
        >
          Chi tiết
        </button>

        <button
          type="button"
          :class="{ active: currentPage === 'login' }"
          @click="currentPage = 'login'"
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
      <Home v-if="currentPage === 'home'" />

      <ProductList
        v-else-if="currentPage === 'products'"
      />

      <ProductDetail
        v-else-if="currentPage === 'detail'"
      />

      <Login v-else />
    </main>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'

import Home from './pages/Home.vue'
import ProductList from './pages/ProductList.vue'
import ProductDetail from './pages/ProductDetail.vue'
import Login from './pages/Login.vue'

const currentPage = ref('home')
const isDark = ref(false)

function toggleTheme() {
  isDark.value = !isDark.value

  document.documentElement.setAttribute(
    'data-theme',
    isDark.value ? 'dark' : 'light'
  )
}

onMounted(() => {
  document.documentElement.setAttribute(
    'data-theme',
    'light'
  )
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