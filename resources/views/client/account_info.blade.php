@extends('client.master')

@section('title', 'Thông tin tài khoản')

@section('head')
<link rel="stylesheet" href="{{ asset('css/client/account-info.css') }}">
@endsection

@section('content')
<div id="vnt-content">
  <div class="vhmemberpg">
    <div class="mbpadd">
      <div class="wrapper">
        <div class="hpmembermm">
          <div class="vtitlemb">
            <h1>Thông tin tài khoản</h1>
          </div>
          <div class="vcontsmb">

            <div class="tpmembermm vborder vpaddall">
              <div class="mmwrap">
                <div class="mmgrip">
                  {{-- =================== FORM 1: CẬP NHẬT AVATAR =================== --}}
                  <div class="mcol">
                    <div class="mmavar">
                      <div class="thumb" id="id-avatar">
                        <a href="{{ $user->avatar ? asset($user->avatar) : asset('images/avatar/default-avatar.jpg') }}"
                          target="_blank" title="Xem ảnh đại diện">
                          <img src="{{ $user->avatar ? asset('images/avatar/' . $user->avatar) : asset('images/avatar/default-avatar.jpg') }}"
                            alt="Avatar" class="user-avatar" />
                        </a>
                      </div>

                      <div class="decss mt-2" style="display: flex; flex-direction: column; align-items: center;">
                        {{-- Form upload ảnh --}}
                        <form id="avatarForm" method="POST" enctype="multipart/form-data"
                          action="{{ route('account.updateAvatar') }}" style="margin-bottom: 8px;">
                          @csrf
                          <label for="file_avatar" class="btn btn-primary btn-sm mb-2">Chọn ảnh</label>
                          <input type="file" name="avatar" id="file_avatar" accept="image/*"
                            onchange="document.getElementById('avatarForm').submit()" style="display: none;">
                        </form>

                        {{-- Form xóa ảnh (chỉ hiện nếu có ảnh avatar thật sự) --}}
                        @if($user->avatar)
                        <form method="POST" action="{{ route('account.updateAvatar') }}"
                          onsubmit="return confirm('Bạn có chắc muốn xóa ảnh đại diện không?')">
                          @csrf
                          <input type="hidden" name="remove_avatar" value="1">
                          <button type="submit" class="btn btn-outline-danger btn-sm">Xóa ảnh đại diện</button>
                        </form>
                        @endif
                      </div>
                    </div>
                  </div>

                  {{-- =================== FORM 2: CẬP NHẬT THÔNG TIN =================== --}}
                  <div class="hcol">
                    <div class="mmform">
                      <div class="vformall">
                        <div class="xlabel"><label>Email đăng nhập</label></div>
                        <div class="xinput div_input">
                          <input type="text" value="{{ $user->email }}" disabled>
                        </div>
                      </div>
                      <div class="vformall">
                        <div class="xlabel"><label>Ngày đăng ký</label></div>
                        <div class="xinput div_input">
                          <input type="text" value="{{ $user->created_at->format('H:i, d/m/Y') }}" disabled>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="bcol">
                    <form role="form" method="POST" enctype="multipart/form-data"
                      class="form validate" id="infoForm" name="infoForm"
                      action="{{ route('account.updateInfo') }}">
                      @csrf
                      <div class="mmform">
                        <div class="vformall">
                          <div class="xlabel"><label>Họ tên của bạn <span>*</span></label></div>
                          <div class="xinput div_input">
                            <input type="text" class="form-control required" name="user_name"
                              value="{{ old('user_name', $user->user_name) }}" required>
                          </div>
                        </div>
                        <div class="vformall">
                          <div class="xlabel"><label>Số điện thoại <span>*</span></label></div>
                          <div class="xinput div_input">
                            <input type="text" class="form-control required" name="phone_number"
                              value="{{ old('phone_number', $user->phone_number) }}" maxlength="11" required>
                          </div>
                        </div>
                        <div class="vformall">
                          <div class="xlabel"><label>Địa chỉ</label></div>
                          <div class="xinput div_input">
                            <input type="text" class="form-control required" name="address"
                              value="{{ old('address', $user->address) }}">
                          </div>
                        </div>
                        <div class="vformall">
                          <div class="xbuton">
                            <button type="submit" id="btn-changeinfo"><span>Cập nhật</span></button>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
{{-- Hiển thị toastr thông báo --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const flashSuccess = "{{ session('success') ?? '' }}";
    const flashError = "{{ $errors->any() ? $errors->first() : '' }}";

    if (flashSuccess) {
      toastr.success(flashSuccess);
    }
    if (flashError) {
      toastr.error(flashError);
    }
  });
</script>

@endsection