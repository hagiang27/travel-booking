// frontend/src/lib/productService.js
import { MOCK_PRODUCTS } from '../mock/mockProducts'

const USE_MOCK = import.meta.env.VITE_USE_MOCK === 'true'
const KHOA_LUU = 'mock-san-pham'

// Đọc dữ liệu đã lưu; nếu chưa có thì dùng dữ liệu mẫu ban đầu
function docDuLieu() {
  try {
    const raw = localStorage.getItem(KHOA_LUU)
    return raw ? JSON.parse(raw) : [...MOCK_PRODUCTS]
  } catch {
    return [...MOCK_PRODUCTS]
  }
}

function ghiDuLieu(ds) {
  try {
    localStorage.setItem(KHOA_LUU, JSON.stringify(ds))
  } catch {
    // Bộ nhớ đầy hoặc bị chặn: dữ liệu vẫn dùng được trong phiên này
  }
}

let dsMock = docDuLieu()

const mock = {
  async danhSach() {
    return { data: [...dsMock] }
  },
  async tao(sp) {
    const so = String(dsMock.length + 1).padStart(4, '0')
    const moi = { ...sp, ma_san_pham: `SP${so}`, diem_danh_gia_tb: 0 }
    dsMock = [...dsMock, moi]
    ghiDuLieu(dsMock)
    return moi
  },
  async capNhat(ma, sp) {
    dsMock = dsMock.map((x) => (x.ma_san_pham === ma ? { ...x, ...sp } : x))
    ghiDuLieu(dsMock)
  },
  async xoa(ma) {
    dsMock = dsMock.filter((x) => x.ma_san_pham !== ma)
    ghiDuLieu(dsMock)
  },
}

// Bản thật: chưa có điểm cuối thêm, sửa, xóa trong tài liệu API v0.1
const real = {
    async danhSach() {
        const res = await fetch('/api/v1/products', {
          credentials: 'same-origin',
          headers: { Accept: 'application/json' },
        })
        if (!res.ok) throw new Error('Không tải được danh sách sản phẩm.')
        return res.json()
      },
  async tao() {
    throw new Error('Chức năng thêm sản phẩm chưa có API (chờ V3).')
  },
  async capNhat() {
    throw new Error('Chức năng sửa sản phẩm chưa có API (chờ V3).')
  },
  async xoa() {
    throw new Error('Chức năng xóa sản phẩm chưa có API (chờ V3).')
  },
}

export const productService = USE_MOCK ? mock : real