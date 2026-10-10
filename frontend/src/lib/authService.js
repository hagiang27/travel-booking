// frontend/src/lib/authService.js
import { MOCK_USERS } from '../mock/mockUsers'

const USE_MOCK = import.meta.env.VITE_USE_MOCK === 'true'
const KHOA = 'mock-nguoi-dung'

const mock = {
  async dangNhap(tenDangNhap, matKhau) {
    const u = MOCK_USERS.find(
      x => x.ten_dang_nhap === tenDangNhap && x.mat_khau === matKhau
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

// Bản thật: sẽ kiểm tra lại khi V3 hoàn thiện API
const real = {
  async dangNhap(tenDangNhap, matKhau) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content
    const res = await fetch('/api/auth/dang-nhap', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrf ?? '',
      },
      body: JSON.stringify({ ten_dang_nhap: tenDangNhap, mat_khau: matKhau }),
    })
    if (!res.ok) throw new Error('Thông tin đăng nhập không đúng.')
    return res.json()
  },
  async layNguoiDungHienTai() { return null },
  async dangXuat() {},
}

export const authService = USE_MOCK ? mock : real
