import { MOCK_PRODUCTS } from '../mock/mockProducts'
import { MOCK_LICH } from '../mock/mockSchedules'

// Đọc từ .env: VITE_USE_MOCK=true|false. Chưa khai báo thì dùng mock.
// Giá trị này phải khớp với authService.js.
const USE_MOCK = import.meta.env.VITE_USE_MOCK === 'true'

const KHOA_LUU = 'mock-san-pham'
const SO_MOI_TRANG_MAC_DINH = 12

const THONG_BAO_LOI = {
  401: 'Bạn cần đăng nhập để thực hiện thao tác này.',
  403: 'Bạn không có quyền thực hiện thao tác này.',
  404: 'Không tìm thấy dữ liệu yêu cầu.',
  419: 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.',
  422: 'Dữ liệu chưa hợp lệ. Vui lòng kiểm tra lại.',
}

function coGiaTri(v) {
  return v !== undefined && v !== null && v !== ''
}

function taoLoi(status, message, kiemTra = {}) {
  const loi = new Error(
    message || THONG_BAO_LOI[status] || 'Có lỗi xảy ra, vui lòng thử lại.'
  )
  loi.status = status
  loi.kiemTra = kiemTra
  return loi
}

/* ---------- Gọi API thật ---------- */

function layHeaderCsrf() {
  const meta = document.querySelector('meta[name="csrf-token"]')
  if (meta) {
    return { 'X-CSRF-TOKEN': meta.getAttribute('content') }
  }

  const cookie = document.cookie
    .split('; ')
    .find((c) => c.startsWith('XSRF-TOKEN='))
  if (cookie) {
    return { 'X-XSRF-TOKEN': decodeURIComponent(cookie.split('=')[1]) }
  }

  return {}
}

async function goi(url, { method = 'GET', body } = {}) {
  const coBody = body !== undefined

  const phanHoi = await fetch(url, {
    method,
    credentials: 'include',
    headers: {
      Accept: 'application/json',
      ...(coBody ? { 'Content-Type': 'application/json' } : {}),
      ...(method !== 'GET' ? layHeaderCsrf() : {}),
    },
    body: coBody ? JSON.stringify(body) : undefined,
  })

  // Riêng DELETE có thể trả về 204 không có nội dung
  const data = await phanHoi.json().catch(() => ({}))

  if (!phanHoi.ok) {
    throw taoLoi(phanHoi.status, THONG_BAO_LOI[phanHoi.status] || data.message, data.loi || {})
  }

  return data
}

/* ---------- Dữ liệu mock (lưu trong localStorage) ---------- */

function docDuLieu() {
  try {
    const raw = localStorage.getItem(KHOA_LUU)
    if (raw) return JSON.parse(raw)
  } catch (e) {
    // localStorage lỗi thì dùng dữ liệu gốc
  }
  return MOCK_PRODUCTS.map((sp) => ({ ...sp }))
}

function ghiDuLieu(ds) {
  try {
    localStorage.setItem(KHOA_LUU, JSON.stringify(ds))
  } catch (e) {
    // Bỏ qua nếu trình duyệt không cho ghi
  }
}

let dsMock = docDuLieu()

// Số chỗ còn lại sau khi giữ chỗ (chỉ lưu trong bộ nhớ, tải lại trang sẽ về số gốc)
const conLaiMock = {}

function layLichMock(maSanPham) {
  return (MOCK_LICH[maSanPham] || []).map((lich) => ({
    ...lich,
    con_lai: conLaiMock[lich.ma_lich] ?? lich.con_lai,
  }))
}

/* ---------- Mock ---------- */

