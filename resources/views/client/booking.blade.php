@extends('layouts.app')

@section('title', 'Đơn đặt tour')

@section('head')
<link rel="stylesheet" href="{{ asset('css/client/booking.css') }}">
@endsection

@section('content')
<div id="vnt-content">
  @if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if (session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  <div class="box_mid">
    <div class="wrapper">
      <div class="mid-content">
        <div class="row cel-p10">
          <div class="col-md-4 col-sm-12 col-xs-12">
            <div class="box-detail boxCart">
              <div class="hTitle">Đơn đặt tour</div>
              <div class="box-info-item">
                <div class="tDetal">
                  <div class="tImg">
                    <img src="{{ asset('images/tours/' . $bookingData['tour_image']) }}" alt="Tour Image">
                  </div>
                  <div class="tTitle">{{ $bookingData['tour_name'] }}</div>
                  <div class="clear"></div>
                </div>
              </div>
              <div class="box-date-item">
                <strong>Ngày đi:</strong> {{ $bookingData['start_date'] }} -
                <strong>Ngày về:</strong> {{ $bookingData['end_date'] }}
              </div>

              {{-- Ô số lượng người lớn và trẻ em --}}
              <div class="box-quan-item">
                {{-- Người lớn --}}
                <div class="qitem">
                  <div class="vvgrip">
                    <div class="mcol">
                      <div class="vtext">
                        <div class="tm">Người lớn</div>
                        <div class="th">>10 tuổi</div>
                      </div>
                    </div>
                    <div class="hcol">
                      <div class="vquan">
                        <div class="iquan q-adult">
                          <button type="button" class="btn-down"> <i class="fa fa-minus"></i></button>
                          <input type="number" min="1" class="ipt-qua adult_quantity" value="{{ $bookingData['adult_quantity'] }}">
                          <button type="button" class="btn-up"><i class="fa fa-plus"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="bcol">
                      <div class="cremo">
                        <div class="rnew i-price" data-price="{{ $bookingData['price_adult_at_booking'] }}">
                          {{ number_format($bookingData['price_adult_at_booking'], 0, ',', '.') }} đ
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Trẻ em --}}
                <div class="qitem">
                  <div class="vvgrip">
                    <div class="mcol">
                      <div class="vtext">
                        <div class="tm">Trẻ em</div>
                        <div class="th">2 - 10 tuổi</div>
                      </div>
                    </div>
                    <div class="hcol">
                      <div class="vquan">
                        <div class="iquan q-child">
                          <button type="button" class="btn-down"> <i class="fa fa-minus"></i></button>
                          <input type="number" min="0" class="ipt-qua child_quantity" value="{{ $bookingData['child_quantity'] }}">
                          <button type="button" class="btn-up"><i class="fa fa-plus"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="bcol">
                      <div class="cremo">
                        <div class="rnew i-price_child" data-price="{{ $bookingData['price_child_at_booking'] }}">
                          {{ number_format($bookingData['price_child_at_booking'], 0, ',', '.') }} đ
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="qitem">
                  <div class="box-info-item">
                    <div class="text-large">
                      <div class="fl">Tổng cộng</div>
                      <div class="fr">
                        <strong class="color4 total-price" id="ext_total_price">
                          {{ number_format($bookingData['total_price'], 0, ',', '.') }} đ
                        </strong>
                      </div>
                      <div class="clear"></div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>

          {{-- Form thông tin --}}
          <div class="col-md-8 col-sm-12 col-xs-12">
            <form action="" method="post" name="fBooking" id="fBooking" class="validate">
              @csrf

              {{-- Thêm thông tin tour bằng input hidden, được gửi đi khi nhấn nút thanh toán --}}
              <input type="hidden" name="tour_id" value="{{ $bookingData['tour_id'] }}">
              <input type="hidden" name="schedule_id" value="{{ $bookingData['schedule_id'] ?? null }}">
              <input type="hidden" name="adult_quantity" class="adult_quantity_hidden" value="{{ $bookingData['adult_quantity'] }}">
              <input type="hidden" name="child_quantity" class="child_quantity_hidden" value="{{ $bookingData['child_quantity'] }}">
              <input type="hidden" name="price_adult_at_booking" class="price_adult_at_booking_hidden" value="{{ $bookingData['price_adult_at_booking'] }}">
              <input type="hidden" name="price_child_at_booking" class="price_child_at_booking_hidden" value="{{ $bookingData['price_child_at_booking'] }}">
              <input type="hidden" name="total_price" id="hidden_total_price" value="{{ $bookingData['total_price'] }}">

              {{-- ... (Phần còn lại của form: Họ tên, SĐT, Email, Địa chỉ) --}}
              <div class="box-detail">
                <div class="hTitle large">Thông tin đặt tour</div>
                <div class="mb15">
                  <label>Họ và tên <span class="color1">*</span></label>
                  <div class="div_input">
                    <input type="text" class="form-control required" id="full_name" name="full_name"
                      value="{{ Auth::guard('client')->user()->user_name }}" />
                  </div>
                </div>
                <div class="row cel-p10">
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="mb15">
                      <label>Số điện thoại <span class="color1">*</span></label>
                      <div class="div_input">
                        <input type="tel" class="form-control required"
                          id="phone"
                          name="phone"
                          inputmode="numeric"
                          pattern="[0-9]+"
                          oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                          value="{{ Auth::guard('client')->user()->phone_number }}"
                          maxlength="10" />
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="mb15">
                      <label>Email <span class="color1">*</span></label>
                      <div class="div_input">
                        <input type="email" class="form-control required" id="email" name="email"
                          value="{{ Auth::guard('client')->user()->email }}" />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mb15">
                  <label>Địa chỉ <span class="color1">*</span></label>
                  <div class="div_input">
                    <input type="text" class="form-control" id="address" name="address"
                      value="{{ Auth::guard('client')->user()->address }}" />
                  </div>
                </div>

                <!-- Phương thức thanh toán -->
                <div class="mb-4">
                  <label class="fw-bold d-block mb-2">Phương thức thanh toán <span class="color1">*</span></label>

                  <div class="payment-options d-flex flex-wrap gap-3">
                    <label class="payment-option">
                      <input type="radio" name="payment_method" value="office" checked>
                      <div class="option-box">
                        <i class="fa fa-building me-2"></i> Thanh toán tại văn phòng
                      </div>
                    </label>

                    <label class="payment-option">
                      <input type="radio" name="payment_method" value="paypal">
                      <div class="option-box">
                        <img src="{{ asset('images/client/paypal-logo.png') }}" alt="Paypal" width="20" height="20" class="me-2"> PayPal
                      </div>
                    </label>

                    <label class="payment-option">
                      <input type="radio" name="payment_method" value="vnpay">
                      <div class="option-box">
                        <img src="{{ asset('images/client/vnpay-logo.png') }}" alt="VNPAY" width="20" height="20" class="me-2"> VNPAY
                      </div>
                    </label>
                  </div>
                </div>

                <!-- Nút hành động -->
                <div class="text-center mt-4">
                  <button type="button" id="btnOffice" class="btn btn-primary px-4 py-2">
                    <i class="fa fa-building me-2"></i> Xác nhận đặt tour
                  </button>

                  <button type="button" id="btnPaypal" class="btn btn-warning px-4 py-2 hidden mt-2">
                    <img src="{{ asset('images/client/paypal-logo.png') }}" alt="PayPal" width="20" height="20" class="me-2">
                    Thanh toán bằng PayPal
                  </button>

                  <button type="button" id="btnVnpay" class="btn btn-vnpay px-4 py-2 hidden mt-2">
                    <img src="{{ asset('images/client/vnpay-logo.png') }}" alt="VNpay" width="20" height="20" class="me-2">
                    Thanh toán bằng VNPAY
                  </button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/client/booking.js') }}"></script>
@endsection