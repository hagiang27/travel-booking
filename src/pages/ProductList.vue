<template>
  <div class="product-page">
    <main class="container">
      <section class="page-header">
        <h1>Danh sách sản phẩm</h1>
        <p>Khám phá các sản phẩm du lịch phù hợp với bạn</p>
      </section>

      <section class="filter-section">
        <div class="filter-group">
          <label for="destination">Điểm đến</label>
          <select id="destination" v-model="selectedDestination">
            <option value="">Tất cả điểm đến</option>
            <option value="HN">Hà Nội</option>
            <option value="NB">Ninh Bình</option>
            <option value="QN">Quảng Ninh</option>
          </select>
        </div>

        <div class="filter-group">
          <label for="type">Loại sản phẩm</label>
          <select id="type" v-model="selectedType">
            <option value="">Tất cả loại</option>
            <option value="tour">Tour</option>
            <option value="hotel">Khách sạn</option>
            <option value="activity">Hoạt động</option>
          </select>
        </div>

        <div class="filter-group">
          <label for="price-from">Giá từ</label>
          <input
            id="price-from"
            v-model.number="priceFrom"
            type="number"
            min="0"
            placeholder="0"
          />
        </div>

        <div class="filter-group">
          <label for="price-to">Giá đến</label>
          <input
            id="price-to"
            v-model.number="priceTo"
            type="number"
            min="0"
            placeholder="Không giới hạn"
          />
        </div>
      </section>

      <section class="result-section">
        <div class="section-heading">
          <h2>Sản phẩm</h2>
          <span>{{ filteredProducts.length }} sản phẩm</span>
        </div>

        <div v-if="filteredProducts.length" class="product-grid">
          <article
            v-for="product in filteredProducts"
            :key="product.ma_san_pham"
            class="product-card"
          >
            <div class="product-image">
              <img
                :src="product.image"
                :alt="product.ten_san_pham"
              />
            </div>

            <div class="product-content">
              <h3>{{ product.ten_san_pham }}</h3>

              <div
                class="product-rating"
                :aria-label="`Điểm đánh giá ${product.diem_danh_gia_tb} trên 5`"
              >
                ★ {{ product.diem_danh_gia_tb }}
              </div>

              <p class="product-price">
                {{ formatPrice(product.gia_co_ban) }}
              </p>

              <button type="button" class="detail-button">
                Xem chi tiết
              </button>
            </div>
          </article>
        </div>

        <div v-else class="empty-state">
          <h3>Không tìm thấy sản phẩm</h3>
          <p>Hãy thử thay đổi điều kiện tìm kiếm.</p>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const selectedDestination = ref('')
const selectedType = ref('')
const priceFrom = ref('')
const priceTo = ref('')

const products = ref([
  {
    ma_san_pham: 'SP001',
    ten_san_pham: 'Tour Hà Nội - Ninh Bình 2 ngày 1 đêm',
    gia_co_ban: 1990000,
    diem_danh_gia_tb: 4.8,
    ma_diem_den: 'NB',
    loai_san_pham: 'tour',
    image: '/images/ninh-binh.webp'
  },
  {
    ma_san_pham: 'SP002',
    ten_san_pham: 'Tour khám phá Vịnh Hạ Long',
    gia_co_ban: 2490000,
    diem_danh_gia_tb: 4.7,
    ma_diem_den: 'QN',
    loai_san_pham: 'tour',
    image: '/images/366093-tour-kham-pha-vinh-ha-long-1-ngay.jpg'
  },
  {
    ma_san_pham: 'SP003',
    ten_san_pham: 'Khách sạn trung tâm Hà Nội',
    gia_co_ban: 850000,
    diem_danh_gia_tb: 4.5,
    ma_diem_den: 'HN',
    loai_san_pham: 'hotel',
    image: '/images/pullman-hanoi-night-facade.jpg'
  }
])

const filteredProducts = computed(() => {
  return products.value.filter((product) => {
    const matchDestination =
      !selectedDestination.value ||
      product.ma_diem_den === selectedDestination.value

    const matchType =
      !selectedType.value ||
      product.loai_san_pham === selectedType.value

    const matchMinPrice =
      !priceFrom.value ||
      product.gia_co_ban >= Number(priceFrom.value)

    const matchMaxPrice =
      !priceTo.value ||
      product.gia_co_ban <= Number(priceTo.value)

    return (
      matchDestination &&
      matchType &&
      matchMinPrice &&
      matchMaxPrice
    )
  })
})

function formatPrice(price) {
  return new Intl.NumberFormat('vi-VN').format(price) + ' VNĐ'
}
</script>

<style scoped>
.product-page {
  min-height: 100vh;
  padding: var(--space-12) 0;
  background: var(--color-bg);
  color: var(--color-text);
}

.container {
  width: min(1200px, calc(100% - 32px));
  margin: 0 auto;
}

.page-header {
  margin-bottom: var(--space-8);
}

.page-header h1 {
  margin: 0 0 var(--space-2);
  font-size: var(--font-size-h1);
  font-weight: var(--font-weight-bold);
}

.page-header p {
  margin: 0;
  color: var(--color-text-secondary);
}

.filter-section {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: var(--space-4);
  padding: var(--space-6);
  margin-bottom: var(--space-8);
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.filter-group label {
  font-size: var(--font-size-small);
  font-weight: var(--font-weight-semibold);
}

.filter-group input,
.filter-group select {
  min-height: 42px;
  padding: 0 var(--space-3);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background: var(--color-bg);
  color: var(--color-text);
  font-size: var(--font-size-body);
}

.section-heading {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--space-4);
}

.section-heading h2 {
  margin: 0;
  font-size: var(--font-size-h2);
}

.section-heading span {
  color: var(--color-text-muted);
  font-size: var(--font-size-small);
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--space-6);
}

.product-card {
  overflow: hidden;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
}

.product-image {
  width: 100%;
  aspect-ratio: 16 / 10;
  overflow: hidden;
  background: var(--color-surface-2);
}

.product-image img {
  width: 100%;
  height: 100%;
  display: block;
  object-fit: cover;
}

.product-content {
  padding: var(--space-4);
}

.product-content h3 {
  min-height: 48px;
  margin: 0 0 var(--space-2);
  font-size: var(--font-size-h3);
}

.product-rating {
  margin-bottom: var(--space-2);
  color: var(--color-warning);
  font-size: var(--font-size-small);
}

.product-price {
  margin: 0 0 var(--space-4);
  color: var(--color-primary);
  font-size: var(--font-size-body);
  font-weight: var(--font-weight-semibold);
}

.detail-button {
  width: 100%;
  min-height: 42px;
  border: none;
  border-radius: var(--radius-md);
  background: var(--color-primary);
  color: var(--color-bg);
  font-size: var(--font-size-body);
  font-weight: var(--font-weight-semibold);
  cursor: pointer;
}

.detail-button:hover {
  background: var(--color-primary-hover);
}

.empty-state {
  padding: var(--space-12);
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

@media (max-width: 900px) {
  .filter-section {
    grid-template-columns: repeat(2, 1fr);
  }

  .product-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 600px) {
  .filter-section {
    grid-template-columns: 1fr;
  }

  .product-grid {
    grid-template-columns: 1fr;
  }
}
</style>