const mock = {
  async danhSach() {
    const ds = dsMock.map((sp) => ({ ...sp }))
    return {
      data: ds,
      tong_so: ds.length,
      trang: 1,
      so_moi_trang: ds.length,
    }
  },

  async timKiemCongKhai(p = {}) {
    let ketQua = dsMock

    if (coGiaTri(p.ma_diem_den)) {
      ketQua = ketQua.filter((sp) => sp.ma_diem_den === p.ma_diem_den)
    }
    if (coGiaTri(p.loai)) {
      ketQua = ketQua.filter((sp) => sp.loai_san_pham === p.loai)
    }
    if (coGiaTri(p.gia_tu)) {
      ketQua = ketQua.filter((sp) => Number(sp.gia_co_ban) >= Number(p.gia_tu))
    }
    if (coGiaTri(p.gia_den)) {
      ketQua = ketQua.filter((sp) => Number(sp.gia_co_ban) <= Number(p.gia_den))
    }

    const trang = Math.max(1, Number(p.trang) || 1)
    const soMoi = SO_MOI_TRANG_MAC_DINH
    const batDau = (trang - 1) * soMoi

    return {
      data: ketQua.slice(batDau, batDau + soMoi).map((sp) => ({ ...sp })),
      tong_so: ketQua.length,
      trang,
      so_moi_trang: soMoi,
    }
  },

  async chiTiet(ma) {
    const sp = dsMock.find((x) => x.ma_san_pham === ma)
    if (!sp) throw taoLoi(404)

    return { ...sp, lich_kha_dung: layLichMock(ma) }
  },

  // Mock giữ chỗ: trừ số chỗ, trả về đơn tạm (chưa tính phụ phí và thuế)
  async giuCho({ ma_san_pham, ma_lich, so_nguoi }) {
    const sp = dsMock.find((x) => x.ma_san_pham === ma_san_pham)
    if (!sp) throw taoLoi(404)

    const lich = layLichMock(ma_san_pham).find((x) => x.ma_lich === ma_lich)
    if (!lich) throw taoLoi(404, 'Lịch khởi hành không tồn tại.')

    const soNguoi = Number(so_nguoi) || 1
    if (lich.con_lai < soNguoi) {
      throw taoLoi(422, null, {
        so_nguoi: [`Lịch này chỉ còn ${lich.con_lai} chỗ.`],
      })
    }

    conLaiMock[ma_lich] = lich.con_lai - soNguoi

    return {
      ma_don: `DH${Date.now()}`,
      trang_thai: 'cho_thanh_toan',
      tong_tien: Number(sp.gia_co_ban) * soNguoi,
      het_han: new Date(Date.now() + 15 * 60 * 1000).toISOString(),
    }
  },

  // Sinh mã mới = số lớn nhất hiện có + 1, tránh trùng sau khi xóa
  async tao(sp) {
    const soLonNhat = dsMock.reduce(
      (max, x) => Math.max(max, Number(String(x.ma_san_pham).replace('SP', '')) || 0),
      0
    )

    const moi = {
      ...sp,
      ma_san_pham: `SP${String(soLonNhat + 1).padStart(4, '0')}`,
      diem_danh_gia_tb: 0,
    }

    dsMock = [...dsMock, moi]
    ghiDuLieu(dsMock)
    return { ...moi }
  },

  async capNhat(ma, sp) {
    const viTri = dsMock.findIndex((x) => x.ma_san_pham === ma)
    if (viTri === -1) throw taoLoi(404)

    const moi = { ...dsMock[viTri], ...sp, ma_san_pham: ma }
    dsMock = dsMock.map((x, i) => (i === viTri ? moi : x))
    ghiDuLieu(dsMock)
    return { ...moi }
  },

  async xoa(ma) {
    const truoc = dsMock.length
    dsMock = dsMock.filter((x) => x.ma_san_pham !== ma)
    if (dsMock.length === truoc) throw taoLoi(404)

    ghiDuLieu(dsMock)
    return true
  },
}

/* ---------- Thật (chờ V3 hoàn thành API) ---------- */

const real = {
  async danhSach() {
    return goi('/api/v1/admin/products')
  },

  async timKiemCongKhai(p = {}) {
    const query = new URLSearchParams()
    for (const [k, v] of Object.entries(p)) {
      if (coGiaTri(v)) query.set(k, v)
    }
    return goi(`/api/v1/products?${query.toString()}`)
  },

  async chiTiet(ma) {
    const data = await goi(`/api/v1/products/${encodeURIComponent(ma)}`)
    return data.data ?? data
  },

  async giuCho({ ma_san_pham, ma_lich, so_nguoi }) {
    const data = await goi('/api/v1/bookings', {
      method: 'POST',
      body: { ma_san_pham, ma_lich, so_nguoi },
    })
    return data.data ?? data
  },

  async tao(sp) {
    const data = await goi('/api/v1/admin/products', {
      method: 'POST',
      body: sp,
    })
    return data.data ?? data
  },

  async capNhat(ma, sp) {
    const data = await goi(`/api/v1/admin/products/${encodeURIComponent(ma)}`, {
      method: 'PUT',
      body: sp,
    })
    return data.data ?? data
  },

  async xoa(ma) {
    return goi(`/api/v1/admin/products/${encodeURIComponent(ma)}`, {
      method: 'DELETE',
    })
  },
}

export const productService = USE_MOCK ? mock : real