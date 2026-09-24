<template>
  <div class="app">
    <header class="header">
      <h1>Nhà Việt</h1>

      <button class="theme-button" @click="toggleTheme">
        {{ isDark ? '☀️ Light' : '🌙 Dark' }}
      </button>
    </header>

    <main class="container">
      <section class="card">
        <h2>Design Token Test</h2>

        <p>
          Giao diện sử dụng hệ thống Design Tokens của Buổi 4.
        </p>

        <button class="primary-button">
          Xem sản phẩm
        </button>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const isDark = ref(false)

function toggleTheme() {
  isDark.value = !isDark.value

  document.documentElement.setAttribute(
    'data-theme',
    isDark.value ? 'dark' : 'light'
  )
}

onMounted(() => {
  document.documentElement.setAttribute('data-theme', 'light')
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
  justify-content: space-between;
  align-items: center;
  padding: var(--space-4) var(--space-8);
  border-bottom: 1px solid var(--color-border);
}

.container {
  max-width: 1000px;
  margin: 0 auto;
  padding: var(--space-8);
}

.card {
  padding: var(--space-8);
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
}

h1,
h2 {
  margin-top: 0;
}

p {
  color: var(--color-text-secondary);
  font-size: var(--font-size-md);
}

.primary-button,
.theme-button {
  padding: var(--space-3) var(--space-4);
  border-radius: var(--radius-md);
  cursor: pointer;
}

.primary-button {
  margin-top: var(--space-4);
  border: none;
  background: var(--color-primary);
  color: white;
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-semibold);
}

.primary-button:hover {
  background: var(--color-primary-hover);
}

.theme-button {
  border: 1px solid var(--color-border);
  background: var(--color-surface);
  color: var(--color-text);
}
</style>