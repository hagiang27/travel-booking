// Rỗng = gọi cùng nguồn gốc, đi qua proxy của Vite khi chạy dev
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL ?? ''
const USE_MOCK = import.meta.env.VITE_USE_MOCK === 'true'

if (USE_MOCK) {
  console.warn('[MOCK] Đang dùng đăng nhập giả, KHÔNG dùng khi quay video nghiệm thu')
}

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
  register: `${API_BASE_URL}/api/v1/register`,
  login: `${API_BASE_URL}/api/v1/login`,
  me: `${API_BASE_URL}/api/v1/me`,
  logout: `${API_BASE_URL}/api/v1/logout`,
  csrfCookie: `${API_BASE_URL}/sanctum/csrf-cookie`,

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
    `${API_BASE_URL}/api/admin/nha-cung-cap/${maNcc}/duyet`,

  // Kiểm tra quyền admin
  adminTest: `${API_BASE_URL}/api/v1/admin/test`,
}

/* ---------- Hàm gọi API dùng chung ---------- */

class ApiError extends Error {
  constructor(message, status = 0, errors = null) {
    super(message)
    this.name = 'ApiError'
    this.status = status
    this.errors = errors // lỗi theo từng trường khi 422: { ten_dang_nhap: ['...'] }
  }
}

function getCookie(name) {
  const m = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'))
  return m ? decodeURIComponent(m[1]) : null
}

async function request(url, { method = 'GET', body } = {}, retried = false) {
  const headers = { Accept: 'application/json' }
  if (body !== undefined) headers['Content-Type'] = 'application/json'
  const xsrf = getCookie('XSRF-TOKEN')
  if (xsrf) headers['X-XSRF-TOKEN'] = xsrf

  let response
  try {
    response = await fetch(url, {
      method,
      credentials: 'include',
      headers,
      body: body !== undefined ? JSON.stringify(body) : undefined,
    })
  } catch {
    throw new ApiError('Không kết nối được máy chủ. Vui lòng thử lại sau.', 0)
  }

  // 419: thiếu hoặc hết hạn CSRF, lấy cookie CSRF rồi thử lại một lần
  if (response.status === 419 && !retried) {
    await fetch(API_ENDPOINTS.csrfCookie, { credentials: 'include' }).catch(() => {})
    return request(url, { method, body }, true)
  }

  const data = await response.json().catch(() => ({}))
  if (!response.ok) {
    throw new ApiError(
      data.message || data.loi || `Lỗi ${response.status}`,
      response.status,
      data.errors || null,
    )
  }
  return data
}

/* ---------- Xác thực ---------- */

/** Đăng nhập (session/cookie). Thành công thì gọi getCurrentUser() để lấy thông tin người dùng */
export async function login(username, password) {
  if (USE_MOCK) return mockLogin(username, password)
  try {
    return await request(API_ENDPOINTS.login, {
      method: 'POST',
      body: { ten_dang_nhap: username, mat_khau: password },
    })
  } catch (e) {
    if (e.status === 401) e.message = 'Tên đăng nhập hoặc mật khẩu không đúng.'
    throw e
  }
}

/** Trả về { ma_nguoi_dung, ho_ten, vai_tro } hoặc null nếu chưa đăng nhập */
export async function getCurrentUser() {
  if (USE_MOCK) return mockMe()
  try {
    return await request(API_ENDPOINTS.me)
  } catch (e) {
    if (e.status === 401) return null
    throw e
  }
}

/** Đăng xuất */
export async function logout() {
  if (USE_MOCK) return mockLogout()
  return request(API_ENDPOINTS.logout, { method: 'POST' })
}

/* ---------- Chế độ mock: chỉ để làm giao diện khi backend chưa sẵn sàng ---------- */
// Tên vai trò nha_cung_cap là đoán, đổi theo giá trị thật mà V3 dùng
const MOCK_USERS = {
  khach: { ma_nguoi_dung: 'ND9001', ho_ten: 'Khách thử nghiệm', vai_tro: 'khach_hang' },
  ncc: { ma_nguoi_dung: 'ND9002', ho_ten: 'Nhà cung cấp thử nghiệm', vai_tro: 'nha_cung_cap' },
  admin: { ma_nguoi_dung: 'ND9003', ho_ten: 'Quản trị thử nghiệm', vai_tro: 'quan_tri' },
}
const MOCK_KEY = 'mock_user' // chỉ lưu thông tin hiển thị, không lưu mật khẩu

async function mockLogin(username, password) {
  const user = MOCK_USERS[username]
  if (!user || password !== 'mock123') {
    throw new ApiError('Tên đăng nhập hoặc mật khẩu không đúng.', 401)
  }
  sessionStorage.setItem(MOCK_KEY, JSON.stringify(user))
  return user
}

async function mockMe() {
  const raw = sessionStorage.getItem(MOCK_KEY)
  return raw ? JSON.parse(raw) : null
}

async function mockLogout() {
  sessionStorage.removeItem(MOCK_KEY)
  return {}
}
/** Gọi điểm cuối chỉ dành cho quản trị. Khách/nhà cung cấp sẽ nhận lỗi 403 */
export async function checkAdminAccess() {
  if (USE_MOCK) {
    const user = await mockMe()
    if (user?.vai_tro !== 'quan_tri') {
      throw new ApiError('Không có quyền thực hiện', 403)
    }
    return { message: '[MOCK] Có quyền quản trị' }
  }
  return request(API_ENDPOINTS.adminTest)
}