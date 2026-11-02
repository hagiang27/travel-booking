<script setup>
import { computed, onBeforeUnmount, ref } from 'vue'
import { RouterLink } from 'vue-router'
import QRCode from 'qrcode'
import { gio, soMucGio, tongTienGio, xoaKhoiGio } from '../lib/gioHang'

const dangThanhToan = ref(false)
const loiThanhToan = ref('')
const donDaTao = ref([])
const donThanhCong = ref([])
const thanhCong = ref(false)
const maQR = ref('')
const hetHanLuc = ref(0)
const conLaiGiay = ref(0)
const anhLoi = ref({})
let boDem = null

const tongQR = computed(() => donDaTao.value.reduce((s, d) => s + d.thanh_tien, 0))
const tongThanhCong = computed(() => donThanhCong.value.reduce((s, d) => s + d.thanh_tien, 0))

const thoiGianConLai = computed(() => {
  const s = conLaiGiay.value
  return `${String(Math.floor(s / 60)).padStart(2, '0')}:${String(s % 60).padStart(2, '0')}`
})

function dinhDang(so) {
  return new Intl.NumberFormat('vi-VN').format(so) + ' ₫'
}

function formatNgay(ngay) {
  if (!ngay || !ngay.includes('-')) return ngay
  const [nam, thang, ngayTrongThang] = ngay.split('-')
  return `${ngayTrongThang}/${thang}/${nam}`
}

function layXsrfToken() {
  const m = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]*)/)
  return m ? decodeURIComponent(m[1]) : ''
}

async function datMotMuc(m) {
  const res = await fetch('/api/v1/bookings', {
    method: 'POST',
    credentials: 'include',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'X-XSRF-TOKEN': layXsrfToken(),
    },
    body: JSON.stringify({
      ma_lich: m.ma_lich,
      so_luong: Number(m.so_nguoi),
    }),
  })
  const data = await res.json().catch(() => ({}))
  if (!res.ok) {
    throw new Error(data.message || `Lỗi ${res.status}`)
  }
  return data
}

async function taoQR(tong, maDons) {
  const noiDung = `DEMO THANH TOAN ${maDons.join(', ')} - ${dinhDang(tong)}`
  maQR.value = await QRCode.toDataURL(noiDung, { width: 240, margin: 1 })
}

function capNhatDemNguoc() {
  conLaiGiay.value = Math.max(0, Math.floor((hetHanLuc.value - Date.now()) / 1000))
  if (conLaiGiay.value === 0) clearInterval(boDem)
}

function batDauDemNguoc() {
  clearInterval(boDem)
  hetHanLuc.value = Date.now() + 15 * 60 * 1000
  capNhatDemNguoc()
  boDem = setInterval(capNhatDemNguoc, 1000)
}

async function thanhToan() {
  if (dangThanhToan.value || soMucGio.value === 0) return
  dangThanhToan.value = true
  loiThanhToan.value = ''

  const daDat = []
  try {
    for (const m of [...gio.mucHang]) {
      try {
        const ketQua = await datMotMuc(m)
        const du = ketQua.data || ketQua
        daDat.push({
          ma_lich: m.ma_lich,
          ma_don: du.ma_don,
          ten: m.ten_san_pham || 'Sản phẩm',
          ngay_khoi_hanh: m.ngay_khoi_hanh,
          so_nguoi: m.so_nguoi,
          thanh_tien: Number(du.tong_tien) || m.gia_1_nguoi * m.so_nguoi,
        })
        xoaKhoiGio(m.ma_lich)
      } catch (e) {
        loiThanhToan.value = `Không đặt được "${m.ten_san_pham || 'sản phẩm'}": ${e.message}`
        break
      }
    }

    if (daDat.length) {
      donDaTao.value = daDat
      const maDons = [...new Set(daDat.map((d) => d.ma_don))]
      await taoQR(tongQR.value, maDons)
      batDauDemNguoc()
      if (loiThanhToan.value) {
        loiThanhToan.value += `. Các đơn đã tạo (${maDons.join(', ')}) sẽ tự hết hạn sau 15 phút nếu không thanh toán.`
      }
    }
  } finally {
    dangThanhToan.value = false
  }
}

