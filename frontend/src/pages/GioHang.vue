<script setup>
import { RouterLink } from 'vue-router'
import { gio, soMucGio, tongTienGio, xoaKhoiGio } from '../lib/gioHang'

function dinhDang(so) {
  return new Intl.NumberFormat('vi-VN').format(so) + ' ₫'
}
</script>

<template>
  <main class="gio-hang">
    <p class="nhan">ĐẶT TOUR</p>
    <h1>Giỏ hàng</h1>

    <div v-if="soMucGio === 0" class="trong">
      <p>Giỏ hàng đang trống.</p>
      <RouterLink to="/san-pham" class="btn-chinh">Xem sản phẩm</RouterLink>
    </div>

    <div v-else class="bo-cuc">
      <section class="ds">
        <article v-for="m in gio.mucHang" :key="m.ma_lich" class="muc">
          <div class="anh">Ảnh sản phẩm</div>
          <div class="thong-tin">
            <h2>{{ m.ten }}</h2>
            <p>Ngày khởi hành: <strong>{{ m.ngay_khoi_hanh }}</strong></p>
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
        <div class="dong">
          <span>Tổng ({{ soMucGio }} mục)</span>
          <strong>{{ dinhDang(tongTienGio) }}</strong>
        </div>
        <button class="btn-chinh" disabled>Thanh toán</button>
        <p class="ghi-chu">Thanh toán sẽ mở khi backend có API đặt chỗ.</p>
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
  grid-template-columns: 1fr 300px;
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
.ghi-chu {
  font-size: 13px;
  color: #64748b;
  margin: 12px 0 0;
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