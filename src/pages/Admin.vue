<template>
  <section class="admin-page">
    <h1>Khu vực quản trị</h1>
    <p>Xin chào {{ currentUser?.ho_ten }}. Đây là trang chỉ dành cho quản trị viên.</p>

    <button type="button" class="check-button" :disabled="checking" @click="checkServer">
      {{ checking ? 'Đang kiểm tra...' : 'Kiểm tra quyền với máy chủ' }}
    </button>

    <p
      v-if="result"
      :class="['result', { 'result--error': resultIsError }]"
      :role="resultIsError ? 'alert' : 'status'"
    >
      {{ result }}
    </p>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { currentUser } from '../services/auth'
import { checkAdminAccess } from '../services/api'

const checking = ref(false)
const result = ref('')
const resultIsError = ref(false)

async function checkServer() {
  checking.value = true
  result.value = ''
  try {
    const data = await checkAdminAccess()
    resultIsError.value = false
    result.value = data.message || 'Máy chủ xác nhận bạn có quyền quản trị.'
  } catch (e) {
    resultIsError.value = true
    result.value =
      e.status === 403
        ? 'Máy chủ từ chối (403): không có quyền thực hiện.'
        : e.message
  } finally {
    checking.value = false
  }
}
</script>

<style scoped>
.admin-page {
  max-width: 720px;
  margin: var(--space-8) auto;
  padding: var(--space-6);
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  color: var(--color-text);
}

.admin-page h1 {
  margin: 0 0 var(--space-3);
  font-size: var(--font-size-h2);
}

.admin-page p {
  color: var(--color-text-secondary);
}

.check-button {
  min-height: 44px;
  padding: 0 var(--space-6);
  border: none;
  border-radius: var(--radius-md);
  background: var(--color-primary);
  color: var(--color-bg);
  font-weight: var(--font-weight-semibold);
  cursor: pointer;
}

.check-button:hover {
  background: var(--color-primary-hover);
}

.check-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.check-button:focus-visible {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
}

.result {
  margin-top: var(--space-4);
  padding: var(--space-3);
  border: 1px solid var(--color-success);
  border-radius: var(--radius-md);
  color: var(--color-success);
}

.result--error {
  border-color: var(--color-error);
  color: var(--color-error);
}
</style>