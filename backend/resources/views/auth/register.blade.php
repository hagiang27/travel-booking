<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Đăng ký</title></head>
<body>
    <h1>Đăng ký tài khoản</h1>
    @if ($errors->any())
        <ul style="color:red">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    @endif
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <p>Họ tên: <input type="text" name="name" value="{{ old('name') }}"></p>
        <p>Email: <input type="email" name="email" value="{{ old('email') }}"></p>
        <p>Mật khẩu: <input type="password" name="password"></p>
        <p>Nhập lại mật khẩu: <input type="password" name="password_confirmation"></p>
        <p>Vai trò:
            <select name="role">
                <option value="khach_hang">Khách hàng</option>
                <option value="nha_cung_cap">Nhà cung cấp</option>
                <option value="quan_tri">Quản trị viên</option>
            </select>
        </p>
        <button type="submit">Đăng ký</button>
    </form>
    <p><a href="{{ route('login') }}">Đã có tài khoản? Đăng nhập</a></p>
</body>
</html>