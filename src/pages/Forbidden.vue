<template>
  <section class="state-page" role="alert">
    <h1>403 – Không có quyền truy cập</h1>
    <p>Tài khoản của bạn không được phép xem trang này.</p>
    <a class="state-link" href="#/home">Về trang chủ</a>
  </section>
</template>

<style scoped>
.state-page {
  max-width: 560px;
  margin: var(--space-8) auto;
  padding: var(--space-8) var(--space-6);
  text-align: center;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  color: var(--color-text);
}

.state-page h1 {
  margin: 0 0 var(--space-3);
  font-size: var(--font-size-h2);
}

.state-page p {
  margin: 0 0 var(--space-6);
  color: var(--color-text-secondary);
}

.state-link {
  display: inline-block;
  padding: var(--space-3) var(--space-6);
  border-radius: var(--radius-md);
  background: var(--color-primary);
  color: var(--color-bg);
  font-weight: var(--font-weight-semibold);
  text-decoration: none;
}

.state-link:hover {
  background: var(--color-primary-hover);
}

.state-link:focus-visible {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
}
</style>