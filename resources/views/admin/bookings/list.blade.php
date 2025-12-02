@extends('layouts.admin')

@section('title', 'Quản lý đơn đặt tour')

@section('head')
<link rel="stylesheet" href="{{ asset('css/admin/bookings/list.css') }}">
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

  @if($errors->any())
  <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $errors->first() }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-center page-header p-4 rounded-3 shadow-sm mb-4 bg-white">
    <div>
      <h3 class="fw-bold mb-1 text-primary"><i class="fas fa-calendar-check me-2"></i>Quản lý đơn đặt tour</h3>
      <p class="mb-0 text-muted">Theo dõi đơn hàng và xử lý thanh toán</p>
    </div>
    <div>
      <span class="badge bg-primary shadow-sm p-2 fs-6"><i class="fas fa-chart-pie me-1"></i> Tổng: {{ $bookings->total() }} đơn</span>
    </div>
  </div>

  {{-- Card Danh sách --}}
  <div class="card shadow border-0 rounded-3 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
      <h5 class="mb-0 text-primary"><i class="fas fa-list-ul me-2"></i>Danh sách đơn hàng</h5>
      <form action="{{ route('admin.booking.index') }}" method="GET" class="d-flex gap-2 align-items-center">
        <div>Lọc theo trạng thái tour:</div>
        <select name="status" class="form-select form-select-sm shadow-sm border-primary" onchange="this.form.submit()">
          <option value="all" {{ $status == 'all' ? 'selected' : '' }}>
            -- Tất cả trạng thái --
          </option>
          <option value="waiting_payment" {{ $status == 'waiting_payment' ? 'selected' : '' }}>
            Chờ thanh toán
          </option>
          <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>
            Đã thanh toán
          </option>
          <option value="active" {{ $status == 'active' ? 'selected' : '' }}>
            Đã xác nhận
          </option>
          <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>
            Hoàn thành
          </option>
          <option value="canceled" {{ $status == 'canceled' ? 'selected' : '' }}>
            Đã hủy
          </option>
        </select>
      </form>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-secondary">
            <tr>
              <th class="py-3 text-center">Mã và Ngày đặt</th>
              <th class="py-3">Khách hàng</th>
              <th class="py-3" style="width: 25%;">Tour và Lịch trình</th>
              <th class="py-3 text-center">Tổng tiền</th>
              <th class="py-3 text-center">Phương thức</th>
              <th class="py-3 text-center">TT Thanh toán</th>
              <th class="py-3 text-center">TT Tour</th>
              <th class="py-3 text-center">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            @forelse($bookings as $booking)
            <tr>
              {{-- 1. Mã và Ngày --}}
              <td class="ps-3">
                <div>
                  <i class="fas fa-barcode me-1"></i>
                  {{ $booking->booking_id }}
                </div>
                <small class="text-muted">{{ $booking->created_at->format('d/m/Y H:i') }}</small>
              </td>

              {{-- 2. Khách hàng --}}
              <td>
                <div class="d-flex align-items-center">
                  @if($booking->user && $booking->user->avatar)
                  <img src="{{ asset('images/client/avatar/' . $booking->user->avatar) }}" class="rounded-circle customer-avatar me-2 shadow-sm">
                  @else
                  <div class="rounded-circle bg-secondary bg-opacity-10 d-flex justify-content-center align-items-center me-2 customer-avatar text-secondary">
                    <i class="fas fa-user"></i>
                  </div>
                  @endif
                  <div>
                    <div class="fw-bold">{{ $booking->user->user_name }}</div>
                    <small class="text-muted d-block"><i class="fas fa-phone-alt me-1"></i>{{ $booking->user->phone_number }}</small>
                  </div>
                </div>
              </td>

              {{-- 3. Thông tin Tour --}}
              <td>
                @if($booking->tour_id)
                {{-- Trường hợp: Tour có sẵn (Có tour_id) --}}
                <a href="{{ route('admin.tour.detail', ['id' => $booking->tour_id]) }}" class="tour-name text-decoration-none mb-1">
                  {{ $booking->tour->tour_name ?? 'Tour không tồn tại' }}
                </a>
                @elseif($booking->request_id)
                {{-- Trường hợp: Tour theo yêu cầu (Có request_id) --}}
                <a href="{{ route('admin.tour_request.detail', ['id' => $booking->request_id]) }}" class="tour-name text-decoration-none mb-1">
                  {{ $booking->tour_name }} {{-- Sử dụng Accessor trong Model --}}
                </a>
                @else
                <span class="text-muted">Thông tin tour bị lỗi</span>
                @endif

                {{-- Ngày khởi hành --}}
                <div class="small text-muted mt-1">
                  <i class="far fa-calendar-alt text-info me-1"></i>
                  @if($booking->schedule)
                  Khởi hành: <strong>{{ \Carbon\Carbon::parse($booking->schedule->start_date)->format('d/m/Y') }}</strong>
                  @elseif($booking->tourRequest)
                  Khởi hành: <strong>{{ \Carbon\Carbon::parse($booking->tourRequest->start_date)->format('d/m/Y') }}</strong>
                  @endif
                </div>
                <div class="small text-muted">
                  <i class="fas fa-users text-secondary me-1"></i>
                  {{ $booking->adult_quantity }} Lớn, {{ $booking->child_quantity }} Trẻ
                </div>
              </td>

              {{-- 4. Tổng tiền --}}
              <td class="text-center">
                <div class="fw-bold text-danger">{{ number_format($booking->total_price, 0, ',', '.') }}₫</div>
              </td>

              {{-- 5. Phương thức --}}
              <td class="text-center">
                <div class="">{{ $booking->payment->payment_method }}</div>
              </td>

              {{-- 6. Trạng thái thanh toán --}}
              <td class="text-center">
                @if($booking->payment)
                <form action="{{ route('admin.booking.updatePaymentStatus', $booking->booking_id) }}" method="POST">
                  @csrf
                  @method('PUT')

                  @php
                  $pStatus = $booking->payment->status;
                  $pConfig = [
                  'waiting_payment' => ['cls'=>'btn-warning text-dark', 'lbl'=>'Chưa thanh toán'],
                  'success' => ['cls'=>'btn-success', 'lbl'=>'Thành công'],
                  'failed' => ['cls'=>'btn-danger', 'lbl'=>'Thất bại'],
                  'pending_refund' => ['cls'=>'btn-dark', 'lbl'=>'Chờ hoàn tiền'],
                  'refunded' => ['cls'=>'btn-secondary', 'lbl'=>'Đã hoàn tiền'],
                  ];
                  $pCurr = $pConfig[$pStatus] ?? ['cls'=>'btn-light border', 'lbl'=>$pStatus];
                  @endphp

                  <div class="btn-group">
                    <button type="button" class="btn {{ $pCurr['cls'] }} btn-sm dropdown-toggle shadow-sm" data-bs-toggle="dropdown">
                      {{ $pCurr['lbl'] }}
                    </button>
                    <ul class="dropdown-menu shadow">
                      <li>
                        <button type="submit" name="payment_status" value="waiting_payment" class="dropdown-item">
                          Chưa thanh toán
                        </button>
                      </li>
                      <li>
                        <button type="submit" name="payment_status" value="success" class="dropdown-item">
                          Thành công
                        </button>
                      </li>
                      <li>
                        <button type="submit" name="payment_status" value="failed" class="dropdown-item">
                          Thất bại
                        </button>
                      </li>
                      <li>
                        <button type="submit" name="payment_status" value="pending_refund" class="dropdown-item">
                          Chờ hoàn tiền
                        </button>
                      </li>
                      <li>
                        <button type="submit" name="payment_status" value="refunded" class="dropdown-item">
                          Đã hoàn tiền
                        </button>
                      </li>
                    </ul>
                  </div>
                </form>
                @else
                <span class="badge bg-light text-muted border">Chưa thanh toán</span>
                @endif
              </td>

              {{-- 7. Trạng thái tour --}}
              <td class="text-center">
                @php
                // Lấy ngày hiện tại, ngày đi, ngày về
                $now = \Carbon\Carbon::now();

                // Xử lý an toàn: Nếu schedule null thì lấy tourRequest, nếu cả 2 null thì lấy today
                $start = $booking->schedule ? \Carbon\Carbon::parse($booking->schedule->start_date)
                : ($booking->tourRequest ? \Carbon\Carbon::parse($booking->tourRequest->start_date) : $now);

                $end = $booking->schedule ? \Carbon\Carbon::parse($booking->schedule->end_date)
                : ($booking->tourRequest ? \Carbon\Carbon::parse($booking->tourRequest->end_date) : $now);

                // Kiểm tra đang đi tour
                $isTraveling = $booking->status === 'active' && $now->between($start, $end);

                // Cấu hình màu sắc và icon cho từng trạng thái
                $bStatus = $booking->status;
                $bConfig = [
                'waiting_payment' => ['cls'=>'btn-warning text-dark', 'lbl'=>'Chờ thanh toán', 'icon'=>'hourglass-half'],
                'paid' => ['cls'=>'btn-info text-white', 'lbl'=>'Đã thanh toán', 'icon'=>'money-bill-wave'],
                'active' => ['cls'=>'btn-primary', 'lbl'=>'Đang du lịch', 'icon'=>'check-circle'],
                'completed' => ['cls'=>'btn-success', 'lbl'=>'Hoàn thành', 'icon'=>'flag-checkered'],
                'canceled' => ['cls'=>'btn-danger', 'lbl'=>'Đã hủy', 'icon'=>'ban'],
                ];
                $bCurr = $bConfig[$bStatus] ?? ['cls'=>'btn-secondary', 'lbl'=>$bStatus, 'icon'=>'question'];
                @endphp

                {{-- Nếu đang đi tour-  Hiện Badge Đang Du Lịch (Nổi bật) --}}
                @if($isTraveling)
                <div class="badge bg-primary bg-opacity-10 text-primary border border-primary mb-1 p-2">
                  <i class="fas fa-plane-departure me-1"></i> Đang du lịch
                  <div class="small mt-1">Kết thúc: {{ $end->diffForHumans() }}</div>
                </div>
                @endif

                {{-- Form Cập nhật --}}
                <form action="{{ route('admin.booking.updateStatus', $booking->booking_id) }}" method="POST">
                  @csrf
                  @method('PUT')

                  <div class="btn-group">
                    <button type="button" class="btn {{ $bCurr['cls'] }} btn-sm dropdown-toggle shadow-sm" data-bs-toggle="dropdown">
                      <i class="fas fa-{{ $bCurr['icon'] }}"></i> {{ $bCurr['lbl'] }}
                    </button>
                    <ul class="dropdown-menu shadow">
                      <li>
                        <button type="submit" name="status" value="waiting_payment" class="dropdown-item">
                          Chờ thanh toán
                        </button>
                      </li>
                      <li>
                        <button type="submit" name="status" value="paid" class="dropdown-item">
                          Đã thanh toán
                        </button>
                      </li>
                      <li>
                        <button type="submit" name="status" value="active" class="dropdown-item">
                          Đang du lịch
                        </button>
                      </li>
                      <li>
                        <button type="submit" name="status" value="completed" class="dropdown-item">
                          Hoàn thành
                        </button>
                      </li>
                      <li>
                        <hr class="dropdown-divider">
                      </li>
                      <li>
                        <button type="submit" name="status" value="canceled" class="dropdown-item text-danger">
                          Hủy đơn
                        </button>
                      </li>
                    </ul>
                  </div>
                </form>
              </td>

              {{-- 8. Thao tác --}}
              <td class="text-center">
                <div class="d-flex justify-content-center gap-2">

                  {{-- Nút Xem Chi Tiết --}}
                  <a href="{{ route('admin.booking.detail', ['id' => $booking->booking_id]) }}"
                    class="btn btn-outline-primary btn-sm shadow-sm"
                    title="Xem chi tiết">
                    <i class="fas fa-eye"></i>
                  </a>

                  {{-- Nút In Hóa Đơn (Mới thêm) --}}
                  <a href="{{ route('admin.booking.invoice', ['id' => $booking->booking_id]) }}"
                    class="btn btn-outline-secondary btn-sm shadow-sm"
                    title="In hóa đơn"
                    target="_blank"> {{-- target="_blank" để mở tab mới --}}
                    <i class="fas fa-print"></i>
                  </a>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center py-5 text-muted">
                <i class="fas fa-box-open fa-3x mb-3 opacity-50"></i>
                <p>Chưa có đơn đặt tour nào.</p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  {{-- Card Footer (Pagination) --}}
  <div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted">
      <small>
        Hiển thị từ <strong>{{ $bookings->firstItem() ?? 0 }}</strong> đến <strong>{{ $bookings->lastItem() ?? 0 }}</strong> trong <strong>{{ $bookings->total() }}</strong> đơn
      </small>
    </div>
    <div>
      {{ $bookings->onEachSide(1)->links('vendor.pagination.custom-pagination') }}
    </div>
  </div>
</div>
@endsection