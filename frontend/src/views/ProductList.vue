<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { productService } from '../lib/productService'

const SO_MOI_TRANG = 12

const DIEM_DEN = [
  { ma: '', ten: 'Tất cả điểm đến' },
  { ma: 'HN', ten: 'Hà Nội' },
  { ma: 'NB', ten: 'Ninh Bình' },
  { ma: 'QN', ten: 'Quảng Ninh' },
]

const LOAI = [
  { ma: '', ten: 'Tất cả loại dịch vụ' },
  { ma: 'tour', ten: 'Tour' },
  { ma: 'stay', ten: 'Lưu trú' },
  { ma: 'transfer', ten: 'Đưa đón' },
]

const route = useRoute()
const router = useRouter()

// Bộ lọc đọc từ URL khi mở trang, để tải lại trang vẫn giữ nguyên
function docBoLocTuUrl() {
  const q = route.query
  return {
    diem_den: typeof q.diem_den === 'string' ? q.diem_den : '',
    loai: typeof q.loai === 'string' ? q.loai : '',
    gia_tu: typeof q.gia_tu === 'string' ? q.gia_tu : '',
    gia_den: typeof q.gia_den === 'string' ? q.gia_den : '',
    trang: Math.max(1, parseInt(q.trang, 10) || 1),
  }
}

const loc = ref(docBoLocTuUrl())
const dangTai = ref(false)
const loi = ref('')
const danhSach = ref([])
const tongSo = ref(0)

const tongTrang = computed(() => Math.max(1, Math.ceil(tongSo.value / SO_MOI_TRANG)))

// Các hàm đọc trường dữ liệu, thử nhiều tên để khớp với productService
const maCua = (sp) => sp.ma_san_pham ?? sp.ma ?? sp.id
const tenCua = (sp) => sp.ten_san_pham ?? sp.ten ?? ''
const giaCua = (sp) => sp.gia_tu ?? sp.gia ?? null
const diemDenCua = (sp) => sp.diem_den ?? sp.ten_diem_den ?? ''
const anhCua = (sp) => sp.anh_dai_dien ?? sp.anh ?? null

function dinhDangGia(n) {
  if (n === null || n === undefined || n === '') return 'Liên hệ'
  return Number(n).toLocaleString('vi-VN') + ' đ'
}

// Ghi bộ lọc lên URL mà không thêm lịch sử trình duyệt
function dongBoUrl() {
  const query = {}
  if (loc.value.diem_den) query.diem_den = loc.value.diem_den
  if (loc.value.loai) query.loai = loc.value.loai
  if (loc.value.gia_tu) query.gia_tu = loc.value.gia_tu
  if (loc.value.gia_den) query.gia_den = loc.value.gia_den
  if (loc.value.trang > 1) query.trang = loc.value.trang
  router.replace({ query })
}

async function taiDanhSach() {
  dangTai.value = true
  loi.value = ''
  try {
    const res = await productService.timKiemCongKhai({
      diem_den: loc.value.diem_den || undefined,
      loai: loc.value.loai || undefined,
      gia_tu: loc.value.gia_tu || undefined,
      gia_den: loc.value.gia_den || undefined,
      trang: loc.value.trang,
      so_moi_trang: SO_MOI_TRANG,
    })
    // Hỗ trợ cả hai dạng trả về: mảng, hoặc object có data và tong_so
    if (Array.isArray(res)) {
      danhSach.value = res
      tongSo.value = res.length
    } else {
      danhSach.value = res?.data ?? []
      tongSo.value = res?.tong_so ?? danhSach.value.length
    }
  } catch (e) {
    loi.value = 'Không tải được danh sách sản phẩm. Vui lòng thử lại.'
    danhSach.value = []
    tongSo.value = 0
  } finally {
    dangTai.value = false
  }
}

function apDungBoLoc() {
  loc.value.trang = 1
  dongBoUrl()
  taiDanhSach()
}

function xoaBoLoc() {
  loc.value = { diem_den: '', loai: '', gia_tu: '', gia_den: '', trang: 1 }
  dongBoUrl()
  taiDanhSach()
}