function dongQR() {
  clearInterval(boDem)
  donDaTao.value = []
  maQR.value = ''
}

function daThanhToanDemo() {
  clearInterval(boDem)
  donThanhCong.value = donDaTao.value
  donDaTao.value = []
  maQR.value = ''
  thanhCong.value = true
}

function dongThanhCong() {
  thanhCong.value = false
  donThanhCong.value = []
}

onBeforeUnmount(() => clearInterval(boDem))
</script>

<template>
  <main class="gio-hang">
    <p class="nhan">ĐẶT TOUR</p>
    <h1>Giỏ hàng</h1>

    <div class="bo-cuc">
      <section class="ds">
        <div v-if="thanhCong" class="thanh-cong-card">
          <div class="tick">✓</div>
          <h2>Thanh toán thành công</h2>
          <p>Cảm ơn bạn đã đặt tour. Thông tin đơn được lưu bên dưới.</p>

          <div class="ds-don">
            <div v-for="d in donThanhCong" :key="d.ma_don" class="dong-don">
              <div>
                <strong>{{ d.ten }}</strong>
                <span>Mã đơn: {{ d.ma_don }}</span>
                <span>Ngày: {{ formatNgay(d.ngay_khoi_hanh) }} · {{ d.so_nguoi }} người</span>
              </div>
              <b>{{ dinhDang(d.thanh_tien) }}</b>
            </div>
          </div>

          <div class="tong-hop">
            <span>Tổng đã thanh toán</span>
            <strong>{{ dinhDang(tongThanhCong) }}</strong>
          </div>

          <p class="ghi-chu-demo">
            Đây là bản demo: hệ thống chưa có API xác nhận thanh toán, nên đơn vẫn ở trạng thái chờ thanh toán trong cơ sở dữ liệu.
          </p>

          <RouterLink to="/san-pham" class="tiep-tuc" @click="dongThanhCong">← Tiếp tục xem sản phẩm</RouterLink>
        </div>

        <div v-else-if="donDaTao.length" class="xac-nhan">
          <span class="huy-hieu">Chờ thanh toán</span>
          <h2>Đã tạo đơn đặt chỗ</h2>
          <p>Chỗ được giữ trong 15 phút. Sau thời gian này, đơn sẽ tự hủy.</p>

          <div class="tien-trinh">
            <div class="xong">✓ Tạo đơn</div>
            <div class="dang">2. Thanh toán QR</div>
            <div>3. Xác nhận</div>
          </div>

          <ol class="buoc">
            <li>Mở ứng dụng ngân hàng hoặc ví điện tử.</li>
            <li>Quét mã QR ở khung bên phải.</li>
            <li>Kiểm tra số tiền và nội dung rồi xác nhận.</li>
            <li>Bấm "Đã thanh toán (demo)" để hoàn tất. Đây chỉ là bước demo, không chuyển tiền thật.</li>
          </ol>

          <RouterLink to="/san-pham" class="tiep-tuc">← Tiếp tục xem sản phẩm</RouterLink>
        </div>

        <div v-if="soMucGio === 0 && !donDaTao.length && !thanhCong" class="trong">
          <p>Giỏ hàng đang trống.</p>
          <RouterLink to="/san-pham" class="btn-chinh">Xem sản phẩm</RouterLink>
        </div>

        <article v-for="m in gio.mucHang" :key="m.ma_lich" class="muc">
          <div class="anh">
            <img
              v-if="!anhLoi[m.ma_san_pham]"
              :src="`/anh-san-pham/${m.ma_san_pham}.jpg`"
              :alt="m.ten_san_pham"
              @error="anhLoi[m.ma_san_pham] = true"
            />
            <span v-else>Ảnh sản phẩm</span>
          </div>
          <div class="thong-tin">
            <h2>{{ m.ten_san_pham || m.ten || 'Sản phẩm' }}</h2>
            <p>Nhà cung cấp: <strong>{{ m.ten_nha_cung_cap || 'Đang cập nhật' }}</strong></p>
            <p>Ngày khởi hành: <strong>{{ formatNgay(m.ngay_khoi_hanh) }}</strong></p>
            <p>Số người: <strong>{{ m.so_nguoi }}</strong></p>
          </div>
          <div class="gia">
            <strong>{{ dinhDang(m.gia_1_nguoi * m.so_nguoi) }}</strong>
            <small>{{ dinhDang(m.gia_1_nguoi) }} / người</small>
            <button class="xoa" @click="xoaKhoiGio(m.ma_lich)">✕ Xóa</button>
          </div>
        </article>
      </section>

      <aside class="tom-tat">
        <template v-if="thanhCong">
          <div class="qr thanh-toan-ok">
            <div class="tick">✓</div>
            <h2>Đã thanh toán</h2>
            <p>Mã đơn: <strong>{{ donThanhCong.map((d) => d.ma_don).join(', ') }}</strong></p>
            <p>Số tiền: <strong>{{ dinhDang(tongThanhCong) }}</strong></p>
            <button class="btn-phu" @click="dongThanhCong">Đóng</button>
          </div>
        </template>

        <template v-else-if="donDaTao.length">
          <div class="qr">
            <h2>Thanh toán qua QR</h2>
            <img :src="maQR" alt="Mã QR thanh toán demo" />

            <div class="mo-ta">
              <p class="tieu-de">Nội dung đơn đặt</p>
              <div v-for="d in donDaTao" :key="d.ma_don" class="dong-mo-ta">
                <strong>{{ d.ten }}</strong>
                <span>Mã đơn: {{ d.ma_don }}</span>
                <span>Ngày: {{ formatNgay(d.ngay_khoi_hanh) }} · {{ d.so_nguoi }} người</span>
                <span>Thành tiền: {{ dinhDang(d.thanh_tien) }}</span>
              </div>
            </div>

            <p class="tong">Tổng cộng: <strong>{{ dinhDang(tongQR) }}</strong></p>
            <p v-if="conLaiGiay > 0">Giữ chỗ còn: <strong>{{ thoiGianConLai }}</strong></p>
            <p v-else class="loi">Đã hết hạn giữ chỗ. Đơn sẽ tự hủy.</p>

            <button class="btn-chinh" :disabled="conLaiGiay === 0" @click="daThanhToanDemo">
              Đã thanh toán (demo)
            </button>
            <button class="btn-phu" @click="dongQR">Đóng</button>
          </div>
        </template>

        <template v-else>
          <div class="dong">
            <span>Tổng ({{ soMucGio }} mục)</span>
            <strong>{{ dinhDang(tongTienGio) }}</strong>
          </div>
          <button
            class="btn-chinh"
            :disabled="soMucGio === 0 || dangThanhToan"
            @click="thanhToan"
          >
            {{ dangThanhToan ? 'Đang đặt...' : 'Thanh toán' }}
          </button>
          <p class="ghi-chu">Bấm Thanh toán để tạo đơn và hiện mã QR.</p>
        </template>

        <p v-if="loiThanhToan" class="loi">{{ loiThanhToan }}</p>
      </aside>
    </div>
  </main>
