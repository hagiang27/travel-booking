<template>
  <div class="product-page">
    <main class="container">
      <section class="page-header">
        <h1>Danh sách sản phẩm</h1>
        <p>Khám phá các sản phẩm du lịch phù hợp với bạn</p>
      </section>

      <form class="filter-section" @submit.prevent="apDungLoc">
        <div class="filter-group">
          <label for="destination">Điểm đến</label>
          <select id="destination" v-model="formDiemDen">
            <option value="">Tất cả điểm đến</option>
            <option v-for="dd in DIEM_DEN" :key="dd.value" :value="dd.value">
              {{ dd.label }}
            </option>
          </select>
        </div>

        <div class="filter-group">
          <label for="type">Loại sản phẩm</label>
          <select id="type" v-model="formLoai">
            <option value="">Tất cả loại</option>
            <option v-for="l in LOAI" :key="l.value" :value="l.value">
              {{ l.label }}
            </option>
          </select>
        </div>

        <div class="filter-group">
          <label for="price-from">Giá từ</label>
          <input id="price-from" v-model="formGiaTu" type="number" min="0" placeholder="0" />
        </div>

        <div class="filter-group">
          <label for="price-to">Giá đến</label>
          <input id="price-to" v-model="formGiaDen" type="number" min="0" placeholder="Không giới hạn" />
        </div>

        <div class="filter-actions">
          <button type="submit" class="btn-primary">Lọc</button>
          <button type="button" class="btn-ghost" @click="xoaLoc">Xóa lọc</button>
        </div>
      </form>

      <section class="result-section">
        <div class="section-heading">
          <h2>Sản phẩm</h2>
          <span>{{ tongSo }} sản phẩm</span>
        </div>

        <div v-if="dangTai" class="empty-state">
          <p>Đang tải danh sách...</p>
        </div>

        <div v-else-if="loi" class="empty-state">
          <h3>Không tải được danh sách</h3>
          <p>{{ loi }}</p>
        </div>

        <div v-else-if="danhSach.length" class="product-grid">
          <article v-for="sp in danhSach" :key="sp.ma_san_pham" class="product-card">
            <div class="product-image">
              <img
                v-if="anhCua(sp)"
                :src="anhCua(sp)"
                :alt="sp.ten_san_pham"
                loading="lazy"
                @error="chuyenAnhDuPhong(sp)"
              />
              <span v-else>Ảnh sản phẩm</span>
            </div>

            <div class="product-content">
              <h3>{{ sp.ten_san_pham }}</h3>

              <div class="product-rating">
                {{ sp.diem_danh_gia_tb > 0 ? `★ ${sp.diem_danh_gia_tb}` : 'Chưa có đánh giá' }}
              </div>

              <p class="product-price">
                {{ formatPrice(sp.gia_co_ban) }}
              </p>

              <RouterLink
                :to="`/san-pham/${sp.ma_san_pham}`"
                class="detail-button"
              >
                Xem chi tiết
              </RouterLink>
            </div>
          </article>
        </div>

        <div v-else class="empty-state">
          <h3>Không tìm thấy sản phẩm</h3>
          <p>Hãy thử thay đổi điều kiện tìm kiếm.</p>
        </div>

        <nav v-if="tongTrang > 1" class="pagination" aria-label="Phân trang">
          <button
            type="button"
            :disabled="trangHienTai <= 1"
            @click="chuyenTrang(trangHienTai - 1)"
          >
            ← Trước
          </button>

          <span>Trang {{ trangHienTai }} / {{ tongTrang }}</span>

          <button
            type="button"
            :disabled="trangHienTai >= tongTrang"
            @click="chuyenTrang(trangHienTai + 1)"
          >
            Sau →
          </button>
        </nav>
      </section>
    </main>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { productService } from '../lib/productService'

const route = useRoute()
const router = useRouter()

// value: mã dùng trên URL. slug: tên không dấu khớp với đường dẫn tỉnh trong database
const DIEM_DEN = [
  { value: 'HN', label: 'Hà Nội', slug: 'ha-noi' },
  { value: 'NB', label: 'Ninh Bình', slug: 'ninh-binh' },
  { value: 'QN', label: 'Quảng Ninh', slug: 'quang-ninh' },
]

