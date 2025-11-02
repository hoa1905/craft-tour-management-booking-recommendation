@extends('client.master')

@section('title', 'Chi tiết đơn đặt tour')

@section('head')
<link rel="stylesheet" href="{{ asset('css/my-booking-detail.css') }}" type="text/css">
@endsection

@section('body')
<div id="vnt-content">
  <div class="vhmemberpg">
    <div class="mbpadd">
      <div class="wrapper" style="max-width: 900px;">

        <h2 style="font-size: 24px; margin: 20px 0px;">
          Chi tiết Đơn đặt tour
          <span style="color: #ec1d25;">(Mã đơn: {{ $booking->booking_id }})</span>
        </h2>

        {{-- Lấy logic trạng thái từ my_booking --}}
        @php
        $statusText = 'Đang xử lý';
        $statusColor = '#ffc107'; // Vàng
        $statusBg = '#fff8e1';

        if ($booking->payment && $booking->payment->status === 'success') {
        $statusText = 'Đã thanh toán';
        $statusColor = '#28a745'; // Xanh lá
        $statusBg = '#eaf7ec';
        } elseif ($booking->payment && $booking->payment->status === 'pending') {
        $statusText = 'Chờ thanh toán';
        } elseif ($booking->status === 'completed') {
        $statusText = 'Hoàn thành';
        $statusColor = '#007bff'; // Xanh dương
        $statusBg = '#e6f2ff';
        } elseif ($booking->status === 'active') {
        $statusText = 'Đang hoạt động';
        $statusColor = '#17a2b8'; // Xanh lơ
        $statusBg = '#e8f6f8';
        } elseif ($booking->status === 'canceled') {
        $statusText = 'Đã hủy';
        $statusColor = '#6c757d'; // Xám
        $statusBg = '#f1f3f5';
        } elseif ($booking->payment && $booking->payment->status === 'refunded') {
        $statusText = 'Đã hoàn tiền';
        $statusColor = '#dc3545'; // Đỏ
        $statusBg = '#fbebec';
        }
        @endphp

        <div class="detail-box">
          <div class="detail-box-header">Trạng thái đơn hàng</div>
          <div class="detail-box-body" style="text-align: center;">
            <div class="status-badge" style="color: {{ $statusColor }}; background-color: {{ $statusBg }}; border: 1px solid {{ $statusColor }};">
              {{ $statusText }}
            </div>
            @if($booking->is_cancelable)
            <div style="margin-top: 15px; font-size: 14px; color: #555;">
              Đơn hàng này đủ điều kiện để Hủy.
              {{-- Form Hủy (tái sử dụng từ trang list) --}}
              @php
              $isPaid = $booking->payment?->status === 'success';
              $confirmMessage = $isPaid
              ? 'Hủy tour sẽ bắt đầu quy trình HOÀN TIỀN. Bạn có chắc chắn?'
              : 'Bạn có chắc chắn muốn hủy đơn đặt tour này?';
              @endphp
              <form action="{{ route('account.myBookings.cancel', $booking->booking_id) }}" method="POST" onsubmit="return confirm('{{ $confirmMessage }}');" style="margin-top: 10px;">
                @csrf
                @method('POST')
                <button type="submit" class="btn-cancel" style="background: #ec1d25; color: #fff; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px;">
                  Yêu cầu Hủy Tour
                </button>
              </form>
            </div>
            @endif
          </div>
        </div>

        <div class="detail-box">
          <div class="detail-box-header">Thông tin Tour</div>
          <div class="detail-box-body">
            <div style="display: flex; gap: 20px; align-items: center;">
              <img src="{{ asset('images/' . $booking->tour->image_main) }}" alt="{{ $booking->tour->tour_name }}" style="width: 150px; height: 100px; object-fit: cover; border-radius: 5px;">
              <div style="flex: 1;">
                <h3 style="margin: 0 0 5px 0; font-size: 18px;">{{ $booking->tour->tour_name }}</h3>
                <p style="margin: 0; color: #555;">Mã tour: {{ $booking->tour->tour_id }}</p>
                <p style="margin: 0; color: #555;">Khởi hành: <strong>{{ $booking->schedule->start_date?->format('d/m/Y') }}</strong></p>
                <p style="margin: 0; color: #555;">Ngày về: <strong>{{ $booking->schedule->end_date?->format('d/m/Y') }}</strong></p>
              </div>
            </div>
          </div>
        </div>

        <div class="detail-box">
          <div class="detail-box-header">Thông tin Liên hệ (Người đặt)</div>
          <div class="detail-box-body">
            <div class="detail-grid">
              <div class="detail-item">
                <span>Họ và tên:</span>
                <span>{{ $booking->full_name }}</span>
              </div>
              <div class="detail-item">
                <span>Số điện thoại:</span>
                <span>{{ $booking->phone_number }}</span>
              </div>
              <div class="detail-item">
                <span>Email:</span>
                <span>{{ $booking->email }}</span>
              </div>
              <div class="detail-item">
                <span>Địa chỉ:</span>
                <span>{{ $booking->address }}</span>
              </div>
            </div>
          </div>
        </div>

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
            <div class="detail-item" style="border-top: 1px solid #ccc; padding-top: 10px; margin-top: 10px;">
              <span class="total-price">Tổng cộng:</span>
              <span class="total-price">{{ number_format($booking->total_price, 0, ',', '.') }} đ</span>
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

            <div class="detail-item">
              <span>Phương thức:</span>
              {{-- Lấy từ bảng Payments --}}
              <span>{{ $booking->payment->payment_method}}</span>
            </div>
            <div class="detail-item">
              <span>Mã giao dịch:</span>
              {{-- Lấy từ bảng Payments --}}
              <span>{{ $booking->payment->transaction_id ?? 'N/A' }}</span>
            </div>
            <div class="detail-item">
              <span>Ngày đặt:</span>
              <span>{{ $booking->created_at->format('d/m/Y H:i:s') }}</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection