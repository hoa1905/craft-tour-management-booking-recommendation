@extends('client.master')
@section('title', 'Trang chủ')
@section('body')
<div id="vnt-content">
  <div class="block-banner-search">
    <div class="vnt-banner">
      <div id="banner_main">
        <div id="bannerCarousel" class="carousel slide" data-ride="carousel">
          <!-- Dấu chấm chỉ định slide -->
          <ol class="carousel-indicators">
            <li data-target="#bannerCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#bannerCarousel" data-slide-to="1"></li>
            <li data-target="#bannerCarousel" data-slide-to="2"></li>
          </ol>

          <!-- Các slide -->
          <div class="carousel-inner">
            <div class="item active">
              <img src="{{ asset('images/banner-1.jpg') }}" alt="Banner 1">
            </div>
            <div class="item">
              <img src="{{ asset('images/banner-2.jpg') }}" alt="Banner 2">
            </div>
            <div class="item">
              <img src="{{ asset('images/banner-3.jpg') }}" alt="Banner 3">
            </div>
          </div>

          <!-- Nút điều hướng trái và phải -->
          <a class="left carousel-control" href="#bannerCarousel" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="right carousel-control" href="#bannerCarousel" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>
      </div>

      <div class="vntformall">
        <div class="wrapper">
          <div class="list-search">
            <ul>
              <li>
                <a class="active" href="" target="_self">
                  <span class="text">TOUR TRỌN GÓI</span>
                  <span><i class="icon fa fa-clipboard-check"></i></span>
                </a>
              </li>
            </ul>
          </div>
          <div class="vhformsall">
            <form id="formSearch" name="formSearch" action="{{ route('tour.search') }}" method="get">
              <div class="hpformsall">
                <div class="vvgrip">
                  <div class="mcol">
                    <div class="tpformsall">
                      <div class="vtitle">CHỌN ĐIỂM ĐI, ĐIỂM ĐẾN</div>
                      <div class="vconts">
                        <div class="frmgrip">
                          <div class="acol">
                            <div class="fmgroup">
                              <select class="form-control chosen-select" id="departure" name="departure">
                                <option value="">Điểm khởi hành ---</option>
                                <option value="Cao Bằng">Cao Bằng</option>
                                <option value="Lạng Sơn">Lạng Sơn</option>
                                <option value="Quảng Ninh">Quảng Ninh</option>
                                <option value="Bắc Ninh">Bắc Ninh</option>
                                <option value="Tuyên Quang">Tuyên Quang</option>
                                <option value="Lào Cai">Lào Cai</option>
                                <option value="Lai Châu">Lai Châu</option>
                                <option value="Điện Biên">Điện Biên</option>
                                <option value="Sơn La">Sơn La</option>
                                <option value="Thái Nguyên">Thái Nguyên</option>
                                <option value="Phú Thọ">Phú Thọ</option>
                                <option value="TP. Hà Nội">TP. Hà Nội</option>
                                <option value="TP. Hải Phòng">TP. Hải Phòng</option>
                                <option value="Hưng Yên">Hưng Yên</option>
                                <option value="Ninh Bình">Ninh Bình</option>
                                <option value="Thanh Hóa">Thanh Hóa</option>
                                <option value="Nghệ An">Nghệ An</option>
                                <option value="Hà Tĩnh">Hà Tĩnh</option>
                                <option value="Quảng Trị">Quảng Trị</option>
                                <option value="TP. Huế">TP. Huế</option>
                                <option value="TP. Đà Nẵng">TP. Đà Nẵng</option>
                                <option value="Quảng Ngãi">Quảng Ngãi</option>
                                <option value="Gia Lai">Gia Lai</option>
                                <option value="Đắk Lắk">Đắk Lắk</option>
                                <option value="Khánh Hòa">Khánh Hòa</option>
                                <option value="Lâm Đồng">Lâm Đồng</option>
                                <option value="Đồng Nai">Đồng Nai</option>
                                <option value="TP. Hồ Chí Minh">TP. Hồ Chí Minh</option>
                                <option value="Tây Ninh">Tây Ninh</option>
                                <option value="Đồng Tháp">Đồng Tháp</option>
                                <option value="TP. Cần Thơ">TP. Cần Thơ</option>
                                <option value="Vĩnh Long">Vĩnh Long</option>
                                <option value="Cà Mau">Cà Mau</option>
                                <option value="An Giang">An Giang</option>
                              </select>
                            </div>
                          </div>
                          <div class="acol">
                            <div class="fmgroup">
                              <select class="form-control chosen-select" id="destination" name="destination">
                                <option value="">Điểm đến ---</option>
                                <optgroup label="Miền Bắc">
                                  <option value="Cao Bằng" class="op-level2">Cao Bằng</option>
                                  <option value="Lạng Sơn" class="op-level2">Lạng Sơn</option>
                                  <option value="Quảng Ninh" class="op-level2">Quảng Ninh</option>
                                  <option value="Bắc Ninh" class="op-level2">Bắc Ninh</option>
                                  <option value="Tuyên Quang" class="op-level2">Tuyên Quang</option>
                                  <option value="Lào Cai" class="op-level2">Lào Cai</option>
                                  <option value="Lai Châu" class="op-level2">Lai Châu</option>
                                  <option value="Điện Biên" class="op-level2">Điện Biên</option>
                                  <option value="Sơn La" class="op-level2">Sơn La</option>
                                  <option value="Thái Nguyên" class="op-level2">Thái Nguyên</option>
                                  <option value="Phú Thọ" class="op-level2">Phú Thọ</option>
                                  <option value="Hưng Yên" class="op-level2">Hưng Yên</option>
                                  <option value="Ninh Bình" class="op-level2">Ninh Bình</option>
                                  <option value="TP. Hà Nội" class="op-level2">TP. Hà Nội</option>
                                  <option value="TP. Hải Phòng" class="op-level2">TP. Hải Phòng</option>
                                </optgroup>
                                <optgroup label="Miền Trung">
                                  <option value="Thanh Hóa" class="op-level2">Thanh Hóa</option>
                                  <option value="Nghệ An" class="op-level2">Nghệ An</option>
                                  <option value="Hà Tĩnh" class="op-level2">Hà Tĩnh</option>
                                  <option value="Quảng Trị" class="op-level2">Quảng Trị</option>
                                  <option value="TP. Huế" class="op-level2">TP. Huế</option>
                                  <option value="TP. Đà Nẵng" class="op-level2">TP. Đà Nẵng</option>
                                  <option value="Quảng Ngãi" class="op-level2">Quảng Ngãi</option>
                                  <option value="Gia Lai" class="op-level2">Gia Lai</option>
                                  <option value="Đắk Lắk" class="op-level2">Đắk Lắk</option>
                                  <option value="Khánh Hòa" class="op-level2">Khánh Hòa</option>
                                  <option value="Lâm Đồng" class="op-level2">Lâm Đồng</option>
                                </optgroup>
                                <optgroup label="Miền Nam">
                                  <option value="Đồng Nai" class="op-level2">Đồng Nai</option>
                                  <option value="TP. Hồ Chí Minh" class="op-level2">TP. Hồ Chí Minh</option>
                                  <option value="Tây Ninh" class="op-level2">Tây Ninh</option>
                                  <option value="Đồng Tháp" class="op-level2">Đồng Tháp</option>
                                  <option value="TP. Cần Thơ" class="op-level2">TP. Cần Thơ</option>
                                  <option value="Vĩnh Long" class="op-level2">Vĩnh Long</option>
                                  <option value="Cà Mau" class="op-level2">Cà Mau</option>
                                  <option value="An Giang" class="op-level2">An Giang</option>
                                </optgroup>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="hcol">
                    <div class="tpformsall">
                      <div class="vtitle">NGÀY KHỞI HÀNH (trong khoảng thời gian)</div>
                      <div class="vconts">
                        <div class="frmgrip">
                          <div class="bcol">
                            <div class="fmgroup fa-calendar">
                              <input class="flatpickr-input" type="text" name="start_date" id="startdate" placeholder="Chọn ngày, tháng ---" value="">
                            </div>
                          </div>
                          <div class="ccol">
                            <div class="fmgroup">
                              <button type="submit" class="btn" value="Tìm tour"><span><i class="fab fa-sistrix"></i>Tìm tour</span></button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <link href="{{ asset('css/flatpickr.min.css') }}" rel="stylesheet">
          <link href="{{ asset('css/flatpickrcus.css') }}" rel="stylesheet">
          <link href="{{ asset('css/monthSelect.min.css') }}" rel="stylesheet">
          <link href="{{ asset('css/search.css') }}" rel="stylesheet" type="text/css">
          <script src="{{ asset('js/classList.min.js') }}"></script>
          <script src="{{ asset('js/flatpickr.min.js') }}"></script>
          <script src="{{ asset('js/rangePlugin.min.js') }}"></script>
          <script src="{{ asset('js/scrollPlugin.min.js') }}"></script>
          <script src="{{ asset('js/monthSelect.js') }}"></script>
          <script src="{{ asset('js/monthSelectindex.js') }}"></script>
          <script src="{{ asset('js/vn.js') }}"></script>
          <script type="text/javascript" src="{{ asset('js/search.js') }}"></script>
          <script type="text/javascript">
            jQuery(window).ready(function() {
              vnTSearch.init();
              if (($(window).innerWidth() <= 992)) {
                var fp = flatpickr(".flatpickr-input", {
                  showMonths: 1,
                  mode: "range",
                  dateFormat: "d/m/Y",
                  minDate: "today",
                })
              } else {
                var fp = flatpickr(".flatpickr-input", {
                  showMonths: 3,
                  mode: "range",
                  dateFormat: "d/m/Y",
                  minDate: "today",
                  locale: "vn"
                })
              }
            });
          </script>
        </div>
      </div>
    </div>
  </div>

  <div class="vnt-tintuc vnt-tour-load box-tour" data-id="0" data-type="focus">
    <div class="wrapper">
      <div class="style-title-1 lazyloading">
        <div class="fl">
          <h2 class="stTitle">Tour Hot</h2>
        </div>
        <div class="clear"></div>
      </div>
      <div class="vhtourkm loadBottom">
        <div id="hot-tour-list" class="row">
          @foreach ($hotTours as $tour)
          <div class="col-md-3 col-sm-6">
            <div class="tour-card">
              <a href="{{ route('tour.detail', ['id' => $tour->tour_id]) }}">
                <div class="tour-image" style="background-image: url(images/banner-tour-detail-1.jpg);">
                  <span class="tour-badge">TOUR BÁN CHẠY</span>
                  <span class="tour-region"><i class="glyphicon glyphicon-map-marker"></i> {{ $tour->destination }}</span>
                </div>
              </a>

              <div class="tour-body">
                <h4 class="tour-title">
                  <a href="{{ route('tour.detail', ['id' => $tour->tour_id]) }}">
                    {{ $tour->tour_name }}
                  </a>
                </h4>

                <div class="tour-price">
                  {{ number_format($tour->price, 0, ',', '.') }} <span class="currency">đ</span>
                </div>

                <ul class="tour-info list-unstyled">
                  <li><strong>Khởi hành:</strong> {{ $tour->departure_point }}</li>
                  <li><strong>Điểm đến:</strong> {{ $tour->destination }}</li>
                  <li><strong>Thời gian:</strong> {{ $tour->duration }}</li>
                </ul>

                <div class="clearfix">
                  <div class="pull-left">
                    <strong>Ngày đi:</strong> {{ \Carbon\Carbon::parse($tour->start_date)->format('d/m') }},
                    {{ \Carbon\Carbon::parse($tour->end_date)->format('d/m') }}
                  </div>
                  <div class="pull-right">
                    @if ($tour->transport === 'Máy bay')
                    <i class="fa fa-plane"></i>
                    @else
                    <i class="fa fa-bus"></i>
                    @endif
                  </div>

                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
        <!-- Nút xem thêm -->
        <div class="viewall vcenter vfullmb html_nav" style="text-align:center; margin:20px 0;">
          <a href="javascript:void(0)" class="load-more-hot-tours" data-offset="8">
            <span class="fa-chevron-down">Xem thêm</span>
          </a>
        </div>

        <!-- Thông báo khi hết tour -->
        <div class="no-more" style="text-align:center; display:none; color:gray;">
          <em>Đã hiển thị tất cả tour</em>
        </div>
      </div>
    </div>
  </div>

  <div id="box_home-1" class="vnt-tintuc box-tour-home" data-id="1" data-type="zone">
    <div class="wrapper">
      <div class="style-title-1 lazyloading">
        <div class="fl">
          <h2 class="stTitle2">Tour miền Bắc</h2>
          <ul class="ul-title">
            @foreach($regions['Tour Miền Bắc']->take(6) as $province)
            <li><a href="{{ url('/tour-search?destination=' . urlencode($province)) }}">{{ $province }}</a></li>
            @endforeach
          </ul>
        </div>
        <div class="fr">
          <div class="div-sub">
            <div class="link-more-3 dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              <a href="javascript:void(0)">Tất cả</a>
            </div>
            <div class="dropdown-menu">
              <ul>
                @foreach ($regions['Tour Miền Bắc'] as $province)
                <li>
                  <a href="{{ url('/tour-search?destination=' . urlencode($province)) }}">
                    {{ $province }}
                  </a>
                </li>
                @endforeach
              </ul>
            </div>
          </div>
        </div>
        <div class="clear"></div>
      </div>
      <div class="vhtourkm loadBottom">
        <div id="mien-bac-tour-list" class="row">
          @foreach ($northTours as $tour)
          @php
          $imageUrl = asset('images/' . $tour->image_url);
          @endphp
          <div class="col-md-3 col-sm-6">
            <div class="tour-card">
              <a href="{{ route('tour.detail', ['id' => $tour->tour_id]) }}">
                <div class="tour-image" style="background-image: url('{{ $imageUrl }}')">
                  <span class="tour-badge">TOUR BÁN CHẠY</span>
                  <span class="tour-region"><i class="glyphicon glyphicon-map-marker"></i> {{ $tour->destination }}</span>
                </div>
              </a>

              <div class="tour-body">
                <h4 class="tour-title">
                  <a href="{{ route('tour.detail', ['id' => $tour->tour_id]) }}">
                    {{ $tour->tour_name }}
                  </a>
                </h4>

                <div class="tour-price">
                  {{ number_format($tour->price, 0, ',', '.') }} <span class="currency">đ</span>
                </div>

                <ul class="tour-info list-unstyled">
                  <li><strong>Khởi hành:</strong> {{ $tour->departure_point }}</li>
                  <li><strong>Điểm đến:</strong> {{ $tour->destination }}</li>
                  <li><strong>Thời gian:</strong> {{ $tour->duration }}</li>
                </ul>

                <div class="clearfix">
                  <div class="pull-left">
                    <strong>Ngày đi:</strong> {{ \Carbon\Carbon::parse($tour->start_date)->format('d/m') }},
                    {{ \Carbon\Carbon::parse($tour->end_date)->format('d/m') }}
                  </div>
                  <div class="pull-right">
                    @if ($tour->transport === 'Máy bay')
                    <i class="fa fa-plane"></i>
                    @else
                    <i class="fa fa-bus"></i>
                    @endif
                  </div>

                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
        <!-- Nút xem thêm -->
        <div class="viewall vcenter vfullmb html_nav" style="text-align:center; margin:20px 0;">
          @if ($totalNorthTours > 8)
          <a href="javascript:void(0)" class="loadmore" data-offset="8" data-region="Tour Miền Bắc">
            <span><i class="fa fa-chevron-down"></i> Xem thêm</span>
          </a>
          @endif
        </div>

        <!-- Thông báo khi hết tour -->
        <div class="no-more" style="text-align:center; display:none; color:gray;">
          <em>Đã hiển thị tất cả tour</em>
        </div>
      </div>
    </div>
  </div>

  <div id="box_home-2" class="vnt-tintuc box-tour-home" data-id="2" data-type="zone">
    <div class="wrapper">
      <div class="style-title-1 lazyloading">
        <div class="fl">
          <h2 class="stTitle2">Tour miền Trung</h2>
          <ul class="ul-title">
            @foreach($regions['Tour Miền Trung']->take(6) as $province)
            <li><a href="{{ url('/tour-search?destination=' . urlencode($province)) }}">{{ $province }}</a></li>
            @endforeach
          </ul>
        </div>
        <div class="fr">
          <div class="div-sub">
            <div class="link-more-3 dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              <a href="javascript:void(0)">Tất cả</a>
            </div>
            <div class="dropdown-menu">
              <ul>
                @foreach ($regions['Tour Miền Trung'] as $province)
                <li>
                  <a href="{{ url('/tour-search?destination=' . urlencode($province)) }}">
                    {{ $province }}
                  </a>
                </li>
                @endforeach
              </ul>
            </div>
          </div>
        </div>
        <div class="clear"></div>
      </div>

      <div class="vhtourkm loadBottom">
        <div id="mien-trung-tour-list" class="row">
          @foreach ($centralTours as $tour)
          @php
          $imageUrl = asset('images/' . $tour->image_url);
          @endphp
          <div class="col-md-3 col-sm-6">
            <div class="tour-card">
              <a href="{{ route('tour.detail', ['id' => $tour->tour_id]) }}">
                <div class="tour-image" style="background-image: url('{{ $imageUrl }}')">
                  <span class="tour-badge">TOUR BÁN CHẠY</span>
                  <span class="tour-region"><i class="glyphicon glyphicon-map-marker"></i> {{ $tour->destination }}</span>
                </div>
              </a>

              <div class="tour-body">
                <h4 class="tour-title">
                  <a href="{{ route('tour.detail', ['id' => $tour->tour_id]) }}">
                    {{ $tour->tour_name }}
                  </a>
                </h4>

                <div class="tour-price">
                  {{ number_format($tour->price, 0, ',', '.') }} <span class="currency">đ</span>
                </div>

                <ul class="tour-info list-unstyled">
                  <li><strong>Khởi hành:</strong> {{ $tour->departure_point }}</li>
                  <li><strong>Điểm đến:</strong> {{ $tour->destination }}</li>
                  <li><strong>Thời gian:</strong> {{ $tour->duration }}</li>
                </ul>

                <div class="clearfix">
                  <div class="pull-left">
                    <strong>Ngày đi:</strong> {{ \Carbon\Carbon::parse($tour->start_date)->format('d/m') }},
                    {{ \Carbon\Carbon::parse($tour->end_date)->format('d/m') }}
                  </div>
                  <div class="pull-right">
                    @if ($tour->transport === 'Máy bay')
                    <i class="fa fa-plane"></i>
                    @else
                    <i class="fa fa-bus"></i>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
        <!-- Nút xem thêm -->
        <div class="viewall vcenter vfullmb html_nav" style="text-align:center; margin:20px 0;">
          @if ($totalCentralTours > 8)
          <a href="javascript:void(0)" class="loadmore" data-offset="8" data-region="Tour Miền Trung">
            <span><i class="fa fa-chevron-down"></i> Xem thêm</span>
          </a>
          @endif
        </div>

        <!-- Thông báo khi hết tour -->
        <div class="no-more" style="text-align:center; display:none; color:gray;">
          <em>Đã hiển thị tất cả tour</em>
        </div>
      </div>
    </div>
  </div>

  <div id="box_home-3" class="vnt-tintuc box-tour-home" data-id="3" data-type="zone">
    <div class="wrapper">
      <div class="style-title-1 lazyloading">
        <div class="fl">
          <h2 class="stTitle2">Tour miền Nam</h2>
          <ul class="ul-title">
            @foreach($regions['Tour Miền Nam']->take(6) as $province)
            <li><a href="{{ url('/tour-search?destination=' . urlencode($province)) }}">{{ $province }}</a></li>
            @endforeach
          </ul>
        </div>
        <div class="fr">
          <div class="div-sub">
            <div class="link-more-3 dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              <a href="javascript:void(0)">Tất cả</a>
            </div>
            <div class="dropdown-menu">
              <ul>
                @foreach ($regions['Tour Miền Nam'] as $province)
                <li>
                  <a href="{{ url('/tour-search?destination=' . urlencode($province)) }}">
                    {{ $province }}
                  </a>
                </li>
                @endforeach
              </ul>
            </div>
          </div>
        </div>
        <div class="clear"></div>
      </div>

      <div class="vhtourkm loadBottom">
        <div id="mien-nam-tour-list" class="row">
          @foreach ($southTours as $tour)
          @php
          $imageUrl = asset('images/' . $tour->image_url);
          @endphp
          <div class="col-md-3 col-sm-6">
            <div class="tour-card">
              <a href="{{ route('tour.detail', ['id' => $tour->tour_id]) }}">
                <div class="tour-image" style="background-image: url('{{ $imageUrl }}')">
                  <span class="tour-badge">TOUR BÁN CHẠY</span>
                  <span class="tour-region"><i class="glyphicon glyphicon-map-marker"></i> {{ $tour->destination }}</span>
                </div>
              </a>

              <div class="tour-body">
                <h4 class="tour-title">
                  <a href="{{ route('tour.detail', ['id' => $tour->tour_id]) }}">
                    {{ $tour->tour_name }}
                  </a>
                </h4>

                <div class="tour-price">
                  {{ number_format($tour->price, 0, ',', '.') }} <span class="currency">đ</span>
                </div>

                <ul class="tour-info list-unstyled">
                  <li><strong>Khởi hành:</strong> {{ $tour->departure_point }}</li>
                  <li><strong>Điểm đến:</strong> {{ $tour->destination }}</li>
                  <li><strong>Thời gian:</strong> {{ $tour->duration }}</li>
                </ul>

                <div class="clearfix">
                  <div class="pull-left">
                    <strong>Ngày đi:</strong> {{ \Carbon\Carbon::parse($tour->start_date)->format('d/m') }},
                    {{ \Carbon\Carbon::parse($tour->end_date)->format('d/m') }}
                  </div>
                  <div class="pull-right">
                    @if ($tour->transport === 'Máy bay')
                    <i class="fa fa-plane"></i>
                    @else
                    <i class="fa fa-bus"></i>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
        <!-- Nút xem thêm -->
        <div class="viewall vcenter vfullmb html_nav" style="text-align:center; margin:20px 0;">
          @if ($totalSouthTours > 8)
          <a href="javascript:void(0)" class="loadmore" data-offset="8" data-region="Tour Miền Nam">
            <span><i class="fa fa-chevron-down"></i> Xem thêm</span>
          </a>
          @endif
        </div>

        <!-- Thông báo khi hết tour -->
        <div class="no-more" style="text-align:center; display:none; color:gray;">
          <em>Đã hiển thị tất cả tour</em>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- (nut Xem them) Load More Tours Script -->
