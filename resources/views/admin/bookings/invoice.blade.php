<!DOCTYPE html>
<html lang="vi">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Hóa đơn đặt tour</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      font-family: "DejaVu Sans", sans-serif;
      font-size: 13px;
      color: #333;
    }

    table {
      width: 100% !important;
      border-collapse: collapse;
    }

    .w-100 {
      width: 100%;
    }

    .w-50 {
      width: 50%;
    }

    .invoice-header {
      border-bottom: 2px solid #0d6efd;
      margin-bottom: 20px;
      padding-bottom: 20px;
    }

    .invoice-title {
      font-size: 24px;
      font-weight: bold;
      color: #0d6efd;
      text-transform: uppercase;
    }

    .company-name {
      font-size: 20px;
      font-weight: bold;
      color: #333;
    }

    .table-info td {
      padding: 5px 0;
      vertical-align: top;
    }

    .custom-table {
      width: 100% !important;
      margin-top: 10px;
    }

    .custom-table thead th {
      background-color: #0d6efd !important;
      color: #ffffff !important;
      border: none;
      padding: 10px;
      text-align: center;
    }

    .custom-table td {
      border-bottom: 1px solid #dee2e6;
      padding: 10px;
    }

    .text-end {
      text-align: right;
    }

    .text-center {
      text-align: center;
    }

    .text-start {
      text-align: left;
    }

    .fw-bold {
      font-weight: bold;
    }

    .text-danger {
      color: #dc3545 !important;
    }

    .text-success {
      color: #198754 !important;
    }

    .text-warning {
      color: #ffc107 !important;
    }

    .mt-4 {
      margin-top: 20px;
    }

    .mb-2 {
      margin-bottom: 8px;
    }

    .signature-section {
      margin-top: 40px;
    }

    .invoice-footer {
      text-align: center;
      margin-top: 50px;
      padding-top: 15px;
      font-size: 11px;
      color: #777;
      font-style: italic;
      border-top: 1px solid #cccccc;
    }
  </style>
</head>

