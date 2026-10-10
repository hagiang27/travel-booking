<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Đăng nhập</title></head>
<body>
    <h1>Đăng nhập</h1>
    @if ($errors->any())
        <p style="color:red">{{ $errors->first() }}</p>
    @endif
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <p>Email: <input type="email" name="email"></p>
        <p>Mật khẩu: <input type="password" name="password"></p>
        <button type="submit">Đăng nhập</button>
    </form>
    <p><a href="{{ route('register') }}">Chưa có tài khoản? Đăng ký</a></p>
</body>
</html>