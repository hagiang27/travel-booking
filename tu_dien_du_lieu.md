# Tu dien du lieu - Buoi 3 (DangHongVan - V2)

## Bang NguoiDung
| Truong | Kieu du lieu | Y nghia |
|---|---|---|
| id | INT | Ma dinh danh duy nhat |
| ho_ten | VARCHAR(100) | Ho ten day du cua nguoi dung |
| email | VARCHAR(150) | Email dang nhap, duy nhat |
| mat_khau_hash | VARCHAR(255) | Mat khau da bam, khong luu dang ro |
| vai_tro | ENUM | Loai tai khoan: khach_hang / nha_cung_cap / quan_tri |

## Bang NhaCungCap
| Truong | Kieu du lieu | Y nghia |
|---|---|---|
| id | INT | Ma dinh danh duy nhat |
| ten_don_vi | VARCHAR(150) | Ten don vi cung cap dich vu |
| trang_thai_duyet | ENUM | Trang thai ho so: cho_duyet / da_duyet / tu_choi |

## Bang DichVu
| Truong | Kieu du lieu | Y nghia |
|---|---|---|
| id | INT | Ma dinh danh duy nhat |
| nha_cung_cap_id | INT | Khoa ngoai tro toi NhaCungCap |
| ten_dich_vu | VARCHAR(200) | Ten dich vu (khach san/tour/ve) |
| loai_dich_vu | ENUM | Phan loai: khach_san / tour / ve |

## Bang HangGia
| Truong | Kieu du lieu | Y nghia |
|---|---|---|
| id | INT | Ma dinh danh duy nhat |
| dich_vu_id | INT | Khoa ngoai tro toi DichVu |
| gia_goc | DECIMAL(12,2) | Gia goc cua hang gia nay |

## Bang ChinhSachPhuThu
| Truong | Kieu du lieu | Y nghia |
|---|---|---|
| id | INT | Ma dinh danh duy nhat |
| loai_phu_thu | VARCHAR(100) | Loai phi phat sinh (thue, phi dich vu, phi huy) |
| so_tien_hoac_ty_le | DECIMAL(10,2) | Gia tri phu thu |