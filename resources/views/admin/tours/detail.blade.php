@extends('layouts.admin')

@section('title', 'Chi tiết tour')

@section('head')
<link rel="stylesheet" href="{{ asset('css/admin/tours/detail.css') }}">
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
      <div id="alert-progress-bar" class="progress-bar bg-white" style="width: 100%;"></div>
    </div>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>

  {{-- Script xóa localStorage khi submit thành công từ edit.blade.php --}}
  <script>
    // Vì trang này đã tải VÀ có thông báo thành công,
    // chúng ta có thể an toàn xóa localStorage 
    localStorage.removeItem('tour_edit_new_schedules');
    localStorage.removeItem('tour_edit_new_itineraries');
  </script>

  {{-- Script xóa localStorage khi submit thành công từ add.blade.php --}}
  <script>
    localStorage.removeItem('tour_add_new_schedules');
    localStorage.removeItem('tour_add_new_itineraries');
  </script>
  @endif

  <div class="d-flex justify-content-between align-items-center bg-white p-md-3 rounded-3 shadow-sm mb-4">
    <div>
      <h3><span class="text-primary">Chi tiết tour: </span>{{ $tour->tour_name }}</h3>
      <p class="text-muted mb-0">Mã tour: {{ $tour->tour_id }}</p>
    </div>
    <div>
      <a href="{{ route('admin.tour.showEditForm', ['id' => $tour->tour_id]) }}" class="btn btn-primary">
        <i class="fas fa-edit me-2"></i>Chỉnh sửa
      </a>
    </div>
  </div>

  <div class="row">
    {{-- Cột bên trái --}}
    <div class="col-lg-7">
      {{-- Card: Thông tin cơ bản --}}
      <div class="card shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
          <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle-fill me-2"></i>Thông tin chung</h5>
        </div>
        <div class="card-body p-4">
          {{-- Sử dụng List Group để căn chỉnh đẹp hơn --}}
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex border-0 px-0">
              <span class="detail-icon me-3"><i class="bi bi-cash-coin"></i></span>
              <strong>Giá người lớn:</strong>
              <span class="text-danger fw-bold ms-auto">{{ number_format($tour->price_adult,0,',','.') }} đ</span>
            </li>
            <li class="list-group-item d-flex border-0 px-0">
              <span class="detail-icon me-3"><i class="bi bi-cash"></i></span>
              <strong>Giá trẻ em:</strong>
              <span class="text-danger fw-bold ms-auto">{{ number_format($tour->price_child,0,',','.') }} đ</span>
            </li>
            <li class="list-group-item d-flex border-0 px-0">
              <span class="detail-icon me-3"><i class="bi bi-geo-alt-fill"></i></span>
              <strong>Điểm khởi hành:</strong>
              <span class="ms-auto">{{ $tour->departure_point }}</span>
            </li>
            <li class="list-group-item d-flex border-0 px-0">
              <span class="detail-icon me-3"><i class="bi bi-pin-map-fill"></i></span>
              <strong>Điểm đến (Tỉnh):</strong>
              <span class="ms-auto">{{ $tour->destination }}</span>
            </li>
            <li class="list-group-item d-flex border-0 px-0">
              <span class="detail-icon me-3"><i class="bi bi-car-front-fill"></i></span>
              <strong>Phương tiện:</strong>
              <span class="ms-auto">{{ $tour->transport }}</span>
            </li>
            <li class="list-group-item d-flex border-0 px-0">
              <span class="detail-icon me-3"><i class="bi bi-clock-fill"></i></span>
              <strong>Thời lượng tour:</strong>
              <span class="ms-auto">{{ $tour->duration }}</span>
            </li>
            <li class="list-group-item d-flex border-0 px-0">
              <span class="detail-icon me-3"><i class="bi bi-toggle-on"></i></span>
              <strong>Trạng thái:</strong>
              <span class="ms-auto">
                @if($tour->status === 'approved')
                <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2">Hoạt động</span>
                @elseif($tour->status === 'inactive')
                <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary px-3 py-2">Ngừng hoạt động</span>
                @elseif($tour->status === 'pending')
                <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-3 py-2">Chờ duyệt</span>
                @else
                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2">Từ chối</span>
                @endif
              </span>
            </li>
          </ul>
        </div>
      </div>

      {{-- Card: Làng nghề và Khu vực --}}
      <div class="card shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
          <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-shop me-2"></i>Khu vực và Làng nghề</h5>
        </div>
        <div class="card-body p-4">
          <h6 class="fw-bold">Miền:</h6>
          @forelse($tour->villages->pluck('province.region.region_name')->unique() as $region)
          <span class="badge rounded-pill bg-warning text-dark fs-6 px-3">{{ $region }}</span>
          @empty
          <span class="text-muted">Chưa xác định</span>
          @endforelse
          <hr>
          <h6 class="fw-bold">Tỉnh:</h6>
          @forelse($tour->villages->pluck('province.province_name')->unique() as $province)
          <span class="badge rounded-pill bg-info text-dark fs-6 px-3">{{ $province }}</span>
          @empty
          <span class="text-muted">Chưa xác định</span>
          @endforelse
          <hr>
          <h6 class="fw-bold">Làng nghề:</h6>
          @forelse($tour->villages as $v)
          <span class="badge rounded-pill fs-6 px-3 mb-1" style="background-color:#f5e6d4; color:#8b5e34;">{{ $v->village_name }}</span>
          @empty
          <span class="text-muted">Chưa có làng nghề nào</span>
          @endforelse
        </div>
      </div>
    </div>

    {{-- Cột phải --}}
    <div class="col-lg-5">
      {{-- Card: Hình ảnh --}}
      <div class="card shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
          <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-image-fill me-2"></i>Hình ảnh tour</h5>
        </div>
        <div class="card-body p-4">
          @if($tour->image_main)
          <img src="{{ asset('images/client/' . $tour->image_main) }}" alt="Ảnh chính" class="rounded shadow-sm mb-3 w-100">
          @else
          <p class="text-muted text-center">Không có ảnh chính</p>
          @endif
          <div class="row">
            @if($tour->image_1)
            <div class="col-6">
              <img src="{{ asset('images/client/' . $tour->image_1) }}" alt="Ảnh 1" class="rounded shadow-sm w-100">
            </div>
            @endif
            @if($tour->image_2)
            <div class="col-6">
              <img src="{{ asset('images/client/' . $tour->image_2) }}" alt="Ảnh 2" class="rounded shadow-sm w-100">
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Card: Lịch khởi hành --}}
  <div class="card shadow border-0 rounded-3 mb-4">
    <div class="card-header bg-white py-3">
      <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-calendar-check-fill me-2"></i>Lịch khởi hành</h5>
    </div>
    <div class="card-body p-4">
      @if($tour->schedules->count() > 0)
      <table class="table table-hover align-middle">
        <thead class="bg-light">
          <tr>
            <th>Ngày bắt đầu</th>
            <th>Ngày kết thúc</th>
            <th class="text-center">Số chỗ trống</th>
          </tr>
        </thead>
        <tbody>
          @foreach($tour->schedules as $schedule)
          <tr>
            <td>{{ $schedule->start_date->format('d/m/Y') }}</td>
            <td>{{ $schedule->end_date->format('d/m/Y') }}</td>
            <td class="text-center fw-bold">{{ $schedule->seats_available }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @else
      <p class="text-muted">Chưa có lịch khởi hành cho tour này.</p>
      @endif
    </div>
  </div>

  {{-- Card: Lịch trình --}}
  <div class="card shadow border-0 rounded-3 mb-4">
    <div class="card-header bg-white py-3">
      <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-list-ol me-2"></i>Lịch trình tour</h5>
    </div>
    <div class="card-body p-4">
      @if($tour->itineraries->count() > 0)
      <div class="accordion" id="accordionItinerary">
        @foreach($tour->itineraries as $itinerary)
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading-{{ $itinerary->itinerary_id }}">
            <button class="accordion-button fw-bold {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse"
              data-bs-target="#collapse-{{ $itinerary->itinerary_id }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
              Ngày {{ $itinerary->day_number }}
            </button>
          </h2>
          <div id="collapse-{{ $itinerary->itinerary_id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}">
            <div class="accordion-body">
              <p><strong><i class="bi bi-person-walking me-2 text-primary"></i>Hoạt động:</strong> {{ $itinerary->activities }}</p>
              <hr>
              <p><strong><i class="bi bi-egg-fried me-2 text-warning"></i>Ăn uống:</strong> {{ $itinerary->meals ?? 'Không có' }}</p>
              <p><strong><i class="bi bi-building me-2 text-info"></i>Chỗ ở:</strong> {{ $itinerary->accommodation ?? 'Không có' }}</p>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      @else
      <p class="text-muted">Chưa có lịch trình chi tiết cho tour này.</p>
      @endif
    </div>
  </div>

  {{-- Card: Mô tả tour --}}
  <div class="card shadow border-0 rounded-3">
    <div class="card-header bg-white py-3">
      <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-file-text-fill me-2"></i>Mô tả chi tiết</h5>
    </div>
    <div class="card-body p-4">
      {{-- Dùng class 'prose' (nếu bạn có) hoặc để tự do --}}
      <div class="prose">
        {!! $tour->description ?? '<p class="text-muted">Không có mô tả.</p>' !!}
      </div>
    </div>
  </div>
</div>
@endsection