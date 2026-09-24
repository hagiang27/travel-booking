<template>
  <div class="detail-page">
    <main class="container">
      <section class="back-section">
        <button
          type="button"
          class="back-button"
          aria-label="Quay lại danh sách sản phẩm"
        >
          ← Quay lại danh sách
        </button>
      </section>

      <section class="product-detail">
        <div class="product-image">
          Ảnh sản phẩm
        </div>

        <div class="product-info">
          <p class="product-code">
            Mã sản phẩm: {{ product.ma_san_pham }}
          </p>

          <h1>{{ product.ten_san_pham }}</h1>

          <p class="product-price">
            {{ formatPrice(product.gia_co_ban) }}
          </p>

          <div class="policy-box">
            <h2>Chính sách hủy</h2>

            <p>
              {{ product.chinh_sach_huy }}
            </p>
          </div>

          <button type="button" class="booking-button">
            Chọn lịch khởi hành
          </button>
        </div>
      </section>

      <section class="schedule-section">
        <div class="section-heading">
          <h2>Lịch khởi hành còn chỗ</h2>
        </div>

        <div
          v-if="schedules.length"
          class="schedule-list"
        >
          <div
            v-for="schedule in schedules"
            :key="schedule.ma_lich"
            class="schedule-item"
          >
            <div>
              <span class="schedule-label">
                Ngày khởi hành
              </span>

              <strong>
                {{ schedule.ngay_khoi_hanh }}
              </strong>
            </div>

            <div>
              <span class="schedule-label">
                Còn lại
              </span>

              <strong>
                {{ schedule.con_lai }} chỗ
              </strong>
            </div>

            <button type="button" class="select-button">
              Chọn
            </button>
          </div>
        </div>

        <div
          v-else
          class="empty-state"
        >
          <h3>Chưa có lịch khởi hành</h3>
          <p>Vui lòng quay lại sau.</p>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const product = ref({
  ma_san_pham: 'SP001',
  ten_san_pham: 'Tour Hà Nội - Ninh Bình 2 ngày 1 đêm',
  gia_co_ban: 1990000,
  chinh_sach_huy: 'Có thể hủy theo điều kiện của sản phẩm.'
})

const schedules = ref([
  {
    ma_lich: 'L001',
    ngay_khoi_hanh: '25/09/2026',
    con_lai: 8
  },
  {
    ma_lich: 'L002',
    ngay_khoi_hanh: '02/10/2026',
    con_lai: 5
  },
  {
    ma_lich: 'L003',
    ngay_khoi_hanh: '09/10/2026',
    con_lai: 12
  }
])

function formatPrice(price) {
  return new Intl.NumberFormat('vi-VN').format(price) + ' VNĐ'
}
</script>

<style scoped>
.detail-page {
  min-height: 100vh;
  padding: var(--space-8) 0 var(--space-12);
  background: var(--color-bg);
  color: var(--color-text);
}

.container {
  width: min(1200px, calc(100% - 32px));
  margin: 0 auto;
}

.back-section {
  margin-bottom: var(--space-6);
}

.back-button {
  padding: 0;
  border: none;
  background: none;
  color: var(--color-primary);
  font-size: var(--font-size-body);
  cursor: pointer;
}

.product-detail {
  display: grid;
  grid-template-columns: 1.1fr 1fr;
  gap: var(--space-8);
  margin-bottom: var(--space-10);
}

.product-image {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 420px;
  border-radius: var(--radius-lg);
  background: var(--color-surface-2);
  color: var(--color-text-muted);
}

.product-info {
  padding: var(--space-4) 0;
}

.product-code {
  margin: 0 0 var(--space-3);
  color: var(--color-text-muted);
  font-size: var(--font-size-small);
}

.product-info h1 {
  margin: 0 0 var(--space-4);
  font-size: var(--font-size-h1);
  line-height: 1.2;
}

.product-price {
  margin: 0 0 var(--space-6);
  color: var(--color-primary);
  font-size: var(--font-size-h2);
  font-weight: var(--font-weight-bold);
}

.policy-box {
  padding: var(--space-4);
  margin-bottom: var(--space-6);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  background: var(--color-surface);
}

.policy-box h2 {
  margin: 0 0 var(--space-2);
  font-size: var(--font-size-h3);
}

.policy-box p {
  margin: 0;
  color: var(--color-text-secondary);
  line-height: 1.6;
}

.booking-button {
  min-height: 44px;
  padding: 0 var(--space-6);
  border: none;
  border-radius: var(--radius-md);
  background: var(--color-primary);
  color: var(--color-bg);
  font-size: var(--font-size-body);
  font-weight: var(--font-weight-semibold);
  cursor: pointer;
}

.booking-button:hover {
  background: var(--color-primary-hover);
}

.schedule-section {
  padding-top: var(--space-6);
  border-top: 1px solid var(--color-border);
}

.section-heading {
  margin-bottom: var(--space-4);
}

.section-heading h2 {
  margin: 0;
  font-size: var(--font-size-h2);
}

.schedule-list {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
}

.schedule-item {
  display: grid;
  grid-template-columns: 1fr 1fr auto;
  align-items: center;
  gap: var(--space-4);
  padding: var(--space-4);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  background: var(--color-surface);
}

.schedule-item > div {
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
}

.schedule-label {
  color: var(--color-text-muted);
  font-size: var(--font-size-small);
}

.select-button {
  min-height: 40px;
  padding: 0 var(--space-4);
  border: 1px solid var(--color-primary);
  border-radius: var(--radius-md);
  background: transparent;
  color: var(--color-primary);
  font-size: var(--font-size-small);
  font-weight: var(--font-weight-semibold);
  cursor: pointer;
}

.select-button:hover {
  background: var(--color-primary);
  color: var(--color-bg);
}

.empty-state {
  padding: var(--space-8);
  text-align: center;
  border: 1px dashed var(--color-border);
  border-radius: var(--radius-lg);
  background: var(--color-surface);
}

.empty-state h3 {
  margin: 0 0 var(--space-2);
}

.empty-state p {
  margin: 0;
  color: var(--color-text-secondary);
}

@media (max-width: 768px) {
  .product-detail {
    grid-template-columns: 1fr;
  }

  .product-image {
    min-height: 300px;
  }
}

@media (max-width: 600px) {
  .schedule-item {
    grid-template-columns: 1fr;
  }

  .product-info h1 {
    font-size: var(--font-size-h2);
  }
}
</style>