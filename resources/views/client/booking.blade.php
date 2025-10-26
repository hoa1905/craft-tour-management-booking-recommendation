@extends('client.master')

@section('title', 'Đơn đặt tour')
@section('body')
{{-- Đặt ở đâu đó gần đầu trang --}}

{{-- Thông báo Thành công --}}
@if (session('success'))
<div class="alert alert-success" role="alert" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
  {{ session('success') }}
</div>
@endif

{{-- Thông báo Lỗi --}}
@if (session('error'))
<div class="alert alert-danger" role="alert" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
  {{ session('error') }}
</div>
@endif
<div id="vnt-content">
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
                    <img src="{{ asset('images/' . $bookingData['tour_image']) }}" alt="Tour Image">
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

              {{-- ... (Phần còn lại của form: Họ tên, SĐT, Email...) --}}
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
                        <input type="tel" class="form-control required" id="phone" name="phone"
                          value="{{ Auth::guard('client')->user()->phone_number }}" maxlength="15" />
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
                  <div class="div_input"><input type="text" class="form-control" id="address" name="address" /></div>
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
                        <img src="{{ asset('images/paypal-logo.png') }}" alt="Paypal" width="20" height="20" class="me-2"> PayPal
                      </div>
                    </label>

                    <label class="payment-option">
                      <input type="radio" name="payment_method" value="momo">
                      <div class="option-box">
                        <img src="{{ asset('images/momo-logo.png') }}" alt="MoMo" width="20" height="20" class="me-2"> MoMo
                      </div>
                    </label>
                  </div>
                </div>

                <!-- Nút hành động -->
                <div class="text-center mt-4">
                  <button type="button" id="btnOffice" class="btn btn-primary px-4 py-2"><i class="fa fa-building me-2"></i> Xác nhận đặt tour
                  </button>

                  <button type="button" id="btnPaypal" class="btn btn-warning px-4 py-2 hidden mt-2">
                    <img src="{{ asset('images/paypal-logo.png') }}" alt="PayPal" width="20" height="20" class="me-2">
                    Thanh toán bằng PayPal
                  </button>

                  <button type="button" id="btnMomo" class="btn btn-pink px-4 py-2 hidden mt-2">
                    <img src="{{ asset('images/momo-logo.png') }}" alt="MoMo" width="20" height="20" class="me-2">
                    Thanh toán bằng MoMo
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
<style>
  .payment-options {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 30px;
  }

  .payment-option {
    flex: 1 1 30%;
    min-width: 200px;
    cursor: pointer;
    position: relative;
  }

  .payment-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
  }

  .option-box {
    border: 2px solid #ddd;
    border-radius: 10px;
    padding: 10px 15px;
    text-align: center;
    transition: all 0.3s ease;
    background-color: #f9f9f9;
    font-weight: 500;
  }

  .payment-option input[type="radio"]:checked+.option-box {
    border-color: #007bff;
    background-color: #e9f3ff;
    color: #007bff;
    box-shadow: 0 2px 6px rgba(0, 123, 255, 0.2);
  }

  .payment-option:hover .option-box {
    border-color: #007bff;
  }

  .is-invalid {
    border-color: red !important;
    background-color: #ffe6e6;
  }

  .btn-pink {
    background-color: #d82d8b !important;
    /* Màu hồng MoMo */
    color: #fff !important;
    border: none !important;
  }

  .btn-pink:hover {
    background-color: #b22574 !important;
  }

  @media (max-width: 768px) {
    .payment-option {
      flex: 1 1 100%;
    }
  }
</style>