function chuyenTrang(t) {
  if (t < 1 || t > tongTrang.value) return
  loc.value.trang = t
  dongBoUrl()
  taiDanhSach()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(taiDanhSach)
</script>

<template>
  <div class="trang-danh-sach">
    <aside class="bo-loc" aria-labelledby="tieu-de-loc">
      <h2 id="tieu-de-loc">Bộ lọc</h2>

      <form @submit.prevent="apDungBoLoc">
        <div class="o-nhap">
          <label for="loc-diem-den">Điểm đến</label>
          <select id="loc-diem-den" v-model="loc.diem_den">
            <option v-for="d in DIEM_DEN" :key="d.ma" :value="d.ma">{{ d.ten }}</option>
          </select>
        </div>

        <div class="o-nhap">
          <label for="loc-loai">Loại dịch vụ</label>
          <select id="loc-loai" v-model="loc.loai">
            <option v-for="l in LOAI" :key="l.ma" :value="l.ma">{{ l.ten }}</option>
          </select>
        </div>

        <div class="o-nhap">
          <label for="loc-gia-tu">Giá từ (đ)</label>
          <input id="loc-gia-tu" type="number" min="0" step="10000" v-model="loc.gia_tu" />
        </div>

        <div class="o-nhap">
          <label for="loc-gia-den">Giá đến (đ)</label>
          <input id="loc-gia-den" type="number" min="0" step="10000" v-model="loc.gia_den" />
        </div>

        <div class="nut-loc">
          <button type="submit" class="nut-chinh">Lọc</button>
          <button type="button" class="nut-phu" @click="xoaBoLoc">Xóa bộ lọc</button>
        </div>
      </form>
    </aside>

    <section class="ket-qua" aria-live="polite">
      <h1>Danh sách sản phẩm</h1>

      <p v-if="dangTai" class="thong-bao">Đang tải sản phẩm...</p>
      <p v-else-if="loi" class="thong-bao loi" role="alert">{{ loi }}</p>
      <p v-else-if="!danhSach.length" class="thong-bao">
        Không có sản phẩm phù hợp với bộ lọc. Hãy thử bỏ bớt điều kiện.
      </p>
      <p v-else class="thong-bao">Có {{ tongSo }} sản phẩm.</p>

      <ul v-if="!dangTai && !loi && danhSach.length" class="danh-sach">
        <li v-for="sp in danhSach" :key="maCua(sp)" class="the-san-pham">
          <RouterLink :to="{ name: 'detail', params: { slug: maCua(sp) } }" class="the-lien-ket">
            <div class="anh-san-pham">
              <img v-if="anhCua(sp)" :src="anhCua(sp)" :alt="tenCua(sp)" />
              <span v-else class="anh-trong">Chưa có ảnh</span>
            </div>
            <div class="noi-dung-the">
              <h3>{{ tenCua(sp) }}</h3>
              <p>{{ diemDenCua(sp) }}</p>
              <p class="gia">Giá từ {{ dinhDangGia(giaCua(sp)) }}</p>
            </div>
          </RouterLink>
        </li>
      </ul>

      <nav v-if="tongTrang > 1" class="phan-trang" aria-label="Phân trang">
        <button type="button" :disabled="loc.trang <= 1" @click="chuyenTrang(loc.trang - 1)">
          Trang trước
        </button>
        <span>Trang {{ loc.trang }} / {{ tongTrang }}</span>
        <button type="button" :disabled="loc.trang >= tongTrang" @click="chuyenTrang(loc.trang + 1)">
          Trang sau
        </button>
      </nav>
    </section>
  </div>
</template>

<style scoped>
.trang-danh-sach {
  display: block;
  max-width: 1120px;
  margin: 0 auto;
  padding: 16px;
  color: var(--color-chu, #16202b);
  background: var(--color-nen, #ffffff);
}

/* Điện thoại (dưới 640px): một cột, bộ lọc nằm trên */
.bo-loc {
  margin-bottom: 16px;
  padding: 16px;
  border: 1px solid var(--color-vien, #d5dde6);
  border-radius: 10px;
  background: var(--color-nen-phu, #f2f5f9);
}

.bo-loc h2 {
  margin: 0 0 12px;
  font-size: 1.1rem;
}

.o-nhap {
  display: flex;
  flex-direction: column;
  gap: 4px;
  margin-bottom: 12px;
}

.o-nhap label {
  font-size: 0.9rem;
  font-weight: 600;
}

.o-nhap select,
.o-nhap input {
  padding: 8px 10px;
  font-size: 1rem;
  border: 1px solid var(--color-vien, #d5dde6);
  border-radius: 6px;
  background: var(--color-nen, #ffffff);
  color: var(--color-chu, #16202b);
}

.nut-loc {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.nut-chinh,
.nut-phu,
.phan-trang button {
  padding: 8px 14px;
  font-size: 0.95rem;
  border-radius: 6px;
  cursor: pointer;
}

.nut-chinh {
  border: 0;
  background: var(--color-chinh, #1c7293);
  color: #ffffff;
}

.nut-phu,
.phan-trang button {
  border: 1px solid var(--color-vien, #d5dde6);
  background: var(--color-nen, #ffffff);
  color: var(--color-chu, #16202b);
}

.phan-trang button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

button:focus-visible,
a:focus-visible,
select:focus-visible,
input:focus-visible {
  outline: 3px solid var(--color-nhan, #b85042);
  outline-offset: 2px;
}

.ket-qua h1 {
  margin: 8px 0 12px;
  font-size: 1.5rem;
}

.thong-bao {
  margin: 0 0 12px;
  color: var(--color-chu-nhat, #566274);
}

.thong-bao.loi {
  color: #b00020;
}

.danh-sach {
  list-style: none;
  margin: 0;
  padding: 0;
  display: grid;
  grid-template-columns: 1fr;
  gap: 16px;
}

.the-san-pham {
  border: 1px solid var(--color-vien, #d5dde6);
  border-radius: 10px;
  background: var(--color-nen, #ffffff);
  overflow: hidden;
}

.the-lien-ket {
  display: block;
  color: inherit;
  text-decoration: none;
}

.anh-san-pham {
  aspect-ratio: 16 / 9;
  background: var(--color-nen-phu, #f2f5f9);
  display: flex;
  align-items: center;
  justify-content: center;
}

.anh-san-pham img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.anh-trong {
  color: var(--color-chu-nhat, #566274);
  font-size: 0.9rem;
}

.noi-dung-the {
  padding: 12px;
}

.noi-dung-the h3 {
  margin: 0 0 6px;
  font-size: 1rem;
}

.noi-dung-the p {
  margin: 0 0 4px;
  font-size: 0.9rem;
}

.gia {
  font-weight: 700;
  color: var(--color-nhan, #b85042);
}

.phan-trang {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-top: 20px;
  flex-wrap: wrap;
}

/* Máy tính bảng (từ 640px): 2 cột */
@media (min-width: 640px) {
  .danh-sach {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* Máy tính để bàn (từ 1024px): bộ lọc bên trái, 3 cột */
@media (min-width: 1024px) {
  .trang-danh-sach {
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 24px;
    align-items: start;
  }

  .bo-loc {
    margin-bottom: 0;
    position: sticky;
    top: 16px;
  }

  .danh-sach {
    grid-template-columns: repeat(3, 1fr);
  }
}
</style>