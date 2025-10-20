<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\confirm;

class AccountController extends Controller
{
    // Trang popup login/register dùng ajax => không cần view riêng
    // Xử lý đăng ký
    public function postRegister(Request $request)
    {
        $request->validate([
            'rName' => 'required|string|max:255',
            'rEmail' => 'required|email|unique:users,email',
            'rPassWord' => 'required|min:6|same:rConfirm_PassWord',
            'r_agree' => 'accepted'
        ], [
            'rName.required' => 'Vui lòng nhập họ tên.',
            'rEmail.required' => 'Vui lòng nhập email.',
            'rEmail.email' => 'Email không hợp lệ.',
            'rEmail.unique' => 'Email này đã được đăng ký.',
            'rPassWord.required' => 'Vui lòng nhập mật khẩu.',
            'rPassWord.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'rPassWord.same' => 'Mật khẩu xác nhận không trùng khớp.',
            'r_agree.accepted' => 'Bạn phải đồng ý với điều khoản.'
        ]);

        $user = User::create([
            'user_name' => $request->rName,
            'email' => $request->rEmail,
            'password' => Hash::make($request->rPassWord),
            'role' => 'client',
            'status' => 'approved',
        ]);

        Auth::guard('client')->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công!',
            'username' => $user->user_name
        ]);
    }

    // Xử lý đăng nhập
    public function postLogin(Request $request)
    {
        $request->validate([
            'login_user' => 'required|email',
            'login_pass' => 'required|min:6'
        ], [
            'login_user.required' => 'Vui lòng nhập email.',
            'login_user.email' => 'Email không hợp lệ.',
            'login_pass.required' => 'Vui lòng nhập mật khẩu.',
            'login_pass.min' => 'Mật khẩu phải có ít nhất 6 ký tự.'
        ]);

        if (Auth::guard('client')->attempt([
            'email' => $request->login_user,
            'password' => $request->login_pass,
            'role' => 'client'
        ], $request->boolean('ch_remember'))) {

            $user = Auth::guard('client')->user();

            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công!',
                'username' => $user->user_name
            ]);
        }

        // Nếu sai tài khoản hoặc mật khẩu
        return response()->json([
            'success' => false,
            'message' => 'Email hoặc mật khẩu không chính xác.'
        ], 401);
    }

    // Đăng xuất
    public function logout()
    {
        Auth::guard('client')->logout();
        return redirect('/');
    }
}
