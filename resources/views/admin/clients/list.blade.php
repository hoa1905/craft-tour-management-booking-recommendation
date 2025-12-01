@extends('layouts.admin')

@section('title', 'Quản lý khách hàng')

@section('head')
<link rel="stylesheet" href="{{ asset('css/admin/clients/list.css') }}">
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
  <!-- Header Section -->
  <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded-3 shadow-sm mb-4">
    <div>
      <h3 class="fw-bold text-primary mb-1">
        <i class="fas fa-users me-2"></i>Quản lý khách hàng
      </h3>
      <p class="text-muted mb-0">Quản lý và cập nhật thông tin khách hàng</p>
    </div>
    <div>
      <a href="{{ route('admin.client.showAddAccount') }}" class="btn btn-primary btn-md">
        <i class="fas fa-plus-circle me-2"></i>Thêm khách hàng mới
      </a>
    </div>
  </div>

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

  <div class="card shadow border-0 rounded-3">
    <div class="card border-0 rounded-3">
      <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary">
          <i class="fas fa-list me-2"></i>Danh sách khách hàng
        </h5>
        <!-- Ô tìm kiếm -->
        <div class="search-container">
          <input type="text" id="searchBrand" class="form-control px-4"
            placeholder="🔍 Tìm kiếm khách hàng" onkeyup="filterTable()">
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0" id="searchUser">
          <thead class="bg-light">
            <tr>
              <th scope="col" class="py-3 text-center">STT</th>
              <th scope="col" class="py-3">Ảnh đại diện</th>
              <th scope="col" class="py-3">Tên</th>
              <th scope="col" class="py-3">Email</th>
              <th scope="col" class="py-3">SĐT</th>
              <th scope="col" class="py-3">Ngày tạo</th>
              <th scope="col" class="py-3 text-center">Trạng thái</th>
              <th scope="col" class="py-3 text-center">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($clients as $client)
            <tr>
              {{-- STT
                  firstItem() của biến $clients (biến này được paginate tạo ra)
                  $clients->firstItem(), Lấy số thứ tự của item đầu tiên (Trang 1 là 1, Trang 2 là 11,...)
                  $loop->index, Lấy index của vòng lặp (bắt đầu từ 0)
              --}}
              <td class="text-center align-middle">
                {{ $clients->firstItem() + $loop->index }}
              </td>
              <td class="align-middle text-center">
                <div class="d-flex align-items-center justify-content-center">
                  @if($client->avatar)
                  <img src="{{ asset('images/client/avatar/' . $client->avatar) }}"
                    alt="{{ $client->user_name }}"
                    class="img-thumbnail rounded-circle"
                    style="width: 45px; height: 45px; object-fit: cover;">
                  @else
                  <div class="bg-primary text-white d-flex align-items-center justify-content-center"
                    style="width: 45px; height: 45px; border-radius: 50%; border: 5px solid white; box-shadow: 0 0 0 1px #dee2e6;">
                    <span class="small fw-medium">{{ substr($client->user_name, 0, 1) }}</span>
                  </div>
                  @endif
                </div>
              </td>
              <td class="align-middle">{{ $client->user_name }}</td>
              <td class="align-middle">{{ $client->email }}</td>
              <td class="align-middle">{{ $client->phone_number }}</td>
              <td class="align-middle">{{ $client->created_at->format('d/m/Y') }}</td>
              <td class="text-center align-middle">
                @if($client->status === 'approved')
                <span class="badge bg-success">Được phê duyệt</span>
                @else
                <span class="badge bg-danger">Đã bị khóa</span>
                @endif
              </td>
              <td class="align-middle">
                <div class="d-flex justify-content-center gap-2">
                  <a href="{{ route('admin.client.showEdit', ['id' => $client->user_id]) }}" class="btn btn-light btn-sm" data-bs-toggle="tooltip" title="Chỉnh sửa">
                    <i class="fas fa-edit text-warning"></i>
                  </a>
                  <form action="{{ route('admin.client.toggleLock', ['id' => $client->user_id]) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-light btn-sm" data-bs-toggle="tooltip" title="{{ $client->status === 'approved' ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}">
                      <i class="fas {{ $client->status === 'approved' ? 'fa-lock text-danger' : 'fa-unlock text-success' }}"></i>
                    </button>
                  </form>
                  <form action="{{ route('admin.client.delete', ['id' => $client->user_id]) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-light btn-sm" data-bs-toggle="tooltip" title="Xóa tài khoản" onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này không?')">
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
        Hiển thị từ <strong>{{ $clients->firstItem() }}</strong> đến <strong>{{ $clients->lastItem() }}</strong> trong tổng số <strong>{{ $clients->total() }}</strong> khách hàng
      </small>
    </div>
    <div>
      {{ $clients->onEachSide(1)->links('vendor.pagination.custom-pagination') }}
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin/clients/list.js') }}"></script>
@endsection