</template>

<style scoped>
.gio-hang {
  max-width: 1100px;
  margin: 0 auto;
  padding: 32px 16px 48px;
}
.nhan {
  color: #2563eb;
  letter-spacing: 2px;
  font-size: 13px;
  margin: 0;
}
h1 {
  margin: 4px 0 24px;
  font-size: clamp(28px, 4vw, 40px);
}
.bo-cuc {
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: 24px;
  align-items: start;
}
.ds {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.muc {
  display: grid;
  grid-template-columns: 140px 1fr auto;
  gap: 16px;
  padding: 16px;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
}
.anh {
  height: 100px;
  background: #f1f5f9;
  border-radius: 8px;
  display: grid;
  place-items: center;
  color: #64748b;
  font-size: 14px;
  overflow: hidden;
}
.anh img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
}
.thong-tin h2 {
  margin: 0 0 8px;
  font-size: 18px;
  font-weight: 500;
}
.thong-tin p {
  margin: 4px 0;
  font-size: 15px;
  color: #475569;
}
.gia {
  text-align: right;
  display: flex;
  flex-direction: column;
  gap: 4px;
  align-items: flex-end;
}
.gia strong {
  font-size: 18px;
}
.gia small {
  color: #64748b;
}
.xoa {
  margin-top: 8px;
  background: none;
  border: none;
  color: #dc2626;
  cursor: pointer;
  font-size: 14px;
}
.tom-tat {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  position: sticky;
  top: 16px;
}
.dong {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 16px;
}
.dong strong {
  font-size: 22px;
}
.btn-chinh {
  display: block;
  width: 100%;
  padding: 12px;
  margin-top: 8px;
  border: none;
  border-radius: 8px;
  background: #2563eb;
  color: #fff;
  font-weight: 600;
  text-align: center;
  text-decoration: none;
  cursor: pointer;
}
.btn-chinh:disabled {
  background: #94a3b8;
  cursor: not-allowed;
}
.btn-phu {
  display: block;
  width: 100%;
  padding: 10px;
  margin-top: 8px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #fff;
  color: #334155;
  cursor: pointer;
}
.ghi-chu {
  font-size: 13px;
  color: #64748b;
  margin: 12px 0 0;
}
.loi {
  margin: 12px 0 0;
  font-size: 14px;
  color: #dc2626;
}
.trong {
  text-align: center;
  padding: 48px 16px;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
}
.trong .btn-chinh {
  display: inline-block;
  width: auto;
  padding: 10px 20px;
}

