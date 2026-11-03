// frontend/src/lib/authService.js
import { MOCK_USERS } from '../mock/mockUsers'

const USE_MOCK = import.meta.env.VITE_USE_MOCK === 'true'
const KHOA = 'mock-nguoi-dung'

function layCsrf() {
  return document.querySelector('meta[name="csrf-token"]')?.content ?? ''
}

// ---------- Bản mock ----------
const mock = {
  async dangNhap(tenDangNhap, matKhau) {
    const u = MOCK_USERS.find(
      (x) => x.ten_dang_nhap === tenDangNhap && x.mat_khau === matKhau
    )
    if (!u) throw new Error('Thông tin đăng nhập không đúng.')
    const { mat_khau: _bo, ...nguoiDung } = u
    sessionStorage.setItem(KHOA, JSON.stringify(nguoiDung))
    return nguoiDung
  },
  async layNguoiDungHienTai() {
    const raw = sessionStorage.getItem(KHOA)
    return raw ? JSON.parse(raw) : null
  },
  async dangXuat() {
    sessionStorage.removeItem(KHOA)
  },
}

// ---------- Bản thật (theo V3: /api/v1, session/cookie) ----------
async function layNguoiDungThat() {
  const res = await fetch('/api/v1/me', {
    credentials: 'same-origin',
    headers: { Accept: 'application/json' },
  })
  if (!res.ok) return null
  const data = await res.json()
  // Chưa rõ V3 bọc trong khóa "user" hay trả thẳng. Xác nhận khi test.
  return data.user ?? data
}

const real = {
  async dangNhap(tenDangNhap, matKhau) {
    const res = await fetch('/api/v1/login', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': layCsrf(),
      },
      body: JSON.stringify({ ten_dang_nhap: tenDangNhap, mat_khau: matKhau }),
    })
    if (!res.ok) throw new Error('Thông tin đăng nhập không đúng.')
    // Lấy lại thông tin người dùng từ phiên vừa tạo
    const nguoiDung = await layNguoiDungThat()
    if (!nguoiDung) throw new Error('Đăng nhập thành công nhưng không lấy được thông tin người dùng.')
    return nguoiDung
  },
  async layNguoiDungHienTai() {
    return layNguoiDungThat()
  },
  async dangXuat() {
    await fetch('/api/v1/logout', {
      method: 'POST',
      credentials: 'same-origin',
      headers: { Accept: 'application/json', 'X-CSRF-TOKEN': layCsrf() },
    })
  },
}

export const authService = USE_MOCK ? mock : real