<template>
  <div class="home-page">

    <!-- BANNER -->
    <header class="home-header">
      <img
        src="/images/banner.webp"
        alt="Du lịch Việt Nam"
        class="banner-image"
      />

      <div class="banner-overlay">
        <div class="banner-content">
          <h1>Du lịch Việt Nam</h1>
          <p>Khám phá những điểm đến hấp dẫn</p>
        </div>
      </div>
    </header>


    <!-- TÌM KIẾM -->
    <section class="search-section">
      <div class="search-box">
        <input
          v-model="searchKeyword"
          type="text"
          placeholder="Bạn muốn đi đâu?"
          @keyup.enter="searchDestination"
        />

        <button @click="searchDestination">
          Tìm kiếm
        </button>
      </div>
    </section>


    <!-- ĐIỂM ĐẾN NỔI BẬT -->
    <section class="destinations-section">

      <div class="section-header">
        <h2>Điểm đến nổi bật</h2>
        <p>Khám phá những địa điểm du lịch hấp dẫn tại Việt Nam</p>
      </div>


      <div class="destination-grid">

        <div
          v-for="destination in destinations"
          :key="destination.ma_diem_den"
          class="destination-card"
        >

          <!-- ẢNH -->
          <div class="card-image">
            <img
              :src="destination.image"
              :alt="destination.ten_diem_den"
            />
          </div>


          <!-- THÔNG TIN -->
          <div class="card-content">

            <h3>
              {{ destination.ten_diem_den }}
            </h3>

            <p class="location">
              📍 {{ destination.tinh_thanh }}
            </p>

            <p class="ticket-price">
              Giá tham quan:
              <strong>
                {{ formatPrice(destination.gia_ve_tham_quan) }}
              </strong>
            </p>

            <button
              class="detail-button"
              @click="viewDestination(destination)"
            >
              Xem chi tiết
            </button>

          </div>

        </div>

      </div>
    </section>

  </div>
</template>


<script setup>
import { ref } from 'vue'


// ==================== TÌM KIẾM ====================

const searchKeyword = ref('')


const searchDestination = () => {

  const keyword = searchKeyword.value.trim()

  if (!keyword) {
    alert('Vui lòng nhập điểm đến bạn muốn tìm kiếm!')
    return
  }

  alert('Bạn đang tìm kiếm: ' + keyword)
}


// ==================== DANH SÁCH ĐIỂM ĐẾN ====================

const destinations = ref([
  {
  ma_diem_den: 1,
  ten_diem_den: 'Tràng An',
  tinh_thanh: 'Ninh Bình',
  gia_ve_tham_quan: 250000,
  image: '/images/trangan.jpg'
},

  {
    ma_diem_den: 2,
    ten_diem_den: 'Vịnh Hạ Long',
    tinh_thanh: 'Quảng Ninh',
    gia_ve_tham_quan: 290000,
    image: '/images/ha-long.jpg'
  },

  {
    ma_diem_den: 3,
    ten_diem_den: 'Phố cổ Hội An',
    tinh_thanh: 'Quảng Nam',
    gia_ve_tham_quan: 120000,
    image: '/images/hoi-an.jpg'
  }
])


// ==================== FORMAT GIÁ ====================

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN').format(price) + ' VNĐ'
}


// ==================== XEM CHI TIẾT ====================

const viewDestination = (destination) => {

  alert(
    'Bạn đã chọn điểm đến: ' +
    destination.ten_diem_den
  )

}
</script>


<style scoped>

/* ==================== TOÀN TRANG ==================== */

.home-page {
  width: 100%;
  min-height: 100vh;
}


/* ==================== BANNER ==================== */

.home-header {
  position: relative;
  width: 100%;
  height: 420px;
  overflow: hidden;
}

.banner-image {
  position: absolute;

  top: 0;
  left: 0;

  width: 100%;
  height: 100%;

  object-fit: cover;

  display: block;
}

.banner-overlay {
  position: absolute;

  top: 0;
  left: 0;

  width: 100%;
  height: 100%;

  background: rgba(0, 0, 0, 0.35);

  display: flex;
  align-items: center;
  justify-content: center;
}

