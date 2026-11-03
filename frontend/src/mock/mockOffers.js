// Chào giá của các nhà cung cấp cho từng sản phẩm. Key là mã sản phẩm.
// Phụ phí và thuế là GIẢ ĐỊNH để minh họa, không phải số liệu thật.
export const MOCK_CHAO_GIA = {
    SP0001: [
      {
        ma_chao: 'CG001', ma_ncc: 'NCC01', ten_ncc: 'Nhà cung cấp A', danh_gia: 4.6,
        gia_nguoi: 2500000, phu_thu_nguoi: 150000, phu_thu_cuoi_tuan_nguoi: 100000,
        chinh_sach_huy: 'Hủy trước 72 giờ: hoàn 100%',
        bao_gom: ['Xe đưa đón', 'Ăn 4 bữa'], khong_bao_gom: ['Đồ uống ngoài chương trình'],
      },
      {
        ma_chao: 'CG002', ma_ncc: 'NCC02', ten_ncc: 'Nhà cung cấp B', danh_gia: 4.3,
        gia_nguoi: 2350000, phu_thu_nguoi: 200000, phu_thu_cuoi_tuan_nguoi: 150000,
        chinh_sach_huy: 'Hủy trước 48 giờ: hoàn 70%',
        bao_gom: ['Xe đưa đón', 'Ăn 3 bữa'], khong_bao_gom: ['Vé tham quan', 'Đồ uống'],
      },
      {
        ma_chao: 'CG003', ma_ncc: 'NCC03', ten_ncc: 'Nhà cung cấp C', danh_gia: 4.8,
        gia_nguoi: 2680000, phu_thu_nguoi: 0, phu_thu_cuoi_tuan_nguoi: 0,
        chinh_sach_huy: 'Hủy trước 72 giờ: hoàn 100%',
        bao_gom: ['Xe đưa đón', 'Ăn 4 bữa', 'Vé tham quan'], khong_bao_gom: ['Đồ uống ngoài chương trình'],
      },
    ],
    SP0002: [
      {
        ma_chao: 'CG004', ma_ncc: 'NCC01', ten_ncc: 'Nhà cung cấp A', danh_gia: 4.6,
        gia_nguoi: 850000, phu_thu_nguoi: 0, phu_thu_cuoi_tuan_nguoi: 100000,
        chinh_sach_huy: 'Hủy trước 24 giờ: hoàn 100%',
        bao_gom: ['Bữa sáng'], khong_bao_gom: ['Ăn trưa, ăn tối'],
      },
      {
        ma_chao: 'CG005', ma_ncc: 'NCC03', ten_ncc: 'Nhà cung cấp C', danh_gia: 4.8,
        gia_nguoi: 920000, phu_thu_nguoi: 50000, phu_thu_cuoi_tuan_nguoi: 0,
        chinh_sach_huy: 'Hủy trước 48 giờ: hoàn 100%',
        bao_gom: ['Bữa sáng', 'Đưa đón sân bay'], khong_bao_gom: ['Ăn trưa, ăn tối'],
      },
    ],
    SP0003: [
      {
        ma_chao: 'CG006', ma_ncc: 'NCC02', ten_ncc: 'Nhà cung cấp B', danh_gia: 4.3,
        gia_nguoi: 250000, phu_thu_nguoi: 0, phu_thu_cuoi_tuan_nguoi: 30000,
        chinh_sach_huy: 'Không hoàn tiền',
        bao_gom: ['Vé tham quan'], khong_bao_gom: ['Đi lại'],
      },
      {
        ma_chao: 'CG007', ma_ncc: 'NCC03', ten_ncc: 'Nhà cung cấp C', danh_gia: 4.8,
        gia_nguoi: 280000, phu_thu_nguoi: 0, phu_thu_cuoi_tuan_nguoi: 0,
        chinh_sach_huy: 'Hủy trước 24 giờ: hoàn 100%',
        bao_gom: ['Vé tham quan', 'Thuyền đi lại trong khu'], khong_bao_gom: ['Đi lại đến điểm hẹn'],
      },
    ],
    SP0004: [
      {
        ma_chao: 'CG008', ma_ncc: 'NCC01', ten_ncc: 'Nhà cung cấp A', danh_gia: 4.6,
        gia_nguoi: 800000, phu_thu_nguoi: 50000, phu_thu_cuoi_tuan_nguoi: 50000,
        chinh_sach_huy: 'Hủy trước 48 giờ: hoàn 100%',
        bao_gom: ['Xe đưa đón', 'Ăn trưa'], khong_bao_gom: ['Vé tham quan'],
      },
      {
        ma_chao: 'CG009', ma_ncc: 'NCC03', ten_ncc: 'Nhà cung cấp C', danh_gia: 4.8,
        gia_nguoi: 850000, phu_thu_nguoi: 0, phu_thu_cuoi_tuan_nguoi: 0,
        chinh_sach_huy: 'Hủy trước 72 giờ: hoàn 100%',
        bao_gom: ['Xe đưa đón', 'Ăn trưa', 'Vé tham quan'], khong_bao_gom: ['Đồ uống'],
      },
    ],
    SP0005: [
      {
        ma_chao: 'CG010', ma_ncc: 'NCC01', ten_ncc: 'Nhà cung cấp A', danh_gia: 4.6,
        gia_nguoi: 100000, phu_thu_nguoi: 0, phu_thu_cuoi_tuan_nguoi: 20000,
        chinh_sach_huy: 'Hủy trước 24 giờ: hoàn 100%',
        bao_gom: ['Xe đạp', 'Hướng dẫn viên', 'Nước uống'], khong_bao_gom: ['Ăn trưa'],
      },
      {
        ma_chao: 'CG011', ma_ncc: 'NCC02', ten_ncc: 'Nhà cung cấp B', danh_gia: 4.3,
        gia_nguoi: 90000, phu_thu_nguoi: 10000, phu_thu_cuoi_tuan_nguoi: 20000,
        chinh_sach_huy: 'Hủy trước 24 giờ: hoàn 70%',
        bao_gom: ['Xe đạp', 'Nước uống'], khong_bao_gom: ['Hướng dẫn viên', 'Ăn trưa'],
      },
    ],
    SP0006: [
      {
        ma_chao: 'CG012', ma_ncc: 'NCC02', ten_ncc: 'Nhà cung cấp B', danh_gia: 4.3,
        gia_nguoi: 250000, phu_thu_nguoi: 0, phu_thu_cuoi_tuan_nguoi: 30000,
        chinh_sach_huy: 'Không hoàn tiền',
        bao_gom: ['Vé đi thuyền'], khong_bao_gom: ['Đi lại đến điểm hẹn'],
      },
      {
        ma_chao: 'CG013', ma_ncc: 'NCC03', ten_ncc: 'Nhà cung cấp C', danh_gia: 4.8,
        gia_nguoi: 270000, phu_thu_nguoi: 0, phu_thu_cuoi_tuan_nguoi: 0,
        chinh_sach_huy: 'Hủy trước 24 giờ: hoàn 100%',
        bao_gom: ['Vé đi thuyền', 'Nước uống'], khong_bao_gom: ['Đi lại đến điểm hẹn'],
      },
    ],
    SP0007: [
      {
        ma_chao: 'CG014', ma_ncc: 'NCC01', ten_ncc: 'Nhà cung cấp A', danh_gia: 4.6,
        gia_nguoi: 500000, phu_thu_nguoi: 30000, phu_thu_cuoi_tuan_nguoi: 100000,
        chinh_sach_huy: 'Hủy trước 72 giờ: hoàn 100%',
        bao_gom: ['Phòng nghỉ', 'Bữa sáng'], khong_bao_gom: ['Ăn trưa, ăn tối'],
      },
      {
        ma_chao: 'CG015', ma_ncc: 'NCC03', ten_ncc: 'Nhà cung cấp C', danh_gia: 4.8,
        gia_nguoi: 520000, phu_thu_nguoi: 0, phu_thu_cuoi_tuan_nguoi: 0,
        chinh_sach_huy: 'Hủy trước 48 giờ: hoàn 100%',
        bao_gom: ['Phòng nghỉ', 'Bữa sáng', 'Hồ bơi'], khong_bao_gom: ['Ăn trưa, ăn tối'],
      },
    ],
    SP0008: [
      {
        ma_chao: 'CG016', ma_ncc: 'NCC01', ten_ncc: 'Nhà cung cấp A', danh_gia: 4.6,
        gia_nguoi: 300000, phu_thu_nguoi: 20000, phu_thu_cuoi_tuan_nguoi: 30000,
        chinh_sach_huy: 'Hủy trước 24 giờ: hoàn 100%',
        bao_gom: ['Xe đưa đón', 'Hướng dẫn viên'], khong_bao_gom: ['Ăn trưa'],
      },
      {
        ma_chao: 'CG017', ma_ncc: 'NCC02', ten_ncc: 'Nhà cung cấp B', danh_gia: 4.3,
        gia_nguoi: 280000, phu_thu_nguoi: 40000, phu_thu_cuoi_tuan_nguoi: 30000,
        chinh_sach_huy: 'Hủy trước 48 giờ: hoàn 70%',
        bao_gom: ['Hướng dẫn viên'], khong_bao_gom: ['Xe đưa đón', 'Ăn trưa'],
      },
    ],
    SP0009: [
      {
        ma_chao: 'CG018', ma_ncc: 'NCC02', ten_ncc: 'Nhà cung cấp B', danh_gia: 4.3,
        gia_nguoi: 150000, phu_thu_nguoi: 0, phu_thu_cuoi_tuan_nguoi: 20000,
        chinh_sach_huy: 'Hủy trước 24 giờ: hoàn 70%',
        bao_gom: ['Ốc ngắm chim', 'Hướng dẫn viên'], khong_bao_gom: ['Đi lại'],
      },
      {
        ma_chao: 'CG019', ma_ncc: 'NCC03', ten_ncc: 'Nhà cung cấp C', danh_gia: 4.8,
        gia_nguoi: 160000, phu_thu_nguoi: 0, phu_thu_cuoi_tuan_nguoi: 0,
        chinh_sach_huy: 'Hủy trước 24 giờ: hoàn 100%',
        bao_gom: ['Ốc ngắm chim', 'Hướng dẫn viên', 'Nước uống'], khong_bao_gom: ['Đi lại'],
      },
    ],
    SP0010: [
      {
        ma_chao: 'CG020', ma_ncc: 'NCC01', ten_ncc: 'Nhà cung cấp A', danh_gia: 4.6,
        gia_nguoi: 480000, phu_thu_nguoi: 20000, phu_thu_cuoi_tuan_nguoi: 80000,
        chinh_sach_huy: 'Hủy trước 72 giờ: hoàn 100%',
        bao_gom: ['Phòng nghỉ', 'Bữa sáng'], khong_bao_gom: ['Ăn trưa, ăn tối'],
      },
      {
        ma_chao: 'CG021', ma_ncc: 'NCC03', ten_ncc: 'Nhà cung cấp C', danh_gia: 4.8,
        gia_nguoi: 520000, phu_thu_nguoi: 0, phu_thu_cuoi_tuan_nguoi: 0,
        chinh_sach_huy: 'Hủy trước 48 giờ: hoàn 100%',
        bao_gom: ['Phòng nghỉ', 'Bữa sáng', 'Xe đạp'], khong_bao_gom: ['Ăn trưa, ăn tối'],
      },
    ],
    SP0011: [
      {
        ma_chao: 'CG022', ma_ncc: 'NCC01', ten_ncc: 'Nhà cung cấp A', danh_gia: 4.6,
        gia_nguoi: 350000, phu_thu_nguoi: 50000, phu_thu_cuoi_tuan_nguoi: 50000,
        chinh_sach_huy: 'Hủy trước 48 giờ: hoàn 100%',
        bao_gom: ['Các món hải sản', 'Hướng dẫn viên'], khong_bao_gom: ['Đồ uống'],
      },
      {
        ma_chao: 'CG023', ma_ncc: 'NCC02', ten_ncc: 'Nhà cung cấp B', danh_gia: 4.3,
        gia_nguoi: 330000, phu_thu_nguoi: 70000, phu_thu_cuoi_tuan_nguoi: 70000,
        chinh_sach_huy: 'Hủy trước 48 giờ: hoàn 70%',
        bao_gom: ['Các món hải sản'], khong_bao_gom: ['Hướng dẫn viên', 'Đồ uống'],
      },
    ],
    SP0012: [
      {
        ma_chao: 'CG024', ma_ncc: 'NCC02', ten_ncc: 'Nhà cung cấp B', danh_gia: 4.3,
        gia_nguoi: 300000, phu_thu_nguoi: 0, phu_thu_cuoi_tuan_nguoi: 40000,
        chinh_sach_huy: 'Không hoàn tiền',
        bao_gom: ['Vé vào cổng'], khong_bao_gom: ['Trò chơi trả phí'],
      },
      {
        ma_chao: 'CG025', ma_ncc: 'NCC03', ten_ncc: 'Nhà cung cấp C', danh_gia: 4.8,
        gia_nguoi: 320000, phu_thu_nguoi: 0, phu_thu_cuoi_tuan_nguoi: 0,
        chinh_sach_huy: 'Hủy trước 24 giờ: hoàn 100%',
        bao_gom: ['Vé vào cổng', 'Xe đưa đón'], khong_bao_gom: ['Trò chơi trả phí'],
      },
    ],
    SP0013: [
      {
        ma_chao: 'CG026', ma_ncc: 'NCC01', ten_ncc: 'Nhà cung cấp A', danh_gia: 4.6,
        gia_nguoi: 700000, phu_thu_nguoi: 50000, phu_thu_cuoi_tuan_nguoi: 100000,
        chinh_sach_huy: 'Hủy trước 72 giờ: hoàn 100%',
        bao_gom: ['Phòng nghỉ', 'Bữa sáng'], khong_bao_gom: ['Ăn trưa, ăn tối'],
      },
      {
        ma_chao: 'CG027', ma_ncc: 'NCC03', ten_ncc: 'Nhà cung cấp C', danh_gia: 4.8,
        gia_nguoi: 730000, phu_thu_nguoi: 0, phu_thu_cuoi_tuan_nguoi: 0,
        chinh_sach_huy: 'Hủy trước 48 giờ: hoàn 100%',
        bao_gom: ['Phòng nghỉ', 'Bữa sáng', 'Hồ bơi'], khong_bao_gom: ['Ăn trưa, ăn tối'],
      },
    ],
    SP0014: [
      {
        ma_chao: 'CG028', ma_ncc: 'NCC02', ten_ncc: 'Nhà cung cấp B', danh_gia: 4.3,
        gia_nguoi: 1450000, phu_thu_nguoi: 100000, phu_thu_cuoi_tuan_nguoi: 150000,
        chinh_sach_huy: 'Hủy trước 72 giờ: hoàn 70%',
        bao_gom: ['Xe đưa đón', 'Ăn 4 bữa'], khong_bao_gom: ['Đồ uống'],
      },
      {
        ma_chao: 'CG029', ma_ncc: 'NCC03', ten_ncc: 'Nhà cung cấp C', danh_gia: 4.8,
        gia_nguoi: 1500000, phu_thu_nguoi: 80000, phu_thu_cuoi_tuan_nguoi: 0,
        chinh_sach_huy: 'Hủy trước 72 giờ: hoàn 100%',
        bao_gom: ['Xe đưa đón', 'Ăn 4 bữa', 'Tắm biển'], khong_bao_gom: ['Đồ uống'],
      },
    ],
    SP0015: [
      {
        ma_chao: 'CG030', ma_ncc: 'NCC01', ten_ncc: 'Nhà cung cấp A', danh_gia: 4.6,
        gia_nguoi: 2500000, phu_thu_nguoi: 150000, phu_thu_cuoi_tuan_nguoi: 150000,
        chinh_sach_huy: 'Hủy trước 72 giờ: hoàn 100%',
        bao_gom: ['Xe đưa đón', 'Ăn 6 bữa', 'Phòng nghỉ'], khong_bao_gom: ['Đồ uống'],
      },
      {
        ma_chao: 'CG031', ma_ncc: 'NCC03', ten_ncc: 'Nhà cung cấp C', danh_gia: 4.8,
        gia_nguoi: 2600000, phu_thu_nguoi: 0, phu_thu_cuoi_tuan_nguoi: 0,
        chinh_sach_huy: 'Hủy trước 72 giờ: hoàn 100%',
        bao_gom: ['Xe đưa đón', 'Ăn 6 bữa', 'Phòng nghỉ', 'Tắm biển'], khong_bao_gom: ['Đồ uống'],
      },
    ],
  }