<body>
  <div class="container-fluid p-0">
    <table class="w-100 invoice-header">
      <tr>
        <td class="w-50">
          <div class="company-name">HÀNH TRÌNH VÀNG</div>
          <div>Địa chỉ: 123 Đường ABC, TP. Đà Nẵng</div>
          <div>Hotline: 0123 456 789</div>
        </td>
        <td class="w-50 text-end">
          <div class="invoice-title">HÓA ĐƠN</div>
          <div><strong>Mã đơn:</strong> {{ $booking->booking_id }}</div>
          <div><strong>Ngày đặt tour:</strong> {{ $booking->created_at->format('d/m/Y') }}</div>
        </td>
      </tr>
    </table>

    <table class="w-100 mb-4 table-info">
      <tr>
        <td class="w-50" style="padding-right: 15px;">
          <div class="p-3 bg-light rounded border">
            <h4 class="fw-bold text-primary mb-2">THÔNG TIN KHÁCH HÀNG</h4>
            <div><strong>Họ tên:</strong> {{ $booking->full_name }}</div>
            <div><strong>SĐT:</strong> {{ $booking->phone_number }}</div>
            <div><strong>Email:</strong> {{ $booking->email }}</div>
            <div><strong>Địa chỉ:</strong> {{ $booking->address }}</div>
          </div>
        </td>
        <td class="w-50" style="padding-left: 15px;">
          <div class="p-3 bg-light rounded border">
            <h4 class="fw-bold text-primary mb-2">THÔNG TIN TOUR</h4>
            {{-- Tên tour sử dụng Accessor $booking->tour_name (An toàn cho cả 2 loại tour) --}}
            <div><strong>Tour:</strong> {{ $booking->tour_name }}</div>

            {{-- 2. XỬ LÝ NGÀY & ĐỊA ĐIỂM LINH HOẠT --}}
            @php
            if ($booking->schedule) {
            $startDate = $booking->schedule->start_date;
            $endDate = $booking->schedule->end_date;
            } elseif ($booking->tourRequest) {
            $startDate = $booking->tourRequest->start_date;
            $endDate = $booking->tourRequest->end_date;
            } else {
            $startDate = null; $endDate = null;
            }

            $departure = $booking->tour ? $booking->tour->departure_point : ($booking->tourRequest->departure_point ?? 'N/A');
            $destination = $booking->tour ? $booking->tour->destination : ($booking->tourRequest->desired_destination ?? 'N/A');
            @endphp

            <div><strong>Thời gian:</strong> {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : '---' }}
              -
              {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : '---' }}
            </div>
            <div><strong>Điểm khởi hành:</strong> {{ $departure }}</div>
            <div><strong>Điểm đến:</strong> {{ $destination }}</div>
          </div>
        </td>
      </tr>
    </table>

    <h4 class="fw-bold mt-4">CHI TIẾT THANH TOÁN</h4>
    <table class="table custom-table" style="width: 100%; border-collapse: collapse;">
      <thead>
        <tr>
          <th class="text-center">Vé</th>
          <th class="text-center">Số lượng</th>
          <th class="text-center">Đơn giá</th>
          <th class="text-center">Thành tiền</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="text-center">Vé Người lớn</td>
          <td class="text-center">{{ $booking->adult_quantity }}</td>
          <td class="text-center">{{ number_format($booking->price_adult_at_booking, 0, ',', '.') }}₫</td>
          <td class="text-center fw-bold">{{ number_format($booking->adult_quantity * $booking->price_adult_at_booking, 0, ',', '.') }}₫</td>
        </tr>
        @if($booking->child_quantity > 0)
        <tr>
          <td>Vé Trẻ em</td>
          <td class="text-center">{{ $booking->child_quantity }}</td>
          <td class="text-center">{{ number_format($booking->price_child_at_booking, 0, ',', '.') }}₫</td>
          <td class="text-center fw-bold">{{ number_format($booking->child_quantity * $booking->price_child_at_booking, 0, ',', '.') }}₫</td>
        </tr>
        @endif
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" class="text-end fw-bold text-danger pt-3">TỔNG CỘNG:</td>
          <td class="text-end fw-bold text-danger pt-3 fs-5" style="font-size: 16px;">
            {{ number_format($booking->total_price, 0, ',', '.') }}₫
          </td>
        </tr>
      </tfoot>
    </table>

    <table class="w-100 signature-section">
      <tr>
        <td class="w-50" style="padding-right: 20px;">
          <div style="background-color: #f8f9fa; border: 1px solid #ddd; padding: 15px; border-radius: 5px;">
            <div><strong>Phương thức thanh toán:</strong> {{ $booking->payment ? $booking->payment->payment_method : 'Chưa thanh toán' }}</div>
            <div style="margin-top: 5px;">
              <strong>Trạng thái:</strong>
              @if($booking->status == 'paid' || $booking->status == 'active' || $booking->status == 'completed')
              <span class="text-success fw-bold">ĐÃ THANH TOÁN</span>
              @elseif($booking->status == 'canceled')
              <span class="text-danger fw-bold">ĐÃ HỦY</span>
              @else
              <span class="text-warning fw-bold">CHỜ THANH TOÁN</span>
              @endif
            </div>
          </div>
        </td>
        <td class="w-50 text-center">
          <div class="mb-5">
            <i>Ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}</i><br>
            <strong>Người lập phiếu</strong>
          </div>
          <br><br><br>
          <div>(Ký, họ tên)</div>
        </td>
      </tr>
    </table>

    <div class="invoice-footer">
      Cảm ơn quý khách đã sử dụng dịch vụ của chúng tôi! <br>
      Website: www.hanhtrinhvang.com
    </div>

  </div>
</body>

</html>