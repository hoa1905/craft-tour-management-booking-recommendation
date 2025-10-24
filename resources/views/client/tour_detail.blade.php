@extends('client.master')
@section('title', 'Trang chi tiết tour')
@section('body')
<div id="vnt-content">
  <div id="vnt-navation" class="breadcrumb">
    <div class="wrapper">
      <div class="navation">
        <ul>
          <li><a href="http://127.0.0.1:8000/">Tour du lịch</a></li>
          @if($tour->villages->isNotEmpty())
          <li>
            <a href="#">
              {{ $tour->villages->first()->province->region->region_name }}
            </a>
          </li>
          @endif
          <li class="hidden-xs"><a
              href="">{{ $tour->tour_name }}</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="box_mid">
    <div class="wrapper">
      <div class="mid-title">
        <h1 class="title-main">{{ $tour->tour_name }}</h1>
      </div>
      <div class="mid-content">
        <div class="wrap-scroll">
          <div class="row cel-p10">
            <div class="col-md-4 col-sm-12 col-xs-12 pull-right">
              <div class="box-detail-tour">
                <table class="table-detail">
                  <tbody>
                    <tr>
                      <td>
                        <div class="i-label fa-qrcode">Mã tour:</div>
                      </td>
                      <td><span class="color1">{{ $tour->tour_id }}</span></td>
                    </tr>
                    <tr>
                      <td>
                        <div class="i-label fa-map-marker-alt">Điểm đi:</div>
                      </td>
                      <td><a href="/tour/tour.html/?departure=1" target="_blank">{{ $tour->departure_point }}</a>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="i-label fa-route"><strong>Điểm Đến:</strong></div>
                      </td>
                      <td><a href="/tour/mien-trung.html" target="_blank">{{ $tour->destination }}</a></td>
                    </tr>
                    <tr>
                      <td>
                        <div class="i-label fa-map-marked-alt">Tham quan:</div>
                      </td>
                      <td>
                        @if($tour->villages->isNotEmpty())
                        @foreach($tour->villages as $village)
                        <a href="#" target="_blank">
                          {{ $village->village_name }}
                        </a>@if(!$loop->last), @endif
                        @endforeach
                        @endif
                      </td>

                    </tr>
                    <tr>
                      <td>
                        <div class="i-label fa-clock">Thời gian:</div>
                      </td>
                      <td>{{ $tour->duration }}</td>
                    </tr>
                    <tr>
                      <td>
                        <div class="i-label fa-bus-alt">Phương tiện:</div>
                      </td>
                      <td>{{ $tour->transport }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div id="advRight" class="hidden-sm hidden-xs">
                <div class="vbackpop"></div>
                <div class="advRight-fixed">
                  <div class="box-detail-tour hasBg">
                    <div class="vpadd">
                      <div class="vtitle">
                        <span>Ngày khởi hành và giá tour</span>
                      </div>
                      <div class="vconts boxCart">
                        <div class="ctcheck">
                          <div class="ckgrip">
                            <div class="xcol">
                              <div class="ckchose clchoose">
                                <ul>
                                  @if($tour->schedules->isNotEmpty())
                                  @foreach($tour->schedules->sortBy('start_date') as $schedule)
                                  <li class="{{ $loop->first ? 'active' : '' }}"
                                    data-schedule-id="{{ $schedule->schedule_id }}"
                                    data-date="{{ \Carbon\Carbon::parse($schedule->start_date)->format('d/m/Y') }}">
                                    {{ \Carbon\Carbon::parse($schedule->start_date)->format('d/m') }}
                                  </li>
                                  @endforeach
                                  @else
                                  <li>Chưa có lịch khởi hành</li>
                                  @endif
                                </ul>

                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="ctchose">
                          <div class="csitem">
                            <div class="iigrip">
                              <div class="xcol">
                                <div class="itext">
                                  <div class="tm">Người lớn</div>
                                  <div class="th">>10 tuổi</div>
                                </div>
                              </div>
                              <div class="ycol">
                                <div class="inumb">
                                  <div class="ncore">X</div>
                                  <div class="ninfo">
                                    <div class="sold i-price_old"></div>
                                    <div class="snew i-price">{{ number_format($tour->price_adult, 0, ',', '.') }} đ</div>
                                  </div>
                                </div>
                              </div>
                              <div class="zcol">
                                <div class="iquan q-adult">
                                  <button type="button" class="btn-down"> <i
                                      class="fa fa-minus"></i></button>
                                  <input type="text"
                                    class="ipt-qua adult_quantity" value="1">
                                  <button type="button" class="btn-up"><i
                                      class="fa fa-plus"></i></button>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="csitem">
                            <div class="iigrip">
                              <div class="xcol">
                                <div class="itext">
                                  <div class="tm">Trẻ em</div>
                                  <div class="th">2 - 10 tuổi</div>
                                </div>
                              </div>
                              <div class="ycol">
                                <div class="inumb">
                                  <div class="ncore">X</div>
                                  <div class="ninfo">
                                    <div class="sold i-price_old_child"></div>
                                    <div class="snew i-price_child">{{ number_format($tour->price_child, 0, ',', '.') }} đ
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="zcol">
                                <div class="iquan">
                                  <button type="button" class="btn-down"> <i
                                      class="fa fa-minus"></i></button>
                                  <input type="text"
                                    class="ipt-qua child_quantity" value="0">
                                  <button type="button" class="btn-up"><i
                                      class="fa fa-plus"></i></button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="ctprice">
                          <div class="prgrip">
                            <div class="xcol">
                              <div class="polds total-price-old"></div>
                              <div class="pnews total-price">{{ number_format($tour->price_adult, 0, ',', '.') }} đ</div>
                            </div>
                            <div class="vcol">
                              <div class="ctlinks">
                                <a href="javascript:void(0)"
                                  class="book-tour addCart" rel="nofollow"
                                  data-id="191">
                                  <i class="fa fa-shopping-cart"></i> Đặt tour
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="ctinfos">
                          <div class="ifgrip">
                            <div class="xcol">
                              <div class="iicon">
                                <img src="{{ asset('images/ihotline.png') }}" alt="ihotline">
                              </div>
                            </div>
                            <div class="vcol">
                              <div class="itext">
                                <div class="tm">Tổng đài tư vấn, đặt tour:</div>
                                <div class="th"><span style="color: #d82727;">0123 456 789</span></div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="div_adv">

                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-8 col-sm-12 col-xs-12">
              <div class="vhupgradenew">
                <div class="tpupgradenm">
                  <div class="nmgrip">
                    <div class="lcol">
                      <div class="nmponsi">
                        <div class="imupgradenm">
                          <div class="slupgradenm">
                            <div class="colsl">
                              <div class="itupgradenma clpopalbum">
                                <img src="{{ asset('images/' . $tour->image_main) }}" alt="">
                              </div>
                            </div>

                            <div class="colsl">
                              <div class="itupgradenma clpopalbum">
                                <img src="{{ asset('images/' . $tour->image_1) }}" alt="">
                              </div>
                            </div>

                            <div class="colsl">
                              <div class="itupgradenma clpopalbum">
                                <img src="{{ asset('images/' . $tour->image_2) }}" alt="">
                              </div>
                            </div>
                          </div>

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab_wrapper tab_responsive">
                <div class="viewtab vhtabtour" id="viewtabDesc">
                  <div class="vvtitle">
                    <div class="icon"><i class="fa fa-paperclip"></i></div>
                    <div class="txt">
                      <h2>Giới thiệu tổng quan về tour</h2>
                    </div>
                  </div>
                  <div class="vvcont">
                    <div class="the-content ">
                      <div class="box-info">
                        <div class="info-content">
                          <div class="hpviewcont">
                            <div class="thecontent desc">
                              <div class="txt-intro">
                                <p style="text-align: justify;">{{ $tour->description }}</p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="viewtab vhtabtour" id="viewtabProgram">
                  <div class="vvtitle vchange">
                    <div class="xxgrip">
                      <div class="xcol">
                        <div class="tname">
                          <div class="icon"><i class="fa fa-info-circle"></i></div>
                          <div class="txt"><b> LỊCH TRÌNH TOUR CHI TIẾT</b></div>
                        </div>
                      </div>
                      <div class="ycol">
                        <div class="tview">
                          <a class="clviewsmore" href="javascript:void(0);"><span
                              class="vview">Xem toàn bộ</span> <span class="xhide">Thu
                              gọn</span></a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="vvcont">
                    <div class="ltschedule">
                      @if($tour->itineraries->isNotEmpty())
                      @foreach($tour->itineraries as $itinerary)
                      <div class="itschedule {{ $loop->first ? 'active' : '' }}">
                        <div class="sctitle">
                          NGÀY {{ $itinerary->day_number }}: {{ $itinerary->activities }}
                        </div>
                        <div class="scconts">
                          <div class="desc">
                            <p>{{ $itinerary->meals }}</p>
                            <p>{{ $itinerary->accommodation }}</p>
                          </div>
                        </div>
                      </div>
                      @endforeach
                      @else
                      <p>Chưa có lịch trình cho tour này.</p>
                      @endif
                    </div>
                  </div>
                  <div class="viewtab vhtabtour" id="viewtabComment">
                    <div class="vvtitle">
                      <div class="icon"><i class="fa fa-comments"></i></div>
                      <div class="txt"><span>Đánh giá tour</span></div>
                    </div>
                    <div class="vvcont">
                      <div class="the-content">
                        <div class="box-info">
                          <div class="info-content">
                            <div class="thecontent">
                              <div class="vnt-review">
                                <div class="itreviewfaq">
                                  <div class="rftitle">
                                    <h3>0 Đánh giá về tour</h3>
                                  </div>
                                  <div class="rfcont">
                                    <div class="box-detail">
                                      <div class="row cel-p10 rowflex">
                                        <div
                                          class="col-md-3 col-sm-12 col-xs-12">
                                          <div class="cricleDG"
                                            data-point="9.5">
                                            <div class="status">Chưa có đánh
                                              giá</div>
                                            <div class="point"></div>
                                            <svg viewBox="0 0 180 180">
                                              <path class="circle"
                                                d="M90 4  a 86 86 0 0 1 0 172  a 86 86 0 0 1 0 -172">
                                              </path>
                                            </svg>
                                          </div>
                                        </div>
                                        <div
                                          class="col-md-6 col-sm-12 col-xs-12">
                                          <div class="row cel-p10">

                                            <div
                                              class="col-md-6 col-sm-6 col-xs-6">
                                              <div class="wrapDG">
                                                <div class="lineDG">
                                                  <div>
                                                    <div class="fl">
                                                      Hướng dẫn
                                                      viên</div>
                                                    <div class="fr">
                                                      <span
                                                        class="color1">0</span>
                                                    </div>
                                                    <div
                                                      class="clear">
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="bgLine">
                                                  <div class="perLine"
                                                    style="width: 0%;">
                                                  </div>
                                                </div>
                                              </div>
                                            </div>

                                            <div
                                              class="col-md-6 col-sm-6 col-xs-6">
                                              <div class="wrapDG">
                                                <div class="lineDG">
                                                  <div>
                                                    <div class="fl">
                                                      Lưu trú
                                                    </div>
                                                    <div class="fr">
                                                      <span
                                                        class="color1">0</span>
                                                    </div>
                                                    <div
                                                      class="clear">
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="bgLine">
                                                  <div class="perLine"
                                                    style="width: 0%;">
                                                  </div>
                                                </div>
                                              </div>
                                            </div>

                                            <div
                                              class="col-md-6 col-sm-6 col-xs-6">
                                              <div class="wrapDG">
                                                <div class="lineDG">
                                                  <div>
                                                    <div class="fl">
                                                      Giá cả</div>
                                                    <div class="fr">
                                                      <span
                                                        class="color1">0</span>
                                                    </div>
                                                    <div
                                                      class="clear">
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="bgLine">
                                                  <div class="perLine"
                                                    style="width: 0%;">
                                                  </div>
                                                </div>
                                              </div>
                                            </div>

                                            <div
                                              class="col-md-6 col-sm-6 col-xs-6">
                                              <div class="wrapDG">
                                                <div class="lineDG">
                                                  <div>
                                                    <div class="fl">
                                                      Phương tiện
                                                    </div>
                                                    <div class="fr">
                                                      <span
                                                        class="color1">0</span>
                                                    </div>
                                                    <div
                                                      class="clear">
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="bgLine">
                                                  <div class="perLine"
                                                    style="width: 0%;">
                                                  </div>
                                                </div>
                                              </div>
                                            </div>

                                            <div
                                              class="col-md-6 col-sm-6 col-xs-6">
                                              <div class="wrapDG">
                                                <div class="lineDG">
                                                  <div>
                                                    <div class="fl">
                                                      Địa điểm
                                                    </div>
                                                    <div class="fr">
                                                      <span
                                                        class="color1">0</span>
                                                    </div>
                                                    <div
                                                      class="clear">
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="bgLine">
                                                  <div class="perLine"
                                                    style="width: 0%;">
                                                  </div>
                                                </div>
                                              </div>
                                            </div>

                                            <div
                                              class="col-md-6 col-sm-6 col-xs-6">
                                              <div class="wrapDG">
                                                <div class="lineDG">
                                                  <div>
                                                    <div class="fl">
                                                      Ăn uống
                                                    </div>
                                                    <div class="fr">
                                                      <span
                                                        class="color1">0</span>
                                                    </div>
                                                    <div
                                                      class="clear">
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="bgLine">
                                                  <div class="perLine"
                                                    style="width: 0%;">
                                                  </div>
                                                </div>
                                              </div>
                                            </div>

                                          </div>
                                        </div>
                                        <div
                                          class="col-md-3 col-sm-12 col-xs-12">
                                          <div class="viewpop">
                                            <a class="popup_edit fancybox.iframe"
                                              href="/tour/tour/popup/reviews.html/?item_id=191">Gửi
                                              đánh giá</a>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <div class="itreviewfaq">
                                <div class="rftitle">
                                  <h3>BÌNH LUẬN</h3>
                                </div>
                                <div class="rfcont">
                                  <div class="facebook-comment"
                                    style="padding-top:10px;">
                                    <div class="fb-comments"
                                      data-width="100%" data-num-posts="5"
                                      data-colorscheme="light"></div>
                                    <div id="fb-root"></div>
                                    <script>
                                      (function(d, s, id) {
                                        var js, fjs = d.getElementsByTagName(s)[0];
                                        if (d.getElementById(id)) return;
                                        js = d.createElement(s);
                                        js.id = id;
                                        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=1305829822896074";
                                        fjs.parentNode.insertBefore(js, fjs);
                                      }(document, "script", "facebook-jssdk"));
                                    </script>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="vnt-load_other" class="mb30 vnt-load-tour">
            <div class="style-title-1">
              <div class="fl"><span class="stTitle">Các tour tương tự</span></div>
              <div class="clear"></div>
            </div>
            <div class="vhtourkm loadBottom">
              <div id="mien-nam-tour-list" class="row">
                <div class="col-md-3 col-sm-6">
                  <div class="tour-card">
                    <a href="">
                      <div class="tour-image" style="background-image: url('')">
                        <span class="tour-region"><i class="glyphicon glyphicon-map-marker"></i> fake</span>
                      </div>
                    </a>

                    <div class="tour-body">
                      <h4 class="tour-title">
                        <a href="">
                          tour name
                        </a>
                      </h4>

                      <div class="tour-price">
                        1122 <span class="currency">đ</span>
                      </div>

                      <ul class="tour-info list-unstyled">
                        <li><strong>Khởi hành:</strong> 5</li>
                        <li><strong>Điểm đến:</strong> Điểm đến</li>
                        <li><strong>Thời gian:</strong> 7</li>
                      </ul>

                      <div class="clearfix">
                        <div class="pull-left">
                          <strong>Ngày đi:</strong> 1
                        </div>
                        <div class="pull-right">

                          <i class="fa fa-plane"></i>

                          <i class="fa fa-bus"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Nút xem thêm -->
              <div class="viewall vcenter vfullmb html_nav" style="text-align:center; margin:20px 0;">
                <a href="javascript:void(0)" class="loadmore" data-offset="8" data-region="Tour Miền Nam">
                  <span><i class="fa fa-chevron-down"></i> Xem thêm</span>
                </a>
              </div>

              <!-- Thông báo khi hết tour -->
              <div class="no-more" style="text-align:center; display:none; color:gray;">
                <em>Đã hiển thị tất cả tour</em>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript" src="{{ asset('js/plusone.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('css/support_fixed.css') }}">
  <script type="text/javascript" src="{{ asset('js/support_fixed.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/Drog.js') }}"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Lấy giá từ backend
      const priceAdult = parseFloat('{{ $tour->price_adult }}');
      const priceChild = parseFloat('{{ $tour->price_child }}');
      const tourId = parseInt('{{ $tour->tour_id }}');
      const csrfToken = '{{ csrf_token() }}';

      // Lấy các elements
      const adultQuantityInput = document.querySelector('.adult_quantity');
      const childQuantityInput = document.querySelector('.child_quantity');
      const totalPriceElement = document.querySelector('.total-price');
      const bookTourBtn = document.querySelector('.book-tour');

      // Hàm format số tiền
      function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
      }

      // Hàm tính tổng giá
      function calculateTotal() {
        const adultQty = parseInt(adultQuantityInput.value) || 0;
        const childQty = parseInt(childQuantityInput.value) || 0;

        const totalAdult = adultQty * priceAdult;
        const totalChild = childQty * priceChild;
        const grandTotal = totalAdult + totalChild;

        totalPriceElement.textContent = formatMoney(grandTotal);

        return {
          adultQty,
          childQty,
          grandTotal
        };
      }

      // XỬ LÝ NÚT TĂNG/GIẢM CHO NGƯỜI LỚN
      const adultBtnUp = document.querySelector('.q-adult .btn-up');
      const adultBtnDown = document.querySelector('.q-adult .btn-down');

      adultBtnUp.addEventListener('click', function(e) {
        e.preventDefault();
        let currentValue = parseInt(adultQuantityInput.value) || 0;
        adultQuantityInput.value = currentValue + 1;
        calculateTotal();
      });

      adultBtnDown.addEventListener('click', function(e) {
        e.preventDefault();
        let currentValue = parseInt(adultQuantityInput.value) || 1;
        if (currentValue > 1) {
          adultQuantityInput.value = currentValue - 1;
          calculateTotal();
        }
      });

      // XỬ LÝ NÚT TĂNG/GIẢM CHO TRẺ EM
      const childQuantityContainer = document.querySelectorAll('.iquan')[1];
      const childBtnUp = childQuantityContainer.querySelector('.btn-up');
      const childBtnDown = childQuantityContainer.querySelector('.btn-down');

      childBtnUp.addEventListener('click', function(e) {
        e.preventDefault();
        let currentValue = parseInt(childQuantityInput.value) || 0;
        childQuantityInput.value = currentValue + 1;
        calculateTotal();
      });

      childBtnDown.addEventListener('click', function(e) {
        e.preventDefault();
        let currentValue = parseInt(childQuantityInput.value) || 0;
        if (currentValue > 0) {
          childQuantityInput.value = currentValue - 1;
          calculateTotal();
        }
      });

      // XỬ LÝ KHI NHẬP TRỰC TIẾP
      adultQuantityInput.addEventListener('blur', function() {
        let value = parseInt(this.value) || 1;
        if (value < 1) value = 1;
        this.value = value;
        calculateTotal();
      });

      adultQuantityInput.addEventListener('input', function() {
        // Chỉ cho phép nhập số
        this.value = this.value.replace(/[^0-9]/g, '');
      });

      childQuantityInput.addEventListener('blur', function() {
        let value = parseInt(this.value) || 0;
        if (value < 0) value = 0;
        this.value = value;
        calculateTotal();
      });

      childQuantityInput.addEventListener('input', function() {
        // Chỉ cho phép nhập số
        this.value = this.value.replace(/[^0-9]/g, '');
      });

      // XỬ LÝ CHỌN NGÀY KHỞI HÀNH
      const dateItems = document.querySelectorAll('.ckchose ul li[data-date]');
      dateItems.forEach(function(item) {
        item.addEventListener('click', function() {
          dateItems.forEach(function(i) {
            i.classList.remove('active');
          });
          this.classList.add('active');
        });
      });

      // XỬ LÝ NÚT ĐẶT TOUR
      bookTourBtn.addEventListener('click', function(e) {
        e.preventDefault();

        const result = calculateTotal();

        // Kiểm tra số lượng
        if (result.adultQty === 0) {
          alert('Vui lòng chọn số lượng người lớn!');
          return;
        }

        // Lấy id của ngày đã chọn
        const selectedDateElement = document.querySelector('.ckchose ul li.active');
        const scheduleId = selectedDateElement ? selectedDateElement.getAttribute('data-schedule-id') : null;

        // if (!scheduleId) {
        //   alert('Vui lòng chọn ngày khởi hành!');
        //   return;
        // }

        if (!window.isLoggedIn) {
          if ($('.vnt-popup-login').length) {
            $('.vnt-popup-login').trigger('click');
          } else {
            alert('Vui lòng đăng nhập để đặt tour!');
          }
          return;
        }

        // Chuẩn bị data
        const bookingData = {
          tour_id: tourId,
          schedule_id: scheduleId,
          adult_quantity: result.adultQty,
          child_quantity: result.childQty,
          total_price: result.grandTotal,
          _token: csrfToken
        };

        console.log(bookingData);

        fetch('{{ route("booking.create") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(bookingData)
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              toastr.success(data.message);
              window.location.href = data.redirect_url;
            } else {
              toastr.error(data.message || 'Có lỗi xảy ra!');
            }
          })
          .catch(err => {
            console.error(err);
            toastr.error('Không thể đặt tour. Vui lòng thử lại!');
          });
      });

      calculateTotal();
    });
  </script>

  <style>
    .iquan {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .btn-up,
    .btn-down {
      background: #f0f0f0;
      border: 1px solid #ddd;
      padding: 5px 10px;
      cursor: pointer;
      transition: all 0.3s;
      border-radius: 3px;
    }

    .btn-up:hover,
    .btn-down:hover {
      background: #007bff;
      color: white;
      border-color: #007bff;
    }

    .ipt-qua {
      width: 50px;
      text-align: center;
      border: 1px solid #ddd;
      padding: 5px;
      border-radius: 3px;
      font-size: 14px;
    }

    .ckchose ul {
      list-style: none;
      padding: 0;
      margin: 0;
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }

    .ckchose ul li {
      padding: 8px 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.3s;
      background: #fff;
      font-size: 14px;
    }

    .ckchose ul li:hover {
      border-color: #007bff;
      background: #f8f9fa;
    }

    .ckchose ul li.active {
      background: #007bff;
      color: #fff;
      border-color: #007bff;
      font-weight: bold;
    }

    .total-price {
      font-size: 22px;
      font-weight: bold;
      color: #e74c3c;
    }

    .book-tour {
      background: #e74c3c;
      color: white !important;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
      display: inline-block;
      transition: all 0.3s;
      border: none;
      cursor: pointer;
    }

    .book-tour:hover {
      background: #c0392b;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .book-tour i {
      margin-right: 5px;
    }
  </style>

</div>
@endsection