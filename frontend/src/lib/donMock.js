// Lưu đơn giữ chỗ (mock) vào sessionStorage.
// Dữ liệu còn khi tải lại trang, nhưng mất khi đóng tab.
const TIEN_TO = 'mock-don-'

export function luuDonMock(don) {
  try {
    sessionStorage.setItem(TIEN_TO + don.ma_don, JSON.stringify(don))
  } catch (e) {
    // Trình duyệt chặn sessionStorage thì bỏ qua
  }
}

export function layDonMock(maDon) {
  try {
    const raw = sessionStorage.getItem(TIEN_TO + maDon)
    return raw ? JSON.parse(raw) : null
  } catch (e) {
    return null
  }
}