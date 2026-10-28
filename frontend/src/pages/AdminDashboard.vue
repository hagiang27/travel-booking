<script setup>
import { computed } from 'vue'
import * as XLSX from 'xlsx'

const chiSo = [
  { nhan: 'Tổng số đơn', giaTri: '128', thayDoi: '+18.5%', mau: '#2563eb' },
  { nhan: 'Doanh thu', giaTri: '412.000.000 ₫', thayDoi: '+22.8%', mau: '#16a34a' },
  { nhan: 'Đơn chờ thanh toán', giaTri: '14', thayDoi: '-5.4%', mau: '#7c3aed' },
  { nhan: 'Tỷ lệ hủy', giaTri: '18.8%', thayDoi: '+2.1%', mau: '#ea580c' },
]

const thang = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6']
const doanhThuThang = [52, 61, 48, 75, 66, 103]
const MAX_TRIEU = 120
const cotBieuDo = computed(() =>
  doanhThuThang.map((v, i) => {
    const cao = (v / MAX_TRIEU) * 200
    return { nhan: thang[i], v, x: 30 + i * 90, y: 220 - cao, cao }
  })
)

const trangThai = [
  { ten: 'Đã thanh toán', so: 96, mau: '#2563eb' },
  { ten: 'Chờ thanh toán', so: 14, mau: '#f59e0b' },
  { ten: 'Đã hủy', so: 18, mau: '#94a3b8' },
]
const tongDon = trangThai.reduce((t, m) => t + m.so, 0)
const CHU_VI = 2 * Math.PI * 60
const donTron = computed(() => {
  let luyKe = 0
  return trangThai.map((m) => {
    const dai = (m.so / tongDon) * CHU_VI
    const kq = { ...m, dai, lech: -luyKe }
    luyKe += dai
    return kq
  })
})

const donGanDay = [
  { ma: 'ORD001', khach: 'Nguyễn Văn An', dichVu: 'Tour Hà Giang 3N2Đ', ngay: '20/05/2024', tien: 7650000, trangThai: 'Đã thanh toán' },
  { ma: 'ORD002', khach: 'Trần Thị Bình', dichVu: 'Khách sạn Đà Nẵng', ngay: '21/05/2024', tien: 6200000, trangThai: 'Đã thanh toán' },
  { ma: 'ORD003', khach: 'Lê Minh Cường', dichVu: 'Vé Đèn Thanh Bình', ngay: '22/05/2024', tien: 950000, trangThai: 'Đã hủy' },
  { ma: 'ORD004', khach: 'Phạm Thu Hà', dichVu: 'Tour Phú Quốc 4N3Đ', ngay: '25/05/2024', tien: 45500000, trangThai: 'Đã thanh toán' },
  { ma: 'ORD005', khach: 'Võ Quốc Đạt', dichVu: 'Đưa đón sân bay', ngay: '27/05/2024', tien: 480000, trangThai: 'Chờ thanh toán' },
]

const dichVu = [
  { ten: 'Tour', so: 52 },
  { ten: 'Lưu trú', so: 41 },
  { ten: 'Vé tham quan', so: 28 },
  { ten: 'Đưa đón', so: 7 },
]

const diemDen = [
  { ten: 'Hà Nội', doanhThu: 98 },
  { ten: 'Đà Nẵng', doanhThu: 76 },
  { ten: 'Phú Quốc', doanhThu: 61 },
  { ten: 'Đắk Lắk', doanhThu: 45 },
]

function dinhDang(so) {
  return new Intl.NumberFormat('vi-VN').format(so)
}

function xuatExcel() {
  const wb = XLSX.utils.book_new()

  const sheetDonHang = XLSX.utils.json_to_sheet(
    donGanDay.map((d) => ({
      'Mã đơn': d.ma,
      'Khách hàng': d.khach,
      'Dịch vụ': d.dichVu,
      'Ngày': d.ngay,
      'Số tiền': d.tien,
      'Trạng thái': d.trangThai,
    }))
  )
  sheetDonHang['!cols'] = [
    { wch: 10 }, { wch: 20 }, { wch: 24 }, { wch: 12 }, { wch: 14 }, { wch: 18 },
  ]
  XLSX.utils.book_append_sheet(wb, sheetDonHang, 'Đơn hàng')

  const sheetDoanhThu = XLSX.utils.json_to_sheet(
    thang.map((t, i) => ({
      'Tháng': t,
      'Doanh thu (triệu ₫)': doanhThuThang[i],
    }))
  )
  sheetDoanhThu['!cols'] = [{ wch: 12 }, { wch: 22 }]
  XLSX.utils.book_append_sheet(wb, sheetDoanhThu, 'Doanh thu theo tháng')

  XLSX.writeFile(wb, 'bang-dieu-khien.xlsx')
}
</script>

