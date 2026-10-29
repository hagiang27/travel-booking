# TỪ ĐIỂN DỮ LIỆU — BUỔI 6 V2

## 1. Thông tin kiểm chứng

- Dự án: travel-booking
- Database kiểm tra: `travel_booking_dt01_recovery_test`
- Môi trường: testing, cổng 3307
- Phạm vi: 15 bảng nghiệp vụ, 125 trường
- Nguồn cấu trúc: `information_schema.COLUMNS`.
- Nguồn khóa ngoại: `information_schema.KEY_COLUMN_USAGE`.
- Ý nghĩa nghiệp vụ dưới đây là mô tả sơ bộ dựa trên tên trường, cần đối chiếu với yêu cầu môn học và logic ứng dụng.

Quy ước: PK = khóa chính; UNI = chỉ mục duy nhất; MUL = chỉ mục không duy nhất; FK = khóa ngoại; NULL = cho phép giá trị rỗng.
Giá trị mặc định `NULL` trong metadata được trình bày là `NULL`; giá trị mặc định không có được ghi là `(không xác định/không có)`.

## 2. Từ điển theo bảng

### Bảng `bai_viet`

| Trường | Kiểu dữ liệu | NULL | Khóa/chỉ mục | Mặc định | Ý nghĩa sơ bộ |
|---|---|---|---|---|---|
| `ma_bai_viet` | `char(6)` | NO | PK | NULL/không có mặc định tường minh | Mã bài viết |
| `tieu_de` | `varchar(200)` | NO | — | NULL/không có mặc định tường minh | Tiêu đề bài viết |
| `duong_dan_tinh` | `varchar(200)` | NO | MUL | NULL/không có mặc định tường minh | Đường dẫn tĩnh hoặc slug |
| `ma_diem_den` | `char(5)` | YES | MUL, FK `fk_bv_dd` | NULL/không có mặc định tường minh | Mã định danh điểm đến |
| `chu_de` | `varchar(50)` | YES | — | NULL/không có mặc định tường minh | Chủ đề bài viết |
| `the_gan` | `varchar(200)` | YES | — | NULL/không có mặc định tường minh | Thẻ gắn với bài viết |
| `tom_tat` | `varchar(500)` | YES | — | NULL/không có mặc định tường minh | Tóm tắt bài viết |
| `so_luot_xem` | `int(11)` | NO | — | 0 | Số lượt xem |
| `ngay_dang` | `date` | NO | — | NULL/không có mặc định tường minh | Ngày đăng bài |
| `trang_thai` | `enum('xuat_ban','ban_nhap')` | NO | — | 'ban_nhap' | Trạng thái bài viết: đã xuất bản hoặc bản nháp |

### Bảng `chi_so_minh_bach_gia`

| Trường | Kiểu dữ liệu | NULL | Khóa/chỉ mục | Mặc định | Ý nghĩa sơ bộ |
|---|---|---|---|---|---|
| `ma_chi_so` | `char(7)` | NO | PK | NULL/không có mặc định tường minh | Mã chỉ số minh bạch giá |
| `ma_ncc` | `char(6)` | NO | MUL, FK `fk_cs_ncc` | NULL/không có mặc định tường minh | Mã nhà cung cấp |
| `diem_minh_bach` | `decimal(5,2)` | NO | — | NULL/không có mặc định tường minh | Điểm minh bạch giá |
| `ngay_tinh` | `date` | NO | — | NULL/không có mặc định tường minh | Ngày tính chỉ số |

### Bảng `chi_tiet_don`

| Trường | Kiểu dữ liệu | NULL | Khóa/chỉ mục | Mặc định | Ý nghĩa sơ bộ |
|---|---|---|---|---|---|
| `ma_chi_tiet` | `char(8)` | NO | PK | NULL/không có mặc định tường minh | Mã chi tiết đơn |
| `ma_don` | `char(7)` | NO | MUL, FK `fk_ct_don` | NULL/không có mặc định tường minh | Mã đơn đặt |
| `ma_san_pham` | `char(6)` | NO | MUL, FK `fk_ct_sp` | NULL/không có mặc định tường minh | Mã sản phẩm/dịch vụ |
| `ma_lich` | `char(9)` | NO | MUL, FK `fk_ct_lich` | NULL/không có mặc định tường minh | Mã lịch khả dụng |
| `ngay_khoi_hanh` | `date` | NO | — | NULL/không có mặc định tường minh | Ngày khởi hành |
| `so_luong` | `int(11)` | NO | — | NULL/không có mặc định tường minh | Số lượng đặt |
| `don_gia` | `int(11)` | NO | — | NULL/không có mặc định tường minh | Đơn giá tại thời điểm đặt |
| `thanh_tien` | `bigint(20)` | NO | — | NULL/không có mặc định tường minh | Thành tiền chi tiết |

