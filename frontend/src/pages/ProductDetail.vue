<template>
  <div class="detail-page">
    <main class="container">
      <section class="back-section">
        <RouterLink to="/san-pham" class="back-button">← Quay lại danh sách</RouterLink>
      </section>

      <div v-if="dangTai" class="empty-state">
        <p>Đang tải sản phẩm...</p>
      </div>

        <div v-else-if="loi" class="empty-state" role="alert">
        <h3>Không tải được sản phẩm</h3>
        <p>{{ loi }}</p>
      </div>

      <template v-else-if="sp">
        <section class="product-detail">
          <div class="product-image">Ảnh sản phẩm</div>

          <div class="product-info">
            <p class="product-code">Mã sản phẩm: {{ sp.ma_san_pham }}</p>
            <h1>{{ sp.ten_san_pham }}</h1>
            <p class="product-price">{{ formatPrice(sp.gia_co_ban) }} / khách</p>

            <div class="policy-box">
              <h2>Chính sách hủy</h2>
              <p>{{ sp.chinh_sach_huy || 'Đang cập nhật.' }}</p>
            </div>

            <RouterLink :to="`/so-sanh/${sp.ma_san_pham}`" class="compare-link">
              So sánh nhà cung cấp →
            </RouterLink>
          </div>
        </section>

        <section class="description-section">
          <h2>Mô tả</h2>
          <p>{{ moTa }}</p>
        </section>

        <section class="schedule-section">
          <div class="section-heading">
            <h2>Lịch khởi hành còn chỗ</h2>
          </div>

          <div v-if="lichKhaDung.length" class="schedule-list">
            <div
              v-for="lich in lichKhaDung"
              :key="lich.ma_lich"
              class="schedule-item"
              :class="{ 'is-selected': lich.ma_lich === maLichChon }"
            >
              <div>
                <span class="schedule-label">Ngày khởi hành</span>
                <strong>{{ formatNgay(lich.ngay_khoi_hanh) }}</strong>
              </div>

              <div>
                <span class="schedule-label">Còn lại</span>
                <strong>{{ lich.con_lai }} chỗ</strong>
              </div>

              <button
                type="button"
                class="select-button"
                :disabled="lich.con_lai <= 0 || !!donTam"
                @click="maLichChon = lich.ma_lich"
              >
                {{ lich.ma_lich === maLichChon ? 'Đã chọn' : 'Chọn' }}
              </button>
            </div>
          </div>

          <div v-else class="empty-state">
            <h3>Chưa có lịch khởi hành</h3>
            <p>Vui lòng quay lại sau.</p>
          </div>
        </section>

        <section class="booking-section">
          <div class="section-heading">
            <h2>Giữ chỗ</h2>
          </div>

          <div class="booking-form">
            <div class="form-group">
              <label for="so-nguoi">Số người (1 đến 30)</label>
              <input
                id="so-nguoi"
                v-model.number="soNguoi"
                type="number"
                min="1"
                max="30"
                :disabled="!!donTam"
              />
            </div>

            <p class="tam-tinh">Tạm tính: {{ formatPrice(tamTinh) }}</p>

            <button
              type="button"
              class="booking-button"
              :disabled="!maLichChon || !!donTam || dangGiuCho"
              @click="giuCho"
            >
              {{ dangGiuCho ? 'Đang giữ chỗ...' : 'Giữ chỗ 15 phút' }}
            </button>

            <p v-if="thongBao" class="message" role="status">{{ thongBao }}</p>
          </div>

          <div v-if="donTam" class="hold-box" role="status">
            <h3>Đã giữ chỗ. Mã đơn: {{ donTam.ma_don }}</h3>
            <p>Thời gian còn lại để thanh toán: <strong>{{ phutConLai }}</strong></p>
            <p>Tổng tạm tính: {{ formatPrice(donTam.tong_tien) }}</p>
            <RouterLink
              :to="{ name: 'booking', params: { code: donTam.ma_don } }"
              class="booking-button link-button"
            >
              Tới trang thanh toán
            </RouterLink>
          </div>
        </section>
      </template>
            <div v-else class="empty-state">
        <h3>Không tìm thấy sản phẩm này</h3>
        <p>Sản phẩm có thể đã bị gỡ hoặc đường dẫn không đúng.</p>
        <RouterLink to="/san-pham" class="back-button">← Quay lại danh sách</RouterLink>
      </div>
    </main>
  </div>
</template>


<script setup>
import { computed, onUnmounted, ref, watch } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { productService } from '../lib/productService'
import { authService } from '../lib/authService'
import { MO_TA } from '../mock/mockDescriptions'
import { luuDonMock } from '../lib/donMock'

const route = useRoute()
const router = useRouter()

// Router có thể đặt tên tham số là slug hoặc ma, đọc cả hai để tránh lỗi
const maSanPham = computed(() => route.params.slug || route.params.ma)

const sp = ref(null)
const lichKhaDung = ref([])
const dangTai = ref(false)
const loi = ref('')

const moTa = computed(
  () => MO_TA[sp.value?.ma_san_pham] || 'Đang cập nhật mô tả cho sản phẩm này.'
)

const maLichChon = ref('')
const soNguoi = ref(1)
const dangGiuCho = ref(false)
const thongBao = ref('')
const donTam = ref(null)
const giayConLai = ref(0)

let boDem = null

const soNguoiHopLe = computed(() => Math.min(30, Math.max(1, Number(soNguoi.value) || 1)))