/* Khối bên trái: đơn chờ thanh toán */
.xac-nhan {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  padding: 28px;
  box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
}
.huy-hieu {
  display: inline-block;
  background: #fef3c7;
  color: #b45309;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  padding: 5px 12px;
  border-radius: 999px;
}
.xac-nhan h2 {
  margin: 12px 0 6px;
  font-size: 24px;
  font-weight: 700;
  color: #0f172a;
}
.xac-nhan > p {
  margin: 0;
  color: #64748b;
  font-size: 14px;
  line-height: 1.6;
}
.tien-trinh {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
  margin: 24px 0 8px;
}
.tien-trinh div {
  padding: 10px 8px;
  border-radius: 12px;
  background: #f1f5f9;
  border: 1px solid transparent;
  color: #64748b;
  font-size: 13px;
  font-weight: 500;
  text-align: center;
}
.tien-trinh .xong {
  background: #dcfce7;
  border-color: #bbf7d0;
  color: #166534;
  font-weight: 600;
}
.tien-trinh .dang {
  background: #eff6ff;
  border-color: #bfdbfe;
  color: #1d4ed8;
  font-weight: 600;
}
.buoc {
  margin: 16px 0 0;
  padding: 0;
  list-style: none;
  counter-reset: buoc;
}
.buoc li {
  counter-increment: buoc;
  position: relative;
  padding: 12px 0 12px 40px;
  border-top: 1px dashed #e2e8f0;
  color: #334155;
  font-size: 15px;
  line-height: 1.5;
}
.buoc li::before {
  content: counter(buoc);
  position: absolute;
  left: 0;
  top: 12px;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background: #2563eb;
  color: #fff;
  font-size: 13px;
  font-weight: 700;
  display: grid;
  place-items: center;
}
.tiep-tuc {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  margin-top: 20px;
  padding: 10px 16px;
  border-radius: 10px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  color: #2563eb;
  font-weight: 600;
  text-decoration: none;
}

