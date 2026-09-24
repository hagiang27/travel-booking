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


## 4. So sánh giá

### GET /api/so-sanh-gia

So sánh giá minh bạch giữa các nhà cung cấp.

Query:
- ma_diem_den
- ngay_khoi_hanh
- so_nguoi
- ma_tien_te

200:

```json
{
  "data": [
    {
      "ma_san_pham": "SP0001",
      "ten_san_pham": "Tour Hạ Long 2N1Đ",
      "gia_co_ban": 2500000,
      "phu_thu": 200000,
      "tong_chi_phi": 2700000,
      "diem_minh_bach": 92
    }
  ]
}
```

### GET /api/san-pham/{ma_san_pham}/phu-thu

Lấy danh sách phụ thu của một sản phẩm.

200:

```json
{
  "data": [
    {
      "ma_phu_thu": "PT001",
      "loai_phu_thu": "Cuối tuần",
      "gia_tri": 200000,
      "kieu_tinh": "co_dinh",
      "mo_ta": "Phụ thu khởi hành cuối tuần"
    }
  ]
}
```

## 5. Quy đổi tiền tệ

### GET /api/ty-gia

Lấy tỷ giá quy đổi theo loại tiền tệ.

Query:
- ma_tien_te

200:

```json
{
  "ma_tien_te": "USD",
  "ty_gia_quy_doi": 26150,
  "ngay_cap_nhat": "2026-09-22"
}
```

## 6. Chỉ số minh bạch

### GET /api/chi-so-minh-bach/{ma_ncc}

Lấy điểm minh bạch của nhà cung cấp.

200:

```json

## 7. Đặt chỗ

### POST /api/don-dat

Tạo đơn đặt tour mới.

Body

```json
{
  "ma_nguoi_dung": "ND0001",
  "ma_lich": "LK0001",
  "so_nguoi": 2
}
```

201:

```json
{
  "ma_don": "DD0001",
  "tong_tien": 5400000,
  "trang_thai": "cho_thanh_toan"
}
```

## 8. Thanh toán

### POST /api/thanh-toan

Thanh toán cho đơn đặt.

Body

```json
{
  "ma_don": "DD0001",
  "phuong_thuc": "vnpay"
}
```

201:

```json
{
  "ma_thanh_toan": "TT0001",
  "trang_thai": "da_thanh_toan"
}
```

## 9. Đánh giá

### POST /api/danh-gia

Gửi đánh giá sau khi hoàn thành tour.

Body

```json
{
  "ma_don": "DD0001",
  "diem": 5,
  "noi_dung": "Tour rất tuyệt"
}
```

201:

```json
{
  "message": "Đánh giá thành công"
}
```

## 10. Quản trị

### PUT /api/admin/nha-cung-cap/{ma_ncc}/duyet

Duyệt trạng thái nhà cung cấp.

Path Parameter:
- ma_ncc

200:

```json
{
  "message": "Đã duyệt nhà cung cấp",
  "ma_ncc": "NCC001",
  "trang_thai": "da_duyet"
}
```

403:

```json
{
  "loi": "Không có quyền thực hiện"
}
```