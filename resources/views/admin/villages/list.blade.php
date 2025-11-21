@extends('layouts.admin')

@section('title', 'Quản lý làng nghề truyền thống')

@section('head')
<link rel="stylesheet" href="{{ asset('css/admin/villages/list.css') }}">
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

  @if(session('warning'))
  <div class="alert alert-warning alert-dismissible fade show shadow-sm border-warning" role="alert" id="status-alert">
    <div class="d-flex align-items-center">
      <div class="alert-icon-container me-3">
        <i class="fas fa-check-circle"></i>
      </div>
      <div>
        <p class="mb-0">{{ session('warning') }}</p>
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
        <i class="bi bi-houses-fill me-2"></i>Quản lý làng nghề truyền thống
      </h3>
      <p class="text-muted mb-0">Quản lý và cập nhật thông tin làng nghề truyền thống</p>
    </div>
    <div>
      <a href="{{ route('admin.village.showAddVillage') }}" class="btn btn-primary btn-md">
        <i class="fas fa-plus-circle me-2"></i>Thêm làng nghề mới
      </a>
    </div>
  </div>

  <div class="card shadow border-0 rounded-3">
    <div class="card border-0 rounded-3">
      <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary">
          <i class="fas fa-list me-2"></i>Danh sách làng nghề truyền thống
        </h5>
        <!-- Ô tìm kiếm -->
        <div class="search-container">
          <input type="text" id="searchVillage" class="form-control px-4"
            placeholder="🔍 Tìm kiếm làng nghề" onkeyup="filterTable()">
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0" id="searchVillage">
          <thead class="bg-light">
            <tr>
              <th scope="col" class="py-3 text-center" width="5%">STT</th>
              <th scope="col" class="py-3" width="10%">Ảnh</th>
              <th scope="col" class="py-3" width="30%">Tên làng nghề</th>
              <th scope="col" class="py-3" width="15%">Tỉnh</th>
              <th scope="col" class="py-3" width="15%">Miền</th>
              <th scope="col" class="py-3" width="10%">Trạng thái</th>
              <th scope="col" class="py-3 text-center" width="15%">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($villages as $village)
            <tr>
              <td class="text-center align-middle">
                {{ $villages->firstItem() + $loop->index }}
              </td>
              <td class="align-middle">
                @if($village->image_main)
                <img src="{{ asset('images/villages/' . $village->image_main) }}"
                  alt="{{ $village->village_name }}"
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
                  <span class="fw-bold text-dark">{{ $village->village_name }}</span>
                  <small class="text-muted">Mã làng nghề: {{ $village->village_id }}</small>
                </div>
              </td>
              <td class="align-middle">
                {{-- Kiểm tra xem có tỉnh không để tránh lỗi --}}
                @if($village->province)
                <span class="text-dark">
                  {{ $village->province->province_name }}
                </span>
                @else
                <span class="text-muted small">Chưa cập nhật</span>
                @endif
              </td>
              <td class="align-middle">
                {{-- Kiểm tra xem có tỉnh và có miền không --}}
                @if($village->province && $village->province->region)
                <span class="text-dark">
                  {{ $village->province->region->region_name }}
                </span>
                @else
                <span class="text-muted small">Chưa cập nhật</span>
                @endif
              </td>
              <td class="align-middle">
                @if($village->status === 'active')
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
                  <a href="{{ route('admin.village.detail', ['id' => $village->village_id]) }}"
                    class="btn btn-light btn-sm"
                    data-bs-toggle="tooltip"
                    title="Xem chi tiết">
                    <i class="fas fa-eye text-info"></i>
                  </a>

                  {{-- Nút chỉnh sửa --}}
                  <a href="{{ route('admin.village.showEditForm', ['id' => $village->village_id]) }}"
                    class="btn btn-light btn-sm"
                    data-bs-toggle="tooltip"
                    title="Chỉnh sửa">
                    <i class="fas fa-edit text-warning"></i>
                  </a>

                  {{-- Nút chuyển đổi trạng thái --}}
                  <form action="{{ route('admin.village.toggleStatus', ['id' => $village->village_id]) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')

                    @if($village->status == 'active')
                    <button type="submit" class="btn btn-warning btn-sm text-white"
                      title="Nhấn để ngừng hoạt động"
                      onclick="return confirm('Bạn muốn ngừng hoạt động làng nghề này? Tour liên quan có thể bị ảnh hưởng.')">
                      <i class="bi bi-pause-circle-fill"></i>
                    </button>
                    @else
                    <button type="submit" class="btn btn-success btn-sm"
                      title="Nhấn để kích hoạt lại">
                      <i class="bi bi-play-circle-fill"></i>
                    </button>
                    @endif
                  </form>

                  {{-- Xóa --}}
                  <form action="{{ route('admin.village.delete', ['id' => $village->village_id]) }}"
                    method="POST"
                    class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                      class="btn btn-light btn-sm"
                      data-bs-toggle="tooltip"
                      title="Xóa làng nghề truyền thống"
                      onclick="return confirm('Bạn có chắc chắn muốn xóa làng nghề này không?')">
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
        Hiển thị từ <strong>{{ $villages->firstItem() }}</strong> đén <strong>{{ $villages->lastItem() }}</strong> trong tổng số <strong>{{ $villages->total() }}</strong> làng nghề truyền thống
      </small>
    </div>
    <div>
      {{ $villages->onEachSide(1)->links('vendor.pagination.custom-pagination') }}
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin/villages/list.js') }}"></script>
@endsection