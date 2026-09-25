# V2 - Minh chứng Buổi 5: Dataset vòng 1

## 1. Kiểm tra dữ liệu người dùng

Bảng `nguoi_dung` có tổng cộng 110 bản ghi.

Phân bố vai trò:

| Vai trò | Số lượng |
|---|---:|
| quan_tri | 2 |
| nha_cung_cap | 20 |
| khach_hang | 75 |
| huong_dan_vien | 8 |
| kiem_thu | 5 |
| **Tổng** | **110** |

## 2. Thống kê dataset

| Bảng | Số lượng |
|---|---:|
| nguoi_dung | 110 |
| nha_cung_cap | 24 |
| diem_den | 76 |
| san_pham | 140 |
| gia_theo_mua | 560 |
| lich_kha_dung | 2150 |
| don_dat | 260 |
| chi_tiet_don | 365 |
| thanh_toan | 209 |
| danh_gia | 88 |
| bai_viet | 60 |
| nhat_ky_he_thong | 200 |
| chinh_sach_phu_thu | 3 |
| chi_so_minh_bach_gia | 3 |
| ty_gia_tien_te | 3 |
| **Tổng** | **4251** |

## 3. Kiểm tra hiển thị tiếng Việt

Dữ liệu tiếng Việt hiển thị đúng trong phpMyAdmin.

Ví dụ:
- Hoàng Thị Oanh
- Vũ Hải Hùng
- Dương Văn Dũng
- Phạm Thu Linh
- Bùi Đức Hà
- Đỗ Văn Đạt
- Trần Thanh Trang
- Lê Hải Lan

## 4. Kiểm tra trường mật khẩu

Bảng `nguoi_dung` có trường:

`mat_khau_hash VARCHAR(255) NOT NULL`

Mật khẩu được thiết kế để lưu dưới dạng hash thay vì lưu trực tiếp mật khẩu thô.

## Kết luận

V2 đã hoàn thành phần chuẩn bị và kiểm tra dataset vòng 1:
- Đã có dữ liệu mẫu.
- Đã thống kê số lượng bản ghi.
- Đã kiểm tra hiển thị tiếng Việt.
- Đã kiểm tra trường lưu mật khẩu dạng hash.
