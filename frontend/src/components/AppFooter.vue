<script setup>
import { RouterLink } from 'vue-router'

const namHienTai = new Date().getFullYear()
</script>

<template>
  <footer class="app-footer">
    <div class="footer-noi-dung">
      <div class="footer-cot footer-thuong-hieu">
        <strong class="footer-ten">Nhà Việt</strong>
        <p>
          Nền tảng đặt dịch vụ du lịch đa nhà cung cấp, có công cụ so sánh giá minh bạch.
        </p>
      </div>

      <div class="footer-cot">
        <h4>Khám phá</h4>
        <RouterLink :to="{ name: 'list' }">Sản phẩm</RouterLink>
        <RouterLink :to="{ name: 'list', query: { loai: 'tour' } }">Tour</RouterLink>
        <RouterLink :to="{ name: 'list', query: { loai: 'stay' } }">Lưu trú</RouterLink>
        <RouterLink :to="{ name: 'list', query: { loai: 'transfer' } }">Đưa đón</RouterLink>
      </div>

      <div class="footer-cot">
        <h4>Hỗ trợ</h4>
        <a href="#">Hướng dẫn đặt dịch vụ</a>
        <a href="#">Chính sách hủy</a>
        <a href="#">Câu hỏi thường gặp</a>
        <a href="#">Liên hệ</a>
      </div>

      <div class="footer-cot">
        <h4>Dành cho đối tác</h4>
        <RouterLink :to="{ name: 'login' }">Đăng nhập nhà cung cấp</RouterLink>
        <a href="#">Đăng ký đối tác</a>
      </div>
    </div>

    <div class="footer-day">
      <span>© Đại học Phhenikaa</span>
      <span class="footer-luu-y">0987654321</span>
    </div>
  </footer>
</template>

<style scoped>
.app-footer {
  background: #0f172a;
  color: #cbd5e1;
  margin-top: auto;
  padding: 40px 16px 24px;
  font-size: 0.92rem;
}

.footer-noi-dung {
  max-width: 1120px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1.6fr 1fr 1fr 1fr;
  gap: 28px;
}

.footer-cot {
  display: flex;
  flex-direction: column;
  gap: 8px;
  min-width: 0;
}

.footer-cot h4 {
  color: #fff;
  font-size: 0.95rem;
  margin: 0 0 4px;
}

.footer-cot a {
  color: #cbd5e1;
  text-decoration: none;
}

.footer-cot a:hover {
  color: #fff;
  text-decoration: underline;
}

.footer-thuong-hieu p {
  margin: 6px 0 0;
  line-height: 1.5;
  opacity: 0.85;
}

.footer-ten {
  color: #fff;
  font-size: 1.1rem;
}

.footer-day {
  max-width: 1120px;
  margin: 28px auto 0;
  padding-top: 18px;
  border-top: 1px solid #1e293b;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 8px;
  font-size: 0.85rem;
  opacity: 0.8;
}

@media (max-width: 800px) {
  .footer-noi-dung {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 480px) {
  .footer-noi-dung {
    grid-template-columns: 1fr;
  }
}
</style>