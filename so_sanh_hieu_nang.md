# So sánh hiệu năng truy vấn trước và sau khi tạo chỉ mục



## Câu truy vấn



Tìm sản phẩm theo khoảng giá và ngày khởi hành.



## Kết quả so sánh



| Chỉ tiêu | Trước khi có chỉ mục | Sau khi có chỉ mục |

|---|---|---|

| type của `lich\_kha\_dung` | range | range |

| key của `lich\_kha\_dung` | idx\_lkd\_ngay | idx\_lkd\_ngay |

| rows của `lich\_kha\_dung` | 1 | 1 |

| type của `san\_pham` | eq\_ref | eq\_ref |

| key của `san\_pham` | PRIMARY | PRIMARY |

| rows của `san\_pham` | 1 | 1 |

| Chỉ mục mới | Chưa có | idx\_sp\_gia\_bosung |



## Nhận xét



Sau khi tạo chỉ mục `idx\_sp\_gia\_bosung` trên cột `gia\_co\_ban`, chỉ mục mới đã xuất hiện trong `possible\_keys`.



Tuy nhiên, kế hoạch thực thi vẫn sử dụng `idx\_lkd\_ngay` cho bảng `lich\_kha\_dung` và `PRIMARY` cho bảng `san\_pham`; các giá trị `type` và `rows` không thay đổi.



Điều này cho thấy MySQL không sử dụng chỉ mục `idx\_sp\_gia\_bosung` trong truy vấn này. Do lượng dữ liệu hiện tại nhỏ và điều kiện ngày khởi hành đã được xử lý bằng `idx\_lkd\_ngay`, kế hoạch thực thi hiện tại vẫn được MySQL lựa chọn.

