<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminAccountController extends Controller
{
  public function index()
  {
    $user = Auth::guard('admin')->user();
    return view('admin.account_info', compact('user'));
  }

  public function updateAccountInfo(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|max:255',

      // Validation cho SĐT:
      // 'nullable' -> Cho phép để trống
      // 'numeric' -> Phải là số
      // 'digits:10' -> Phải có đúng 10 chữ số (nếu không trống)
      'phone' => 'nullable|numeric|digits:10',

      'address' => 'nullable|string|max:1000',
    ], [
      'phone.digits' => 'Số điện thoại phải có đúng 10 chữ số.',
      'phone.numeric' => 'Số điện thoại chỉ được chứa số.',
    ]);
    try {
      /** @var \App\Models\User $user */
      $user = Auth::guard('admin')->user();

      $user->user_name = $request->input('name');
      $user->email = $request->input('email');
      $user->phone_number = $request->input('phone');
      $user->address = $request->input('address');

      $user->save();
      return redirect()->route('admin.profile.index')->with('status', 'Cập nhật thành công!');
    } catch (Exception $e) {
      return redirect()->back()->with('error', 'Có lỗi xảy ra!');
    }
  }

  public function updateAvatar(Request $request)
  {
    try {
      /** @var \App\Models\User $user */
      $user = Auth::guard('admin')->user();

      if ($request->hasFile('image')) {
        $request->validate([
          'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);
        if ($user->avatar) {
          $imagePath = public_path('images/admin/avatar/' . $user->avatar);
          if (file_exists($imagePath)) {
            unlink($imagePath);
          }
        }
        $file = $request->file('image');
        $fileName = $user->user_id . '-' . $user->user_name . '-' . $file->getClientOriginalName();
        $file->move(public_path('images/admin/avatar/'), $fileName);
        $user->avatar = $fileName;
        $user->save();
      }
      return redirect()->route('admin.profile.index')->with('status', 'Cập nhật ảnh đại diện thành công!');
    } catch (Exception $e) {
      Log::debug($e->getMessage());
      return redirect()->back()->with('error', 'Đã xảy ra lỗi khi cập nhật ảnh đại diện!');
    }
  }
  public function deleteAvatar()
  {
    try {
      /** @var \App\Models\User $user */
      $user = Auth::guard('admin')->user();

      if (!empty($user->avatar)) {
        $imagePath = public_path('images/admin/avatar/' . $user->avatar);
        unlink($imagePath);

        $user->avatar = null;
        $user->save();
      }
      return redirect()->route('admin.profile.index')->with('status', 'Xóa ảnh đại diện thành công!');
    } catch (Exception $e) {
      Log::debug($e->getMessage());
      return redirect()->back()->with('error', 'Đã xảy ra lỗi khi xóa ảnh đại diện!');
    }
  }
  public function updatePassword(Request $request)
  {
    try {
      /** @var \App\Models\User $user */
      $user = Auth::guard('admin')->user();

      if (!Hash::check($request->current_password, $user->password)) {
        return redirect()
          ->to(route('admin.profile.index') . '#password-tab-pane')
          ->with('error', 'Mật khẩu hiện tại không đúng!');
      }

      $user->password = Hash::make($request->password);
      $user->save();

      Auth::guard('admin')->logout();

      return redirect()->route("admin.login");
    } catch (Exception $e) {
      Log::debug($e->getMessage());
      return redirect()
        ->to(route('admin.profile.index') . '#password-tab-pane')
        ->with('error', 'Đã xảy ra lỗi trong quá trình đổi mật khẩu!');
    }
  }

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
        return redirect()->intended(route('admin.dashboard'))->with("status", "Đăng nhập thành công");
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