### Bảng `chinh_sach_phu_thu`

| Trường | Kiểu dữ liệu | NULL | Khóa/chỉ mục | Mặc định | Ý nghĩa sơ bộ |
|---|---|---|---|---|---|
| `ma_phu_thu` | `char(7)` | NO | PK | NULL/không có mặc định tường minh | Mã chính sách phụ thu |
| `ma_san_pham` | `char(6)` | NO | MUL, FK `fk_pt_sp` | NULL/không có mặc định tường minh | Mã sản phẩm/dịch vụ |
| `loai_phu_thu` | `varchar(60)` | NO | — | NULL/không có mặc định tường minh | Loại phụ thu |
| `gia_tri` | `decimal(12,2)` | NO | — | NULL/không có mặc định tường minh | Giá trị phụ thu |
| `kieu_tinh` | `enum('co_dinh','theo_ty_le')` | NO | — | 'co_dinh' | Kiểu tính phụ thu |
| `mo_ta` | `varchar(255)` | YES | — | NULL/không có mặc định tường minh | Mô tả |

### Bảng `danh_gia`

| Trường | Kiểu dữ liệu | NULL | Khóa/chỉ mục | Mặc định | Ý nghĩa sơ bộ |
|---|---|---|---|---|---|
| `ma_danh_gia` | `char(7)` | NO | PK | NULL/không có mặc định tường minh | Mã đánh giá |
| `ma_san_pham` | `char(6)` | NO | MUL, FK `fk_dg_sp` | NULL/không có mặc định tường minh | Mã sản phẩm/dịch vụ |
| `ma_don` | `char(7)` | NO | MUL, FK `fk_dg_don` | NULL/không có mặc định tường minh | Mã đơn đặt |
| `ma_nguoi_dung` | `char(6)` | NO | MUL, FK `fk_dg_nd` | NULL/không có mặc định tường minh | Mã người dùng |
| `diem` | `tinyint(4)` | NO | — | NULL/không có mặc định tường minh | Điểm đánh giá |
| `noi_dung` | `varchar(500)` | YES | — | NULL/không có mặc định tường minh | Nội dung đánh giá |
| `ngay_danh_gia` | `date` | NO | — | NULL/không có mặc định tường minh | Ngày đánh giá |
| `trang_thai_kiem_duyet` | `enum('da_duyet','cho_duyet','tu_choi')` | NO | — | 'cho_duyet' | Trạng thái kiểm duyệt đánh giá |

### Bảng `diem_den`

| Trường | Kiểu dữ liệu | NULL | Khóa/chỉ mục | Mặc định | Ý nghĩa sơ bộ |
|---|---|---|---|---|---|
| `ma_diem_den` | `char(5)` | NO | PK | NULL/không có mặc định tường minh | Mã định danh điểm đến |
| `ten_diem_den` | `varchar(150)` | NO | — | NULL/không có mặc định tường minh | Tên điểm đến |
| `duong_dan_tinh` | `varchar(180)` | NO | UNI | NULL/không có mặc định tường minh | Đường dẫn tĩnh hoặc slug |
| `tinh_thanh` | `varchar(60)` | NO | MUL | NULL/không có mặc định tường minh | Tỉnh/thành liên quan |
| `vung_mien` | `varchar(40)` | NO | — | NULL/không có mặc định tường minh | Vùng miền |
| `loai_hinh` | `varchar(60)` | NO | MUL | NULL/không có mặc định tường minh | Loại hình điểm đến |
| `vi_do` | `decimal(9,6)` | NO | MUL | NULL/không có mặc định tường minh | Vĩ độ địa lý |
| `kinh_do` | `decimal(9,6)` | NO | — | NULL/không có mặc định tường minh | Kinh độ địa lý |
| `mua_phu_hop` | `varchar(30)` | YES | — | NULL/không có mặc định tường minh | Mùa phù hợp để tham quan |
| `thoi_luong_tham_quan_gio` | `decimal(4,1)` | YES | — | NULL/không có mặc định tường minh | Thời lượng tham quan dự kiến, tính bằng giờ |
| `gia_ve_tham_quan` | `int(11)` | NO | — | 0 | Giá vé tham quan |
| `suc_tai_ngay` | `int(11)` | YES | — | NULL/không có mặc định tường minh | Sức tải/ngưỡng phục vụ trong ngày |
| `trang_thai` | `enum('hoat_dong','tam_dung')` | NO | — | 'hoat_dong' | Trạng thái điểm đến: hoạt động hoặc tạm dừng |

