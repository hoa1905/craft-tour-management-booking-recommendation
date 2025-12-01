@extends('layouts.app')

@section('title', 'Chi tiết tour theo yêu cầu')

@section('content')
<div class="container" style="margin-top: 30px; margin-bottom: 50px;">
  @if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="row">
    <div class="col-md-10 col-md-offset-1">
      <div class="panel panel-info">
        <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
          {{-- 1. Tiêu đề --}}
          <h3 class="panel-title" style="font-size: 18px; font-weight: bold; margin: 0; text-transform: uppercase;">
            <i class="fa fa-file-text-o"></i> CHI TIẾT ĐẶT TOUR THEO YÊU CẦU
          </h3>

          <div style="display: flex; align-items: center;">
            {{-- Nút Sửa (Chỉ hiện khi pending) --}}
            @if($request->status == 'pending')
            <a href="{{ route('tour_request.edit', ['id' => $request->request_id]) }}"
              class="btn btn-warning btn-sm"
              title="Chỉnh sửa thông tin"
              style="margin-right: 10px; font-weight: 600;">
              <i class="fa fa-edit"></i> Chỉnh sửa
            </a>
            @endif

            {{-- Nhãn trạng thái --}}
            <span class="label {{ $request->status_class }}" style="font-size: 100%; padding: 6px 12px; display: inline-block;">
              {{ $request->status_label }}
            </span>
          </div>
        </div>

        <div class="panel-body">
          {{-- Kiểm tra trạng thái chốt và thanh toán --}}
          @if($request->status == 'accepted')
          {{-- Trường hợp 1: Đã chốt nhưng chưa tạo Booking (Chưa thanh toán) --}}
          <div class="alert alert-success text-center">
            <h4><i class="fa fa-check-circle"></i> Yêu cầu đã được chấp nhận!</h4>
            <p>HanhTrinhVang đã chốt lịch trình với mức giá: <strong>{{ number_format($request->final_total_price, 0, ',', '.') }} đ</strong></p>
            <p>Tiến hành thanh toán để hoàn tất việc đặt tour.</p>
            <a href="{{ route('tour_request.checkout', ['id' => $request->request_id]) }}" class="btn btn-success btn-lg" style="margin-top: 10px;">
              <i class="fa fa-credit-card"></i> Thanh toán
            </a>
          </div>

          @elseif($request->status == 'completed')
          {{-- Trường hợp 2: Đã tạo Booking (Đã bấm thanh toán) --}}
          <div class="alert alert-info text-center">
            <h4><i class="fa fa-info-circle"></i> Đã tạo đơn đặt tour</h4>

            {{-- Kiểm tra booking liên quan --}}
            @if($request->booking)
            <p>Trạng thái hiện tại:
              <span class="label label-default" style="font-size: 100%;">
                @if($request->booking->payment && $request->booking->payment->status == 'success')
                <i class="fa fa-check"></i> Đã thanh toán
                @else
                <i class="fa fa-clock-o"></i> Chờ thanh toán
                @endif
              </span>
            </p>
            <a href="{{ route('account.myBookings.detail', $request->booking->booking_id) }}" class="btn btn-info" style="margin-top: 5px;">
              <i class="fa fa-eye"></i> Xem chi tiết đơn đặt tour - Mã đặt tour theo yêu cầu: {{ $request->booking->booking_id }}
            </a>
            @else
            <p>Đơn hàng đang được xử lý.</p>
            @endif
          </div>
          @endif

          {{-- Chỉ hiện khi admin đã nhập giá --}}
          @if($request->final_total_price > 0)
          <div class="panel panel-success" style="border-color: #d6e9c6;">
            <div class="panel-heading" style="background-color: #dff0d8; color: #3c763d;">
              <h3 class="panel-title"><i class="fa fa-money"></i> <strong>BÁO GIÁ CHI TIẾT TỪ HANHTRINHVANG</strong></h3>
            </div>
            <div class="panel-body">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr class="active">
                    <th>Đối tượng</th>
                    <th class="text-center">Số lượng</th>
                    <th class="text-right">Đơn giá chốt</th>
                    <th class="text-right">Thành tiền</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><strong>Người lớn</strong></td>
                    <td class="text-center">{{ $request->adult_quantity }}</td>
                    <td class="text-right">{{ number_format($request->price_adult_final, 0, ',', '.') }} đ</td>
                    <td class="text-right">{{ number_format($request->adult_quantity * $request->price_adult_final, 0, ',', '.') }} đ</td>
                  </tr>
                  @if($request->child_quantity > 0)
                  <tr>
                    <td><strong>Trẻ em</strong></td>
                    <td class="text-center">{{ $request->child_quantity }}</td>
                    <td class="text-right">{{ number_format($request->price_child_final, 0, ',', '.') }} đ</td>
                    <td class="text-right">{{ number_format($request->child_quantity * $request->price_child_final, 0, ',', '.') }} đ</td>
                  </tr>
                  @endif
                  <tr class="warning">
                    <td colspan="3" class="text-right text-danger"><strong>TỔNG CỘNG:</strong></td>
                    <td class="text-right text-danger" style="font-size: 18px; font-weight: bold;">
                      {{ number_format($request->final_total_price, 0, ',', '.') }} đ
                    </td>
                  </tr>
                </tbody>
              </table>

              @if($request->status == 'offering')
              <div class="alert alert-warning text-center">
                <p>Đây là báo giá cuối cùng dựa trên yêu cầu của bạn. Nếu đồng ý, vui lòng liên hệ:</p>

                {{-- Làm nổi bật số điện thoại --}}
                <a href="tel:0123456789" style="font-size: 24px; font-weight: bold; color: #d9534f; text-decoration: none; display: block; margin-top: 5px;">
                  <i class="fa fa-phone-square"></i> 0123 456 789
                </a>

                <p class="small" style="margin-top: 5px;">(Để nhân viên xác nhận và chốt đơn cho bạn)</p>
              </div>
              @endif
            </div>
          </div>
          @endif

          {{-- Phản hồi từ Quản trị viên --}}
          @if($request->admin_note)
          {{-- Trường hợp 1: Có ghi chú từ Admin -> Hiện ghi chú --}}
          <div class="panel panel-success">
            <div class="panel-heading">
              <h3 class="panel-title" style="font-size: 17px"><i class="fa fa-commenting"></i> Phản hồi từ HanhTrinhVang</h3>
            </div>
            <div class="panel-body">
              <p style="font-size: 15px;">{{ $request->admin_note }}</p>
            </div>
          </div>

          @elseif($request->status == 'canceled')
          {{-- Trường hợp 2: Đã hủy -> Hiện thông báo hủy (Hoặc để trống nếu muốn ẩn hoàn toàn) --}}
          <div class="alert alert-danger">
            <i class="fa fa-ban"></i> Yêu cầu này đã bị hủy.
          </div>

          @else
          {{-- Trường hợp 3: Chưa có ghi chú & Chưa hủy -> Hiện đang xử lý --}}
          <div class="alert alert-info">
            <i class="fa fa-spinner fa-spin"></i> Yêu cầu đang được xử lý. HanhTrinhVang sẽ sớm phản hồi lại cho bạn.
          </div>
          @endif

          {{-- Thông tin người liên hệ --}}
          <h4 class="text-primary" style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: 0; font-size: 17px">
            <i class="fa fa-user"></i> Thông tin người liên hệ
          </h4>
          <div class="row">
            <div class="col-md-4">
              <p><strong>Họ tên:</strong> {{ $request->full_name }}</p>
            </div>
            <div class="col-md-4">
              <p><strong>Số điện thoại:</strong> {{ $request->phone_number }}</p>
            </div>
            <div class="col-md-4">
              <p><strong>Email:</strong> {{ $request->email }}</p>
            </div>
            <div class="col-md-12">
              <p class="text-muted"><small><i class="fa fa-clock-o"></i> Ngày gửi yêu cầu: {{ $request->created_at->format('d/m/Y H:i') }}</small></p>
            </div>
          </div>

          <br>

          {{-- Thông tin lộ trình --}}
          <h4 class="text-primary" style="border-bottom: 1px solid #eee; padding-bottom: 10px; font-size: 17px">
            <i class="fa fa-map-marker"></i> Thông tin lộ trình
          </h4>
          <div class="row">
            <div class="col-md-6">
              <p><strong><i class="fa fa-map-marker text-danger"></i> Điểm khởi hành:</strong> {{ $request->departure_point }}</p>
              <p><strong><i class="fa fa-flag-checkered text-success"></i> Điểm đến dự kiến:</strong> {{ $request->desired_destination }}</p>
            </div>
            <div class="col-md-6">
              <p><strong><i class="fa fa-calendar"></i> Ngày đi:</strong> {{ $request->start_date->format('d/m/Y') }}</p>
              <p><strong><i class="fa fa-calendar-check-o"></i> Ngày về:</strong> {{ $request->end_date->format('d/m/Y') }}</p>
              {{-- Tính toán số ngày --}}
              @php
              $days = $request->start_date->diffInDays($request->end_date) + 1;
              @endphp
              <p class="text-info"><em>(Thời gian dự kiến: {{ $days }} ngày {{ $days > 1 ? $days - 1 : 0 }} đêm)</em></p>
            </div>
          </div>

          <br>

          {{-- Điểm tham quan và Trải nghiệm --}}
          <h4 class="text-primary" style="border-bottom: 1px solid #eee; padding-bottom: 10px; font-size: 17px">
            <i class="fa fa-camera"></i> Điểm tham quan và Trải nghiệm
          </h4>

          <div class="well bg-white">
            @if($selectedVillages->count() > 0)
            {{-- logic nhóm theo tỉnh --}}
            @php
            $groupedVillages = $selectedVillages->groupBy(function($item) {
            return $item->province->province_name ?? 'Chưa xác định';
            });
            @endphp

            <label style="margin-bottom: 15px;">Các làng nghề đã chọn:</label>
            <div class="row">
              @foreach($groupedVillages as $provinceName => $villages)
              <div class="col-md-6" style="margin-bottom: 15px;">
                <div style="border-left: 3px solid #337ab7; padding-left: 10px;">
                  <strong style="font-size: 15px; color: #333;">
                    <i class="fa fa-map"></i> {{ $provinceName }}
                  </strong>
                  <ul style="margin-top: 5px; padding-left: 20px;">
                    @foreach($villages as $village)
                    <li>
                      <a href="{{ route('villages.detail', ['id' => $village->village_id]) }}" target="_blank" style="color: #555; font-weight: 500;">
                        {{ $village->village_name }}
                      </a>
                    </li>
                    @endforeach
                  </ul>
                </div>
              </div>
              @endforeach
            </div>
            @else
            <p class="text-muted"><em>Khách hàng chưa chọn làng nghề cụ thể nào.</em></p>
            @endif
          </div>

          {{-- Workshop --}}
          <div class="form-group">
            <label style="color: #d35400;"><i class="fa fa-cogs"></i> Hoạt động trải nghiệm / Workshop mong muốn:</label>
            @if($request->workshop)
            <div class="alert alert-warning" style="margin-bottom: 0; background-color: #fcf8e3; border-color: #faebcc; color: #8a6d3b;">
              {{ $request->workshop }}
            </div>
            @else
            <div class="alert alert-warning" style="margin-bottom: 0; background-color: #fcf8e3; border-color: #faebcc; color: #8a6d3b;">
              Không có yêu cầu cụ thể.
            </div>
            @endif
          </div>

          <br>

          {{-- Ngân sách và Dịch vụ --}}
          <h4 class="text-primary" style="border-bottom: 1px solid #eee; padding-bottom: 10px; font-size: 17px">
            <i class="fa fa-credit-card"></i> Ngân sách và Dịch vụ
          </h4>
          <div class="row">
            <div class="col-md-4">
              <p><strong>Số lượng khách:</strong></p>
              <ul class="list-unstyled">
                <li><i class="fa fa-male" style="width:20px"></i> Người lớn: <strong>{{ $request->adult_quantity }}</strong></li>
                <li><i class="fa fa-child" style="width:20px"></i> Trẻ em: <strong>{{ $request->child_quantity }}</strong></li>
              </ul>
            </div>
            <div class="col-md-4">
              <p><strong>Phương tiện di chuyển:</strong></p>
              <p><span class="label label-default" style="font-size: 100%;"> {{ $request->transportation }}</span></p>
            </div>
            <div class="col-md-4">
              <p><strong>Ngân sách dự kiến (người):</strong></p>
              <p class="text-danger" style="font-weight: bold; font-size: 16px;">
                @if($request->budget_min || $request->budget_max)
                {{ $request->budget_min ? number_format($request->budget_min, 0, ',', '.') : '0' }} đ
                -
                {{ $request->budget_max ? number_format($request->budget_max, 0, ',', '.') : '...' }} đ
                @else
                Chưa xác định
                @endif
              </p>
            </div>
          </div>

          <div class="form-group" style="margin-top: 15px;">
            <label>Ghi chú thêm (Ăn uống, khách sạn...):</label>
            @if($request->requirements)
            <div class="well well-sm">
              {{ $request->requirements }}
            </div>
            @else
            <div class="well well-sm">
              Không có yêu cầu cụ thể.
            </div>
            @endif
          </div>

          <hr style="border-top: 2px dashed #ddd;">
        </div>

        @if($request->status == 'pending')
        <div class="panel-footer text-right">
          <form action="{{ route('tour_request.cancel', $request->request_id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn hủy yêu cầu này?');">
            @csrf
            <button type="submit" class="btn btn-danger">
              <i class="fa fa-times"></i> Hủy yêu cầu
            </button>
          </form>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection