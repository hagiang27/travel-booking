import { reactive, computed } from 'vue'
import { tinhGia } from './gia'

const KHOA = 'gio-hang'

function docGio() {
  try {
    const raw = localStorage.getItem(KHOA)
    return raw ? JSON.parse(raw) : []
  } catch (e) {
    return []
  }
}

const trangThai = reactive({ muc: docGio() })

function luu() {
  try {
    localStorage.setItem(KHOA, JSON.stringify(trangThai.muc))
  } catch (e) {
    // Trình duyệt chặn localStorage thì bỏ qua
  }
}

export function tongTienMuc(muc) {
  return tinhGia(muc.chao, {
    ngayKhoiHanh: muc.ngay_khoi_hanh,
    soNguoi: muc.so_nguoi,
  })
}

export function useGioHang() {
  const items = computed(() => trangThai.muc)
  const soMuc = computed(() => trangThai.muc.length)
  const tongTien = computed(() =>
    trangThai.muc.reduce((tong, muc) => tong + tongTienMuc(muc).tong, 0)
  )

  function them(muc) {
    if (trangThai.muc.some((m) => m.id === muc.id)) return false
    trangThai.muc = [...trangThai.muc, muc]
    luu()
    return true
  }

  function xoa(id) {
    trangThai.muc = trangThai.muc.filter((m) => m.id !== id)
    luu()
  }

  return { items, soMuc, tongTien, them, xoa }
}