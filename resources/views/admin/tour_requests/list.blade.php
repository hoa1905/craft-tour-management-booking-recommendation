@extends('layouts.admin')

@section('title', 'Quản lý tour theo yêu cầu')

@section('content')
<div class="container-fluid px-4 py-4">

  <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded-3 shadow-sm mb-4">
    <div>
      <h3 class="fw-bold text-primary mb-1"><i class="bi bi-envelope-paper-fill me-2"></i>Quản lý tour theo yêu cầu</h3>
      <p class="text-muted mb-0">Danh sách khách hàng đặt tour thiết kế riêng</p>
    </div>
  </div>

  {{-- Bộ lọc & Danh sách --}}
  <div class="card shadow border-0 rounded-3">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
      <h5 class="mb-0 text-primary"><i class="fas fa-list-ul me-2"></i>Danh sách tour theo yêu cầu</h5>

      {{-- Form lọc trạng thái --}}
      <form action="{{ route('admin.tour_request.index') }}" method="GET" class="d-flex gap-2 align-items-center">
        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 200px;">
          <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>-- Tất cả trạng thái --</option>
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
          <option value="offering" {{ request('status') == 'offering' ? 'selected' : '' }}>Đã báo giá</option>
          <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Đã chốt</option>
          <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
        </select>
      </form>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-secondary">
            <tr>
              <th class="text-center">STT</th>
              <th>Khách hàng</th>
              <th>Điểm đến và Thời gian</th>
              <th class="text-center">Số khách</th>
              <th class="text-center">Trạng thái</th>
              <th class="text-center">Ngày gửi</th>
              <th class="text-center">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            @forelse($requests as $request)
            <tr>
              <td class="text-center align-middle">
                {{ $requests->firstItem() + $loop->index }}
              </td>
              <td>
                <div class="d-flex align-items-center">
                  @if($request->user && $request->user->avatar)
                  <img src="{{ asset('images/client/avatar/' . $request->user->avatar) }}" class="rounded-circle me-2 shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                  @else
                  <div class="rounded-circle bg-secondary bg-opacity-10 d-flex justify-content-center align-items-center me-2 customer-avatar text-secondary">
                    <i class="fas fa-user"></i>
                  </div>
                  @endif
                  <div>
                    <div class="fw-bold">{{ $request->user->user_name }}</div>
                    <small class="text-muted d-block">Mã KH: {{ $request->user->user_id }}</small>
                  </div>
                </div>
              </td>
              <td>
                <div class="fw-bold text-dark mb-1">{{ $request->desired_destination }}</div>
                <small class="text-muted">
                  <i class="far fa-calendar-alt text-info me-1"></i>
                  {{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }} -
                  {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}
                </small>
              </td>
              <td class="text-center">
                <div class="small text-muted">
                  <i class="fas fa-users text-secondary me-1"></i>
                  {{ $request->adult_quantity }} Lớn, {{ $request->child_quantity }} Trẻ
                </div>
              </td>
              <td class="text-center">
                {{-- Sử dụng Helper trong Model để hiển thị label/class --}}
                <span class="badge {{ str_replace('label-', 'bg-', $request->status_class) }}">
                  {{ $request->status_label }}
                </span>
              </td>
              <td class="text-center text-muted small">
                {{ $request->created_at->format('d/m/Y H:i') }}
              </td>
              <td class="align-middle">
                <div class="d-flex justify-content-center gap-2">
                  {{-- Nút xem chi tiết --}}
                  <a href="{{ route('admin.tour_request.detail', ['id' => $request->request_id]) }}"
                    class="btn btn-light btn-sm"
                    data-bs-toggle="tooltip"
                    title="Xem chi tiết">
                    <i class="fas fa-eye text-info"></i>
                  </a>
                  {{-- Nút Chỉnh sửa --}}
                  <a href="{{ route('admin.tour_request.edit', ['id' => $request->request_id]) }}"
                    class="btn btn-light btn-sm"
                    data-bs-toggle="tooltip"
                    title="Chỉnh sửa và Xử lý">
                    <i class="fas fa-edit text-warning"></i>
                  </a>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center py-5 text-muted">Không có yêu cầu nào.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted">
      <small>
        Hiển thị từ <strong>{{ $requests->firstItem() }}</strong> đến <strong>{{ $requests->lastItem() }}</strong> trong tổng số <strong>{{ $requests->total() }}</strong> tour theo yêu cầu
      </small>
    </div>
    <div>
      {{ $requests->onEachSide(1)->links('vendor.pagination.custom-pagination')}}
    </div>
  </div>
</div>
@endsection