<script>
  document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const adultInput = document.querySelector('.adult_quantity');
    const childInput = document.querySelector('.child_quantity');
    const totalPriceEl = document.getElementById('ext_total_price');
    const priceAdult = parseInt("{{ $bookingData['price_adult_at_booking'] }}");
    const priceChild = parseInt("{{ $bookingData['price_child_at_booking'] }}");

    // Lấy các input hidden
    const hiddenAdultInput = document.querySelector('.adult_quantity_hidden');
    const hiddenChildInput = document.querySelector('.child_quantity_hidden');
    const hiddenTotalPriceInput = document.getElementById('hidden_total_price');

    const btnOffice = document.getElementById('btnOffice');
    const btnPaypal = document.getElementById('btnPaypal');
    const btnMomo = document.getElementById('btnMomo');
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');

    //  Hàm tính tổng tiền
    function updateTotal() {
      const adult = parseInt(adultInput.value) || 1;
      const child = parseInt(childInput.value) || 0;
      const total = (adult * priceAdult) + (child * priceChild);
      totalPriceEl.textContent = total.toLocaleString('vi-VN') + ' đ';

      // Thêm cập nhật cho các trường input hidden
      hiddenAdultInput.value = adult;
      hiddenChildInput.value = child;
      hiddenTotalPriceInput.value = total;
    }

    // Giới hạn chỉ nhập số & xử lý khi nhập 0
    function validateInput(input, minValue = 0) {
      input.addEventListener('input', function() {
        // Loại bỏ ký tự không phải số
        this.value = this.value.replace(/[^0-9]/g, '');

        // Nếu trống thì đặt lại giá trị tối thiểu
        if (this.value === '') this.value = minValue;

        // Nếu nhập 0 ở người lớn => đặt về 1
        if (this.classList.contains('adult_quantity') && parseInt(this.value) < 1) {
          this.value = 1;
        }

        updateTotal();
      });
    }

    // Nút tăng giảm
    function setupQuantityButtons(container, input, minValue = 0) {
      const btnDown = container.querySelector('.btn-down');
      const btnUp = container.querySelector('.btn-up');

      btnUp.addEventListener('click', function() {
        let value = parseInt(input.value) || minValue;
        value++;
        input.value = value;
        updateTotal();
      });

      btnDown.addEventListener('click', function() {
        let value = parseInt(input.value) || minValue;
        value = Math.max(value - 1, minValue);
        if (input.classList.contains('adult_quantity') && value < 1) value = 1; // người lớn >= 1
        input.value = value;
        updateTotal();
      });
    }

    // Gọi hàm setup
    validateInput(adultInput, 1);
    validateInput(childInput, 0);

    setupQuantityButtons(document.querySelector('.q-adult'), adultInput, 1);
    setupQuantityButtons(childInput.closest('.iquan'), childInput, 0);

    updateTotal();

    // Ẩn/hiện nút thanh toán theo phương thức được chọn
    paymentRadios.forEach(radio => {
      radio.addEventListener('change', function() {
        btnOffice.classList.add('hidden');
        btnPaypal.classList.add('hidden');
        btnMomo.classList.add('hidden');

        if (this.value === 'office') btnOffice.classList.remove('hidden');
        if (this.value === 'paypal') btnPaypal.classList.remove('hidden');
        if (this.value === 'momo') btnMomo.classList.remove('hidden');
      });
    });

    // Hàm kiểm tra form trước khi thanh toán
    function validateForm() {
      let valid = true;
      const requiredFields = ['full_name', 'phone', 'email', 'address'];

      requiredFields.forEach(id => {
        const field = document.getElementById(id);
        if (!field.value.trim()) {
          field.classList.add('is-invalid');
          valid = false;
        } else {
          field.classList.remove('is-invalid');
        }
      });

      if (!valid) {
        alert('Vui lòng nhập đầy đủ thông tin bắt buộc trước khi thanh toán!');
      }

      return valid;
    }

    // Hàm gửi form đến server (Laravel route)
    // dữ liệu từ các trường input (cả hidden và visible)
    async function submitPayment(url, successMessage) {
      const formData = new FormData(document.getElementById('fBooking'));
      try {
        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            // Báo cho Laravel biết ta muốn nhận JSON
            'Accept': 'application/json'
          },
          body: formData
        });
        const data = await res.json();
        if (data.success) {
          if (data.redirect_url) {
            // Nếu là PayPal/Momo, chuyển hướng
            window.location.href = data.redirect_url;
          } else {
            // Nếu là thanh toán tại văn phòng
            toastr.success(data.message || successMessage);
            // Thành công chuyển hướng đến trang quản lý đơn
            // window.location.href = '{{ route("account.myBookings") }}'; 
          }
        } else {
          toastr.error(data.message || 'Có lỗi xảy ra!');
        }
      } catch (error) {
        toastr.error('Không thể gửi yêu cầu. Vui lòng thử lại!');
        console.error(error);
      }
    }

    // Thanh toán tại văn phòng
    btnOffice.addEventListener('click', function() {
      if (!validateForm()) return;
      submitPayment('{{ route("payment.officePayment") }}', 'Đặt tour thành công tại văn phòng!');
    });

    // PayPal sandbox
    btnPaypal.addEventListener('click', function() {
      if (!validateForm()) return;
      submitPayment('{{ route("payment.paypal") }}', 'Đang chuyển hướng đến PayPal Sandbox...');
    });

    // MoMo sandbox
    btnMomo.addEventListener('click', function() {
      if (!validateForm()) return;
      submitPayment('{{ route("payment.momo") }}', 'Đang chuyển hướng đến MoMo Sandbox...');
    });
  });
</script>

@endsection