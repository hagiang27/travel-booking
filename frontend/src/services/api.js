const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || ''

export const API_ENDPOINTS = {
  // Điểm đến
  destinations: `${API_BASE_URL}/api/diem-den`,

  // Sản phẩm
  products: `${API_BASE_URL}/api/san-pham`,
  productDetail: (maSanPham) =>
    `${API_BASE_URL}/api/san-pham/${maSanPham}`,
  availableSchedules: (maSanPham) =>
    `${API_BASE_URL}/api/san-pham/${maSanPham}/lich-kha-dung`,

  // Xác thực
  register: `${API_BASE_URL}/api/auth/dang-ky`,
  login: `${API_BASE_URL}/api/auth/dang-nhap`,

  // So sánh giá
  priceComparison: `${API_BASE_URL}/api/so-sanh-gia`,
  productSurcharges: (maSanPham) =>
    `${API_BASE_URL}/api/san-pham/${maSanPham}/phu-thu`,

  // Tỷ giá
  exchangeRate: `${API_BASE_URL}/api/ty-gia`,

  // Chỉ số minh bạch
  transparencyScore: (maNcc) =>
    `${API_BASE_URL}/api/chi-so-minh-bach/${maNcc}`,

  // Đặt chỗ
  booking: `${API_BASE_URL}/api/don-dat`,

  // Thanh toán
  payment: `${API_BASE_URL}/api/thanh-toan`,

  // Đánh giá
  review: `${API_BASE_URL}/api/danh-gia`,

  // Quản trị
  approveSupplier: (maNcc) =>
    `${API_BASE_URL}/api/admin/nha-cung-cap/${maNcc}/duyet`
}