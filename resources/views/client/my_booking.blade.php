@extends('layouts.app')

@section('title', 'Đơn đặt tour của bạn')

@section('head')
<link rel="stylesheet" href="{{ asset('css/client/my-booking.css') }}">
@endsection

@section('content')
<div id="vnt-content">
  @if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if (session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="vhmemberpg">
    <div class="mbpadd">
      <div class="wrapper">
        <div class="hpmemberbb">
          <div class="vcontsmb">
            <div class="tpmemberbb">
              <div class="tpmemberbm">
                <div class="bmtitle hidden-md hidden-lg">Tất cả</div>
                <div class="bmconts">
                  <ul id="statusTabs">
                    <li class="{{ $status == 'all' ? 'active' : '' }}"><a href="#" data-status="all">Tất cả</a></li>
                    <li class="{{ $status == 'waiting_payment' ? 'active' : '' }}"><a href="#" data-status="waiting_payment">Chờ thanh toán</a></li>
                    <li class="{{ $status == 'paid' ? 'active' : '' }}"><a href="#" data-status="paid">Đã thanh toán</a></li>
                    <li class="{{ $status == 'active' ? 'active' : '' }}"><a href="#" data-status="active">Đang hoạt động</a></li>
                    <li class="{{ $status == 'completed' ? 'active' : '' }}"><a href="#" data-status="completed">Hoàn thành</a></li>
                    <li class="{{ $status == 'canceled' ? 'active' : '' }}"><a href="#" data-status="canceled">Đã hủy</a></li>
                    <li class="{{ $status == 'pending_refund' ? 'active' : '' }}"><a href="#" data-status="pending_refund">Chờ hoàn tiền</a></li>
                    <li class="{{ $status == 'refunded' ? 'active' : '' }}"><a href="#" data-status="refunded">Đã hoàn tiền</a></li>
                  </ul>
                </div>
              </div>

              <div class="tpmemberbh">
                <div class="bhlist" id="bookingList">
                  @if ($bookings->isEmpty())
                  <div style="text-align:center; padding:40px 0;">Chưa có đơn hàng trong trạng thái này.</div>
                  @else
                  @foreach ($bookings as $booking)
                  <a href="{{ route('account.myBookings.detail', $booking->booking_id) }}">
                    <div class="itmemberbh vborder">
                      <div class="bhgrip">
                        <div class="mcol">
                          <div class="vthumb">
                            <img src="{{ asset('images/client/' . $booking->tour->image_main) }}" alt="Ảnh tour">
                          </div>
                        </div>
                        <div class="hcol">
                          <div class="vdecss">
                            <div class="dstitle">
                              <h3>{{ $booking->tour->tour_name }}</h3>
                            </div>
                            <div class="dscodes">Mã tour: {{ $booking->tour->tour_id }}</div>
                          </div>
                        </div>
                        <div class="bcol">
                          <div class="vdecss">
                            <div class="dsinfos">
                              <ul>
                                <li>
                                  <div class="tm">Ngày đi:</div>
                                  <div class="th">{{ $booking->schedule->start_date->format('d/m/Y') }}</div>
                                </li>
                                <li>
                                  <div class="tm">Ngày về:</div>
                                  <div class="th">{{ $booking->schedule->end_date->format('d/m/Y') }}</div>
                                </li>
                                <li>
                                  <div class="tm">Số khách:</div>
                                  <div class="th">
                                    {{ $booking->adult_quantity }} người lớn - {{ $booking->child_quantity }} trẻ em
                                  </div>
                                </li>
                                <li>
                                  <div class="tm">Tổng cộng:</div>
                                  <div class="th vcolor"><strong>{{ number_format($booking->total_price, 0, ',', '.') }} đ</strong></div>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </div>
                        <div class="tcol">
                          <div class="vdecss dright">
                            <div class="dstends">
                              <ul>
                                <li><strong>Mã đơn:</strong> <strong class="vcolor">{{ $booking->booking_id }}</strong></li>
                                <li><strong>Ngày đặt tour:</strong> {{ $booking->created_at->format('d/m/Y') }}</li>
                              </ul>
                            </div>
                            <div class="dslinks">
                              <div class="vstatus" style="border:1px solid #ec1d25;color:#ec1d25;">
                                @if ($booking->payment && $booking->payment->status === 'success')
                                Đã thanh toán
                                @elseif ($booking->payment && $booking->payment->status === 'waiting_payment')
                                Chờ thanh toán
                                @elseif ($booking->status === 'completed')
                                Hoàn thành
                                @elseif ($booking->status === 'active')
                                Đang hoạt động
                                @elseif ($booking->status === 'canceled')
                                Đã hủy
                                @elseif ($booking->payment && $booking->payment->status === 'refunded')
                                Đã hoàn tiền
                                @else
                                Đang xử lý
                                @endif
                              </div>

                              {{-- NÚT HỦY TOUR --}}
                              @if ($booking->is_cancelable)
                              {{-- Xác định xem tour đã trả tiền chưa để hiện cảnh báo --}}
                              @php
                              $isPaid = $booking->payment?->status === 'success';
                              $confirmMessage = $isPaid
                              ? 'Tour này đã thanh toán. Hủy tour sẽ bắt đầu quy trình HOÀN TIỀN theo chính sách. Bạn có chắc chắn?'
                              : 'Bạn có chắc chắn muốn hủy đơn đặt tour này?';
                              @endphp

                              <form action="{{ route('account.myBookings.cancel', $booking->booking_id) }}" method="POST" onsubmit="return confirm('{{ $confirmMessage }}');" style="margin-top: 10px; text-align: right;">
                                @csrf
                                @method('POST') {{-- Hoặc 'DELETE' nếu bạn muốn dùng (phải đổi cả route) --}}
                                <button type="submit" class="btn-cancel" style="background: #ec1d25; color: #fff; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px;">
                                  Hủy Tour
                                </button>
                              </form>
                              @endif
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </a>
                  @endforeach
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.querySelectorAll('#statusTabs a').forEach(tab => {
    tab.addEventListener('click', function(e) {
      e.preventDefault();
      const status = this.getAttribute('data-status');
      const statusText = this.textContent; // Lấy text của tab được click ("Tất cả", "Đã hoàn thành",...)
      fetch(`{{ route('account.myBookings') }}?status=${status}`, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(res => res.text())
        .then(html => {
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, 'text/html');
          const list = doc.querySelector('#bookingList').innerHTML;
          document.querySelector('#bookingList').innerHTML = list;

          // Cập nhật trạng thái active
          document.querySelectorAll('#statusTabs li').forEach(li => li.classList.remove('active'));
          this.parentElement.classList.add('active');

          // Cập nhật text
          document.querySelector('.tpmemberbm .bmtitle').textContent = statusText;

          // Đóng dropdown sau khi chọn
          document.querySelector('.tpmemberbm').classList.remove('active')

          // Cập nhật URL trên thanh địa chỉ
          history.pushState({}, '', `?status=${status}`)
        });
    });
  });

  // Toggle dropdown khi click vào bmtitle
  document.querySelector('.tpmemberbm .bmtitle')?.addEventListener('click', function() {
    this.parentElement.classList.toggle('active');
  });
</script>
@endsection