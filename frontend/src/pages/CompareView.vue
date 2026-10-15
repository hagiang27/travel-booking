<template>
  <div class="compare-page">
    <main class="container">
      <RouterLink :to="`/san-pham/${maSanPham}`" class="back-link">← Quay lại chi tiết</RouterLink>

      <div v-if="dangTai" class="empty-state">
        <p>Đang tải...</p>
      </div>

      <div v-else-if="loi" class="empty-state">
        <h3>Không tải được sản phẩm</h3>
        <p>{{ loi }}</p>
      </div>

      <template v-else-if="sp">
        <header class="page-header">
          <h1>So sánh nhà cung cấp</h1>
          <p>{{ sp.ten_san_pham }} ({{ sp.ma_san_pham }})</p>
        </header>

        <section class="filter-box">
          <div class="form-group">
            <label for="ngay">Ngày khởi hành</label>
            <select id="ngay" v-model="ngayChon">
              <option v-for="l in lich" :key="l.ma_lich" :value="l.ngay_khoi_hanh">
                {{ formatNgay(l.ngay_khoi_hanh) }}{{ laCuoiTuan(l.ngay_khoi_hanh) ? ' (cuối tuần)' : '' }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label for="so-nguoi">Số người (1 đến 30)</label>
            <input id="so-nguoi" v-model.number="soNguoi" type="number" min="1" max="30" />
          </div>
        </section>

        <div v-if="!ketQua.length" class="empty-state">
          <h3>Chưa có chào giá</h3>
          <p>Sản phẩm này chưa có nhà cung cấp nào để so sánh.</p>
        </div>

        <ul v-else class="offer-list">
          <li
            v-for="(ket, i) in ketQua"
            :key="ket.chao.ma_chao"
            class="offer-card"
            :class="{ 'is-best': i === 0 }"
          >
            <div class="offer-head">
              <div>
                <h2>{{ ket.chao.ten_ncc }}</h2>
                <p class="rating">★ {{ ket.chao.danh_gia }}</p>
              </div>
              <span v-if="i === 0" class="badge">Giá thấp nhất</span>
            </div>

            <dl class="breakdown">
              <div>
                <dt>Giá × {{ ket.so_nguoi }} người</dt>
                <dd>{{ formatPrice(ket.tien_hang) }}</dd>
              </div>
              <div>
                <dt>Phụ phí{{ ket.cuoi_tuan ? ' (cuối tuần)' : '' }}</dt>
                <dd>{{ formatPrice(ket.phu_thu) }}</dd>
              </div>
              <div>
                <dt>Thuế 8%</dt>
                <dd>{{ formatPrice(ket.thue) }}</dd>
              </div>
              <div class="total">
                <dt>Tổng cộng</dt>
                <dd>{{ formatPrice(ket.tong) }}</dd>
              </div>
            </dl>

            <p><strong>Chính sách hủy:</strong> {{ ket.chao.chinh_sach_huy }}</p>
            <p><strong>Bao gồm:</strong> {{ (ket.chao.bao_gom || []).join(', ') || 'Đang cập nhật' }}</p>
            <p><strong>Không bao gồm:</strong> {{ (ket.chao.khong_bao_gom || []).join(', ') || 'Đang cập nhật' }}</p>
          </li>
        </ul>

        <p class="note">
          Phụ phí và thuế trong trang này là số liệu giả định để minh họa, không phải giá thật.
        </p>
      </template>
    </main>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { productService } from '../lib/productService'
import { MOCK_CHAO_GIA } from '../mock/mockOffers'
import { laCuoiTuan, soSanhGia } from '../lib/gia'

const route = useRoute()

// Router có thể đặt tên tham số là slug hoặc ma, đọc cả hai để tránh lỗi
const maSanPham = computed(() => route.params.slug || route.params.ma)

const sp = ref(null)
const lich = ref([])
const dangTai = ref(false)
const loi = ref('')
const ngayChon = ref('')
const soNguoi = ref(1)

const ketQua = computed(() =>
  soSanhGia(MOCK_CHAO_GIA[maSanPham.value] || [], {
    ngayKhoiHanh: ngayChon.value,
    soNguoi: soNguoi.value,
  })
)

async function taiSanPham() {
  dangTai.value = true
  loi.value = ''

  try {
    const data = await productService.chiTiet(maSanPham.value)
    sp.value = data
    lich.value = data.lich_kha_dung || []
    ngayChon.value = lich.value[0]?.ngay_khoi_hanh || ''
  } catch (e) {
    sp.value = null
    loi.value = e.status === 404 ? 'Sản phẩm không tồn tại.' : e.message
  } finally {
    dangTai.value = false
  }
}

watch(maSanPham, taiSanPham, { immediate: true })

function formatNgay(ngay) {
  if (!ngay || !ngay.includes('-')) return ngay
  const [nam, thang, ngayTrongThang] = ngay.split('-')
  return `${ngayTrongThang}/${thang}/${nam}`
}

function formatPrice(price) {
  return new Intl.NumberFormat('vi-VN').format(price) + ' VNĐ'
}
</script>

<style scoped>
.compare-page {
  min-height: 100vh;
  padding: var(--space-8) 0 var(--space-12);
  background: var(--color-bg);
  color: var(--color-text);
}

.container {
  width: min(1000px, calc(100% - 32px));
  margin: 0 auto;
}

.back-link {
  color: var(--color-primary);
  text-decoration: none;
}

.page-header {
  margin: var(--space-6) 0;
}

.page-header h1 {
  margin: 0 0 var(--space-2);
  font-size: var(--font-size-h1);
}

.page-header p {
  margin: 0;
  color: var(--color-text-secondary);
}

.filter-box {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--space-4);
  margin-bottom: var(--space-6);
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

.form-group select,
.form-group input {
  min-height: 44px;
  padding: 0 var(--space-4);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background: var(--color-bg);
  color: var(--color-text);
  font-size: var(--font-size-body);
}

.offer-list {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
  padding: 0;
  margin: 0;
  list-style: none;
}

.offer-card {
  padding: var(--space-6);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  background: var(--color-surface);
}

.offer-card.is-best {
  border-color: var(--color-primary);
}

.offer-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: var(--space-4);
}

.offer-head h2 {
  margin: 0;
  font-size: var(--font-size-h3);
}

.rating {
  margin: var(--space-1) 0 0;
  color: var(--color-warning);
  font-size: var(--font-size-small);
}

.badge {
  padding: var(--space-1) var(--space-3);
  border-radius: var(--radius-md);
  background: var(--color-primary);
  color: var(--color-bg);
  font-size: var(--font-size-small);
  font-weight: var(--font-weight-semibold);
}

.breakdown {
  margin: 0 0 var(--space-4);
}

.breakdown > div {
  display: flex;
  justify-content: space-between;
  padding: var(--space-2) 0;
  border-bottom: 1px solid var(--color-border);
}

.breakdown dt,
.breakdown dd {
  margin: 0;
}

.breakdown .total {
  font-weight: var(--font-weight-bold);
  color: var(--color-primary);
}

.offer-card p {
  margin: 0 0 var(--space-2);
  color: var(--color-text-secondary);
  line-height: 1.6;
}

.note {
  margin-top: var(--space-6);
  color: var(--color-text-muted);
  font-size: var(--font-size-small);
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

@media (max-width: 600px) {
  .filter-box {
    grid-template-columns: 1fr;
  }
}
</style>