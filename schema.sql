-- Schema co so du lieu - DT-01
-- Nen tang dat dich vu du lich da nha cung cap co so sanh gia minh bach

CREATE TABLE NguoiDung (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ho_ten VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mat_khau_hash VARCHAR(255) NOT NULL,
    vai_tro ENUM('khach_hang', 'nha_cung_cap', 'quan_tri') NOT NULL DEFAULT 'khach_hang',
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE NhaCungCap (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_don_vi VARCHAR(150) NOT NULL,
    email_lien_he VARCHAR(150) NOT NULL,
    trang_thai_duyet ENUM('cho_duyet', 'da_duyet', 'tu_choi') NOT NULL DEFAULT 'cho_duyet',
    ngay_dang_ky DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE DichVu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nha_cung_cap_id INT NOT NULL,
    ten_dich_vu VARCHAR(200) NOT NULL,
    loai_dich_vu ENUM('khach_san', 'tour', 've') NOT NULL,
    dia_diem VARCHAR(150),
    mo_ta TEXT,
    FOREIGN KEY (nha_cung_cap_id) REFERENCES NhaCungCap(id)
);

CREATE TABLE HangGia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dich_vu_id INT NOT NULL,
    ten_hang VARCHAR(100) NOT NULL,
    gia_goc DECIMAL(12,2) NOT NULL CHECK (gia_goc >= 0),
    so_khach_toi_da INT NOT NULL CHECK (so_khach_toi_da > 0),
    mua_ap_dung VARCHAR(50),
    FOREIGN KEY (dich_vu_id) REFERENCES DichVu(id)
);

CREATE TABLE ChinhSachPhuThu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dich_vu_id INT NOT NULL,
    loai_phu_thu VARCHAR(100) NOT NULL,
    so_tien_hoac_ty_le DECIMAL(10,2) NOT NULL,
    mo_ta VARCHAR(255),
    FOREIGN KEY (dich_vu_id) REFERENCES DichVu(id)
);

CREATE TABLE LichKhaDung (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dich_vu_id INT NOT NULL,
    ngay DATE NOT NULL,
    so_luong_tong INT NOT NULL,
    so_luong_dang_giu INT NOT NULL DEFAULT 0,
    so_luong_da_ban INT NOT NULL DEFAULT 0,
    FOREIGN KEY (dich_vu_id) REFERENCES DichVu(id),
    CONSTRAINT chk_khadung CHECK (so_luong_dang_giu + so_luong_da_ban <= so_luong_tong)
);

CREATE TABLE GioHang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id INT NOT NULL,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_dung_id) REFERENCES NguoiDung(id)
);

CREATE TABLE ChiTietGioHang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gio_hang_id INT NOT NULL,
    hang_gia_id INT NOT NULL,
    so_luong INT NOT NULL CHECK (so_luong > 0),
    FOREIGN KEY (gio_hang_id) REFERENCES GioHang(id),
    FOREIGN KEY (hang_gia_id) REFERENCES HangGia(id)
);

CREATE TABLE DonDatCho (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id INT NOT NULL,
    tong_tien DECIMAL(12,2) NOT NULL CHECK (tong_tien >= 0),
    trang_thai ENUM('cho_xu_ly', 'da_xac_nhan', 'da_huy') NOT NULL DEFAULT 'cho_xu_ly',
    ngay_dat DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_dung_id) REFERENCES NguoiDung(id)
);

CREATE TABLE ThanhToan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    don_dat_cho_id INT NOT NULL,
    so_tien DECIMAL(12,2) NOT NULL,
    trang_thai_thanh_toan ENUM('cho_thanh_toan', 'da_thanh_toan', 'that_bai') NOT NULL DEFAULT 'cho_thanh_toan',
    phuong_thuc VARCHAR(50),
    FOREIGN KEY (don_dat_cho_id) REFERENCES DonDatCho(id)
);

CREATE TABLE DanhGia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    don_dat_cho_id INT NOT NULL,
    diem_so INT NOT NULL CHECK (diem_so BETWEEN 1 AND 5),
    noi_dung TEXT,
    ngay_danh_gia DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (don_dat_cho_id) REFERENCES DonDatCho(id)
);

CREATE TABLE TyGiaTienTe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ma_tien_te VARCHAR(10) NOT NULL,
    ty_gia_quy_doi DECIMAL(10,4) NOT NULL,
    ngay_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ChiSoMinhBachGia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nha_cung_cap_id INT NOT NULL,
    diem_minh_bach DECIMAL(5,2) NOT NULL CHECK (diem_minh_bach BETWEEN 0 AND 100),
    ngay_tinh DATE NOT NULL,
    FOREIGN KEY (nha_cung_cap_id) REFERENCES NhaCungCap(id)
);