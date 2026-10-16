<script setup>
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { productService } from '../lib/productService'
import { luuDonMock } from '../lib/donMock'
import { useGioHang, tongTienMuc } from '../lib/gioHang'

const router = useRouter()
const { items, soMuc, tongTien, xoa } = useGioHang()

const dangThanhToan = ref('')
const loi = ref('')

function dinhDangTien(so) {
  return Number(so || 0).toLocaleString('vi-VN') + ' đ'
}

function formatNgay(ngay) {
  if (!ngay || !ngay.includes('-')) return ngay
  const [nam, thang, ngayTrongThang] = ngay.split('-')
  return `${ngayTrongThang}/${thang}/${nam}`
}

async function thanhToanMuc(muc) {
  loi.value = ''
  dangThanhToan.value = muc.id

  try {
    const don = await productService.giuCho({
      ma_san_pham: muc.ma_san_pham,
      ma_lich: muc.ma_lich,
      so_nguoi: muc.so_nguoi,
    })

    luuDonMock({
      ...don,
      ma_san_pham: muc.ma_san_pham,
      ten_san_pham: muc.ten_san_pham,
      ten_nha_cung_cap: muc.chao.ten_ncc,
      ngay_khoi_hanh: muc.ngay_khoi_hanh,
      so_nguoi: muc.so_nguoi,
      chao: muc.chao,
    })

    xoa(muc.id)
    router.push({ name: 'booking', params: { code: don.ma_don } })
  } catch (e) {
    loi.value =
      e.kiemTra?.so_nguoi?.[0] || e.message || 'Không giữ được chỗ, vui lòng thử lại.'
  } finally {
    dangThanhToan.value = ''
  }
}
</script>

<template>
  <div class="gio-hang">
    <RouterLink :to="{ name: 'list' }" class="quay-lai">← Tiếp tục xem sản phẩm</RouterLink>

    <h1>
      Giỏ dịch vụ của tôi
      <span class="so-muc">{{ soMuc }} mục</span>
    </h1>

    <div v-if="!soMuc" class="trong">
      <p>Giỏ hàng đang trống.</p>
      <RouterLink :to="{ name: 'list' }" class="nut-chinh link">Xem sản phẩm</RouterLink>
    </div>

    <div v-else class="bo-cuc">
      <section class="ds-muc">
        <p v-if="loi" class="loi">{{ loi }}</p>

        <article v-for="muc in items" :key="muc.id" class="muc">
          <div class="anh">{{ muc.ma_san_pham }}</div>

          <div class="noi-dung">
            <h3>{{ muc.ten_san_pham }}</h3>
            <p class="phu">
              {{ muc.chao.ten_ncc }} · {{ muc.so_nguoi }} người · {{ formatNgay(muc.ngay_khoi_hanh) }}
            </p>
            <p class="phu">Chính sách hủy: {{ muc.chao.chinh_sach_huy }}</p>

            <p class="gia">
              <strong>{{ dinhDangTien(tongTienMuc(muc).tong) }}</strong>
              <span class="phu">đã gồm phụ phí và thuế</span>
            </p>

            <div class="hanh-dong">
              <button
                type="button"
                class="nut-chinh"
                :disabled="!!dangThanhToan"
                @click="thanhToanMuc(muc)"
              >
                {{ dangThanhToan === muc.id ? 'Đang giữ chỗ...' : 'Thanh toán mục này' }}
              </button>
              <button type="button" class="nut-phu" @click="xoa(muc.id)">Xóa</button>
            </div>
          </div>
        </article>
      </section>

      <aside class="tong-ket">
        <h2>Tổng tạm tính</h2>
        <p class="tong-tien">{{ dinhDangTien(tongTien) }}</p>
        <p class="phu">{{ soMuc }} mục · đã gồm phụ phí và thuế (giả định trong bản demo)</p>
        <p class="ghi-chu">
          Mỗi mục được thanh toán riêng. Sau khi thanh toán xong, mục đó sẽ được xóa khỏi giỏ.
        </p>
      </aside>
    </div>
  </div>
</template>

<style scoped>
.gio-hang {
  max-width: 1040px;
  margin: 0 auto;
  padding: 24px 16px 48px;
  color: var(--color-text, #1f2937);
}

.quay-lai {
  color: var(--color-primary, #2563eb);
  text-decoration: none;
  font-size: 0.92rem;
}

h1 {
  font-size: 1.6rem;
  margin: 12px 0 20px;
}

.so-muc {
  font-size: 0.95rem;
  font-weight: 400;
  opacity: 0.7;
  margin-left: 8px;
}

.trong {
  text-align: center;
  padding: 40px 16px;
  border: 1px dashed var(--color-border, #e2e8f0);
  border-radius: 12px;
}

.bo-cuc {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 20px;
  align-items: start;
}

.ds-muc {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.muc {
  display: grid;
  grid-template-columns: 140px 1fr;
  gap: 16px;
  padding: 16px;
  border: 1px solid var(--color-border, #e2e8f0);
  border-radius: 12px;
  background: var(--color-surface, #fff);
}

.anh {
  min-height: 110px;
  border-radius: 8px;
  background: linear-gradient(135deg, #bfdbfe, #7dd3fc);
  color: #1e3a8a;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
}

.noi-dung h3 {
  margin: 0 0 6px;
  font-size: 1.05rem;
}

.noi-dung p {
  margin: 4px 0;
}

.phu {
  opacity: 0.75;
  font-size: 0.9rem;
}

.gia {
  margin-top: 8px !important;
}

.hanh-dong {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  margin-top: 12px;
}

.nut-chinh,
.nut-phu {
  min-height: 40px;
  padding: 0 16px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  font-size: 0.92rem;
}

.nut-chinh {
  background: var(--color-primary, #2563eb);
  color: #fff;
  border: none;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
}

.nut-chinh:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.nut-phu {
  background: transparent;
  color: var(--color-text, #1f2937);
  border: 1px solid var(--color-border, #e2e8f0);
}

.tong-ket {
  position: sticky;
  top: 16px;
  padding: 20px;
  border: 1px solid var(--color-border, #e2e8f0);
  border-radius: 12px;
  background: var(--color-surface, #fff);
}

.tong-ket h2 {
  font-size: 1.1rem;
  margin: 0 0 10px;
}

.tong-tien {
  font-size: 1.6rem;
  font-weight: 700;
  color: #ea580c;
  margin: 0 0 6px;
}

.ghi-chu {
  font-size: 0.85rem;
  opacity: 0.75;
  margin-top: 12px;
}

.loi {
  color: #b91c1c;
  font-size: 0.92rem;
}

@media (max-width: 800px) {
  .bo-cuc {
    grid-template-columns: 1fr;
  }

  .tong-ket {
    position: static;
  }
}

@media (max-width: 480px) {
  .muc {
    grid-template-columns: 1fr;
  }

  .anh {
    min-height: 90px;
  }
}
</style>