.banner-content {
  text-align: center;

  color: white;

  padding: 20px;
}

.banner-content h1 {
  margin: 0 0 28px;

  font-size: clamp(38px, 6vw, 68px);

  font-weight: 700;

  color: white;

  text-shadow:
    0 2px 8px rgba(0, 0, 0, 0.6);
}

.banner-content p {
  margin: 0;

  font-size: clamp(18px, 2.5vw, 28px);

  color: white;

  text-shadow:
    0 2px 6px rgba(0, 0, 0, 0.6);
}


/* ==================== SEARCH ==================== */

.search-section {
  padding: 30px 20px;

  background: var(--color-surface, #ffffff);
}

.search-box {
  max-width: 850px;

  margin: 0 auto;

  display: flex;

  gap: 12px;
}

.search-box input {
  flex: 1;

  height: 52px;

  padding: 0 18px;

  border: 1px solid #ddd;

  border-radius: 8px;

  font-size: 16px;

  outline: none;
}

.search-box input:focus {
  border-color: #42b883;
}

.search-box button {
  height: 52px;

  padding: 0 28px;

  border: none;

  border-radius: 8px;

  background: #42b883;

  color: white;

  font-size: 16px;

  font-weight: 600;

  cursor: pointer;

  transition: 0.2s;
}

.search-box button:hover {
  background: #369f6e;
}


/* ==================== ĐIỂM ĐẾN ==================== */

.destinations-section {
  max-width: 1200px;

  margin: 0 auto;

  padding: 50px 20px 70px;
}

.section-header {
  text-align: center;

  margin-bottom: 35px;
}

.section-header h2 {
  margin: 0 0 10px;

  font-size: 32px;

  font-weight: 700;
}

.section-header p {
  margin: 0;

  color: #777;

  font-size: 16px;
}


/* ==================== GRID ==================== */

.destination-grid {
  display: grid;

  grid-template-columns:
    repeat(3, 1fr);

  gap: 25px;
}


/* ==================== CARD ==================== */

.destination-card {
  overflow: hidden;

  background: white;

  border-radius: 12px;

  box-shadow:
    0 4px 15px rgba(0, 0, 0, 0.08);

  transition:
    transform 0.3s ease,
    box-shadow 0.3s ease;
}

.destination-card:hover {
  transform: translateY(-5px);

  box-shadow:
    0 8px 25px rgba(0, 0, 0, 0.12);
}


/* ==================== CARD IMAGE ==================== */

.card-image {
  width: 100%;

  aspect-ratio: 16 / 10;

  overflow: hidden;

  background: #f3f3f3;
}

.card-image img {
  width: 100%;

  height: 100%;

  display: block;

  object-fit: cover;

  transition:
    transform 0.3s ease;
}

.destination-card:hover
.card-image img {
  transform: scale(1.05);
}


/* ==================== CARD CONTENT ==================== */

.card-content {
  padding: 20px;
}

.card-content h3 {
  margin: 0 0 10px;

  font-size: 21px;

  font-weight: 700;
}

.location {
  margin: 0 0 12px;

  color: #666;

  font-size: 15px;
}

.ticket-price {
  margin: 0 0 18px;

  color: #666;

  font-size: 14px;
}

.ticket-price strong {
  color: #42b883;

  font-size: 16px;
}


/* ==================== BUTTON ==================== */

.detail-button {
  width: 100%;

  height: 42px;

  border: none;

  border-radius: 7px;

  background: #42b883;

  color: white;

  font-size: 15px;

  font-weight: 600;

  cursor: pointer;

  transition: 0.2s;
}

.detail-button:hover {
  background: #369f6e;
}


/* ==================== RESPONSIVE ==================== */

@media (max-width: 800px) {

  .home-header {
    height: 350px;
  }

  .destination-grid {
    grid-template-columns: 1fr;
  }

  .search-box {
    flex-direction: column;
  }

  .search-box button {
    width: 100%;
  }

}

</style>