### Bảng `don_dat`

| Trường | Kiểu dữ liệu | NULL | Khóa/chỉ mục | Mặc định | Ý nghĩa sơ bộ |
|---|---|---|---|---|---|
| `ma_don` | `char(7)` | NO | PK | NULL/không có mặc định tường minh | Mã đơn đặt |
| `ma_nguoi_dung` | `char(6)` | NO | MUL, FK `fk_don_nd` | NULL/không có mặc định tường minh | Mã người dùng |
| `ngay_dat` | `date` | NO | — | NULL/không có mặc định tường minh | Ngày đặt đơn |
| `trang_thai` | `enum('cho_thanh_toan','da_thanh_toan','hoan_tat','da_huy','cho_hoan_tien')` | NO | MUL | NULL/không có mặc định tường minh | Trạng thái đơn đặt: chờ thanh toán, đã thanh toán, hoàn tất, đã hủy hoặc chờ hoàn tiền |
| `tong_tien` | `bigint(20)` | NO | — | NULL/không có mặc định tường minh | Tổng tiền đơn đặt |
| `phi_huy` | `bigint(20)` | NO | — | 0 | Phí hủy |
| `so_tien_hoan` | `bigint(20)` | NO | — | 0 | Số tiền hoàn |
| `kenh_dat` | `varchar(30)` | YES | — | NULL/không có mặc định tường minh | Kênh đặt |
| `ghi_chu` | `varchar(255)` | YES | — | NULL/không có mặc định tường minh | Ghi chú |
| `hold_expires_at` | `datetime` | YES | MUL | NULL/không có mặc định tường minh | Thời điểm hết hạn giữ chỗ |

### Bảng `gia_theo_mua`

| Trường | Kiểu dữ liệu | NULL | Khóa/chỉ mục | Mặc định | Ý nghĩa sơ bộ |
|---|---|---|---|---|---|
| `ma_gia_mua` | `char(7)` | NO | PK | NULL/không có mặc định tường minh | Mã giá theo mùa |
| `ma_san_pham` | `char(6)` | NO | MUL, FK `fk_gm_sp` | NULL/không có mặc định tường minh | Mã sản phẩm/dịch vụ |
| `mua` | `varchar(30)` | NO | — | NULL/không có mặc định tường minh | Mùa áp dụng |
| `he_so_gia` | `decimal(4,2)` | NO | — | NULL/không có mặc định tường minh | Hệ số giá theo mùa |
| `gia_ap_dung` | `int(11)` | NO | — | NULL/không có mặc định tường minh | Giá áp dụng theo mùa |

### Bảng `lich_kha_dung`

| Trường | Kiểu dữ liệu | NULL | Khóa/chỉ mục | Mặc định | Ý nghĩa sơ bộ |
|---|---|---|---|---|---|
| `ma_lich` | `char(9)` | NO | PK | NULL/không có mặc định tường minh | Mã lịch khả dụng |
| `ma_san_pham` | `char(6)` | NO | MUL, FK `fk_lkd_sp` | NULL/không có mặc định tường minh | Mã sản phẩm/dịch vụ |
| `ngay_khoi_hanh` | `date` | NO | MUL | NULL/không có mặc định tường minh | Ngày khởi hành |
| `tong_cho` | `int(11)` | NO | — | NULL/không có mặc định tường minh | Tổng số chỗ |
| `da_dat` | `int(11)` | NO | — | 0 | Số chỗ đã đặt |
| `con_lai` | `int(11)` | NO | — | NULL/không có mặc định tường minh | Số chỗ còn lại |
| `trang_thai` | `enum('mo_ban','het_cho','tam_dung')` | NO | — | 'mo_ban' | Trạng thái lịch khả dụng: mở bán, hết chỗ hoặc tạm dừng |

