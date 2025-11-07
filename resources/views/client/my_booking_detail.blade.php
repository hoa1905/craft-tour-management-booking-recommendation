@extends('client.master')

@section('title', 'Chi tiết đơn đặt tour')

@section('head')
<link rel="stylesheet" href="{{ asset('css/my-booking-detail.css') }}">
@endsection

@section('content')
<div id="vnt-content">
  <div class="vhmemberpg">
    <div class="mbpadd">
      <div class="wrapper booking-wrapper">

        <h2 class="booking-title">
          Chi tiết Đơn đặt tour
          <span class="booking-id">(Mã đơn: {{ $booking->booking_id }})</span>
        </h2>

        {{-- Xử lý trạng thái --}}
        @php
        $statusText = 'Chờ hoàn tiền - Đang xử lý';
        $statusClass = 'status-processing';

        if ($booking->payment && $booking->payment->status === 'success') {
        $statusText = 'Đã thanh toán';
        $statusClass = 'status-success';
        } elseif ($booking->payment && $booking->payment->status === 'waiting_payment') {
        $statusText = 'Chờ thanh toán';
        $statusClass = '.status-waiting-payment';
        } elseif ($booking->status === 'completed') {
        $statusText = 'Hoàn thành';
        $statusClass = 'status-completed';
        } elseif ($booking->status === 'active') {
        $statusText = 'Đang hoạt động';
        $statusClass = 'status-active';
        } elseif ($booking->status === 'canceled') {
        $statusText = 'Đã hủy';
        $statusClass = 'status-canceled';
        } elseif ($booking->payment && $booking->payment->status === 'refunded') {
        $statusText = 'Đã hoàn tiền';
        $statusClass = 'status-refunded';
        }
        @endphp

        {{-- Trạng thái đơn hàng --}}
        <div class="detail-box">
          <div class="detail-box-header">Trạng thái đơn hàng</div>
          <div class="detail-box-body text-center">
            <div class="status-badge {{ $statusClass }}">
              {{ $statusText }}
            </div>

            @if($booking->is_cancelable)
            <div class="cancel-section">
              <p>Đơn hàng này đủ điều kiện để Hủy.</p>
              @php
              $isPaid = $booking->payment?->status === 'success';
              $confirmMessage = $isPaid
              ? 'Hủy tour sẽ bắt đầu quy trình HOÀN TIỀN. Bạn có chắc chắn?'
              : 'Bạn có chắc chắn muốn hủy đơn đặt tour này?';
              @endphp

              <form action="{{ route('account.myBookings.cancel', $booking->booking_id) }}"
                method="POST"
                onsubmit="return confirm('{{ $confirmMessage }}');"
                class="cancel-form">
                @csrf
                @method('POST')
                <button type="submit" class="btn-cancel">Yêu cầu Hủy Tour</button>
              </form>
            </div>
            @endif
          </div>
        </div>

        {{-- Thông tin Tour --}}
        <div class="detail-box">
          <div class="detail-box-header">Thông tin Tour</div>
          <div class="detail-box-body">
            <a href="{{ route('tour.detail', ['id' => $booking->tour->tour_id]) }}">
              <div class="tour-info">
                <img src="{{ asset('images/' . $booking->tour->image_main) }}"
                  alt="{{ $booking->tour->tour_name }}"
                  class="tour-image">
                <div class="tour-info-text">
                  <h3>{{ $booking->tour->tour_name }}</h3>
                  <p>Mã tour: {{ $booking->tour->tour_id }}</p>
                  <p>Khởi hành: <strong>{{ $booking->schedule->start_date?->format('d/m/Y') }}</strong></p>
                  <p>Ngày về: <strong>{{ $booking->schedule->end_date?->format('d/m/Y') }}</strong></p>
                  <p>Phương tiện: <strong> @if ($booking->tour->transport === 'Máy bay')
                      <i class="fa fa-plane"></i>
                      @else
                      <i class="fa fa-bus"></i>
                      @endif</strong></p>
                </div>
              </div>
            </a>
          </div>
        </div>

        {{-- Thông tin Liên hệ --}}
        <div class="detail-box">
          <div class="detail-box-header">Thông tin Liên hệ (Người đặt)</div>
          <div class="detail-box-body">
            <div class="detail-grid">
              <div class="detail-item"><span>Họ và tên:</span><span>{{ $booking->full_name }}</span></div>
              <div class="detail-item"><span>Số điện thoại:</span><span>{{ $booking->phone_number }}</span></div>
              <div class="detail-item"><span>Email:</span><span>{{ $booking->email }}</span></div>
              <div class="detail-item"><span>Địa chỉ:</span><span>{{ $booking->address }}</span></div>
            </div>
          </div>
        </div>

        {{-- Chi tiết Thanh toán --}}
        <div class="detail-box">
          <div class="detail-box-header">Chi tiết Thanh toán</div>
          <div class="detail-box-body">
            <div class="detail-item">
              <span>Người lớn:</span>
              <span>{{ $booking->adult_quantity }} x {{ number_format($booking->price_adult_at_booking, 0, ',', '.') }} đ</span>
            </div>
            <div class="detail-item">
              <span>Trẻ em:</span>
              <span>{{ $booking->child_quantity }} x {{ number_format($booking->price_child_at_booking, 0, ',', '.') }} đ</span>
            </div>
            <div class="detail-item total-line">
              <span class="total-price-label">Tổng cộng:</span>
              <span class="total-price-value">{{ number_format($booking->total_price, 0, ',', '.') }} đ</span>
            </div>

            <hr class="divider">

            <div class="detail-item"><span>Phương thức:</span><span>{{ $booking->payment->payment_method }}</span></div>
            <div class="detail-item"><span>Mã giao dịch:</span><span>{{ $booking->payment->transaction_id ?? 'N/A' }}</span></div>
            <div class="detail-item"><span>Ngày đặt:</span><span>{{ $booking->created_at->format('d/m/Y H:i:s') }}</span></div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection