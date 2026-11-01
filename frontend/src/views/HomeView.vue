<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { productService } from '../lib/productService'

const router = useRouter()

const diemDen = ref('')
const loai = ref('')
const ngayDi = ref('')
const soNguoi = ref(2)

const DIEM_DEN = [
  { ma: 'HN', ten: 'Hà Nội' },
  { ma: 'NB', ten: 'Ninh Bình' },
  { ma: 'QN', ten: 'Quảng Ninh' },
]

const LOAI_DICH_VU = [
  { ma: 'tour', ten: 'Tour', icon: '🧭', mota: 'Tour ghép và tour riêng' },
  { ma: 'stay', ten: 'Lưu trú', icon: '🏨', mota: 'Khách sạn, homestay' },
  { ma: 'transfer', ten: 'Đưa đón', icon: '🚐', mota: 'Xe đưa đón sân bay' },
  { ma: 'ticket', ten: 'Vé', icon: '🎫', mota: 'Vé tham quan, vé show' },
  { ma: 'experience', ten: 'Trải nghiệm', icon: '🌿', mota: 'Hoạt động địa phương' },
]

const LY_DO = [
  {
    icon: '⚖️',
    tieu_de: 'So sánh giá minh bạch',
    noi_dung: 'Xem cùng lúc giá của nhiều nhà cung cấp, đã gồm phụ phí và thuế, sắp xếp từ thấp đến cao.',
  },
  {
    icon: '🏪',
    tieu_de: 'Nhiều nhà cung cấp',
    noi_dung: 'Một nơi để đặt tour, khách sạn, xe và vé từ nhiều đối tác khác nhau.',
  },
  {
    icon: '⏱️',
    tieu_de: 'Giữ chỗ 15 phút',
    noi_dung: 'Chỗ được giữ trong 15 phút để bạn hoàn tất đặt. Quá hạn, chỗ tự động trả lại cho người khác.',
  },
]

const CACH_DUNG = [
  { so: '1', tieu_de: 'Tìm dịch vụ', noi_dung: 'Chọn điểm đến, loại dịch vụ và ngày đi.' },
  { so: '2', tieu_de: 'So sánh', noi_dung: 'Xem giá và điều kiện của từng nhà cung cấp.' },
  { so: '3', tieu_de: 'Giữ chỗ và đặt', noi_dung: 'Giữ chỗ trong 15 phút và hoàn tất thanh toán.' },
]

const sanPhamNoiBat = ref([])
const dangTai = ref(true)
const loiTai = ref('')

function layTen(sp) {
  return sp.ten_san_pham ?? sp.ten ?? sp.ma_san_pham
}

function layGia(sp) {
  const gia = sp.gia_tu ?? sp.gia
  if (gia == null) return ''
  return Number(gia).toLocaleString('vi-VN') + ' đ'
}

function timKiem() {
  const query = {}
  if (diemDen.value) query.diem_den = diemDen.value
  if (loai.value) query.loai = loai.value
  router.push({ name: 'list', query })
}

function chonLoai(ma) {
  router.push({ name: 'list', query: { loai: ma } })
}

onMounted(async () => {
  try {
    const kq = await productService.timKiemCongKhai({})
    const ds = kq?.data ?? kq ?? []
    sanPhamNoiBat.value = ds.slice(0, 6)
  } catch (e) {
    loiTai.value = 'Chưa tải được sản phẩm nổi bật. Thử lại sau.'
  } finally {
    dangTai.value = false
  }
})
</script>

<template>
  <main class="home">
    <!-- Hero + tìm kiếm -->
    <section class="hero">
      <div class="hero-noi-dung">
        <p class="hero-nhan">Nền tảng đặt dịch vụ du lịch đa nhà cung cấp</p>
        <h1>Đặt tour, khách sạn, xe và vé. So sánh giá trước khi chọn.</h1>
        <p class="hero-mo-ta">
          Giá hiển thị đã gồm phụ phí và thuế, để bạn so sánh đúng và không phát sinh chi phí ngoài dự kiến.
        </p>

        <form class="form-tim" @submit.prevent="timKiem">
          <label>
            <span>Điểm đến</span>
            <select v-model="diemDen">
              <option value="">Tất cả</option>
              <option v-for="d in DIEM_DEN" :key="d.ma" :value="d.ma">{{ d.ten }}</option>
            </select>
          </label>
          <label>
            <span>Loại dịch vụ</span>
            <select v-model="loai">
              <option value="">Tất cả</option>
              <option v-for="l in LOAI_DICH_VU" :key="l.ma" :value="l.ma">{{ l.ten }}</option>
            </select>
          </label>
          <label>
            <span>Ngày đi</span>
            <input type="date" v-model="ngayDi" />
          </label>
          <label>
            <span>Số người</span>
            <input type="number" min="1" max="20" v-model.number="soNguoi" />
          </label>
          <button type="submit" class="btn-chinh">Tìm dịch vụ</button>
        </form>
      </div>
    </section>

    <!-- Loại dịch vụ -->
    <section class="khoi">
      <h2>Chọn loại dịch vụ</h2>
      <div class="loai-grid">
        <button
          v-for="l in LOAI_DICH_VU"
          :key="l.ma"
          class="loai-the"
          type="button"
          @click="chonLoai(l.ma)"
        >
          <span class="loai-icon">{{ l.icon }}</span>
          <strong>{{ l.ten }}</strong>
          <small>{{ l.mota }}</small>
        </button>
      </div>
    </section>

    <!-- Lý do chọn -->
    <section class="khoi khoi-nen">
      <h2>Vì sao nên đặt qua nền tảng này</h2>
      <div class="ly-do-grid">
        <article v-for="ly in LY_DO" :key="ly.tieu_de" class="ly-do-the">
          <span class="ly-do-icon">{{ ly.icon }}</span>
          <h3>{{ ly.tieu_de }}</h3>
          <p>{{ ly.noi_dung }}</p>
        </article>
      </div>
    </section>

    <!-- Sản phẩm nổi bật -->
    <section class="khoi">
      <div class="khoi-tieu-de">
        <h2>Sản phẩm nổi bật</h2>
        <RouterLink :to="{ name: 'list' }" class="xem-them">Xem tất cả →</RouterLink>
      </div>

      <p v-if="dangTai" class="trang-thai">Đang tải sản phẩm...</p>
      <p v-else-if="loiTai" class="trang-thai loi">{{ loiTai }}</p>
      <p v-else-if="sanPhamNoiBat.length === 0" class="trang-thai">Chưa có sản phẩm nào.</p>

      <div v-else class="sp-grid">
        <RouterLink
          v-for="sp in sanPhamNoiBat"
          :key="sp.ma_san_pham"
          :to="`/san-pham/${sp.ma_san_pham}`"
          class="sp-the"
        >
          <div class="sp-anh">{{ sp.ma_san_pham }}</div>
          <div class="sp-noi-dung">
            <h3>{{ layTen(sp) }}</h3>
            <p v-if="sp.diem_danh_gia_tb > 0" class="sp-danh-gia">★ {{ sp.diem_danh_gia_tb }}</p>
            <p class="sp-gia">
              <span v-if="layGia(sp)">Từ <strong>{{ layGia(sp) }}</strong></span>
              <span v-else>Xem giá</span>
            </p>
            <span class="sp-so-sanh">So sánh nhà cung cấp →</span>
          </div>
        </RouterLink>
      </div>
    </section>

    <!-- Cách hoạt động -->
    <section class="khoi khoi-nen">
      <h2>Cách đặt dịch vụ</h2>
      <ol class="buoc-grid">
        <li v-for="b in CACH_DUNG" :key="b.so" class="buoc-the">
          <span class="buoc-so">{{ b.so }}</span>
          <h3>{{ b.tieu_de }}</h3>
          <p>{{ b.noi_dung }}</p>
        </li>
      </ol>
    </section>

    <!-- Kêu gọi hành động -->
    <section class="khoi">
      <div class="cta">
        <div>
          <h2>Bắt đầu chuyến đi của bạn</h2>
          <p>Xem danh sách dịch vụ hoặc so sánh giá ngay bây giờ.</p>
        </div>
        <RouterLink :to="{ name: 'list' }" class="btn-chinh">Xem sản phẩm</RouterLink>
      </div>
    </section>
  </main>
</template>

<style scoped>
.home {
  display: flex;
  flex-direction: column;
  gap: 0;
  color: var(--text, #1f2937);
}

.khoi {
  max-width: 1120px;
  width: 100%;
  margin: 0 auto;
  padding: 40px 16px;
  box-sizing: border-box;
}

.khoi-nen {
  background: var(--surface, #f5f7fb);
  max-width: none;
  padding-left: 16px;
  padding-right: 16px;
}

.khoi-nen > * {
  max-width: 1120px;
  margin-left: auto;
  margin-right: auto;
}

h2 {
  font-size: 1.5rem;
  margin: 0 0 20px;
}

/* Hero */
.hero {
  background:
    linear-gradient(to bottom, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0.5)),
    url('/hero-banner.webp') center / cover no-repeat;
  color: #fff;
  padding: 120px 16px 150px;
  min-height: 520px;
  display: flex;
  align-items: center;
}

.hero-noi-dung {
  max-width: 1120px;
  width: 100%;
  margin: 0 auto;
  text-align: center;
}

.hero-nhan {
  text-transform: uppercase;
  letter-spacing: 0.08em;
  font-size: 0.9rem;
  color: #fff;
  opacity: 0.95;
  margin: 0 0 16px;
  text-shadow: 0 1px 4px rgba(0, 0, 0, 0.5);
}

.hero h1 {
  font-size: clamp(2rem, 4.5vw, 3.2rem);
  line-height: 1.15;
  margin: 0 auto 16px;
  color: #fff;
  max-width: 820px;
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
}

.hero-mo-ta {
  max-width: 680px;
  margin: 0 auto 32px;
  color: #fff;
  font-size: 1.05rem;
  opacity: 0.95;
  text-shadow: 0 1px 6px rgba(0, 0, 0, 0.5);
}
/* Sửa cỡ chữ hero và khung tìm kiếm */
.hero h1 {
  font-size: clamp(1.8rem, 3.6vw, 2.6rem);
  max-width: 780px;
  margin-left: auto;
  margin-right: auto;
}

.hero-mo-ta {
  font-size: 1rem;
}

.form-tim {
  max-width: 980px;
  margin: 0 auto;
  background: #fff;
  color: #1f2937;
  border-radius: 14px;
  padding: 18px;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 12px;
  align-items: end;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.25);
  text-align: left;
}

.form-tim label {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 0.85rem;
  font-weight: 600;
}

.form-tim select,
.form-tim input {
  width: 100%;
  box-sizing: border-box;
  padding: 10px 12px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  font-size: 0.95rem;
  background: #fff;
  color: #1f2937;
  min-width: 0;
}

.btn-chinh {
  background: #f97316;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 11px 18px;
  font-weight: 700;
  font-size: 0.95rem;
  cursor: pointer;
  text-align: center;
  display: inline-block;
}

.btn-chinh:hover {
  background: #ea580c;
}

/* Hero */
.hero {
  width: auto;
  max-width: 1200px;
  margin: 0 auto;
  min-height: 0;
  padding: 96px 24px 40px;
  border-radius: 16px;
}

.hero-noi-dung {
  max-width: 1280px;
}

.hero h1 {
  font-size: clamp(2rem, 3.8vw, 3.2rem);
  max-width: 1000px;
}

.hero-mo-ta {
  max-width: 800px;
}

.form-tim {
  max-width: 1200px;
}


/* Loại dịch vụ */
.loai-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
  gap: 14px;
}

.loai-the {
  background: var(--card, #fff);
  color: inherit;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 20px 16px;
  text-align: left;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  gap: 6px;
  transition: transform 0.15s, box-shadow 0.15s;
}

.loai-the:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
}

.loai-icon {
  font-size: 1.8rem;
}

.loai-the small {
  opacity: 0.7;
}

/* Lý do */
.ly-do-grid,
.buoc-grid {
  list-style: none;
  padding: 0;
  margin: 0;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 16px;
}

.ly-do-the,
.buoc-the {
  background: var(--card, #fff);
  border-radius: 12px;
  padding: 22px;
  border: 1px solid #e2e8f0;
}

.ly-do-icon {
  font-size: 1.6rem;
}

.ly-do-the h3,
.buoc-the h3 {
  font-size: 1.05rem;
  margin: 10px 0 6px;
}

.ly-do-the p,
.buoc-the p {
  margin: 0;
  opacity: 0.8;
  line-height: 1.5;
}

.buoc-so {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: #2563eb;
  color: #fff;
  font-weight: 700;
}

/* Sản phẩm */
.khoi-tieu-de {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 12px;
  margin-bottom: 20px;
}

.khoi-tieu-de h2 {
  margin: 0;
}

.xem-them {
  color: #2563eb;
  font-weight: 600;
  text-decoration: none;
}

.trang-thai {
  opacity: 0.75;
}

.trang-thai.loi {
  color: #b91c1c;
  opacity: 1;
}

.sp-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 18px;
}

.sp-the {
  background: var(--card, #fff);
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  overflow: hidden;
  text-decoration: none;
  color: inherit;
  display: flex;
  flex-direction: column;
  transition: transform 0.15s, box-shadow 0.15s;
}

.sp-the:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.1);
}

.sp-anh {
  height: 120px;
  background: linear-gradient(135deg, #bfdbfe, #7dd3fc);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  color: #1e3a8a;
  letter-spacing: 0.05em;
}

.sp-noi-dung {
  padding: 14px 16px 16px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.sp-noi-dung h3 {
  font-size: 1rem;
  margin: 0;
}

.sp-noi-dung p {
  margin: 0;
}

.sp-danh-gia {
  font-size: 0.9rem;
  color: #b45309;
}

.sp-gia {
  font-size: 0.95rem;
}

.sp-gia strong {
  color: #ea580c;
}

.sp-so-sanh {
  color: #2563eb;
  font-size: 0.9rem;
  font-weight: 600;
}

/* CTA */
.cta {
  background: linear-gradient(135deg, #1e3a8a, #2563eb);
  color: #fff;
  border-radius: 14px;
  padding: 32px;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
}

.cta h2 {
  color: #fff;
  margin: 0 0 6px;
}

.cta p {
  margin: 0;
  opacity: 0.9;
}

/* Responsive */
@media (max-width: 640px) {
  .hero {
    padding: 36px 16px 44px;
  }

  .form-tim {
    grid-template-columns: 1fr 1fr;
  }

  .form-tim .btn-chinh {
    grid-column: 1 / -1;
  }

  .khoi {
    padding: 28px 16px;
  }
}

@media (prefers-color-scheme: dark) {
  :root:not([data-theme='light']) .home {
    color: #e2e8f0;
  }
}
</style>