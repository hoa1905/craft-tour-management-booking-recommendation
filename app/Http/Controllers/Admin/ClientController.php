<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ClientController extends Controller
{
    public function index()
    {
        $clients = User::where('role', 'client')->paginate(10);
        return view('admin.clients.list', compact('clients'));
    }

    public function showEditForm($id)
    {
        try {
            $client = User::findOrFail($id);
            return view('admin.clients.edit', compact('client'));
        } catch (Exception $e) {
            Log::debug($e->getMessage());
        }
    }
    public function updateAccountInfo(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // Yêu cầu 'ignore' (bỏ qua) $id của user hiện tại
            'email' => [
                'required',
                'email',
                // unique: bảng 'users', cột 'email'
                // ignore: $id (là user_id)
                // user_id: tên cột khóa chính để so sánh (dựa trên các lượt trước)
                Rule::unique('users', 'email')->ignore($id, 'user_id'),
            ],

            // Validation cho SĐT:
            // 'nullable' -> Cho phép để trống
            // 'numeric' -> Phải là số
            // 'digits:10' -> Phải có đúng 10 chữ số (nếu không trống)
            'phone' => 'required|numeric|digits:10',
            'address' => 'nullable|string|max:1000',
        ], [
            // Thông báo lỗi tùy chỉnh
            'name.required' => 'Vui lòng nhập họ tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.digits' => 'Số điện thoại phải có đúng 10 chữ số.',
            'phone.numeric' => 'Số điện thoại chỉ được chứa số.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.unique' => 'Email này đã được đăng ký bởi một người dùng khác.',
        ]);

        try {
            $client = User::findOrFail($id);
            $client->user_name = $request->input('name');
            $client->email = $request->input('email');
            $client->address = $request->input('address');
            $client->phone_number = $request->input('phone');
            $client->save();
            return redirect()->route('admin.client.index')->with('status', 'Cập nhật thông tin khách hàng thành công!');
        } catch (Exception $e) {
            Log::debug($e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra, không thể cập nhật!');
        }
    }

    public function showAddAccountForm()
    {
        return view('admin.clients.add');
    }

    public function addAccountForm(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|numeric|digits:10',
            'password' => [
                'required',               // Bắt buộc
                'max:255',                // Tối đa 255 ký tự
                'same:password_confirmation', // Phải khớp với 'password_confirmation'

                // Bắt đầu các quy tắc phức tạp:
                Password::min(8)          // 1. Ít nhất 8 ký tự
                    ->mixedCase()         // 2. Bao gồm chữ hoa và chữ thường
                    ->symbols()           // 3. Ít nhất 1 ký tự đặc biệt (ví dụ: !, @, #, $, %)
                    ->numbers()           // 4. Ít nhất 1 số
            ],
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email này đã được đăng ký.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.digits' => 'Số điện thoại phải có đúng 10 chữ số',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Vui lòng nhập mật khẩu ít nhất 8 ký tự.',
            'password_confirmation.required' => 'Vui lòng nhập mật khẩu xác nhận.',
            'password.same' => 'Mật khẩu xác nhận không trùng khớp.',
        ]);
        try {
            $client = new User();

            $client->user_name = $request->input('name');
            $client->email = $request->input('email');
            $client->address = $request->input('address');
            $client->phone_number = $request->input('phone');
            $client->password = Hash::make($request->input('password'));
            $client->role = 'client';
            $client->status = 'approved';

            $client->save();
            return redirect()->route('admin.client.index')->with('status', 'Thêm khách hàng thành công!');
        } catch (\Throwable $th) {
            Log::error('Lỗi thêm tài khoản khách hàng: ' . $th->getMessage());
            Log::error('File: ' . $th->getFile() . ' | Dòng: ' . $th->getLine());
            return redirect()->back()->withErrors(['error' => 'Có lỗi xảy ra, vui lòng thử lại.']);
        }
    }

    public function toggleLock($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->status = $user->status === 'approved' ? 'locked' : 'approved';
            $user->save();
            return redirect()->route('admin.client.index')->with('status', 'Cập nhật trang thái tài khoản thành công!');
        } catch (Exception $e) {
            Log::debug($e->getMessage());
        }
    }

    public function deleteAccount($id)
    {
        $client = User::findOrFail($id);
        $client->delete();
        return redirect()->back()->with('status', 'Đã xóa tài khoản thành công!');
    }
}
