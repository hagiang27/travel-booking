# \# Ma tran phan quyen theo vai tro - Buoi 5 (V2 chuan bi cho V3)

# 

# De tai DT-01 uu tien trien khai truoc 3 vai tro chinh cho Moc M1

# (theo dung "ba vai tro" trong kiem thu bat buoc Muc 5.5 so tay):

# 

# | Vai tro (gia tri ENUM) | Duoc lam gi (Moc M1) | Duong dan quan tri chan truy cap |

# |---|---|---|

# | khach\_hang | Xem danh sach, tim kiem, dat cho, danh gia | Khong duoc vao /quan-tri, /nha-cung-cap |

# | nha\_cung\_cap | Quan ly dich vu, hang gia, phu thu cua CHINH MINH; xem don dat cua minh | Khong duoc vao /quan-tri; chi xem du lieu cua minh (khong xem duoc NCC khac) |

# | quan\_tri | Duyet ho so nha cung cap; quan ly toan bo du lieu; xem chi so minh bach gia | Duoc vao tat ca |

# 

# Hai vai tro con lai (huong\_dan\_vien, kiem\_thu) chua can trien khai o Moc M1,

# de danh cho buoi sau neu de tai phat sinh nhu cau.

# 

# \## Yeu cau ky thuat bat buoc (theo Muc 6.3 tai lieu ky thuat)

# \- Phan quyen PHAI kiem tra o tang may chu (middleware/policy), KHONG duoc

# &#x20; chi an nut tren giao dien - day la loi nghiem trong bi tru diem.

# \- Nha cung cap chi duoc xem/sua du lieu CUA CHINH MINH (chong tham chieu

# &#x20; doi tuong truc tiep khong an toan - IDOR), dung lop Policy, khong kiem

# &#x20; tra roi rac trong tung ham.

