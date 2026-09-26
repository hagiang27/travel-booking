
# 5 ca kiem thu phan quyen - Buoi 5 (V5 - SV4)

## TC1 - Kiem tra mat khau da duoc bam dung chuan

Cach thuc hien: Vao phpMyAdmin, chay:

 SELECT mat_khau_hash FROM nguoi_dung WHERE ten_dang_nhap = '[ten dang nhap test]';

Ket qua mong doi: Chuoi tra ve co tien to thuat toan ($2b$ neu dung bcrypt,

$argon2id$ neu dung Argon2id), KHONG phai mat khau dang chu ro.

Ket qua thuc te: [dien sau khi test]

Trang thai: [Dat / Khong dat]

## TC2 - Phan quyen giao dien: khach hang khong vao duoc trang quan tri

Cach thuc hien: Dang nhap bang tai khoan vai_tro = khach_hang, sau do go

truc tiep vao thanh dia chi duong dan quan tri (vi du /quan-tri hoac

/admin tuy V3 dat ten).

Ket qua mong doi: Bi tu choi, tra ve loi 403 (Khong du quyen truy cap),

KHONG duoc vao trang.

Ket qua thuc te: [dien sau khi test]

Trang thai: [Dat / Khong dat]

## TC3 - Phan quyen API: goi thang API quan tri bang phien khach hang

Cach thuc hien: Dang nhap tren trinh duyet bang tai khoan khach_hang,

mo cong cu nha phat trien (F12) lay cookie phien, roi dung lenh:

 curl -s -b "cookie_phien=..." http://localhost:8000/api/v1/quan-tri/...

Ket qua mong doi: Tra ve ma trang thai 403, KHONG tra ve du lieu quan tri.

Ket qua thuc te: [dien sau khi test]

Trang thai: [Dat / Khong dat]

## TC4 - Chong tham chieu doi tuong truc tiep (IDOR): nha cung cap A

khong xem duoc du lieu cua nha cung cap B

Cach thuc hien: Dang nhap bang tai khoan nha_cung_cap A, thu doi ma

(id) trong duong dan/API de xem don dat hoac san pham cua nha cung

cap B (vi du sua ma_ncc trong URL).

Ket qua mong doi: He thong tu choi hoac tra ve rong, KHONG hien thi

du lieu cua nha cung cap khac.

Ket qua thuc te: [dien sau khi test]

Trang thai: [Dat / Khong dat]

## TC5 - Phien bi huy hoan toan sau khi dang xuat

Cach thuc hien: Dang nhap thanh cong, vao 1 trang can dang nhap (vi du

trang ca nhan), bam dang xuat, sau do bam nut Quay lai (Back) cua

trinh duyet.

Ket qua mong doi: KHONG duoc vao lai trang can dang nhap do; phai bi

chuyen huong ve trang dang nhap.

Ket qua thuc te: [dien sau khi test]

Trang thai: [Dat / Khong dat]

