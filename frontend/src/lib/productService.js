// frontend/src/lib/productService.js
import { MOCK_PRODUCTS } from '../mock/mockProducts'

const USE_MOCK = import.meta.env.VITE_USE_MOCK === 'true'

// Bản mock: dữ liệu nằm trong bộ nhớ, tải lại trang thì về ban đầu
let dsMock = [...MOCK_PRODUCTS]

const mock = {
  async danhSach() {
    return { data: [...dsMock] }
  },
  async tao(sp) {
    const so = String(dsMock.length + 1).padStart(4, '0')
    const moi = { ...sp, ma_san_pham: `SP${so}`, diem_danh_gia_tb: 0 }
    dsMock.push(moi)
    return moi
  },
  async capNhat(ma, sp) {
    dsMock = dsMock.map((x) => (x.ma_san_pham === ma ? { ...x, ...sp } : x))
  },
  async xoa(ma) {
    dsMock = dsMock.filter((x) => x.ma_san_pham !== ma)
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