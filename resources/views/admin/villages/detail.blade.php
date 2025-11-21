@extends('layouts.admin')

@section('title', 'Chi tiết làng nghề truyền thống')

@section('head')
<link rel="stylesheet" href="{{ asset('css/admin/villages/detail.css') }}">
{{-- Lightbox CSS (Optional: Nếu bạn muốn click vào ảnh để phóng to xịn hơn) --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

  {{-- Thông báo --}}
  @if(session('status'))
  <div class="alert custom-alert alert-success alert-dismissible fade show border-0 shadow" role="alert" id="status-alert">
    <div class="d-flex align-items-center">
      <div class="alert-icon-container me-3">
        <i class="fas fa-check-circle"></i>
      </div>
      <div>
        <p class="mb-0">{{ session('status') }}</p>
      </div>
    </div>
    <div class="progress mt-2" style="height: 3px;">
      <div id="alert-progress-bar" class="progress-bar bg-success" style="width: 100%;"></div>
    </div>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-center bg-white p-md-3 rounded-3 shadow-sm mb-4">
    <div>
      <h3><span class="text-primary">Chi tiết: </span>{{ $village->village_name }}</h3>
      <p class="text-muted mb-0">Mã làng nghề: {{ $village->village_id }}</p>
    </div>
    <div>
      <a href="{{ route('admin.village.showEditForm', ['id' => $village->village_id]) }}" class="btn btn-primary">
        <i class="fas fa-edit me-2"></i>Chỉnh sửa
      </a>
    </div>
  </div>

  <div class="row">
    {{-- CỘT TRÁI: NỘI DUNG CHÍNH --}}
    <div class="col-lg-8">
      <div class="card shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
          <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle-fill me-2"></i>Thông tin quản lý</h5>
        </div>
        <div class="card-body p-4">
          {{-- Sử dụng List Group để căn chỉnh đẹp hơn --}}
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex border-0 px-0">
              <span class="detail-icon me-3"><i class="bi bi-calendar-plus"></i></span>
              <strong>Ngày tạo:</strong>
              <span class="ms-auto">{{ $village->created_at }}</span>
            </li>
            <li class="list-group-item d-flex border-0 px-0">
              <span class="detail-icon me-3"><i class="bi bi-clock-history"></i></span>
              <strong>Cập nhật lần cuối:</strong>
              <span class="ms-auto">{{ $village->updated_at }}</span>
            </li>
            <li class="list-group-item d-flex border-0 px-0">
              <span class="detail-icon me-3"><i class="bi bi-toggle-on"></i></span>
              <strong>Trạng thái:</strong>
              <span class="ms-auto">
                @if($village->status == 'active')
                <span class="badge bg-success bg-opacity-10 text-success border border-success badge-custom">
                  <i class="bi bi-check-circle me-1"></i> Đang hoạt động
                </span>
                @else
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger badge-custom">
                  <i class="bi bi-x-circle me-1"></i> Ngừng hoạt động
                </span>
                @endif
              </span>
            </li>
          </ul>
        </div>
      </div>

      {{-- Card: Thư viện ảnh --}}
      <div class="card shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
          <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-images me-2"></i>Thư viện ảnh</h5>
        </div>
        <div class="card-body p-4">
          @if(!empty($village->gallery) && count($village->gallery) > 0)
          <div class="gallery-grid">
            @foreach($village->gallery as $img)
            <a href="{{ asset('images/villages/' . $img) }}" data-lightbox="village-gallery" data-title="Thư viện ảnh">
              <div class="gallery-item">
                <img src="{{ asset('images/villages/' . $img) }}" alt="Gallery Image" loading="lazy">
              </div>
            </a>
            @endforeach
          </div>
          @else
          <div class="text-center py-4 text-muted">
            <p>Chưa có ảnh nào trong thư viện.</p>
          </div>
          @endif
        </div>
      </div>

      {{-- Card: Mô tả --}}
      <div class="card shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
          <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-file-text me-2"></i>Mô tả làng nghề truyền thống</h5>
        </div>
        <div class="card-body p-4">
          {{-- Dùng {!! !!} để hiển thị HTML từ CKEditor --}}
          <div class="content-body">
            {!! $village->description ?? '<p class="text-muted fst-italic">Chưa có nội dung chi tiết.</p>' !!}
          </div>
        </div>
      </div>

    </div>

    {{-- Cột phải --}}
    <div class="col-lg-4">

      {{-- Card: Ảnh đại diện --}}
      <div class="card shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
          <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle me-2"></i>Ảnh đại diện làng nghề</h5>
        </div>
        <div class="card-body p-4 text-center">
          <div class="main-image-container mb-3">
            @if($village->image_main)
            <img src="{{ asset('images/villages/' . $village->image_main) }}" alt="Ảnh đại diện" class="main-image">
            @else
            <div class="bg-light py-5 rounded text-muted">
              <i class="bi bi-image fs-1"></i><br>Không có ảnh đại diện
            </div>
            @endif
          </div>
        </div>
      </div>

      {{-- Card: Thông tin hành chính --}}
      <div class="card shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
          <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle me-2"></i>Thông tin hành chính</h5>
        </div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            <li class="list-group-item p-3 d-flex justify-content-between">
              <span class="text-muted">Tỉnh/Thành phố:</span>
              <span class="fw-bold">{{ $village->province->province_name }}</span>
            </li>
            <li class="list-group-item p-3 d-flex justify-content-between">
              <span class="text-muted">Miền:</span>
              <span class="fw-bold">{{ $village->province->region->region_name }}</span>
            </li>
            <li class="list-group-item p-3">
              <span class="text-muted d-block mb-1">Địa chỉ cụ thể:</span>
              <span class="fw-bold text-dark"><i class="bi bi-geo-alt text-danger"></i> {{ $village->address }}</span>
            </li>
            <li class="list-group-item p-3">
              <span class="text-muted d-block mb-1">Tọa độ:</span>
              <div class="d-flex justify-content-between align-items-center">
                <small class="font-monospace bg-light px-2 py-1 rounded">
                  {{ $village->latitude }}, {{ $village->longitude }}
                </small>
                <a href="https://www.google.com/maps/search/?api=1&query={{ $village->latitude }},{{ $village->longitude }}"
                  target="_blank" class="map-link text-primary">
                  Xem bản đồ <i class="bi bi-box-arrow-up-right ms-1"></i>
                </a>
              </div>
            </li>
            <li>
              <div class="mt-2 rounded overflow-hidden shadow-sm border">
                <iframe
                  width="100%"
                  height="250"
                  frameborder="0"
                  style="border:0"
                  scrolling="no"
                  marginheight="0"
                  marginwidth="0"
                  src="https://maps.google.com/maps?q={{ $village->latitude }},{{ $village->longitude }}&hl=vi&z=14&output=embed">
                </iframe>
              </div>
            </li>
          </ul>
        </div>
      </div>

      {{-- Card: Các Tour liên quan --}}
      <div class="card shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
          <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-map me-2"></i>Tour đi qua đây</h5>
        </div>
        <div class="card-body p-0">
          @if($village->tours && $village->tours->count() > 0)
          <ul class="list-group list-group-flush">
            @foreach($village->tours as $tour)
            <li class="list-group-item p-3">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                  @if($tour->image_main)
                  <img src="{{ asset('images/tour/' . $tour->image_main) }}"
                    class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                  @else
                  <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-image text-muted"></i>
                  </div>
                  @endif
                </div>
                <div class="flex-grow-1">
                  <a href="{{ route('admin.tour.detail', ['id' => $tour->tour_id]) }}" class="text-decoration-none text-dark fw-bold hover-primary">
                    {{ $tour->tour_name }}
                  </a>
                  <div class="small text-muted">Mã tour: {{ $tour->tour_id }}</div>
                </div>
              </div>
            </li>
            @endforeach
          </ul>
          @else
          <div class="p-4 text-center text-muted">
            <small>Chưa có tour nào liên kết với làng nghề này.</small>
          </div>
          @endif
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@section('scripts')
{{-- Lightbox JS (Optional) - Giúp xem ảnh đẹp hơn --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
@endsection