<script>
  $(function() {
    $('.loadmore').on('click', function() {
      var button = $(this); // chỉ lấy nút được click
      var offset = parseInt(button.attr('data-offset'));
      var region = button.attr('data-region');

      // Tạo slug: "Tour Miền Bắc" => "mien-bac"
      const slug = region.replace(/^Tour\s*/i, '') // loại bỏ tiền tố "Tour"
        .normalize("NFD").replace(/[\u0300-\u036f]/g, '') // loại bỏ dấu tiếng Việt
        .replace(/\s+/g, '-') // thay khoảng trắng bằng dấu gạch ngang
        .toLowerCase(); // chuyển về chữ thường

      $.ajax({
        url: "{{ route('tours.loadMoreTours') }}",
        method: "GET",
        data: {
          offset: offset,
          region: region
        },
        dataType: "json",
        beforeSend: function() {
          button.find('span').html('<i class="fa fa-spinner fa-spin"></i> Đang tải...');
        },
        success: function(data) {
          if (data && data.length > 0) {
            data.forEach(function(tour) {
              const imageUrl = `/images/${tour.image_url}`;
              const detailUrl = `/tour/${tour.tour_id}`; // route detail
              const startDate = new Date(tour.start_date).toLocaleDateString('vi-VN', {
                day: '2-digit',
                month: '2-digit'
              }).replaceAll('-', '/');
              const endDate = new Date(tour.end_date).toLocaleDateString('vi-VN', {
                day: '2-digit',
                month: '2-digit'
              }).replaceAll('-', '/');
              // Thêm dữ liệu mới vào vùng đúng theo slug
              $('#' + slug + '-tour-list').append(`
          <div class="col-md-3 col-sm-6">
            <div class="tour-card">
              <div class="tour-image" style="background-image: url('${imageUrl}');">
                <span class="tour-badge">TOUR BÁN CHẠY</span>
                <span class="tour-region"><i class="glyphicon glyphicon-map-marker"></i> ${tour.destination }</span>
              </div>

              <div class="tour-body">
                <h4 class="tour-title">
                  <a href="${detailUrl}">${tour.tour_name}</a>
                </h4>

                <div class="tour-price">
                  ${Number(tour.price).toLocaleString('vi-VN').replaceAll('.', ',')} <span class="currency">đ</span>
                </div>

                <ul class="tour-info list-unstyled">
                  <li><strong>Khởi hành:</strong> ${tour.departure_point}</li>
                  <li><strong>Điểm đến:</strong> ${tour.destination}</li>
                  <li><strong>Thời gian:</strong> ${tour.duration}</li>
                </ul>

                <div class="clearfix">
                  <div class="pull-left">
                    <strong>Ngày đi:</strong> ${startDate},
                    ${endDate}
                  </div>
                  <div class="pull-right">
                    ${tour.transport === 'Máy bay' ? '<i class="fa fa-plane"></i>' : '<i class="fa fa-bus"></i>'}
                  </div>
                </div>
              </div>
            </div>
          </div>
            `);
            });

            // Cập nhật offset riêng nút được click
            button.attr('data-offset', offset + data.length);

            // Nếu trả về ít hơn 4 tour → đã hết
            if (data.length < 4) {
              button.hide();
              button.closest('.viewall').next('.no-more').show();
            } else {
              button.find('span').html('<i class="fa fa-chevron-down"></i> Xem thêm');
            }
          } else {
            button.hide();
            button.closest('.viewall').next('.no-more').show();
          }
        },
        error: function(xhr) {
          console.error(xhr.responseText);
          button.find('span').html('<i class="fa fa-exclamation-triangle"></i> Lỗi tải dữ liệu');
        }
      });
    });
  });
</script>

<script type="text/javascript" src="{{ asset('js/plusone.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/support_fixed.css') }}">
<script type="text/javascript" src="{{ asset('js/support_fixed.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/Drog.js') }}"></script>
@endsection