/* Khối bên trái: thanh toán thành công */
.thanh-cong-card {
  background: #fff;
  border: 1px solid #bbf7d0;
  border-radius: 16px;
  padding: 28px;
  box-shadow: 0 8px 24px rgba(22, 101, 52, 0.08);
}
.tick {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background: #16a34a;
  color: #fff;
  font-size: 26px;
  font-weight: 700;
  display: grid;
  place-items: center;
  margin: 0 auto 12px;
}
.thanh-cong-card h2 {
  margin: 0 0 6px;
  text-align: center;
  font-size: 24px;
  color: #0f172a;
}
.thanh-cong-card > p {
  margin: 0;
  text-align: center;
  color: #64748b;
  font-size: 14px;
}
.ds-don {
  margin-top: 20px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  overflow: hidden;
}
.dong-don {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  border-top: 1px solid #e2e8f0;
}
.dong-don:first-child {
  border-top: none;
}
.dong-don div {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.dong-don strong {
  color: #0f172a;
  font-size: 15px;
}
.dong-don span {
  color: #64748b;
  font-size: 13px;
}
.dong-don b {
  color: #0f172a;
  font-size: 15px;
  white-space: nowrap;
}
.tong-hop {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 16px;
  padding: 14px 16px;
  background: #f0fdf4;
  border-radius: 10px;
  color: #166534;
  font-size: 14px;
}
.tong-hop strong {
  font-size: 18px;
}
.ghi-chu-demo {
  margin: 16px 0 0;
  font-size: 13px;
  color: #64748b;
  line-height: 1.6;
  text-align: center;
}
.thanh-cong-card .tiep-tuc {
  display: flex;
  justify-content: center;
}

/* Khung QR bên phải */
.tom-tat:has(.qr) {
  background: transparent;
  border: none;
  padding: 0;
}
.qr {
  text-align: center;
  background: linear-gradient(180deg, #eff6ff 0%, #ffffff 70%);
  border: 1px solid #dbeafe;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 8px 24px rgba(37, 99, 235, 0.08);
}
.qr h2 {
  font-size: 18px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 14px;
}
.qr img {
  width: 200px;
  height: 200px;
  padding: 10px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.15);
  margin: 0 auto 16px;
}
.mo-ta {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 14px;
  text-align: left;
}
.tieu-de {
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: #2563eb;
  margin: 0 0 4px;
}
.dong-mo-ta {
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding: 10px 0;
  border-top: 1px dashed #cbd5e1;
}
.dong-mo-ta strong {
  font-size: 15px;
  line-height: 1.35;
  color: #0f172a;
}
.dong-mo-ta span {
  font-size: 13px;
  color: #64748b;
}
.qr > p {
  margin: 8px 0;
  font-size: 14px;
  color: #475569;
}
.qr > p strong {
  color: #0f172a;
  font-variant-numeric: tabular-nums;
}
.qr > .tong {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 14px 0 8px;
  padding: 12px 14px;
  background: #2563eb;
  color: #fff;
  border-radius: 10px;
  font-size: 14px;
}
.qr > .tong strong {
  color: #fff;
  font-size: 18px;
}
.qr .btn-chinh {
  margin-top: 12px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}
.qr .btn-phu {
  margin-top: 8px;
  border-radius: 10px;
  color: #475569;
}
.thanh-toan-ok .tick {
  margin-bottom: 12px;
}

@media (max-width: 800px) {
  .bo-cuc {
    grid-template-columns: 1fr;
  }
  .tom-tat {
    position: static;
  }
}
@media (max-width: 560px) {
  .muc {
    grid-template-columns: 1fr;
  }
  .gia {
    text-align: left;
    align-items: flex-start;
  }
}
</style>