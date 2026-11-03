export const THUE_SUAT = 0.08

// Kiểm tra ngày khởi hành (yyyy-mm-dd) có rơi vào thứ Bảy hoặc Chủ nhật không
export function laCuoiTuan(ngay) {
  if (!ngay) return false
  const [nam, thang, ngayTrongThang] = ngay.split('-').map(Number)
  const thu = new Date(nam, thang - 1, ngayTrongThang).getDay()
  return thu === 0 || thu === 6
}

// Tính giá cuối cùng cho một chào giá:
// tiền hàng = giá người × số người
// phụ phí = (phụ phí thường + phụ phí cuối tuần nếu có) × số người
// thuế = 8% của (tiền hàng + phụ phí)
export function tinhGia(chao, { ngayKhoiHanh, soNguoi }) {
  const n = Math.min(30, Math.max(1, Number(soNguoi) || 1))
  const cuoiTuan = laCuoiTuan(ngayKhoiHanh)

  const tienHang = chao.gia_nguoi * n
  const phuThuNguoi = (chao.phu_thu_nguoi || 0) + (cuoiTuan ? chao.phu_thu_cuoi_tuan_nguoi || 0 : 0)
  const phuThu = phuThuNguoi * n
  const truocThue = tienHang + phuThu
  const thue = Math.round(truocThue * THUE_SUAT)

  return {
    so_nguoi: n,
    cuoi_tuan: cuoiTuan,
    tien_hang: tienHang,
    phu_thu: phuThu,
    truoc_thue: truocThue,
    thue,
    tong: truocThue + thue,
  }
}

// Tính giá cho tất cả chào giá và sắp xếp từ rẻ đến đắt
export function soSanhGia(danhSachChao, { ngayKhoiHanh, soNguoi }) {
  return danhSachChao
    .map((chao) => ({ chao, ...tinhGia(chao, { ngayKhoiHanh, soNguoi }) }))
    .sort((a, b) => a.tong - b.tong)
}