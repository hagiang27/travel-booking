<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { RouterLink } from 'vue-router'
import QRCode from 'qrcode'
import { tinhGia } from '../lib/gia'
import { layDonMock } from '../lib/donMock'

const props = defineProps({
  code: { type: String, required: true },
})

const don = ref(layDonMock(props.code))
const conLai = ref(0)
const maQr = ref('')
const daThanhToan = ref(false)
let bo_dem = null

// Đơn từ trang so sánh có nhà cung cấp (chao).
// Đơn từ trang chi tiết không có chao, nên dùng giá gốc của sản phẩm.
const chiTiet = computed(() => {
  if (!don.value) return null
  const chao = don.value.chao || {
    gia_nguoi: don.value.gia_co_ban,
    phu_thu_nguoi: 0,
    phu_thu_cuoi_tuan_nguoi: 0,
  }
  return tinhGia(chao, {
    ngayKhoiHanh: don.value.ngay_khoi_hanh,
    soNguoi: don.value.so_nguoi,
  })
})

const hetGio = computed(() => !!don.value && conLai.value <= 0 && !daThanhToan.value)

function capNhatDemNguoc() {
  if (!don.value) return
  const hetHan = new Date(don.value.het_han).getTime()
  conLai.value = Math.max(0, Math.floor((hetHan - Date.now()) / 1000))
  if (conLai.value === 0 && bo_dem) {
    clearInterval(bo_dem)
    bo_dem = null
  }
}

function dinhDangGio(giay) {
  const phut = Math.floor(giay / 60)
  const giayDu = giay % 60
  return String(phut).padStart(2, '0') + ':' + String(giayDu).padStart(2, '0')
}

function dinhDangTien(so) {
  return Number(so || 0).toLocaleString('vi-VN') + ' đ'
}

function xacNhanThanhToan() {
  daThanhToan.value = true
  if (bo_dem) clearInterval(bo_dem)
}

onMounted(async () => {
  if (!don.value) return
  capNhatDemNguoc()
  bo_dem = setInterval(capNhatDemNguoc, 1000)

  if (chiTiet.value) {
    const noiDungQr = 'DEMO|' + don.value.ma_don + '|' + chiTiet.value.tong
    maQr.value = await QRCode.toDataURL(noiDungQr, { width: 220, margin: 1 })
  }
})

onUnmounted(() => {
  if (bo_dem) clearInterval(bo_dem)
})
</script>

<template>
  <div class="trang-thanh-toan">
    <RouterLink :to="{ name: 'list' }" class="quay-lai">← Quay lại danh sách</RouterLink>

    <!-- Không tìm thấy đơn -->
    <section v-if="!don" class="khoi">
      <h1>Không tìm thấy đơn {{ code }}</h1>
      <p class="can-giua">
        Đơn có thể đã hết hạn, hoặc được tạo trên một trình duyệt khác.
        Hãy giữ chỗ lại từ trang sản phẩm.
      </p>
    </section>

    <template v-else>
      <!-- Tiêu đề và tóm tắt đơn -->
      <section class="khoi can-giua">
        <p class="nhan-demo">Thông tin thanh toán mô phỏng, không có giao dịch thật</p>
        <h1>Thanh toán đơn {{ don.ma_don }}</h1>
        <p class="tom-tat">
          <strong>{{ don.ten_san_pham }}</strong>
          · {{ don.ten_nha_cung_cap }}
        </p>
        <p class="tom-tat">
          Ngày khởi hành: {{ don.ngay_khoi_hanh }} · {{ don.so_nguoi }} người
        </p>

        <div v-if="daThanhToan" class="thong-bao thanh-cong">
          Đã thanh toán (mô phỏng). Đơn {{ don.ma_don }} đã được ghi nhận trên trình duyệt này.
        </div>
        <div v-else-if="hetGio" class="thong-bao loi">
          Đơn đã hết hạn. Hãy giữ chỗ lại từ trang sản phẩm.
        </div>
        <div v-else class="thong-bao">
          Thời gian còn lại để thanh toán: <strong>{{ dinhDangGio(conLai) }}</strong>
        </div>
      </section>

      <!-- Chi tiết giá -->
      <section v-if="chiTiet" class="khoi">
        <h2>Chi tiết giá</h2>
        <table class="bang-gia">
          <tbody>
            <tr>
              <td>Tiền hàng ({{ don.so_nguoi }} người)</td>
              <td class="so-tien">{{ dinhDangTien(chiTiet.tien_hang) }}</td>
            </tr>
            <tr>
              <td>{{ chiTiet.cuoi_tuan ? 'Phụ phí cuối tuần' : 'Phụ phí' }}</td>
              <td class="so-tien">{{ dinhDangTien(chiTiet.phu_thu) }}</td>
            </tr>
            <tr>
              <td>Trước thuế</td>
              <td class="so-tien">{{ dinhDangTien(chiTiet.truoc_thue) }}</td>
            </tr>
            <tr>
              <td>Thuế (8%)</td>
              <td class="so-tien">{{ dinhDangTien(chiTiet.thue) }}</td>
            </tr>
            <tr class="dong-tong">
              <td><strong>Tổng cộng</strong></td>
              <td class="so-tien"><strong>{{ dinhDangTien(chiTiet.tong) }}</strong></td>
            </tr>
          </tbody>
        </table>
        <p class="ghi-chu can-giua">
          Phụ phí và thuế trong bản demo là giả định để minh họa.
        </p>
      </section>

      <!-- Bao gồm / không bao gồm / chính sách hủy -->
      <section v-if="don.chao" class="khoi">
        <div class="hai-cot">
          <div>
            <h3>Đã bao gồm</h3>
            <ul>
              <li v-for="muc in don.chao.bao_gom" :key="muc">{{ muc }}</li>
            </ul>
          </div>
          <div>
            <h3>Chưa bao gồm</h3>
            <ul>
              <li v-for="muc in don.chao.khong_bao_gom" :key="muc">{{ muc }}</li>
            </ul>
          </div>
        </div>
        <h3>Chính sách hủy</h3>
        <p>{{ don.chao.chinh_sach_huy }}</p>
      </section>

      <!-- Thanh toán -->
      <section v-if="!daThanhToan && !hetGio" class="khoi">
        <h2>Chuyển khoản (mô phỏng)</h2>
        <div class="thanh-toan">
          <img v-if="maQr" :src="maQr" alt="Mã QR demo" class="ma-qr" />
          <div class="thong-tin-ck">
            <p>Ngân hàng: <strong>Ngân hàng Demo</strong></p>
            <p>Số tài khoản: <strong>0000 0000 0000</strong></p>
            <p>Chủ tài khoản: <strong>NHOM 4 DEMO</strong></p>
            <p>Số tiền: <strong>{{ dinhDangTien(chiTiet && chiTiet.tong) }}</strong></p>
            <p>Nội dung chuyển khoản: <strong>{{ don.ma_don }}</strong></p>
          </div>
        </div>

        <button type="button" class="nut-chinh" @click="xacNhanThanhToan">
          Tôi đã thanh toán (mô phỏng)
        </button>
      </section>
    </template>
  </div>
