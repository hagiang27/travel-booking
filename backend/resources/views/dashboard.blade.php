<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Trang chủ</title></head>
<body>
    <h1>Xin chào, {{ auth()->user()->name }}</h1>
    <p>Vai trò: {{ auth()->user()->role }}</p>
    @if (auth()->user()->role === 'quan_tri')
        <p><a href="{{ route('admin') }}">Vào trang Quản trị</a></p>
    @endif
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Đăng xuất</button>
    </form>
</body>
</html>