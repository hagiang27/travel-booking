# Buổi 4 – SV4 – Danh sách điều chỉnh mô hình dữ liệu và đối chiếu API

## 1. Vai trò
- Sinh viên: SV4
- Vai trò kiêm nhiệm: V2 – Kiến trúc sư dữ liệu; V5 – Kiểm thử và triển khai.
- Đề tài: ĐT-01 – Nền tảng đặt dịch vụ du lịch đa nhà cung cấp có công cụ so sánh giá minh bạch.

## 2. Căn cứ của Buổi 4
Buổi 4 yêu cầu V2 đối chiếu hợp đồng API với mô hình dữ liệu và phát hiện trường thiếu; đầu ra cá nhân là **Danh sách điều chỉnh mô hình**.
V5 chủ trì kế hoạch kiểm thử và bảng phân bổ **60 ca theo mô-đun**.

## 3. Danh sách trường cần đối chiếu với API

| Nhóm nghiệp vụ DT-01 | Dữ liệu cần có khi API phục vụ chức năng | Bảng Buổi 3 cần đối chiếu | Cách xử lý |
|---|---|---|---|
| Danh sách dịch vụ | mã dịch vụ, tên, loại, giá cơ bản, nhà cung cấp, điểm đến, thời lượng | `san_pham`, `nha_cung_cap`, `diem_den` | Đối chiếu tên trường API với cột thật; thiếu thì ghi bổ sung |
| Tìm kiếm/lọc | từ khóa, loại dịch vụ, địa điểm, giá min/max, ngày, sắp xếp, phân trang | `san_pham`, `diem_den`, `lich_kha_dung` | API phải nhận đúng tham số và không trả toàn bộ bảng rồi mới lọc |
| So sánh giá | giá cơ bản, phụ thu, tổng chi phí, tiền tệ, tỷ giá | `san_pham`, `chinh_sach_phu_thu`, `ty_gia_tien_te` | Không chỉ trả giá niêm yết; phải có dữ liệu để tính tổng chi phí |
| Giá theo thời vụ | giá theo mùa/ngày và điều kiện áp dụng | `gia_theo_mua`, `lich_kha_dung` | Đối chiếu khóa sản phẩm + thời gian + mức giá |
| Tồn chỗ | ngày khởi hành, tổng chỗ, chỗ đã giữ, chỗ đã bán hoặc các trường tương đương của mô hình nhóm | `lich_kha_dung` | API đặt/giữ chỗ phải đọc được tồn theo ngày |
| Chính sách phụ thu | loại phụ thu, giá trị, kiểu tính, mô tả | `chinh_sach_phu_thu` | API phải đủ dữ liệu để giải thích từng khoản phụ thu |
| Chỉ số minh bạch | nhà cung cấp, điểm minh bạch, ngày tính | `chi_so_minh_bach_gia` | API dashboard/so sánh phải có dữ liệu công bố chỉ số |
| Đặt chỗ | khách hàng, sản phẩm, ngày, số khách, tổng tiền, trạng thái | `don_dat`, `chi_tiet_don` | Đối chiếu khóa chính/khóa ngoại và trạng thái |
| Thanh toán | đơn, số tiền, phương thức, trạng thái, thời điểm | `thanh_toan` | API không được tạo thanh toán cho đơn không hợp lệ |
| Đánh giá | người dùng, đơn/chi tiết đơn, điểm, nội dung, thời điểm | `danh_gia` | Chỉ cho đánh giá sau sử dụng theo nghiệp vụ nhóm |
| Nhật ký | hành động, đối tượng, người thực hiện, thời gian | `nhat_ky_he_thong` | Các thao tác nhạy cảm nên có dữ liệu phục vụ truy vết |
| Bài viết | tiêu đề, nội dung, đường dẫn, trạng thái | `bai_viet` | Chỉ đối chiếu nếu API cẩm nang/tin tức được nhóm triển khai |

## 4. Ba điểm DT-01 phải kiểm tra kỹ

### 4.1. Tổng chi phí minh bạch
API so sánh không nên chỉ trả `giá cơ bản`. Cần có đủ dữ liệu để giao diện giải thích:
- Giá cơ bản.
- Các khoản phụ thu.
- Cách tính phụ thu: cố định hoặc theo tỷ lệ.
- Tổng chi phí sau phụ thu.
- Đơn vị tiền tệ và tỷ giá nếu quy đổi.
- Thời điểm/ngày áp dụng giá.

### 4.2. Quy đổi tiền tệ
Đối chiếu `ty_gia_tien_te` với API. Nếu API có quy đổi, phải xác định rõ:
- tiền tệ gốc;
- tiền tệ hiển thị;
- tỷ giá sử dụng;
- ngày cập nhật tỷ giá.

### 4.3. Chỉ số minh bạch giá
Đối chiếu `chi_so_minh_bach_gia` với API. Không được nhầm điểm minh bạch của nhà cung cấp với giá sản phẩm.

## 5. Kết luận cần ghi trong biên bản
Sau khi V3 hoàn thành bản hợp đồng API 0.1, SV4 đối chiếu từng
