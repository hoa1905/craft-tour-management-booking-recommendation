@extends('layouts.admin')

@section('title', 'Cập nhật thông tin khách hàng')

@section('head')
<link rel="stylesheet" href="{{ asset('css/admin/clients/edit.css') }}">
@endsection

@section('content')
<div class="container-fluid py-4 px-4">
  <div class="col-12 mb-4">
    <h4 class="fw-bold text-primary border-bottom pb-3">
      <i class="bi bi-person-plus-fill me-2"></i>Cập nhật thông tin khách hàng
    </h4>
  </div>

  <div class="row">
    <div class="col-lg-12 col-md-9 mx-auto">
      <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="card-header d-flex align-items-center py-2 px-3" style="background: linear-gradient(to right, #f8f9fa, #e9ecef);">
          <div class="d-flex align-items-center">
            <div class="avatar-circle me-2 bg-primary text-white">
              <span class="small fw-medium">{{ substr($client->user_name, 0, 1) }}</span>
            </div>
            <div>
              <h6 class="mb-0 fw-semibold">{{ $client->user_name }}</h6>
              <span class="text-muted x-small">{{ $client->email }}</span>
            </div>
          </div>
        </div>

        <div class="card-body p-3">
          @if(session('status'))
          <div class="alert alert-success alert-dismissible fade show py-2 d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle me-1 small"></i>
            <div class="small">{{ session('status') }}</div>
            <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          <form action="{{ route('admin.client.update', ['id' => $client->user_id]) }}" method="POST" class="row g-2 mt-1">
            @csrf
            @method('PUT')

            <div class="col-md-6 mb-2">
              <label for="name" class="form-label small text-secondary mb-1">
                <i class="bi bi-person-fill text-primary me-1"></i>Tên khách hàng <span class="text-danger">*</span>
              </label>
              <input type="text" name="name" id="name"
                class="form-control form-control-sm bg-light @error('name') is-invalid @enderror"
                value="{{ old('name', $client->user_name) }}">
              @error('name')
              <div class="invalid-feedback x-small">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-2">
              <label for="email" class="form-label small text-secondary mb-1">
                <i class="bi bi-envelope-fill text-primary me-1"></i>Email <span class="text-danger">*</span>
              </label>
              <input type="email" name="email" id="email"
                class="form-control form-control-sm bg-light @error('email') is-invalid @enderror"
                value="{{ old('email', $client->email) }}" placeholder="example@gmail.com" required>
              @error('email')
              <div class="invalid-feedback x-small">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-2">
              <label for="address" class="form-label small text-secondary mb-1">
                <i class="bi bi-geo-alt-fill text-primary me-1"></i>Địa chỉ
              </label>
              <input type="text" name="address" id="address"
                class="form-control form-control-sm bg-light @error('address') is-invalid @enderror"
                value="{{ old('address', $client->address) }}">
              @error('address')
              <div class="invalid-feedback x-small">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-2">
              <label for="phone" class="form-label small text-secondary mb-1">
                <i class="bi bi-phone-fill text-primary me-1"></i>Số điện thoại <span class="text-danger">*</span>
              </label>
              <input type="text" name="phone" id="phone"
                class="form-control form-control-sm bg-light @error('phone') is-invalid @enderror"
                value="{{ old('phone', $client->phone_number) }}" pattern="[0-9]{10}">
              @error('phone')
              <div class=" invalid-feedback x-small">{{ $message }}
              </div>
              @enderror
            </div>

            <div class="col-12 mt-3">
              <div class="d-flex gap-2 justify-content-between align-items-center">
                <a href="{{ route('admin.client.index') }}" class="btn btn-sm btn-outline-secondary">
                  <i class="bi bi-arrow-left me-1"></i>Quay lại
                </a>
                <button type="submit" class="btn btn-sm btn-primary px-3">
                  <i class="bi bi-save me-1"></i>Lưu thay đổi
                </button>
              </div>
            </div>
          </form>
        </div>

        <div class="card-footer bg-light p-2">
          <div class="d-flex align-items-center">
            <i class="bi bi-clock-history text-secondary me-1 small"></i>
            <span class="x-small text-muted">Cập nhật lần cuối: {{ $client->updated_at->format('d/m/Y H:i') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin/clients/edit.js') }}"></script>
@endsection