// Số liệu mẫu cho bảng điều khiển. Không phải dữ liệu thật.
export const MOCK_THONG_KE = {
    tong_don: 128,
    doanh_thu: 412000000,
    don_cho_thanh_toan: 14,
    doanh_thu_theo_thang: [
      { thang: 'T1', tien: 52000000 },
      { thang: 'T2', tien: 61000000 },
      { thang: 'T3', tien: 48000000 },
      { thang: 'T4', tien: 75000000 },
      { thang: 'T5', tien: 66000000 },
      { thang: 'T6', tien: 110000000 },
    ],
    don_theo_trang_thai: [
      { trang_thai: 'Đã thanh toán', so_don: 90 },
      { trang_thai: 'Chờ thanh toán', so_don: 14 },
      { trang_thai: 'Đã hủy', so_don: 24 },
    ],
  }
  
  // Kết quả mẫu từ mô-đun Python. dang_hoat_dong: false để thử nhánh dự phòng.
  export const MOCK_PYTHON = {
    dang_hoat_dong: false,
    mua_vu: [
      { thang: 'T1', ty_le_lap_cho: 0.42, mua: 'thấp điểm' },
      { thang: 'T6', ty_le_lap_cho: 0.81, mua: 'cao điểm' },
    ],
  }