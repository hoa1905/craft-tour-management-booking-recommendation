<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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


    //
    function showChangePasswordForm()
    {
        return view('client.change_password');
    }

    public function updatePassword(Request $request)
    {
        // Bước 1: Kiểm tra hợp lệ
        $request->validate([
            'old_pass' => 'required|min:6',
            'new_pass' => 'required|min:6|different:old_pass',
            're_new_pass' => 'required|same:new_pass',
        ], [
            'old_pass.required' => 'Vui lòng nhập mật khẩu cũ.',
            'old_pass.min' => 'Mật khẩu cũ phải có ít nhất 6 ký tự.',
            'new_pass.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_pass.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_pass.different' => 'Mật khẩu mới phải khác mật khẩu cũ.',
            're_new_pass.required' => 'Vui lòng nhập lại mật khẩu mới.',
            're_new_pass.same' => 'Mật khẩu nhập lại không trùng khớp.',
        ]);

        // Bước 2: Lấy user hiện tại
        /** @var \App\Models\User $user */
        $user = Auth::guard('client')->user();

        // Bước 3: Kiểm tra mật khẩu cũ
        if (!Hash::check($request->old_pass, $user->password)) {
            return back()->withErrors(['old_pass' => 'Mật khẩu cũ không chính xác.']);
        }

        // Bước 4: Cập nhật mật khẩu mới
        $user->password = Hash::make($request->new_pass);
        $user->save();

        // Bước 5: Logout để bảo mật
        Auth::guard('client')->logout();

        // Bước 6: Thông báo kết quả
        return redirect('/')->with('success', 'Đổi mật khẩu thành công! Vui lòng đăng nhập lại.');
    }

    public function showAccountInfo()
    {
        $user = Auth::guard('client')->user();
        return view('client.account_info', compact('user'));
    }

    public function updateAccountInfo(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string|max:100',
            'phone_number' => 'required|string|max:15',
            'address' => 'nullable|string|max:255',
        ], [
            'user_name.required' => 'Vui lòng nhập họ tên.',
            'phone_number.required' => 'Vui lòng nhập số điện thoại.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::guard('client')->user();

        $user->update([
            'user_name' => $request->user_name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
        ]);

        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    public function updateAvatar(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::guard('client')->user();

        // Nếu người dùng bấm nút "Xóa ảnh đại diện"
        if ($request->has('remove_avatar')) {
            // Nếu có file cũ thì xóa file vật lý (tùy chọn)
            if ($user->avatar && file_exists(public_path($user->avatar)) && $user->avatar !== 'images/avatar/default-avatar.jpg') {
                unlink(public_path($user->avatar));
            }

            // Gán về null trong DB
            $user->avatar = null;
            $user->save();

            return back()->with('success', 'Ảnh đại diện đã được xóa và đặt về mặc định!');
        }

        // Nếu người dùng tải lên ảnh mới
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $file = $request->file('avatar');
        $fileName = time() . '-' . $file->getClientOriginalName();
        $file->move(public_path('images/avatar'), $fileName);

        // Nếu có avatar cũ, xóa file cũ (nếu không phải ảnh mặc định)
        if ($user->avatar && file_exists(public_path($user->avatar)) && $user->avatar !== 'images/avatar/default-avatar.jpg') {
            unlink(public_path($user->avatar));
        }

        $user->avatar = 'images/avatar/' . $fileName;
        $user->save();

        return back()->with('success', 'Ảnh đại diện đã được cập nhật!');
    }

    // Đăng xuất
    public function logout()
    {
        Auth::guard('client')->logout();
        return redirect('/');
    }
}
