import bcrypt

mat_khau_goc = "Test@123"  # mat khau demo dung chung cho ca 3 tai khoan test
hash_ket_qua = bcrypt.hashpw(mat_khau_goc.encode('utf-8'), bcrypt.gensalt(rounds=12))
print(hash_ket_qua.decode('utf-8'))