### Bảng `nguoi_dung`

| Trường | Kiểu dữ liệu | NULL | Khóa/chỉ mục | Mặc định | Ý nghĩa sơ bộ |
|---|---|---|---|---|---|
| `ma_nguoi_dung` | `char(6)` | NO | PK | NULL/không có mặc định tường minh | Mã người dùng |
| `ho_ten` | `varchar(100)` | NO | — | NULL/không có mặc định tường minh | Họ tên |
| `ten_dang_nhap` | `varchar(80)` | NO | UNI | NULL/không có mặc định tường minh | Tên dùng để đăng nhập |
| `thu_dien_tu_mo_phong` | `varchar(120)` | NO | UNI | NULL/không có mặc định tường minh | Địa chỉ thư điện tử mô phỏng |
| `mat_khau_hash` | `varchar(255)` | NO | — | '' | Giá trị băm mật khẩu |
| `vai_tro` | `enum('quan_tri','nha_cung_cap','khach_hang','huong_dan_vien','kiem_thu')` | NO | MUL | NULL/không có mặc định tường minh | Vai trò liên quan |
| `ma_ncc` | `char(6)` | YES | MUL, FK `fk_nd_ncc` | NULL/không có mặc định tường minh | Mã nhà cung cấp |
| `so_dien_thoai_mo_phong` | `varchar(20)` | YES | — | NULL/không có mặc định tường minh | Số điện thoại mô phỏng |
| `so_thich` | `varchar(200)` | YES | — | NULL/không có mặc định tường minh | Sở thích người dùng |
| `ngay_tao` | `date` | NO | — | NULL/không có mặc định tường minh | Ngày tạo bản ghi |
| `trang_thai` | `enum('kich_hoat','khoa')` | NO | — | 'kich_hoat' | Trạng thái tài khoản: kích hoạt hoặc khóa |

### Bảng `nha_cung_cap`

| Trường | Kiểu dữ liệu | NULL | Khóa/chỉ mục | Mặc định | Ý nghĩa sơ bộ |
|---|---|---|---|---|---|
| `ma_ncc` | `char(6)` | NO | PK | NULL/không có mặc định tường minh | Mã nhà cung cấp |
| `ten_ncc` | `varchar(150)` | NO | — | NULL/không có mặc định tường minh | Tên nhà cung cấp |
| `ma_so_thue_mo_phong` | `varchar(20)` | YES | — | NULL/không có mặc định tường minh | Mã số thuế mô phỏng |
| `tinh_thanh` | `varchar(60)` | YES | — | NULL/không có mặc định tường minh | Tỉnh/thành liên quan |
| `nguoi_dai_dien` | `varchar(100)` | YES | — | NULL/không có mặc định tường minh | Người đại diện |
| `dien_thoai_mo_phong` | `varchar(20)` | YES | — | NULL/không có mặc định tường minh | Số điện thoại mô phỏng |
| `thu_dien_tu_mo_phong` | `varchar(120)` | YES | — | NULL/không có mặc định tường minh | Địa chỉ thư điện tử mô phỏng |
| `ngay_hop_tac` | `date` | YES | — | NULL/không có mặc định tường minh | Ngày bắt đầu hợp tác |
| `trang_thai_hop_tac` | `enum('dang_hop_tac','tam_dung')` | NO | — | 'dang_hop_tac' | Trạng thái hợp tác |

### Bảng `nhat_ky_he_thong`

