@extends('layouts.admin')

@section('title', 'Chỉnh sửa làng nghề truyền thống')

@section('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<link rel="stylesheet" href="{{ asset('css/admin/villages/edit.css') }}">
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
  {{-- SỬA: Route update --}}
  <form action="{{ route('admin.village.update', ['id' => $village->village_id]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- Tiêu đề và Nút Submit --}}
    <div class="d-flex justify-content-between align-items-center bg-white p-md-3 rounded-3 shadow-sm mb-4">
      <div>
        <h3><span class="text-primary">Chỉnh sửa: </span>{{ $village->village_name }}</h3>
        <p class="text-muted mb-0">Mã tour: {{ $village->village_id }}</p>
      </div>
      <div>
        <button type="submit" class="btn btn-success btn-lg px-4 d-flex align-items-center justify-content-center">
          <i class="bi bi-check-circle-fill me-2"></i>
          <span style="font-size: 1.1rem; font-weight: 600;">Cập nhật</span>
        </button>
      </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger">
      <strong>Vui lòng kiểm tra lại dữ liệu:</strong>
      <ul class="mb-0">
        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
      </ul>
    </div>
    @endif

    <div class="row">
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
                  name="village_name" value="{{ old('village_name', $village->village_name) }}">
                @error('village_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            {{-- Tỉnh & Miền --}}
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="province_id" class="form-label">Thuộc tỉnh <span class="text-danger">*</span></label>
                <select name="province_id" id="province_id" class="form-control select2-basic @error('province_id') is-invalid @enderror">
                  @foreach($provinces as $p)
                  <option value="{{ $p->province_id}}"
                    data-region="{{ $p->region->region_name ?? '' }}"
                    {{ old('province_id', $village->province_id) == $p->province_id ? 'selected' : '' }}>
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

            {{-- Địa chỉ --}}
            <div class="row mb-3">
              <div class="col-md-12">
                <label for="address" class="form-label">Địa điểm cụ thể <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('address') is-invalid @enderror" name="address"
                  value="{{ old('address', $village->address) }}">
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            {{-- Vĩ độ & Kinh độ --}}
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="latitude" class="form-label">Vĩ độ (Latitude) <span class="text-danger">*</span></label>
                <input type="number" step="0.00000001" min="-90" max="90"
                  class="form-control @error('latitude') is-invalid @enderror" name="latitude"
                  value="{{ old('latitude', $village->latitude) }}">
                @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label for="longitude" class="form-label">Kinh độ (Longitude) <span class="text-danger">*</span></label>
                <input type="number" step="0.00000001" min="-180" max="180"
                  class="form-control @error('longitude') is-invalid @enderror" name="longitude"
                  value="{{ old('longitude', $village->longitude) }}">
                @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                    <label for="image_main" class="form-label fw-semibold mb-2">Ảnh đại diện (Thay đổi nếu cần)</label>

                    {{-- Hiển thị ảnh cũ --}}
                    @if($village->image_main)
                    <div class="mb-2 text-center">
                      <img src="{{ asset('images/villages/' . $village->image_main) }}" class="img-thumbnail" style="height: 120px; object-fit: cover;">
                      <div class="text-muted small mt-1">Ảnh hiện tại</div>
                    </div>
                    @endif

                    <div class="upload-zone p-3 border-2 border-dashed rounded-3 position-relative d-flex flex-column justify-content-center align-items-center bg-white flex-grow-1">
                      <div class="text-center upload-instruction">
                        <i class="bi bi-cloud-arrow-up-fill fa-2x text-secondary mb-1"></i>
                        <p class="mb-0 text-muted small">Chọn ảnh mới để thay thế</p>
                      </div>
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
                    <label for="images" class="form-label fw-semibold mb-2">Thư viện ảnh</label>

                    {{-- Input ẩn chứa danh sách ảnh cũ CÒN GIỮ LẠI --}}
                    {{-- Nếu validation fail, dùng old(), nếu không dùng gallery từ DB --}}
                    @php
                    // Chuyển mảng gallery thành chuỗi JSON để JS dễ xử lý hoặc chuỗi cách nhau dấu phẩy
                    // Ở đây ta dùng cách đơn giản: Tạo input hidden cho từng ảnh
                    @endphp

                    {{-- Khu vực hiển thị ảnh cũ --}}
                    <div id="old-gallery-container" class="mb-2 d-flex flex-wrap gap-2 justify-content-center">
                      @if($village->gallery)
                      @foreach($village->gallery as $img)
                      <div class="position-relative old-image-item">
                        <img src="{{ asset('images/villages/' . $img) }}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">

                        {{-- Nút Xóa --}}
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0 d-flex justify-content-center align-items-center rounded-circle remove-old-image-btn"
                          style="width: 20px; height: 20px; transform: translate(30%, -30%);"
                          data-image-name="{{ $img }}">
                          <i class="bi bi-x" style="font-size: 14px;"></i>
                        </button>

                        {{-- Input ẩn cho ảnh này. Nếu div này bị xóa, input này cũng mất -> Controller biết ảnh đã bị xóa --}}
                        <input type="hidden" name="existing_gallery[]" value="{{ $img }}">
                      </div>
                      @endforeach
                      @endif
                    </div>

                    @if($village->gallery)
                    <div class="text-center text-muted small mb-2 fst-italic" id="old-gallery-note">Ảnh hiện tại (Nhấn X để xóa)</div>
                    @endif

                    {{-- Khu vực upload ảnh mới (Giữ nguyên) --}}
                    <div class="upload-zone p-3 border-2 border-dashed rounded-3 position-relative d-flex flex-column justify-content-center align-items-center bg-white flex-grow-1">
                      <div class="text-center upload-instruction">
                        <i class="bi bi-images fa-2x text-secondary mb-1"></i>
                        <p class="mb-0 text-muted small">Chọn thêm nhiều ảnh mới</p>
                      </div>
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
            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-file-text-fill me-2"></i> Mô tả chi tiết <span class="text-danger">*</span></h5>
          </div>
          <div class="card-body p-4">
            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $village->description) }}</textarea>
            @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>
    </div>

    <button type="submit" class="btn btn-success w-50 p-3 d-flex align-items-center justify-content-center mx-auto">
      <i class="bi bi-check-circle-fill me-2"></i>
      <span style="font-size: 1.3rem;">Cập nhật làng nghề</span>
    </button>
  </form>
</div>
@endsection

@section('scripts')
<script src="//cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
{{-- Tái sử dụng JS của add --}}
<script src="{{ asset('js/admin/villages/edit.js') }}"></script>
@endsection