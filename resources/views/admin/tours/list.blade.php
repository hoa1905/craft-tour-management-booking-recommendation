@extends('layouts.admin')

@section('title', 'Quản lý tour')

@section('head')
<link rel="stylesheet" href="{{ asset('css/admin/tours/list.css') }}">
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
  @endif

  <!-- Header Section -->
  <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-3 shadow-sm mb-4">
    <div>
      <h3 class="fw-bold text-primary mb-1">
        <i class="bi bi-map-fill me-2"></i>Quản lý tour
      </h3>
      <p class="text-muted mb-0">Quản lý và cập nhật thông tin tour</p>
    </div>
    <div>
      <a href="{{ route('admin.tour.showAddTour') }}" class="btn btn-primary btn-md">
        <i class="fas fa-plus-circle me-2"></i>Thêm tour mới
      </a>
    </div>
  </div>

  <div class="card shadow border-0 rounded-3">
    <div class="card border-0 rounded-3">
      <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary">
          <i class="fas fa-list me-2"></i>Danh sách tour
        </h5>
        <!-- Ô tìm kiếm -->
        <div class="search-container">
          <input type="text" id="searchProduct" class="form-control px-4"
            placeholder="🔍 Tìm kiếm tour" onkeyup="filterTable()">
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0" id="searchProduct">
          <thead class="bg-light">
            <tr>
              <th scope="col" class="py-3 text-center" width="5%">STT</th>
              <th scope="col" class="py-3" width="10%">Ảnh</th>
              <th scope="col" class="py-3" width="25%">Tên tour</th>
              <th scope="col" class="py-3" width="15%">Giá</th>
              <th scope="col" class="py-3" width="15%">Làng nghề</th>
              <th scope="col" class="py-3" width="10%">Tỉnh</th>
              <th scope="col" class="py-3" width="10%">Miền</th>
              <th scope="col" class="py-3" width="10%">Trạng thái</th>
              <th scope="col" class="py-3 text-center" width="15%">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($tours as $tour)
            <tr>
              <td class="text-center align-middle">
                {{ $tours->firstItem() + $loop->index }}
              </td>
              <td class="align-middle">
                @if($tour->image_main)
                <img src="{{ asset('images/tours/' . $tour->image_main) }}"
                  alt="{{ $tour->tour_name }}"
                  class="rounded shadow-sm"
                  style="width: 60px; height: 60px; object-fit: cover;">
                @else
                <span class="badge bg-light text-secondary">
                  <i class="fas fa-image-slash me-1"></i>Không có ảnh
                </span>
                @endif
              </td>
              <td class="align-middle">
                <div class="d-flex flex-column">
                  <span class="fw-bold text-dark">{{ $tour->tour_name }}</span>
                  <small class="text-muted">Mã tour: {{ $tour->tour_id }}</small>
                </div>
              </td>
              <td class="align-middle">
                <span>
                  Giá người lớn:
                  <br>
                  <span class="text-danger">
                    {{ number_format($tour->price_adult, 0, ',', '.') }}₫
                  </span>
                </span>
                <br>
                <span>
                  Giá trẻ em:
                  <br>
                  <span class="text-danger">
                    {{ number_format($tour->price_child, 0, ',', '.') }}₫
                  </span>
                </span>
              </td>
              <td class="align-middle">
                @foreach ($tour->villages as $v)
                <span class="badge rounded-pill px-3"
                  style="background-color:#f5e6d4; color:#8b5e34;">
                  {{ $v->village_name }}
                </span>
                @endforeach
              </td>
              <td class="align-middle">
                @foreach ($tour->villages as $v)
                <span class="badge rounded-pill bg-transparent text-dark px-3">
                  {{ $v->province->province_name }}
                </span>
                @endforeach
              </td>
              <td class="align-middle">
                @foreach ($tour->villages as $v)
                <span class="badge rounded-pill bg-transparent text-dark px-3">
                  {{ $v->province->region->region_name }}
                </span>
                @endforeach
              </td>
              <td class="align-middle">
                @if($tour->status === 'active')
                <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3">
                  Hoạt động
                </span>
                @else
                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3">
                  Ngừng hoạt động
                </span>
                @endif
              </td>
              <td class="align-middle">
                <div class="d-flex justify-content-center gap-2">
                  {{-- Nút xem chi tiết --}}
                  <a href="{{ route('admin.tour.detail', ['id' => $tour->tour_id]) }}"
                    class="btn btn-light btn-sm"
                    data-bs-toggle="tooltip"
                    title="Xem chi tiết">
                    <i class="fas fa-eye text-info"></i>
                  </a>
                  {{-- Nút Chỉnh sửa --}}
                  <a href="{{ route('admin.tour.showEditForm', ['id' => $tour->tour_id]) }}"
                    class="btn btn-light btn-sm"
                    data-bs-toggle="tooltip"
                    title="Chỉnh sửa">
                    <i class="fas fa-edit text-warning"></i>
                  </a>
                  {{-- Nút chuyển đổi trạng thái --}}
                  <form action="{{ route('admin.tour.toggleStatus', ['id' => $tour->tour_id]) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')

                    @if($tour->status == 'active')
                    <button type="submit" class="btn btn-warning btn-sm text-white"
                      title="Nhấn để ngừng hoạt động"
                      onclick="return confirm('Bạn muốn ngừng hoạt động tour này?')">
                      <i class="bi bi-pause-circle-fill"></i>
                    </button>
                    @else
                    <button type="submit" class="btn btn-success btn-sm"
                      title="Nhấn để kích hoạt lại">
                      <i class="bi bi-play-circle-fill"></i>
                    </button>
                    @endif
                  </form>

                  {{-- Nút xóa tour --}}
                  <form action="{{ route('admin.tour.delete', ['id' => $tour->tour_id]) }}"
                    method="POST"
                    class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                      class="btn btn-light btn-sm"
                      data-bs-toggle="tooltip"
                      title="Xóa tour"
                      onclick="return confirm('Bạn có chắc chắn muốn xóa tour này không?')">
                      <i class="fas fa-trash text-danger"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted">
      <small>
        Hiển thị từ <strong>{{ $tours->firstItem() }}</strong> đến <strong>{{ $tours->lastItem() }}</strong> trong tổng số <strong>{{ $tours->total() }}</strong> tour
      </small>
    </div>
    <div>
      {{ $tours->onEachSide(1)->links('vendor.pagination.custom-pagination') }}
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin/tours/list.js') }}"></script>
@endsection