| Trường | Kiểu dữ liệu | NULL | Khóa/chỉ mục | Mặc định | Ý nghĩa sơ bộ |
|---|---|---|---|---|---|
| `ma_nhat_ky` | `char(7)` | NO | PK | NULL/không có mặc định tường minh | Mã nhật ký |
| `thoi_diem` | `datetime` | NO | MUL | NULL/không có mặc định tường minh | Thời điểm ghi nhận; cần xem ngữ cảnh bảng |
| `ma_nguoi_dung` | `char(6)` | YES | — | NULL/không có mặc định tường minh | Mã người dùng |
| `vai_tro` | `varchar(20)` | YES | — | NULL/không có mặc định tường minh | Vai trò liên quan |
| `hanh_dong` | `varchar(40)` | NO | — | NULL/không có mặc định tường minh | Hành động được ghi nhận |
| `doi_tuong` | `varchar(40)` | YES | — | NULL/không có mặc định tường minh | Đối tượng của hành động |
| `muc` | `enum('info','warning','error')` | NO | MUL | 'info' | Mức độ nhật ký |
| `dia_chi_ip_mo_phong` | `varchar(45)` | YES | — | NULL/không có mặc định tường minh | Địa chỉ IP mô phỏng |

### Bảng `san_pham`

| Trường | Kiểu dữ liệu | NULL | Khóa/chỉ mục | Mặc định | Ý nghĩa sơ bộ |
|---|---|---|---|---|---|
| `ma_san_pham` | `char(6)` | NO | PK | NULL/không có mặc định tường minh | Mã sản phẩm/dịch vụ |
| `ten_san_pham` | `varchar(200)` | NO | — | NULL/không có mặc định tường minh | Tên sản phẩm/dịch vụ |
| `duong_dan_tinh` | `varchar(200)` | NO | MUL | NULL/không có mặc định tường minh | Đường dẫn tĩnh hoặc slug |
| `ma_ncc` | `char(6)` | NO | MUL, FK `fk_sp_ncc` | NULL/không có mặc định tường minh | Mã nhà cung cấp |
| `ma_diem_den` | `char(5)` | NO | MUL, FK `fk_sp_dd` | NULL/không có mặc định tường minh | Mã định danh điểm đến |
| `loai_san_pham` | `varchar(50)` | NO | MUL | NULL/không có mặc định tường minh | Loại sản phẩm/dịch vụ |
| `gia_co_ban` | `int(11)` | NO | MUL | NULL/không có mặc định tường minh | Giá cơ bản |
| `don_vi_tinh` | `varchar(20)` | NO | — | NULL/không có mặc định tường minh | Đơn vị tính |
| `so_ngay` | `int(11)` | NO | — | 1 | Số ngày theo quy ước sản phẩm |
| `suc_chua_toi_da` | `int(11)` | NO | — | NULL/không có mặc định tường minh | Sức chứa tối đa |
| `chinh_sach_huy` | `varchar(120)` | YES | — | NULL/không có mặc định tường minh | Mô tả chính sách hủy |
| `diem_danh_gia_tb` | `decimal(3,2)` | NO | — | 0.00 | Điểm đánh giá trung bình |
| `so_luot_danh_gia` | `int(11)` | NO | — | 0 | Số lượt đánh giá |
| `trang_thai` | `enum('dang_ban','ngung_ban')` | NO | — | 'dang_ban' | Trạng thái sản phẩm: đang bán hoặc ngừng bán |
| `ngay_tao` | `date` | NO | — | NULL/không có mặc định tường minh | Ngày tạo bản ghi |

### Bảng `thanh_toan`

| Trường | Kiểu dữ liệu | NULL | Khóa/chỉ mục | Mặc định | Ý nghĩa sơ bộ |
|---|---|---|---|---|---|
| `ma_thanh_toan` | `char(7)` | NO | PK | NULL/không có mặc định tường minh | Mã thanh toán |
| `ma_don` | `char(7)` | NO | MUL, FK `fk_tt_don` | NULL/không có mặc định tường minh | Mã đơn đặt |
| `phuong_thuc` | `varchar(40)` | NO | — | NULL/không có mặc định tường minh | Phương thức thanh toán |
| `so_tien` | `bigint(20)` | NO | — | NULL/không có mặc định tường minh | Số tiền thanh toán |
| `trang_thai` | `enum('thanh_cong','that_bai','nguoi_dung_huy','cho_xu_ly')` | NO | — | NULL/không có mặc định tường minh | Trạng thái thanh toán: thành công, thất bại, người dùng hủy hoặc chờ xử lý |
| `ma_giao_dich_sandbox` | `varchar(30)` | YES | — | NULL/không có mặc định tường minh | Mã giao dịch trong môi trường mô phỏng |
| `thoi_diem` | `datetime` | NO | — | NULL/không có mặc định tường minh | Thời điểm ghi nhận; cần xem ngữ cảnh bảng |

