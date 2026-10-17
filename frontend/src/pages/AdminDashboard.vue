<script setup>
import { ref, computed, nextTick, onUnmounted, onMounted } from 'vue'
import {
  Chart, BarController, BarElement, CategoryScale, LinearScale,
  DoughnutController, ArcElement, Tooltip, Legend,
} from 'chart.js'
import { MOCK_THONG_KE, MOCK_PYTHON } from '../mock/mockThongKe'
import { xuatCsv } from '../lib/xuatCsv'

Chart.register(
  BarController, BarElement, CategoryScale, LinearScale,
  DoughnutController, ArcElement, Tooltip, Legend,
)

const dangTai = ref(true)
const loi = ref('')
const thongKe = ref(null)
const xuHuong = ref([])
const pythonLoi = ref('')

const canvasDoanhThu = ref(null)
const canvasTrangThai = ref(null)
let bieuDo = []

const dinhDangTien = (n) => new Intl.NumberFormat('vi-VN').format(n) + ' đ'
const dinhDangPhanTram = (x) => (x * 100).toFixed(1) + '%'

// Tỷ lệ hủy tính từ số đơn, không lấy số cứng
const tyLeHuy = computed(() => {
  const t = thongKe.value
  if (!t || !t.tong_don) return 0
  const huy = t.don_theo_trang_thai.find((x) => x.trang_thai === 'Đã hủy')?.so_don ?? 0
  return huy / t.tong_don
})

const chiSo = computed(() => {
  const t = thongKe.value
  if (!t) return []
  return [
    { nhan: 'Tổng số đơn', gia_tri: t.tong_don },
    { nhan: 'Doanh thu', gia_tri: dinhDangTien(t.doanh_thu) },
    { nhan: 'Đơn chờ thanh toán', gia_tri: t.don_cho_thanh_toan },
    { nhan: 'Tỷ lệ hủy', gia_tri: dinhDangPhanTram(tyLeHuy.value) },
  ]
})

