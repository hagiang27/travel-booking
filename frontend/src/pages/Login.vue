<template>
  <div class="login-page">
    <div class="login-card">
      <div class="login-header">
        <h1>Đăng nhập</h1>
        <p>Đăng nhập để tiếp tục sử dụng Nhà Việt</p>
      </div>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label for="username">
            Tên đăng nhập
          </label>

          <input
            id="username"
            v-model="username"
            type="text"
            autocomplete="username"
            placeholder="Nhập tên đăng nhập"
            required
          />
        </div>

        <div class="form-group">
          <label for="password">
            Mật khẩu
          </label>

          <input
            id="password"
            v-model="password"
            type="password"
            autocomplete="current-password"
            placeholder="Nhập mật khẩu"
            required
          />
        </div>

        <div
          v-if="message"
          class="login-message"
          role="status"
        >
          {{ message }}
        </div>

        <button
          type="submit"
          class="login-button"
        >
          Đăng nhập
        </button>
      </form>

      <div class="login-footer">
        <p>
          Chưa có tài khoản?
          <button type="button" class="register-link">
            Đăng ký
          </button>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const username = ref('')
const password = ref('')
const message = ref('')

function handleLogin() {
  message.value = 'Đang chờ kết nối API đăng nhập của hệ thống.'
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: var(--space-8) var(--space-4);
  background: var(--color-bg);
  color: var(--color-text);
}

.login-card {
  width: min(420px, 100%);
  padding: var(--space-8);
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
}

.login-header {
  margin-bottom: var(--space-6);
  text-align: center;
}

.login-header h1 {
  margin: 0 0 var(--space-2);
  font-size: var(--font-size-h1);
  font-weight: var(--font-weight-bold);
}

.login-header p {
  margin: 0;
  color: var(--color-text-secondary);
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.form-group label {
  font-size: var(--font-size-small);
  font-weight: var(--font-weight-semibold);
}

.form-group input {
  width: 100%;
  min-height: 44px;
  box-sizing: border-box;
  padding: 0 var(--space-4);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background: var(--color-bg);
  color: var(--color-text);
  font-size: var(--font-size-body);
}

.form-group input::placeholder {
  color: var(--color-text-muted);
}

.form-group input:focus-visible {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
}

.login-message {
  padding: var(--space-3);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background: var(--color-surface-2);
  color: var(--color-text-secondary);
  font-size: var(--font-size-small);
  line-height: 1.5;
}

.login-button {
  width: 100%;
  min-height: 44px;
  border: none;
  border-radius: var(--radius-md);
  background: var(--color-primary);
  color: var(--color-bg);
  font-size: var(--font-size-body);
  font-weight: var(--font-weight-semibold);
  cursor: pointer;
}

.login-button:hover {
  background: var(--color-primary-hover);
}

.login-button:focus-visible {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
}

.login-footer {
  margin-top: var(--space-6);
  text-align: center;
}

.login-footer p {
  margin: 0;
  color: var(--color-text-secondary);
  font-size: var(--font-size-small);
}

.register-link {
  padding: 0;
  border: none;
  background: transparent;
  color: var(--color-primary);
  font-size: var(--font-size-small);
  font-weight: var(--font-weight-semibold);
  cursor: pointer;
}

.register-link:hover {
  color: var(--color-primary-hover);
}

.register-link:focus-visible {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
}

@media (max-width: 480px) {
  .login-card {
    padding: var(--space-6);
  }

  .login-header h1 {
    font-size: var(--font-size-h2);
  }
}
</style>