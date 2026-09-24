<template>
  <div class="home-page">
    <header class="home-header">
      <div class="container">
        <h1>Du lịch Việt Nam</h1>
        <p>Khám phá những điểm đến hấp dẫn</p>
      </div>
    </header>

    <main class="container">
      <section class="search-section">
        <h2>Tìm kiếm điểm đến</h2>

        <div class="search-box">
          <label for="destination-search" class="sr-only">
            Tìm kiếm điểm đến
          </label>

          <input
            id="destination-search"
            v-model="search"
            type="text"
            placeholder="Nhập tên điểm đến..."
          />

          <button type="button">
            Tìm kiếm
          </button>
        </div>
      </section>

      <section class="destination-section">
        <div class="section-heading">
          <h2>Điểm đến nổi bật</h2>
          <span>{{ filteredDestinations.length }} điểm đến</span>
        </div>

        <div v-if="filteredDestinations.length" class="destination-grid">
          <article
            v-for="destination in filteredDestinations"
            :key="destination.ma_diem_den"
            class="destination-card"
          >
            <div class="card-image">
              Ảnh điểm đến
            </div>

            <div class="card-content">
              <h3>{{ destination.ten_diem_den }}</h3>

              <p class="province">
                {{ destination.tinh_thanh }}
              </p>

              <p class="price">
                {{ formatPrice(destination.gia_ve_tham_quan) }}
              </p>
            </div>
          </article>
        </div>

        <div v-else class="empty-state">
          <h3>Không tìm thấy điểm đến</h3>
          <p>Hãy thử nhập tên điểm đến hoặc tỉnh thành khác.</p>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const search = ref('')

const destinations = ref([
  {
    ma_diem_den: 1,
    ten_diem_den: 'Tràng An',
    tinh_thanh: 'Ninh Bình',
    gia_ve_tham_quan: 250000
  },
  {
    ma_diem_den: 2,
    ten_diem_den: 'Vịnh Hạ Long',
    tinh_thanh: 'Quảng Ninh',
    gia_ve_tham_quan: 290000
  },
  {
    ma_diem_den: 3,
    ten_diem_den: 'Phố cổ Hội An',
    tinh_thanh: 'Quảng Nam',
    gia_ve_tham_quan: 120000
  }
])

const filteredDestinations = computed(() => {
  const keyword = search.value.trim().toLowerCase()

  if (!keyword) {
    return destinations.value
  }

  return destinations.value.filter((destination) => {
    return (
      destination.ten_diem_den.toLowerCase().includes(keyword) ||
      destination.tinh_thanh.toLowerCase().includes(keyword)
    )
  })
})

function formatPrice(price) {
  return new Intl.NumberFormat('vi-VN').format(price) + ' VNĐ'
}
</script>

<style scoped>
.home-page {
  min-height: 100vh;
  background: var(--color-bg);
  color: var(--color-text);
}

.container {
  width: min(1200px, calc(100% - 32px));
  margin: 0 auto;
}

.home-header {
  padding: var(--space-12) 0;
  background: var(--color-surface);
  border-bottom: 1px solid var(--color-border);
}

.home-header h1 {
  margin: 0 0 var(--space-2);
  font-size: var(--font-size-h1);
  font-weight: var(--font-weight-bold);
}

.home-header p {
  margin: 0;
  color: var(--color-text-secondary);
}

.search-section {
  padding: var(--space-8) 0;
}

.search-section h2,
.destination-section h2 {
  margin: 0;
  font-size: var(--font-size-h2);
}

.search-box {
  display: flex;
  gap: var(--space-3);
  margin-top: var(--space-4);
}

.search-box input {
  flex: 1;
  min-height: 44px;
  padding: 0 var(--space-4);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background: var(--color-surface);
  color: var(--color-text);
  font-size: var(--font-size-body);
}

.search-box input:focus-visible {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
}

.search-box button {
  padding: 0 var(--space-6);
  border: none;
  border-radius: var(--radius-md);
  background: var(--color-primary);
  color: var(--color-bg);
  font-size: var(--font-size-body);
  font-weight: var(--font-weight-semibold);
  cursor: pointer;
}

.search-box button:hover {
  background: var(--color-primary-hover);
}

.search-box button:focus-visible {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
}

.destination-section {
  padding-bottom: var(--space-12);
}

.section-heading {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--space-4);
}

.section-heading span {
  color: var(--color-text-muted);
  font-size: var(--font-size-small);
}

.destination-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--space-6);
}

.destination-card {
  overflow: hidden;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  background: var(--color-surface);
}

.card-image {
  display: flex;
  align-items: center;
  justify-content: center;
  aspect-ratio: 16 / 10;
  background: var(--color-surface-2);
  color: var(--color-text-muted);
}

.card-content {
  padding: var(--space-4);
}

.card-content h3 {
  margin: 0 0 var(--space-2);
  font-size: var(--font-size-h3);
}

.province {
  margin: 0 0 var(--space-2);
  color: var(--color-text-secondary);
}

.price {
  margin: 0;
  color: var(--color-primary);
  font-weight: var(--font-weight-semibold);
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

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

@media (max-width: 768px) {
  .destination-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .search-box {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  .destination-grid {
    grid-template-columns: 1fr;
  }
}
</style>