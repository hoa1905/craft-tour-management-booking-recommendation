<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAccountController extends Controller
{
    /**
     * Hiển thị form đăng nhập Admin
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        //  Validate đầu vào
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        // Đăng nhập bằng guard 'admin' (đã cấu hình trong auth.php)
        if (Auth::guard('admin')->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ], $request->boolean('remember'))) {

            $user = Auth::guard('admin')->user();

            // Kiểm tra role
            if ($user->role === 'admin') {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }

            // Nếu không phải admin, đăng xuất và cấm truy cập
            Auth::guard('admin')->logout();
            abort(403, 'Bạn không có quyền truy cập vào khu vực quản trị.');
        }

        // Sai tài khoản/mật khẩu
        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->onlyInput('email');
    }

    /**
     * Đăng xuất Admin
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
