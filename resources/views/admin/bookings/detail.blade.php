@extends('layouts.admin')

@section('title', 'Chi tiết đơn đặt tour')

@section('head')
{{-- Lightbox CSS (Optional: click vào ảnh để phóng to) --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/admin/bookings/detail.css') }}">
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
      <h3 class="fw-bold mb-1 text-primary">
        <i class="fas fa-file-invoice-dollar me-2"></i> Chi tiết đơn đặt tour
      </h3>
      <p class="mb-0 text-muted fw-bold">
        <i class="fas fa-barcode me-1"></i> Mã đơn: {{ $booking->booking_id }}
      </p>
      <p class="mb-0 text-muted fw-bold">Ngày, giờ đặt tour: {{ $booking->created_at->format('d/m/Y H:i') }}</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.booking.invoice', ['id' => $booking->booking_id]) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2">
        <i class="bi bi-printer"></i> In hóa đơn
      </a>
    </div>
  </div>

  <div class="row">
    {{-- Cột trái--}}
    <div class="col-lg-8">
      <div class="card shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
          <h5 class="mb-0 fw-bold text-primary">
            <i class="fas fa-map-marked-alt"></i> Thông tin tour
          </h5>
        </div>
        <div class="card-body p-4">
          <div class="d-flex align-items-start">
            <div class="flex-shrink-0 me-4">
              @if($booking->tour && $booking->tour->image_main)
              {{-- Ảnh Tour thường --}}
              <img src="{{ asset('images/tours/' . $booking->tour->image_main) }}"
                alt="Ảnh tour"
                class="rounded shadow-sm border"
                style="width: 180px; height: 120px; object-fit: cover;">
              @else
              {{-- Ảnh Tour yêu cầu --}}
              <img src="{{ asset('images/tours/custom-tour.jpg') }}"
                alt="Tour theo yêu cầu"
                class="rounded shadow-sm border"
                style="width: 180px; height: 120px; object-fit: cover;">
              @endif
            </div>
            <div class="flex-grow-1">
              <h5 class="fw-bold mb-2">
                @if($booking->tour_id)
                <a href="{{ route('admin.tour.detail', ['id' => $booking->tour_id]) }}" class="text-decoration-none text-dark">
                  {{ $booking->tour_name }}
                </a>
                @elseif($booking->request_id)
                <a href="{{ route('admin.tour_request.detail', ['id' => $booking->request_id]) }}" class="text-decoration-none text-dark">
                  {{ $booking->tour_name }}
                </a>
                @else
                {{ $booking->tour_name }}
                @endif

                <p class="text-muted small mb-3 mt-1">
                  @if($booking->tour_id)
                  Mã tour: <strong>{{ $booking->tour_id }}</strong>
                  @elseif($booking->request_id)
                  Mã tour theo yêu cầu: <strong>{{ $booking->request_id }}</strong>
                  @endif
                </p>
              </h5>

              {{-- Xử lý thông tin chi tiết --}}
              @php
              // 1. Lấy ngày đi & về
              if ($booking->schedule) {
              $startDate = $booking->schedule->start_date;
              $endDate = $booking->schedule->end_date;
              } elseif ($booking->tourRequest) {
              $startDate = $booking->tourRequest->start_date;
              $endDate = $booking->tourRequest->end_date;
              } else {
              $startDate = null; $endDate = null;
              }

              // 2. Lấy địa điểm
              $departure = $booking->tour ? $booking->tour->departure_point : ($booking->tourRequest->departure_point ?? 'N/A');
              $destination = $booking->tour ? $booking->tour->destination : ($booking->tourRequest->desired_destination ?? 'N/A');

              // 3. Tính toán thời lượng
              if ($booking->tour) {
              // Nếu là tour có sẵn: Lấy text có sẵn trong DB (VD: "3 ngày 2 đêm")
              $duration = $booking->tour->duration;
              } elseif ($startDate && $endDate) {
              // Nếu là tour yêu cầu: Tự tính toán
              $cStart = \Carbon\Carbon::parse($startDate);
              $cEnd = \Carbon\Carbon::parse($endDate);

              // diffInDays trả về khoảng cách, +1 để tính cả ngày khởi hành
              $days = $cStart->diffInDays($cEnd) + 1;
              $nights = $days > 1 ? $days - 1 : 0;

              $duration = "$days ngày $nights đêm";
              } else {
              $duration = 'Chưa xác định';
              }
              @endphp

              <div class="row g-3">
                <div class="col-md-4">
                  <small class="text-muted d-block">Khởi hành:</small>
                  <strong><i class="far fa-calendar-alt me-1 text-info"></i> {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : '---' }}</strong>
                </div>
                <div class="col-md-4">
                  <small class="text-muted d-block">Kết thúc:</small>
                  <strong><i class="far fa-calendar-check me-1 text-success"></i> {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : '---' }}</strong>
                </div>
                <div class="col-md-4">
                  <small class="text-muted d-block">Thời lượng tour:</small>
                  {{-- Hiển thị kết quả tính toán --}}
                  <span>{{ $duration }}</span>
                </div>
                <div class="col-md-4">
                  <small class="text-muted d-block">Điểm khởi hành:</small>
                  <span>{{ $departure }}</span>
                </div>
                <div class="col-md-4">
                  <small class="text-muted d-block">Điểm đến:</small>
                  <span>{{ $destination }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Cột phải --}}
    <div class="col-lg-4">
      <div class="card shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
          <h5 class="mb-0 fw-bold text-primary">
            <i class="fas fa-user-circle"></i> Thông tin khách hàng
          </h5>
        </div>
        <div class="card-body p-4">
          <div class="row">
            <div class="col-lg-4">
              <div class="text-center">
                @if($booking->user && $booking->user->avatar)
                <img src="{{ asset('images/client/avatar/' . $booking->user->avatar) }}" class="rounded-circle shadow-sm mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                @else
                <div class="rounded-circle bg-secondary bg-opacity-10 d-flex justify-content-center align-items-center mx-auto mb-2 text-secondary" style="width: 80px; height: 80px;">
                  <i class="fas fa-user fa-2x"></i>
                </div>
                @endif
              </div>
            </div>
            <div class="col-lg-8">
              <h5 class="fw-bold mb-0">{{ $booking->full_name }}</h5>
              <small class="text-muted">Tài khoản: {{ $booking->user->user_name}}</small>
            </div>
            <hr>
            <div class="row g-1">
              <div class="col-md-5">
                <span><i class="fas fa-phone-alt text-muted me-2" style="width: 20px;"></i> {{ $booking->phone_number }}</span>
              </div>
              <div class="col-md-7">
                <span><i class="fas fa-envelope text-muted me-2" style="width: 20px;"></i> {{ $booking->email }}</span>
              </div>
              <div class="col-md-12">
                <span><i class="fas fa-map-marker-alt text-muted me-2" style="width: 20px;"></i> {{ $booking->address }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow border-0 rounded-3 mb-4">
      <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-primary">
          <i class="fas fa-users"></i> Chi tiết hành khách và Giá
        </h5>
      </div>
      <div class="card-body p-4">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th class="ps-4">Đối tượng</th>
              <th class="text-center">Số lượng</th>
              <th class="text-end">Đơn giá</th>
              <th class="text-end pe-4">Thành tiền</th>
            </tr>
          </thead>
          <tbody>
            {{-- Người lớn --}}
            <tr>
              <td class="ps-4 fw-bold"><i class="fas fa-user me-2 text-primary"></i> Người lớn</td>
              <td class="text-center">{{ $booking->adult_quantity }}</td>
              <td class="text-end">{{ number_format($booking->price_adult_at_booking, 0, ',', '.') }}₫</td>
              <td class="text-end pe-4 fw-bold">{{ number_format($booking->adult_quantity * $booking->price_adult_at_booking, 0, ',', '.') }}₫</td>
            </tr>
            {{-- Trẻ em --}}
            @if($booking->child_quantity > 0)
            <tr>
              <td class="ps-4 fw-bold"><i class="fas fa-child me-2 text-warning"></i> Trẻ em</td>
              <td class="text-center">{{ $booking->child_quantity }}</td>
              <td class="text-end">{{ number_format($booking->price_child_at_booking, 0, ',', '.') }}₫</td>
              <td class="text-end pe-4 fw-bold">{{ number_format($booking->child_quantity * $booking->price_child_at_booking, 0, ',', '.') }}₫</td>
            </tr>
            @endif
          </tbody>
          <tfoot class="bg-white">
            <tr>
              <td colspan="3" class="text-end fw-bold py-3 text-danger border-0">TỔNG CỘNG:</td>
              <td class="text-end pe-4 total-price py-3 fw-bold text-danger border-0 fs-5">{{ number_format($booking->total_price, 0, ',', '.') }}₫</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    @if($booking->payment)
    <div class="card shadow border-0 rounded-3 mb-4">
      <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-primary">
          <i class="fas fa-credit-card"></i> Thông tin thanh toán
        </h5>
      </div>
      <div class="card-body p-4">
        <table class="table table-hover align-middle mb-0">
          <tbody>
            <tr>
              <th class="ps-4">Ngày, giờ thanh toán:</th>
              <td>{{ $booking->payment->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
              <th class="ps-4" style="width: 30%;">Mã giao dịch:</th>
              <td class="fw-bold fs-5"><code>{{ $booking->payment->transaction_id }}</code></td>
            </tr>
            <tr>
              <th class="ps-4">Người thanh toán:</th>
              <td>
                {{ $booking->payment->payper_name }} <br>
                <small class="text-muted">{{ $booking->payment->payper_email }}</small>
              </td>
            </tr>
            <tr>
              <th class="ps-4">Phương thức:</th>
              <td>{{ $booking->payment->payment_method }}</td>
            </tr>
            <tr>
              <th class="ps-4">Số tiền thanh toán:</th>
              <td>
                @if($booking->payment->payment_method === 'Paypal')
                {{-- 1. Hiển thị tiền USD gốc --}}
                <span class="fw-bold text-success fs-5">
                  ${{ number_format($booking->payment->amount, 2, '.', ',') }}
                </span>

                {{-- 2. Hiển thị quy đổi VND --}}
                @php
                $exchangeRate = config('paypal.vnd_to_usd_rate'); // Lấy từ config: tỷ giá 1 USD = 26.310 VND
                $vndEstimate = $booking->payment->amount * $exchangeRate;
                @endphp
                <div class="text-muted small fst-italic">
                  (~ {{ number_format($vndEstimate, 0, ',', '.') }}VND)
                </div>

                @else
                <span class="fw-bold text-success fs-5">
                  {{ number_format($booking->payment->amount, 0, ',', '.') }}₫
                </span>
                @endif
              </td>
            </tr>
            <tr>
              <th class="ps-4">Trạng thái:</th>
              <td>
                @php
                $pStatus = $booking->payment->status;

                // 1. Map màu sắc (Class CSS)
                $pClass = match($pStatus) {
                'waiting_payment' => 'warning',
                'success' => 'success',
                'failed' => 'danger',
                'pending_refund' => 'dark',
                'refunded' => 'secondary'
                };

                // 2. Map nội dung hiển thị (Tiếng Việt)
                $pLabel = match($pStatus) {
                'waiting_payment' => 'Chưa thanh toán',
                'success' => 'Thành công',
                'failed' => 'Thất bại',
                'pending_refund' => 'Chờ hoàn tiền',
                'refunded' => 'Đã hoàn tiền'
                };
                @endphp

                <span class="badge bg-{{ $pClass }} fs-6">
                  {{ $pLabel }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    @else
    <div class="alert alert-warning shadow-sm">
      <i class="fas fa-exclamation-triangle me-2"></i> Đơn đặt tour này chưa có thông tin thanh toán.
    </div>
    @endif
  </div>
  @endsection