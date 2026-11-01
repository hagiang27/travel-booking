import { reactive, computed, watch } from 'vue'

const KHOA_LUU = 'nha-viet-gio-hang'
const GIOI_HAN_NGUOI = 30

function docGioDaLuu() {
  try {
    const du = JSON.parse(localStorage.getItem(KHOA_LUU) || '[]')
    return Array.isArray(du) ? du : []
  } catch {
    return []
  }
}

export const gio = reactive({ mucHang: docGioDaLuu() })

watch(
  () => gio.mucHang,
  (ds) => {
    try {
      localStorage.setItem(KHOA_LUU, JSON.stringify(ds))
    } catch {
      // Trình duyệt chặn lưu thì giỏ vẫn hoạt động trong phiên hiện tại
    }
  },
  { deep: true }
)

export const tongTienGio = computed(() =>
  gio.mucHang.reduce((tong, m) => tong + m.gia_1_nguoi * m.so_nguoi, 0)
)

export const soMucGio = computed(() => gio.mucHang.length)

export function themVaoGio(mucMoi) {
  const soNguoi = Math.min(
    Math.max(1, Number(mucMoi.so_nguoi) || 1),
    mucMoi.con_lai ?? GIOI_HAN_NGUOI,
    GIOI_HAN_NGUOI
  )
  const viTri = gio.mucHang.findIndex((m) => m.ma_lich === mucMoi.ma_lich)
  if (viTri >= 0) {
    gio.mucHang[viTri].so_nguoi = soNguoi
  } else {
    gio.mucHang.push({ ...mucMoi, so_nguoi: soNguoi })
  }
}

export function xoaKhoiGio(maLich) {
  gio.mucHang = gio.mucHang.filter((m) => m.ma_lich !== maLich)
}