// value: đúng với giá trị loai_san_pham trong database
const LOAI = [
  { value: 'Tour trọn gói', label: 'Tour trọn gói' },
  { value: 'Lưu trú', label: 'Lưu trú' },
  { value: 'Vận chuyển', label: 'Vận chuyển' },
  { value: 'Vé tham quan', label: 'Vé tham quan' },
  { value: 'Dịch vụ trải nghiệm', label: 'Dịch vụ trải nghiệm' },
]

// Ảnh dự phòng theo loại sản phẩm
const ANH_THEO_LOAI = {
  'Dịch vụ trải nghiệm': 'dich-vu-trai-nghiem.jpg',
  'Lưu trú': 'luu-tru.jpg',
  'Tour trọn gói': 'tour-tron-goi.jpg',
  'Vận chuyển': 'van-chuyen.jpg',
  'Vé tham quan': 've-tham-quan.jpg',
}

// Giá trị đang chọn trong form (chưa áp dụng)
const formDiemDen = ref('')
const formLoai = ref('')
const formGiaTu = ref('')
const formGiaDen = ref('')

// Kết quả từ service
const danhSach = ref([])
const tongSo = ref(0)
const soMoiTrang = ref(12)
const dangTai = ref(false)
const loi = ref('')

// Số lần ảnh của từng sản phẩm bị lỗi: 0 = ảnh riêng, 1 = ảnh loại, 2 = hết ảnh
const buocAnh = ref({})

const trangHienTai = computed(() => Math.max(1, Number(route.query.trang) || 1))
const tongTrang = computed(() => Math.max(1, Math.ceil(tongSo.value / soMoiTrang.value)))

// Đổi mã điểm đến (ví dụ NB) thành slug không dấu (ví dụ ninh-binh) để khớp với database
function slugTheoMa(ma) {
  const dd = DIEM_DEN.find((d) => d.value === ma)
  return dd ? dd.slug : ''
}

function anhCua(sp) {
  const buoc = buocAnh.value[sp.ma_san_pham] || 0
  if (buoc === 0) return `/anh-san-pham/${sp.ma_san_pham}.jpg`
  if (buoc === 1) {
    const file = ANH_THEO_LOAI[sp.loai_san_pham] || 'dich-vu-trai-nghiem.jpg'
    return file ? `/anh-san-pham/${file}` : ''
  }
  return ''
}

function chuyenAnhDuPhong(sp) {
  buocAnh.value[sp.ma_san_pham] = (buocAnh.value[sp.ma_san_pham] || 0) + 1
}

function dongBoFormTuUrl() {
  formDiemDen.value = route.query.ma_diem_den || ''
  formLoai.value = route.query.loai || ''
  formGiaTu.value = route.query.gia_tu || ''
  formGiaDen.value = route.query.gia_den || ''
}

async function taiDanhSach() {
  dangTai.value = true
  loi.value = ''

  try {
    const res = await productService.timKiemCongKhai({
      tinh_thanh: slugTheoMa(route.query.ma_diem_den),
      loai: route.query.loai || '',
      gia_tu: route.query.gia_tu || '',
      gia_den: route.query.gia_den || '',
      trang: trangHienTai.value,
    })
    danhSach.value = res.data || []
    tongSo.value = res.tong_so || 0
    soMoiTrang.value = res.so_moi_trang || 12
    buocAnh.value = {}
  } catch (e) {
    danhSach.value = []
    tongSo.value = 0
    loi.value = e.message || 'Vui lòng thử lại sau.'
  } finally {
    dangTai.value = false
  }
}

watch(
  () => route.query,
  () => {
    dongBoFormTuUrl()
    taiDanhSach()
  },
  { immediate: true }
)

function apDungLoc() {
  const query = {}
  if (formDiemDen.value) query.ma_diem_den = formDiemDen.value
  if (formLoai.value) query.loai = formLoai.value
  if (formGiaTu.value !== '') query.gia_tu = formGiaTu.value
  if (formGiaDen.value !== '') query.gia_den = formGiaDen.value
  router.push({ path: route.path, query })
}

function xoaLoc() {
  router.push({ path: route.path, query: {} })
}

function chuyenTrang(trang) {
  router.push({ path: route.path, query: { ...route.query, trang } })
}

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
  grid-template-columns: repeat(4, 1fr) auto;
  gap: var(--space-4);
  align-items: end;
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