### Bảng `ty_gia_tien_te`

| Trường | Kiểu dữ liệu | NULL | Khóa/chỉ mục | Mặc định | Ý nghĩa sơ bộ |
|---|---|---|---|---|---|
| `ma_ty_gia` | `char(6)` | NO | PK | NULL/không có mặc định tường minh | Mã tỷ giá |
| `ma_tien_te` | `varchar(10)` | NO | — | NULL/không có mặc định tường minh | Mã tiền tệ |
| `ty_gia_quy_doi` | `decimal(12,4)` | NO | — | NULL/không có mặc định tường minh | Tỷ giá quy đổi |
| `ngay_cap_nhat` | `date` | NO | — | NULL/không có mặc định tường minh | Ngày cập nhật tỷ giá |

## 3. Các quan hệ khóa ngoại đã xác minh

| Bảng nguồn | Trường nguồn | Bảng đích | Trường đích | Ràng buộc |
|---|---|---|---|---|
| `bai_viet` | `ma_diem_den` | `diem_den` | `ma_diem_den` | `fk_bv_dd` |
| `chinh_sach_phu_thu` | `ma_san_pham` | `san_pham` | `ma_san_pham` | `fk_pt_sp` |
| `chi_so_minh_bach_gia` | `ma_ncc` | `nha_cung_cap` | `ma_ncc` | `fk_cs_ncc` |
| `chi_tiet_don` | `ma_don` | `don_dat` | `ma_don` | `fk_ct_don` |
| `chi_tiet_don` | `ma_lich` | `lich_kha_dung` | `ma_lich` | `fk_ct_lich` |
| `chi_tiet_don` | `ma_san_pham` | `san_pham` | `ma_san_pham` | `fk_ct_sp` |
| `danh_gia` | `ma_don` | `don_dat` | `ma_don` | `fk_dg_don` |
| `danh_gia` | `ma_nguoi_dung` | `nguoi_dung` | `ma_nguoi_dung` | `fk_dg_nd` |
| `danh_gia` | `ma_san_pham` | `san_pham` | `ma_san_pham` | `fk_dg_sp` |
| `don_dat` | `ma_nguoi_dung` | `nguoi_dung` | `ma_nguoi_dung` | `fk_don_nd` |
| `gia_theo_mua` | `ma_san_pham` | `san_pham` | `ma_san_pham` | `fk_gm_sp` |
| `lich_kha_dung` | `ma_san_pham` | `san_pham` | `ma_san_pham` | `fk_lkd_sp` |
| `nguoi_dung` | `ma_ncc` | `nha_cung_cap` | `ma_ncc` | `fk_nd_ncc` |
| `san_pham` | `ma_diem_den` | `diem_den` | `ma_diem_den` | `fk_sp_dd` |
| `san_pham` | `ma_ncc` | `nha_cung_cap` | `ma_ncc` | `fk_sp_ncc` |
| `thanh_toan` | `ma_don` | `don_dat` | `ma_don` | `fk_tt_don` |

## 4. Giới hạn và việc cần đối chiếu

- Cấu trúc, kiểu dữ liệu, khả năng NULL, khóa/chỉ mục và khóa ngoại được lấy từ metadata của database sandbox.
- Ý nghĩa nghiệp vụ là mô tả sơ bộ theo tên trường, không thay thế đặc tả nghiệp vụ của giảng viên.
- Chưa kết luận số quan hệ nhiều-nhiều chỉ dựa vào số khóa ngoại; cần đối chiếu khóa chính/duy nhất và quy tắc nghiệp vụ.
- Chưa xác nhận nguồn gốc dữ liệu mẫu, khả năng tái tạo schema/seed hoặc kết quả EXPLAIN trước/sau chỉ mục.
- Mọi kiểm tra được thực hiện trên `travel_booking_dt01_recovery_test` ở cổng 3307; không tác động database gốc cổng 3306.
