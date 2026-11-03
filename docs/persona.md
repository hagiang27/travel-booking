# Persona khách hàng — Website Du lịch số

## Persona 1: Chị Lan — Nhân viên văn phòng
- Tuổi: 34
- Nghề nghiệp: Nhân viên văn phòng
- Thiết bị chính: Điện thoại (dùng lúc nghỉ trưa)
- Mục tiêu: Đặt tour cuối tuần cho gia đình 4 người, ngân sách rõ ràng
- Rào cản: Mất kiên nhẫn nếu web tải chậm, sợ đặt nhầm ngày hết phòng
- Gợi ý thiết kế UI:
  - Trang danh sách phải lọc nhanh theo giá và ngày
  - Ảnh tải nhanh, không load chậm trên mạng di động
  - Nút "Đặt ngay" nổi bật, dễ bấm bằng ngón tay cái trên điện thoại

## Persona 2: Anh Minh — Sinh viên
- Tuổi: 21
- Nghề nghiệp: Sinh viên
- Thiết bị chính: Điện thoại và laptop
- Mục tiêu: Tìm tour giá rẻ, đi nhóm bạn, linh hoạt ngày giờ
- Rào cản: Ngân sách hạn chế, cần so sánh nhiều lựa chọn trước khi quyết định
- Gợi ý thiết kế UI:
  - Cần chức năng so sánh nhiều tour cùng lúc
  - Hiển thị rõ giá theo từng mùa/thời điểm (price_override)
  - Badge "còn X chỗ" để tạo cảm giác khan hiếm, thúc đẩy quyết định nhanh

## Persona 3: Cô Hương — Khách lớn tuổi
- Tuổi: 58
- Nghề nghiệp: Đã nghỉ hưu
- Thiết bị chính: Máy tính bàn, ít rành công nghệ
- Mục tiêu: Đặt tour an toàn, tin cậy, có người hỗ trợ
- Rào cản: Dễ bối rối với giao diện phức tạp, sợ nhập sai thông tin thanh toán
- Gợi ý thiết kế UI:
  - Chữ đủ lớn, độ tương phản cao (đạt chuẩn AA)
  - Các bước đặt chỗ phải rõ ràng, tuần tự, có xác nhận từng bước
  - Hiển thị số hotline hỗ trợ dễ thấy

## Luồng màn hình chính (wireframe sơ bộ)

1. **Trang chủ** → hiển thị danh sách tour nổi bật, ô tìm kiếm theo điểm đến/ngày
2. **Trang danh sách** → bộ lọc (giá, ngày, điểm đến), lưới card tour (ảnh + giá + tên)
3. **Trang chi tiết tour** → ảnh lớn, mô tả, lịch trình, chính sách hủy, đánh giá, nút "Đặt ngay"
4. **Trang đặt chỗ** → chọn ngày, số lượng khách, xem giá tổng, nhập thông tin liên hệ
5. **Trang xác nhận** → tóm tắt đơn, mã đặt chỗ, hướng dẫn bước tiếp theo