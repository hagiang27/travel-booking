# Tài liệu API - Phiên bản 0.1

## 1. Điểm đến
### GET /api/diem-den
Danh sách điểm đến, hỗ trợ lọc.
- Query: ?tinh_thanh=&loai_hinh=&trang=1&so_luong=20
- 200: { "data": [{ "ma_diem_den", "ten_diem_den", "tinh_thanh", "gia_ve_tham_quan" }], "tong_so": 76 }

## 2. Sản phẩm
### GET /api/san-pham
Tìm kiếm/lọc sản phẩm du lịch.
- Query: ?ma_diem_den=&loai_san_pham=&gia_tu=&gia_den=&trang=1
- 200: { "data": [{ "ma_san_pham", "ten_san_pham", "gia_co_ban", "diem_danh_gia_tb" }] }

### GET /api/san-pham/{ma_san_pham}
Chi tiết 1 sản phẩm.
- 200: { "ma_san_pham", "ten_san_pham", "gia_co_ban", "chinh_sach_huy", ... }
- 404: { "loi": "Khong tim thay san pham" }

### GET /api/san-pham/{ma_san_pham}/lich-kha-dung
Lịch khởi hành còn chỗ của 1 sản phẩm.
- 200: { "data": [{ "ma_lich", "ngay_khoi_hanh", "con_lai" }] }

## 3. Xác thực
### POST /api/auth/dang-ky
Đăng ký tài khoản khách hàng mới.
- Body: { "ho_ten", "ten_dang_nhap", "thu_dien_tu_mo_phong", "mat_khau" }
- 201: { "ma_nguoi_dung", "ho_ten", "vai_tro": "khach_hang" }
- 409: { "loi": "Ten dang nhap hoac email da ton tai" }

### POST /api/auth/dang-nhap
Đăng nhập, tạo phiên làm việc.
- Body: { "ten_dang_nhap", "mat_khau" }
- 200: { "ma_nguoi_dung",