const tamTinh = computed(() =>
  sp.value ? Number(sp.value.gia_co_ban) * soNguoiHopLe.value : 0
)

const phutConLai = computed(() => {
  const phut = Math.floor(giayConLai.value / 60)
  const giay = String(giayConLai.value % 60).padStart(2, '0')
  return `${phut}:${giay}`
})

async function taiChiTiet() {
  dangTai.value = true
  loi.value = ''

  try {
    const data = await productService.chiTiet(maSanPham.value)
    sp.value = data
    lichKhaDung.value = data.lich_kha_dung || []
  } catch (e) {
    sp.value = null
    lichKhaDung.value = []
    loi.value = e.status === 404 ? 'Sản phẩm không tồn tại.' : e.message
  } finally {
    dangTai.value = false
  }
}

watch(
  maSanPham,
  () => {
    maLichChon.value = ''
    donTam.value = null
    thongBao.value = ''
    dungDemNguoc()
    taiChiTiet()
  },
  { immediate: true }
)

function batDemNguoc(hetHan) {
  dungDemNguoc()

  const tinh = () => {
    giayConLai.value = Math.max(0, Math.floor((new Date(hetHan) - Date.now()) / 1000))

    if (giayConLai.value <= 0) {
      dungDemNguoc()
      donTam.value = null
      thongBao.value = 'Đã hết thời gian giữ chỗ. Vui lòng giữ lại.'
    }
  }

  tinh()
  boDem = setInterval(tinh, 1000)
}

function dungDemNguoc() {
  if (boDem) {
    clearInterval(boDem)
    boDem = null
  }
}

async function giuCho() {
  thongBao.value = ''

  const nguoiDung = await authService.layNguoiDungHienTai()
  if (!nguoiDung) {
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }

  dangGiuCho.value = true

  try {
    const don = await productService.giuCho({
      ma_san_pham: sp.value.ma_san_pham,
      ma_lich: maLichChon.value,
      so_nguoi: soNguoiHopLe.value,
    })

    donTam.value = don
        const lichChon = lichKhaDung.value.find((l) => l.ma_lich === maLichChon.value)
    luuDonMock({
      ...don,
      ma_san_pham: sp.value.ma_san_pham,
      ten_san_pham: sp.value.ten_san_pham,
      ten_nha_cung_cap: 'Giá niêm yết',
      ngay_khoi_hanh: lichChon?.ngay_khoi_hanh || '',
      so_nguoi: soNguoiHopLe.value,
      gia_co_ban: sp.value.gia_co_ban,
      chao: null,
    })
    batDemNguoc(don.het_han)

    // Tải lại lịch để cập nhật số chỗ còn lại
    const data = await productService.chiTiet(maSanPham.value)
    lichKhaDung.value = data.lich_kha_dung || []
  } catch (e) {
    thongBao.value =
      e.kiemTra?.so_nguoi?.[0] || e.message || 'Không giữ được chỗ, vui lòng thử lại.'
  } finally {
    dangGiuCho.value = false
  }
}

function formatNgay(ngay) {
  if (!ngay || !ngay.includes('-')) return ngay
  const [nam, thang, ngayTrongThang] = ngay.split('-')
  return `${ngayTrongThang}/${thang}/${nam}`
}

function formatPrice(price) {
  return new Intl.NumberFormat('vi-VN').format(price) + ' VNĐ'
}

onUnmounted(dungDemNguoc)
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
  color: var(--color-primary);
  font-size: var(--font-size-body);
  text-decoration: none;
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
  margin-bottom: var(--space-4);
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

.compare-link {
  display: inline-block;
  margin-top: var(--space-2);
  color: var(--color-primary);
  font-weight: var(--font-weight-semibold);
  text-decoration: none;
}

.description-section {
  margin-bottom: var(--space-8);
}

.description-section h2 {
  margin: 0 0 var(--space-3);
  font-size: var(--font-size-h2);
}

.description-section p {
  margin: 0;
  color: var(--color-text-secondary);
  line-height: 1.7;
}

.section-heading {
  margin-bottom: var(--space-4);
}

.section-heading h2 {
  margin: 0;
  font-size: var(--font-size-h2);
}

.schedule-section {
  padding-top: var(--space-6);
  border-top: 1px solid var(--color-border);
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

.schedule-item.is-selected {
  border-color: var(--color-primary);
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

.select-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.booking-section {
  margin-top: var(--space-8);
  padding-top: var(--space-6);
  border-top: 1px solid var(--color-border);
}

.booking-form {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
  max-width: 420px;
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
  min-height: 44px;
  padding: 0 var(--space-4);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background: var(--color-bg);
  color: var(--color-text);
  font-size: var(--font-size-body);
}

.tam-tinh {
  margin: 0;
  font-weight: var(--font-weight-semibold);
}

.booking-button {
  min-height: 44px;
  border: none;
  border-radius: var(--radius-md);
  background: var(--color-primary);
  color: var(--color-bg);
  font-size: var(--font-size-body);
  font-weight: var(--font-weight-semibold);
  cursor: pointer;
}

.booking-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.message {
  margin: 0;
  color: var(--color-warning);
  font-size: var(--font-size-small);
}

.hold-box {
  margin-top: var(--space-6);
  padding: var(--space-4);
  border: 1px solid var(--color-primary);
  border-radius: var(--radius-lg);
  background: var(--color-surface);
}

.hold-box h3 {
  margin: 0 0 var(--space-2);
}

.hold-box p {
  margin: 0 0 var(--space-1);
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
.link-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-top: var(--space-3);
  text-decoration: none;
}
</style>