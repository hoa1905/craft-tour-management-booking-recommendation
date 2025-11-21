@extends('layouts.admin')

@section('title', 'Thêm làng nghề truyền thống')

@section('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
{{-- Link CSS riêng --}}
<link rel="stylesheet" href="{{ asset('css/admin/villages/add.css') }}">
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
  {{-- Action trỏ về route xử lý --}}
  <form action="{{ route('admin.village.addVillage') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="d-flex justify-content-between align-items-center bg-white p-md-3 rounded-3 shadow-sm mb-4">
      <div>
        <h3><span class="text-primary">Thêm làng nghề truyền thống</span></h3>
      </div>
      <div>
        <button type="submit" class="btn btn-success btn-lg px-4 d-flex align-items-center justify-content-center">
          <i class="bi bi-plus-circle-fill me-2"></i>
          <span style="font-size: 1.1rem; font-weight: 600;">Lưu làng nghề</span>
        </button>
      </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger">
      <strong>Vui lòng kiểm tra lại dữ liệu:</strong>
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <div class="row">
      {{-- Cột trái: Thông tin --}}
      <div class="col-lg-12">
        {{-- Card 1: Thông tin cơ bản --}}
        <div class="card shadow-sm mb-3">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle-fill me-2"></i> Thông tin chung</h5>
          </div>
          <div class="card-body p-4">
            {{-- Tên làng nghề --}}
            <div class="row mb-3">
              <div class="col-md-12">
                <label for="village_name" class="form-label">Tên làng nghề <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('village_name') is-invalid @enderror"
                  name="village_name" value="{{ old('village_name') }}" placeholder="VD: Làng gốm Bát Tràng">
                @error('village_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            {{-- Tỉnh & Miền --}}
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="province_id" class="form-label">Thuộc tỉnh <span class="text-danger">*</span></label>
                <select name="province_id" id="province_id" class="form-control select2-basic @error('province_id') is-invalid @enderror">
                  <option value="">-- Chọn tỉnh --</option>
                  @foreach($provinces as $p)
                  {{-- Thêm data-region để JS đọc --}}
                  <option value="{{ $p->province_id}}"
                    data-region="{{ $p->region->region_name ?? '' }}"
                    {{ old('province_id') == $p->province_id ? 'selected' : '' }}>
                    {{ $p->province_name }}
                  </option>
                  @endforeach
                </select>
                @error('province_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label for="village-regions-display" class="form-label">Thuộc miền</label>
                <input type="text" id="village-regions-display" class="form-control" disabled placeholder="(Tự động cập nhật)">
              </div>
            </div>

            {{-- Địa chỉ cụ thể --}}
            <div class="row mb-3">
              <div class="col-md-12">
                <label for="address" class="form-label">Địa điểm cụ thể <span class="text-danger">*</span></label>
                {{-- SỬA LỖI: Đổi name="longitude" thành name="address" --}}
                <input type="text" class="form-control @error('address') is-invalid @enderror" name="address"
                  value="{{ old('address') }}" placeholder="VD: đường, xã">
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            {{-- Vĩ độ & Kinh độ --}}
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="latitude" class="form-label">Vĩ độ (Giá trị từ -90 đến 90) <span class="text-danger">*</span></label>
                <input type="number"
                  step="0.00000001"
                  min="-90"
                  max="90"
                  class="form-control @error('latitude') is-invalid @enderror"
                  id="latitude"
                  name="latitude"
                  value="{{ old('latitude') }}"
                  placeholder="VD: 20.9772"
                  required>
                {{-- Thông báo lỗi từ Server (Laravel) --}}
                @error('latitude')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label for="longitude" class="form-label">Kinh độ (Giá trị từ -180 đến 180) <span class="text-danger">*</span></label>
                <input type="number"
                  step="0.00000001"
                  min="-180"
                  max="180"
                  class="form-control @error('longitude') is-invalid @enderror"
                  id="longitude"
                  name="longitude"
                  value="{{ old('longitude') }}"
                  placeholder="VD: 105.9172"
                  required>
                @error('longitude')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
        </div>

        {{-- Card 2: Hình ảnh --}}
        <div class="card shadow-sm mb-3">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary"> <i class="fas fa-images me-2"></i> Hình ảnh</h5>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">

              {{-- Ảnh đại diện --}}
              <div class="col-md-6">
                <div class="card bg-light border-0 shadow-sm h-100">
                  <div class="card-body d-flex flex-column">
                    <label for="image_main" class="form-label fw-semibold mb-2">Ảnh đại diện làng nghề (1 ảnh) <span class="text-danger">*</span></label>

                    <div class="upload-zone p-3 border-2 border-dashed rounded-3 position-relative d-flex flex-column justify-content-center align-items-center bg-white flex-grow-1" style="min-height: 160px;">
                      <div class="text-center upload-instruction">
                        <i class="bi bi-cloud-arrow-up-fill fa-3x text-secondary mb-2 fs-1"></i>
                        <p class="mb-0 text-muted small">Chọn ảnh chính</p>
                      </div>

                      {{-- SỬA: Đã xóa onchange --}}
                      <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer"
                        id="image_main" name="image_main" accept="image/*">

                      <div id="image-preview" class="d-flex flex-wrap w-100 justify-content-center mt-2"></div>
                    </div>
                    @error('image_main') <div class="text-danger mt-2 small">{{ $message }}</div> @enderror
                  </div>
                </div>
              </div>

              {{-- Thư viện ảnh --}}
              <div class="col-md-6">
                <div class="card bg-light border-0 shadow-sm h-100">
                  <div class="card-body d-flex flex-column">
                    <label for="images" class="form-label fw-semibold mb-2">Thư viện ảnh (Nhiều ảnh)</label>

                    <div class="upload-zone p-3 border-2 border-dashed rounded-3 position-relative d-flex flex-column justify-content-center align-items-center bg-white flex-grow-1" style="min-height: 160px;">
                      <div class="text-center upload-instruction">
                        <i class="bi bi-images fa-3x text-secondary mb-2 fs-1"></i>
                        <p class="mb-0 text-muted small">Chọn hoặc kéo thả nhiều ảnh</p>
                      </div>

                      {{-- SỬA: Đã xóa onchange --}}
                      <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer"
                        id="images" name="images[]" multiple accept="image/*">

                      <div id="description-image-preview" class="d-flex flex-wrap w-100 justify-content-center mt-2 gap-2"></div>
                    </div>
                    @error('images') <div class="text-danger mt-2 small">{{ $message }}</div> @enderror
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        {{-- Card 3: Mô tả --}}
        <div class="card shadow-sm mb-3">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-file-text-fill me-2"></i> Mô tả làng nghề truyền thống <span class="text-danger">*</span></h5>
          </div>
          <div class="card-body p-4">
            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>
            @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>
    </div>

    <button type="submit" class="btn btn-success w-50 p-3 d-flex align-items-center justify-content-center mx-auto">
      <i class="bi bi-plus-circle-fill me-2"></i>
      <span style="font-size: 1.3rem;">Lưu làng nghề</span>
    </button>
  </form>
</div>
@endsection

@section('scripts')
<script src="//cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/admin/villages/add.js') }}"></script>
@endsection