function veBieuDo() {
  bieuDo.forEach((c) => c.destroy())
  bieuDo = []
  const t = thongKe.value
  if (!t) return

  if (canvasDoanhThu.value) {
    bieuDo.push(new Chart(canvasDoanhThu.value, {
      type: 'bar',
      data: {
        labels: t.doanh_thu_theo_thang.map((x) => x.thang),
        datasets: [{
          label: 'Doanh thu (đ)',
          data: t.doanh_thu_theo_thang.map((x) => x.tien),
          backgroundColor: '#1c7293',
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
      },
    }))
  }

  if (canvasTrangThai.value) {
    bieuDo.push(new Chart(canvasTrangThai.value, {
      type: 'doughnut',
      data: {
        labels: t.don_theo_trang_thai.map((x) => x.trang_thai),
        datasets: [{
          data: t.don_theo_trang_thai.map((x) => x.so_don),
          backgroundColor: ['#1c7293', '#b85042', '#8a94a6'],
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
      },
    }))
  }
}

async function taiPython() {
  try {
    if (!MOCK_PYTHON.dang_hoat_dong) {
      throw new Error('Dịch vụ Python đang tắt')
    }
    xuHuong.value = MOCK_PYTHON.mua_vu
    pythonLoi.value = ''
  } catch (e) {
    xuHuong.value = []
    pythonLoi.value = 'Phân tích mùa vụ đang tạm dừng. Bảng điều khiển vẫn dùng số liệu cơ bản ở trên.'
  }
}

async function taiDuLieu() {
  dangTai.value = true
  loi.value = ''
  try {
    // Sau này thay dòng này bằng lời gọi API thật của V3
    thongKe.value = MOCK_THONG_KE
  } catch (e) {
    loi.value = 'Không tải được số liệu thống kê. Vui lòng thử lại.'
    thongKe.value = null
  } finally {
    dangTai.value = false
  }
  await nextTick()
  veBieuDo()
  await taiPython()
}

function xuatDoanhThu() {
  xuatCsv('doanh-thu-theo-thang.csv', thongKe.value.doanh_thu_theo_thang)
}

function xuatTrangThai() {
  xuatCsv('don-theo-trang-thai.csv', thongKe.value.don_theo_trang_thai)
}

onMounted(taiDuLieu)
onUnmounted(() => bieuDo.forEach((c) => c.destroy()))
</script>

<template>
  <div class="bang-dieu-khien">
    <h1>Bảng điều khiển quản trị</h1>
    <p class="ghi-chu">Số liệu mẫu, chưa phải dữ liệu thật.</p>

    <p v-if="dangTai" role="status">Đang tải số liệu...</p>

    <div v-else-if="loi" class="thong-bao-loi" role="alert">
      <p>{{ loi }}</p>
      <button type="button" class="nut" @click="taiDuLieu">Thử lại</button>
    </div>

    <template v-else-if="thongKe">
      <section class="the-chi-so" aria-label="Chỉ số chính">
        <article v-for="cs in chiSo" :key="cs.nhan" class="the">
          <p class="the-nhan">{{ cs.nhan }}</p>
          <p class="the-gia-tri">{{ cs.gia_tri }}</p>
        </article>
      </section>

      <section class="khoi-bieu-do">
        <div class="o-bieu-do">
          <div class="dong-tieu-de">
            <h2>Doanh thu theo tháng</h2>
            <button type="button" class="nut" @click="xuatDoanhThu">Xuất CSV</button>
          </div>
          <div class="khung-canvas">
            <canvas ref="canvasDoanhThu" role="img" aria-label="Biểu đồ cột doanh thu theo tháng"></canvas>
          </div>
        </div>

        <div class="o-bieu-do">
          <div class="dong-tieu-de">
            <h2>Đơn theo trạng thái</h2>
            <button type="button" class="nut" @click="xuatTrangThai">Xuất CSV</button>
          </div>
          <div class="khung-canvas">
            <canvas ref="canvasTrangThai" role="img" aria-label="Biểu đồ tròn đơn theo trạng thái"></canvas>
          </div>
        </div>
      </section>

      <section class="khoi-python" aria-labelledby="tieu-de-python">
        <h2 id="tieu-de-python">Phân tích mùa vụ (mô-đun Python)</h2>
        <p v-if="pythonLoi" class="thong-bao">{{ pythonLoi }}</p>
        <ul v-else-if="xuHuong.length">
          <li v-for="x in xuHuong" :key="x.thang">
            {{ x.thang }}: tỷ lệ lấp chỗ {{ dinhDangPhanTram(x.ty_le_lap_cho) }} ({{ x.mua }})
          </li>
        </ul>
      </section>
    </template>
  </div>
</template>

<style scoped>
.bang-dieu-khien {
  max-width: 1120px;
  margin: 0 auto;
  padding: 16px;
  color: var(--color-chu, #16202b);
  background: var(--color-nen, #ffffff);
}

h1 {
  margin: 8px 0 4px;
  font-size: 1.5rem;
}

.ghi-chu {
  margin: 0 0 16px;
  color: var(--color-chu-nhat, #566274);
  font-size: 0.9rem;
}

.the-chi-so {
  display: grid;
  grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
  gap: 12px;
  margin-bottom: 20px;
}

.the {
  padding: 16px;
  border: 1px solid var(--color-vien, #d5dde6);
  border-radius: 10px;
  background: var(--color-nen-phu, #f2f5f9);
}

.the-nhan {
  margin: 0 0 6px;
  color: var(--color-chu-nhat, #566274);
  font-size: 0.85rem;
}

.the-gia-tri {
  margin: 0;
  font-size: 1.35rem;
  font-weight: 700;
}

.khoi-bieu-do {
  display: grid;
  grid-template-columns: minmax(0, 1fr);
  gap: 16px;
  margin-bottom: 20px;
}

.o-bieu-do {
  padding: 16px;
  border: 1px solid var(--color-vien, #d5dde6);
  border-radius: 10px;
  background: var(--color-nen, #ffffff);
}

.dong-tieu-de {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  margin-bottom: 12px;
}

.dong-tieu-de h2 {
  margin: 0;
  font-size: 1rem;
}

.khung-canvas {
  position: relative;
  height: 280px;
}

.khoi-python {
  padding: 16px;
  border: 1px solid var(--color-vien, #d5dde6);
  border-radius: 10px;
  background: var(--color-nen-phu, #f2f5f9);
}

.khoi-python h2 {
  margin: 0 0 8px;
  font-size: 1rem;
}

.khoi-python p,
.khoi-python li {
  margin: 0 0 4px;
  font-size: 0.95rem;
}

.thong-bao {
  color: var(--color-chu-nhat, #566274);
}

.thong-bao-loi {
  padding: 16px;
  border: 1px solid #b00020;
  border-radius: 10px;
}

.nut {
  padding: 6px 12px;
  border: 1px solid var(--color-chinh, #1c7293);
  border-radius: 6px;
  background: var(--color-nen, #ffffff);
  color: var(--color-chinh, #1c7293);
  font-size: 0.9rem;
  cursor: pointer;
}

button:focus-visible,
canvas:focus-visible {
  outline: 3px solid var(--color-nhan, #b85042);
  outline-offset: 2px;
}

@media (min-width: 640px) {
  .the-chi-so {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (min-width: 1024px) {
  .khoi-bieu-do {
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
  }
}
</style>