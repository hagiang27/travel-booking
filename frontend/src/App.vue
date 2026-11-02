<template>
  <div class="app">
  <a class="lien-bo-qua" href="#noi-dung-chinh">Bỏ qua điều hướng</a>
    <header class="header">
      <div class="brand">
        <h1>Nhà Việt</h1>
      </div>

      <nav class="navigation" aria-label="Điều hướng chính">
        <button
          type="button"
          :class="{ active: route.name === 'home' }"
          @click="router.push('/')"
        >
          Trang chủ
        </button>

        <button
          v-if="!user || laKhach"
          type="button"
          :class="{ active: route.name === 'list' || route.name === 'detail' }"
          @click="router.push('/san-pham')"
        >
          Sản phẩm
        </button>

        <button
          v-if="laNhaCungCap"
          type="button"
          :class="{ active: route.path.startsWith('/quan-tri/san-pham') }"
          @click="router.push('/quan-tri/san-pham')"
        >
          Quản lý sản phẩm
        </button>

        <button
          v-if="laQuanTri"
          type="button"
          :class="{ active: route.path.startsWith('/quan-tri/bang-dieu-khien') }"
          @click="router.push('/quan-tri/bang-dieu-khien')"
        >
          Bảng điều khiển
        </button>

        <template v-if="user">
          <span class="nguoi-dung" :title="user.ten_dang_nhap">
            {{ user.ho_ten }}
          </span>
          <button type="button" @click="dangXuat">
            Đăng xuất
          </button>
        </template>

        <button
          v-else
          type="button"
          :class="{ active: route.name === 'login' }"
          @click="router.push('/dang-nhap')"
        >
          Đăng nhập
        </button>
      </nav>

      <div class="hanh-dong-phai">
        <RouterLink
          v-if="!user || laKhach"
          to="/gio-hang"
          class="gio-hang-link"
          :class="{ active: route.name === 'gio-hang' }"
          :aria-label="`Giỏ hàng, ${soMucGio} mục`"
        >
          <svg
            width="22"
            height="22"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
          >
            <circle cx="9" cy="21" r="1" />
            <circle cx="20" cy="21" r="1" />
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
          </svg>
          <span v-if="soMucGio > 0" class="gio-hang-so">{{ soMucGio }}</span>
        </RouterLink>

        <button
          type="button"
          class="theme-button"
          :aria-label="
            isDark
              ? 'Chuyển sang giao diện sáng'
              : 'Chuyển sang giao diện tối'
          "
          @click="toggleTheme"
        >
          {{ isDark ? '☀️ Light' : '🌙 Dark' }}
        </button>
      </div>
    </header>


        <main id="noi-dung-chinh">
      <router-view />
    </main>

    <AppFooter />
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { authService } from './lib/authService'
import { soMucGio } from './lib/gioHang'
import AppFooter from './components/AppFooter.vue'

const KHOA_THEME = 'theme'

const route = useRoute()
const router = useRouter()
const isDark = ref(false)
const user = ref(null)

const laKhach = computed(() => !!user.value && user.value.vai_tro === 'khach_hang')
const laNhaCungCap = computed(() => !!user.value && user.value.vai_tro === 'nha_cung_cap')
const laQuanTri = computed(() => !!user.value && user.value.vai_tro === 'quan_tri')

async function taiNguoiDung() {
  user.value = await authService.layNguoiDungHienTai()
}

async function dangXuat() {
  await authService.dangXuat()
  try {
    localStorage.removeItem('nha-viet-gio-hang')
  } catch {
    // trình duyệt chặn lưu trữ thì bỏ qua
  }
  window.location.href = '/dang-nhap'
}

function apDungTheme(toi) {
  isDark.value = toi
  document.documentElement.setAttribute('data-theme', toi ? 'dark' : 'light')
  try {
    localStorage.setItem(KHOA_THEME, toi ? 'dark' : 'light')
  } catch {
    // trình duyệt chặn lưu trữ thì bỏ qua
  }
}

function toggleTheme() {
  apDungTheme(!isDark.value)
}

onMounted(() => {
  let luu = null
  try {
    luu = localStorage.getItem(KHOA_THEME)
  } catch {
    // bỏ qua
  }
  apDungTheme(luu === 'dark')
  taiNguoiDung()
})

watch(() => route.fullPath, taiNguoiDung)
</script>

<style scoped>
.app {
  width: 100%;
  max-width: none;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: var(--color-bg);
  color: var(--color-text);
  font-family: var(--font-family);
}

.lien-bo-qua {
  position: absolute;
  left: -9999px;
  top: 8px;
  z-index: 100;
  padding: 8px 12px;
  background: #ffffff;
  color: #000000;
  border-radius: 6px;
}

.lien-bo-qua:focus {
  left: 8px;
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-6);
  padding: var(--space-4) var(--space-6);
  border-bottom: 1px solid var(--color-border);
  background: var(--color-surface);
}

.brand h1 {
  margin: 0;
  font-size: var(--font-size-h3);
  font-weight: var(--font-weight-bold);
}

.navigation {
  display: flex;
  gap: var(--space-2);
  flex-wrap: wrap;
  align-items: center;
}

.hanh-dong-phai {
  display: flex;
  align-items: center;
  gap: var(--space-2);
}

.navigation button,
.theme-button {
  min-height: 40px;
  padding: 0 var(--space-4);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background: var(--color-surface);
  color: var(--color-text);
  font-size: var(--font-size-small);
  cursor: pointer;
}

.navigation button:hover,
.theme-button:hover {
  background: var(--color-surface-2);
}

.navigation button:focus-visible,
.theme-button:focus-visible,
.gio-hang-link:focus-visible {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
}

.navigation button.active {
  background: var(--color-primary);
  border-color: var(--color-primary);
  color: var(--color-bg);
}

.gio-hang-link {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background: var(--color-surface);
  color: var(--color-text);
  text-decoration: none;
}

.gio-hang-link:hover {
  background: var(--color-surface-2);
}

.gio-hang-link.active {
  border-color: var(--color-primary);
  color: var(--color-primary);
}

.gio-hang-so {
  position: absolute;
  top: -6px;
  right: -6px;
  min-width: 18px;
  height: 18px;
  padding: 0 5px;
  border-radius: 999px;
  background: var(--color-primary);
  color: var(--color-bg);
  font-size: 11px;
  font-weight: var(--font-weight-bold);
  line-height: 18px;
  text-align: center;
}

.nguoi-dung {
  font-size: var(--font-size-small);
  font-weight: var(--font-weight-semibold);
  padding: 0 var(--space-2);
}

.theme-button {
  white-space: nowrap;
}

@media (max-width: 768px) {
  .header {
    flex-wrap: wrap;
  }

  .navigation {
    width: 100%;
    justify-content: center;
    order: 3;
  }
}

@media (max-width: 480px) {
  .header {
    padding: var(--space-3) var(--space-4);
  }

  .navigation button,
  .theme-button {
    padding: 0 var(--space-3);
  }
}
.lien-bo-qua {
  position: absolute;
  left: -9999px;
  top: 8px;
  z-index: 100;
  padding: 8px 12px;
  background: #ffffff;
  color: #000000;
  border-radius: 6px;
}

.lien-bo-qua:focus {
  left: 8px;
}
</style>