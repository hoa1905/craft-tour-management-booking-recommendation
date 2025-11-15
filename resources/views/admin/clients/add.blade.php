@extends('layouts.admin')

@section('title', 'Thêm khách hàng mới')

@section('head')
<link rel="stylesheet" href="{{ asset('css/admin/clients/add.css') }}">
@endsection

@section('content')
<div class="container-fluid px-4 py-5">
  <div class="row">
    <!-- Card form bên trái -->
    <div class="col-lg-8">
      <div class="card shadow border-0 rounded-lg mb-4">
        <div class="card-header bg-primary text-white py-2">
          <h5 class="mb-0">
            <i class="bi bi-person-plus me-2"></i>Thêm khách hàng mới
          </h5>
        </div>
        <div class="card-body p-3">
          @if(session('status'))
          <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <i class="bi bi-check-circle me-1"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          <form action="{{ route('admin.client.addAccount') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-6">
              <label for="name" class="form-label">
                <i class="bi bi-person me-1 text-primary"></i>Tên khách hàng <span class="text-danger">*</span>
              </label>
              <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name') }}" placeholder="Nhập tên đầy đủ">
              @error('name')
              <div class="invalid-feedback small">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="email" class="form-label">
                <i class="bi bi-envelope me-1 text-primary"></i>Email <span class="text-danger">*</span>
              </label>
              <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder="example@gmail.com">
              @error('email')
              <div class="invalid-feedback small">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="address" class="form-label">
                <i class="bi bi-geo-alt me-1 text-primary"></i>Địa chỉ
              </label>
              <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror"
                value="{{ old('address') }}">
              @error('address')
              <div class="invalid-feedback small">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="phone" class="form-label">
                <i class="bi bi-phone me-1 text-primary"></i>Số điện thoại <span class="text-danger">*</span>
              </label>
              <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
                value="{{ old('phone') }}">
              @error('phone')
              <div class="invalid-feedback small">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="password" class="form-label">
                <i class="bi bi-shield-lock me-1 text-primary"></i>Mật khẩu <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <input type="password" name="password" id="password"
                  class="form-control @error('password') is-invalid @enderror"
                  placeholder="Nhập mật khẩu an toàn">
                <button class="btn btn-outline-secondary toggle-password"
                  type="button" onclick="togglePassword('password')">
                  <i class="bi bi-eye-fill"></i>
                </button>
                @error('password')
                <div class="invalid-feedback small">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="col-md-6">
              <label for="password_confirmation" class="form-label">
                <i class="bi bi-shield-check me-1 text-primary"></i>Xác nhận mật khẩu <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <input type="password" name="password_confirmation" id="password_confirmation"
                  class="form-control @error('password_confirmation') is-invalid @enderror"
                  placeholder="Nhập lại mật khẩu">
                <button class="btn btn-outline-secondary toggle-password"
                  type="button" onclick="togglePassword('password_confirmation')">
                  <i class="bi bi-eye-fill"></i>
                </button>
                @error('password_confirmation')
                <div class="invalid-feedback small">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="col-12 mt-3">
              <div class="d-flex justify-content-between">
                <a href="{{ route('admin.client.index') }}" class="btn btn-sm btn-outline-secondary">
                  <i class="bi bi-arrow-left me-1"></i>Quay lại
                </a>
                <button type="submit" class="btn btn-primary px-4">
                  <i class="bi bi-person-plus-fill me-1"></i>Thêm khách hàng
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Thông tin hướng dẫn bên phải -->
    <div class="col-lg-4">
      <div class="card shadow border-0 rounded-lg bg-light mb-4">
        <div class="card-header bg-info text-white py-2">
          <h5 class="mb-0">
            <i class="bi bi-info-circle me-2"></i>Hướng dẫn
          </h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <h6 class="text-primary"><i class="bi bi-person-badge me-1"></i>Thông tin khách hàng</h6>
            <p class="small">Nhập đầy đủ thông tin khách hàng. Đảm bảo email chưa đăng ký bởi một người dùng khác.</p>
          </div>

          <div class="mb-3">
            <h6 class="text-primary"><i class="bi bi-shield-lock me-1"></i>Yêu cầu mật khẩu</h6>
            <ul class="list-group list-group-flush small">
              <li class="list-group-item bg-transparent py-1">
                <i class="bi bi-check-circle-fill text-success me-1"></i>Ít nhất 8 ký tự
              </li>
              <li class="list-group-item bg-transparent py-1">
                <i class="bi bi-check-circle-fill text-success me-1"></i>Ít nhất 1 số
              </li>
              <li class="list-group-item bg-transparent py-1">
                <i class="bi bi-check-circle-fill text-success me-1"></i>Bao gồm chữ hoa và chữ thường
              </li>
              <li class="list-group-item bg-transparent py-1">
                <i class="bi bi-check-circle-fill text-success me-1"></i>Ít nhất 1 ký tự đặc biệt (ví dụ: !, @, #, $, %)
              </li>
            </ul>
          </div>

          <div class="alert alert-warning py-2 small">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <strong>Lưu ý:</strong> Thông báo cho khách hàng mới, hãy đổi mật khẩu khi đăng nhập lần đầu.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin/clients/add.js') }}"></script>
@endsection