<template>
  <main class="dash">
    <header class="tieu-de">
      <div>
        <h1>Bảng điều khiển quản trị</h1>
        <p class="ghi-chu">Số liệu mẫu, chưa phải dữ liệu thật.</p>
      </div>
      <select class="chon-thoi-gian">
        <option>Tháng 5/2024</option>
      </select>
    </header>

    <section class="luoi-chi-so">
      <div v-for="c in chiSo" :key="c.nhan" class="the-chi-so">
        <div class="vach" :style="{ background: c.mau }"></div>
        <p class="nhan-chi-so">{{ c.nhan }}</p>
        <strong>{{ c.giaTri }}</strong>
        <small :class="c.thayDoi.startsWith('-') ? 'xuong' : 'len'">{{ c.thayDoi }} so với tháng trước</small>
      </div>
    </section>

    <section class="hang-2">
      <div class="the">
        <div class="dau-the">
          <h2>Doanh thu theo tháng (triệu ₫)</h2>
          <button class="nut-phu" @click="xuatExcel">Xuất Excel</button>
        </div>
        <svg viewBox="0 0 600 260" class="bieu-do" role="img" aria-label="Doanh thu theo tháng">
          <line x1="20" y1="220" x2="590" y2="220" stroke="#e5e7eb" />
          <g v-for="c in cotBieuDo" :key="c.nhan">
            <rect :x="c.x" :y="c.y" width="56" :height="c.cao" rx="6" fill="#2563eb" />
            <text :x="c.x + 28" y="244" text-anchor="middle" class="truc">{{ c.nhan }}</text>
            <text :x="c.x + 28" :y="c.y - 6" text-anchor="middle" class="truc">{{ c.v }}</text>
          </g>
        </svg>
      </div>

      <div class="the">
        <div class="dau-the">
          <h2>Đơn theo trạng thái</h2>
          <button class="nut-phu" @click="xuatExcel">Xuất Excel</button>
        </div>
        <div class="tron-wrap">
          <svg viewBox="0 0 160 160" class="tron">
            <circle
              v-for="m in donTron"
              :key="m.ten"
              cx="80"
              cy="80"
              r="60"
              fill="none"
              :stroke="m.mau"
              stroke-width="22"
              :stroke-dasharray="`${m.dai} ${CHU_VI}`"
              :stroke-dashoffset="m.lech"
              transform="rotate(-90 80 80)"
            />
            <text x="80" y="86" text-anchor="middle" class="tong-tron">{{ tongDon }}</text>
          </svg>
          <ul class="chu-thich">
            <li v-for="m in trangThai" :key="m.ten">
              <span class="cham" :style="{ background: m.mau }"></span>
              {{ m.ten }}: <strong>{{ m.so }}</strong>
            </li>
          </ul>
        </div>
      </div>
    </section>

    <section class="hang-3">
      <div class="the">
        <h2>Đơn gần đây</h2>
        <div class="bang-wrap">
          <table class="bang">
            <thead>
              <tr>
                <th>Mã đơn</th>
                <th>Khách hàng</th>
                <th>Dịch vụ</th>
                <th>Ngày</th>
                <th class="so">Số tiền</th>
                <th>Trạng thái</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="d in donGanDay" :key="d.ma">
                <td>{{ d.ma }}</td>
                <td>{{ d.khach }}</td>
                <td>{{ d.dichVu }}</td>
                <td>{{ d.ngay }}</td>
                <td class="so">{{ dinhDang(d.tien) }} ₫</td>
                <td>
                  <span class="nhan-trang-thai" :class="d.trangThai === 'Đã thanh toán' ? 'ok' : d.trangThai === 'Đã hủy' ? 'huy' : 'cho'">
                    {{ d.trangThai }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="the">
        <h2>Đơn theo dịch vụ</h2>
        <div class="luoi-dich-vu">
          <div v-for="dv in dichVu" :key="dv.ten" class="o-dich-vu">
            <strong>{{ dv.so }}</strong>
            <span>{{ dv.ten }}</span>
          </div>
        </div>
      </div>
    </section>

    <section class="the">
      <h2>Điểm đến doanh thu cao</h2>
      <div v-for="dd in diemDen" :key="dd.ten" class="dong-thanh">
        <span class="ten-dd">{{ dd.ten }}</span>
        <div class="thanh-nen">
          <div class="thanh-tien" :style="{ width: (dd.doanhThu / diemDen[0].doanhThu) * 100 + '%' }"></div>
        </div>
        <span class="so-tien">{{ dd.doanhThu }} triệu ₫</span>
      </div>
    </section>

    <p class="ghi-chu-python">Phân tích mùa vụ (mô-đun Python): đang chờ V3, tạm chưa hiển thị.</p>
  </main>
</template>

<style scoped>
.dash {
  max-width: 1200px;
  margin: 0 auto;
  padding: 24px 16px 48px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.tieu-de {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
  flex-wrap: wrap;
}
h1 {
  margin: 0;
  font-size: clamp(24px, 3.5vw, 32px);
}
h2 {
  margin: 0 0 12px;
  font-size: 17px;
  font-weight: 600;
}
.ghi-chu {
  margin: 4px 0 0;
  color: #64748b;
  font-size: 14px;
}
.chon-thoi-gian {
  padding: 8px 12px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  background: #fff;
}
.luoi-chi-so {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}
.the-chi-so,
.the {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 16px;
  overflow: hidden;
}
.the-chi-so {
  position: relative;
  padding-top: 20px;
}
.vach {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
}
.nhan-chi-so {
  margin: 0 0 6px;
  color: #64748b;
  font-size: 14px;
}
.the-chi-so strong {
  font-size: 22px;
  display: block;
}
.the-chi-so small {
  font-size: 13px;
}
.len {
  color: #16a34a;
}
.xuong {
  color: #dc2626;
}
.hang-2,
.hang-3 {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 16px;
}
.dau-the {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 8px;
}
.dau-the h2 {
  margin-bottom: 0;
}
.nut-phu {
  padding: 6px 12px;
  border: 1px solid #2563eb;
  border-radius: 8px;
  background: #fff;
  color: #2563eb;
  cursor: pointer;
  font-size: 13px;
}
.bieu-do {
  width: 100%;
  height: auto;
  margin-top: 12px;
}
.truc {
  font-size: 13px;
  fill: #475569;
}
.tron-wrap {
  display: flex;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
  justify-content: center;
  margin-top: 12px;
}
.tron {
  width: 160px;
  height: 160px;
}
.tong-tron {
  font-size: 20px;
  font-weight: 600;
  fill: #0f172a;
}
.chu-thich {
  list-style: none;
  padding: 0;
  margin: 0;
  font-size: 14px;
}
.chu-thich li {
  margin-bottom: 8px;
}
.cham {
  display: inline-block;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  margin-right: 6px;
}
.bang-wrap {
  overflow-x: auto;
}
.bang {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}
.bang th,
.bang td {
  padding: 10px 8px;
  border-bottom: 1px solid #e5e7eb;
  text-align: left;
  white-space: nowrap;
}
.bang th {
  color: #64748b;
  font-weight: 500;
}
.so {
  text-align: right !important;
}
.nhan-trang-thai {
  padding: 3px 10px;
  border-radius: 999px;
  font-size: 12px;
}
.ok {
  background: #dcfce7;
  color: #166534;
}
.cho {
  background: #fef3c7;
  color: #92400e;
}
.huy {
  background: #e2e8f0;
  color: #334155;
}
.luoi-dich-vu {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}
.o-dich-vu {
  background: #f8fafc;
  border-radius: 10px;
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.o-dich-vu strong {
  font-size: 22px;
  color: #2563eb;
}
.o-dich-vu span {
  font-size: 13px;
  color: #475569;
}
.dong-thanh {
  display: grid;
  grid-template-columns: 110px 1fr 130px;
  gap: 12px;
  align-items: center;
  margin-bottom: 12px;
  font-size: 14px;
}
.thanh-nen {
  background: #f1f5f9;
  border-radius: 999px;
  height: 10px;
  overflow: hidden;
}
.thanh-tien {
  height: 100%;
  background: #2563eb;
  border-radius: 999px;
}
.so-tien {
  text-align: right;
  color: #334155;
}
.ghi-chu-python {
  background: #f8fafc;
  border: 1px dashed #cbd5e1;
  border-radius: 10px;
  padding: 14px;
  color: #64748b;
  font-size: 14px;
  margin: 0;
}

@media (max-width: 1000px) {
  .luoi-chi-so {
    grid-template-columns: repeat(2, 1fr);
  }
  .hang-2,
  .hang-3 {
    grid-template-columns: 1fr;
  }
}
@media (max-width: 560px) {
  .luoi-chi-so {
    grid-template-columns: 1fr;
  }
  .dong-thanh {
    grid-template-columns: 90px 1fr;
  }
  .so-tien {
    grid-column: 1 / -1;
    text-align: left;
  }
}
</style>