</template>

<style scoped>
.trang-thanh-toan {
  max-width: 880px;
  margin: 0 auto;
  padding: 24px 16px 48px;
  color: var(--color-text, #1f2937);
}

.trang-thanh-toan h1 {
  font-size: 1.6rem;
  line-height: 1.3;
  margin: 0 0 8px;
  text-align: center;
}

.trang-thanh-toan h2 {
  font-size: 1.25rem;
  line-height: 1.3;
  margin: 0 0 12px;
  text-align: center;
}

.trang-thanh-toan h3 {
  font-size: 1rem;
  line-height: 1.3;
  margin: 12px 0 6px;
  text-align: center;
}

.quay-lai {
  display: block;
  text-align: center;
  color: var(--color-primary, #2563eb);
  text-decoration: none;
  font-size: 0.92rem;
}

.khoi {
  background: var(--color-surface, #fff);
  border: 1px solid var(--color-border, #e2e8f0);
  border-radius: 12px;
  padding: 20px;
  margin-top: 16px;
}

.can-giua {
  text-align: center;
}

.nhan-demo {
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #b45309;
  text-align: center;
  margin: 0 0 8px;
}

.tom-tat {
  margin: 4px 0;
  text-align: center;
}

.thong-bao {
  margin-top: 12px;
  padding: 12px;
  border-radius: 8px;
  background: #eff6ff;
  text-align: center;
}

.thong-bao.thanh-cong {
  background: #ecfdf5;
  color: #065f46;
}

.thong-bao.loi {
  background: #fef2f2;
  color: #991b1b;
}

.bang-gia {
  width: 100%;
  border-collapse: collapse;
}

.bang-gia td {
  padding: 8px 4px;
  border-bottom: 1px solid var(--color-border, #e2e8f0);
  text-align: left;
}

.bang-gia .so-tien {
  text-align: right;
  white-space: nowrap;
}

.bang-gia .dong-tong td {
  border-bottom: none;
  font-size: 1.05rem;
}

.ghi-chu {
  font-size: 0.85rem;
  opacity: 0.75;
}

.hai-cot {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 16px;
}

.hai-cot h3,
.hai-cot ul {
  text-align: left;
}

.thanh-toan {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  align-items: center;
  justify-content: center;
}

.ma-qr {
  width: 220px;
  height: 220px;
  background: #fff;
  padding: 8px;
  border-radius: 8px;
}

.thong-tin-ck p {
  margin: 6px 0;
}

.nut-chinh {
  display: block;
  margin: 16px auto 0;
  background: var(--color-primary, #2563eb);
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 12px 18px;
  font-weight: 700;
  cursor: pointer;
}

.nut-chinh:hover {
  opacity: 0.92;
}

@media (max-width: 480px) {
  .trang-thanh-toan h1 {
    font-size: 1.35rem;
  }
}
</style>