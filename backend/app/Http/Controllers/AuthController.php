<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:khach_hang,nha_cung_cap,quan_tri',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
    public function apiLogin(Request $request)
{
    $data = $request->validate([
        'ten_dang_nhap' => 'required|string',
        'mat_khau' => 'required|string',
    ]);

    /*
     * Backend V1 hiện đang dùng email làm thông tin đăng nhập.
     * Vì API của nhóm quy định tên trường là ten_dang_nhap,
     * nên tạm ánh xạ:
     *
     * ten_dang_nhap -> email
     * mat_khau      -> password
     */

    $credentials = [
        'email' => $data['ten_dang_nhap'],
        'password' => $data['mat_khau'],
    ];

    if (!Auth::attempt($credentials)) {
        return response()->json([
            'loi' => 'Tên đăng nhập hoặc mật khẩu không đúng',
        ], 401);
    }

    $request->session()->regenerate();

    $user = Auth::user();

    return response()->json([
        'message' => 'Đăng nhập thành công',
        'data' => [
            'ma_nguoi_dung' => $user->id,
            'ho_ten' => $user->name,
            'ten_dang_nhap' => $user->email,
            'vai_tro' => $user->role,
        ],
    ], 200);
}

public function apiMe(Request $request)
{
    $user = $request->user();

    return response()->json([
        'data' => [
            'ma_nguoi_dung' => $user->id,
            'ho_ten' => $user->name,
            'ten_dang_nhap' => $user->email,
            'vai_tro' => $user->role,
        ],
    ]);
}

public function apiLogout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json([
        'message' => 'Đăng xuất thành công',
    ]);
}   
}