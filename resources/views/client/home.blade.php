@extends('layouts.app')

@section('title', 'Trang chủ')

@section('head')
<!-- Truyền giá trị session vào HTML. Khi đó, home.js sẽ đọc được để hiện thông báo toastr -->
<meta name="flash-success" content="{{ session('change_password_success') ?? '' }}">
<link rel="stylesheet" href="{{ asset('css/client/search.css') }}">
<link rel="stylesheet" href="{{ asset('css/client/support_fixed.css') }}">
<link rel="stylesheet" href="{{ asset('css/client/flatpickr.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/client/flatpickrcus.css') }}">
<link rel="stylesheet" href="{{ asset('css/client/monthSelect.min.css') }}">
@endsection

@section('content')
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
              <img src="{{ asset('images/client/banner-1.jpg') }}" alt="Banner 1">
            </div>
            <div class="item">
              <img src="{{ asset('images/client/banner-2.jpg') }}" alt="Banner 2">
            </div>
            <div class="item">
              <img src="{{ asset('images/client/banner-3.jpg') }}" alt="Banner 3">
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
                <a class="active" href="javascript:void(0)" target="_self">
                  <span class="text">TOUR TRỌN GÓI</span>
                  <span><i class="icon fa fa-clipboard-check"></i></span>
                </a>
              </li>
            </ul>
          </div>
          <div class="vhformsall">
            <form id="formSearch" name="formSearch" action="{{ route('tour.search') }}" method="get">
              <!-- Tham số phân biệt form -->
              <input type="hidden" name="search_type" value="exact">
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
                                @foreach($regions as $region => $provinces)
                                @foreach($provinces as $province)
                                <option value="{{ $province }}">{{ $province }}</option>
                                @endforeach
                                @endforeach
                              </select>
                            </div>
                          </div>
                          <div class="acol">
                            <div class="fmgroup">
                              <select class="form-control chosen-select" id="destination" name="destination">
                                <option value="">Điểm đến ---</option>
                                @foreach($regions as $region => $provinces)
                                <optgroup label="{{ $region }}">
                                  @foreach($provinces as $province)
                                  <option value="{{ $province }}">{{ $province }}</option>
                                  @endforeach
                                </optgroup>
                                @endforeach
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
        </div>
      </div>
    </div>
  </div>

  {{-- Hệ khuyến nghị: Gợi ý dành cho bạn --}}
  <div id="recommended-for-you-section" class="vnt-tintuc vnt-tour-load box-tour" style="display: none;">
    <div class="wrapper">
      <div class="style-title-1 lazyloading">
        <div class="fl">
          <h2 class="stTitle2">
            <i class="fas fa-magic me-2"></i> Gợi ý dành cho bạn
          </h2>
        </div>
        <div class="clear"></div>
      </div>

      <div class="recommendation-message"></div>

      <div class="vhtourkm loadBottom">
        <div id="recommended-tours-list" class="row">
          {{-- JavaScript sẽ load tours vào đây --}}
        </div>

        <div id="recommended-loading" class="text-center" style="padding: 40px; display: none;">
          <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
          <p class="mt-3">Đang tìm tour phù hợp cho bạn...</p>
        </div>
      </div>
    </div>
  </div>

  <div class="vnt-tintuc vnt-tour-load box-tour" data-id="0" data-type="focus">
    <div class="wrapper">
      <div class="style-title-1 lazyloading">
        <div class="fl">
          <h2 class="stTitle2">Tour Hot</h2>
        </div>
        <div class="clear"></div>
      </div>
      <div class="vhtourkm loadBottom">
        <div id="hot-tour-list" class="row">
          @foreach ($hotTours as $tour)
          @php
          $imageUrl = asset('images/tours/' . $tour->image_main);
          @endphp
          <div class="col-md-3 col-sm-6">
            <div class="tour-card">
              <a href="{{ route('tour.detail', ['id' => $tour->tour_id]) }}">
                <div class="tour-image" style="background-image: url('{{ $imageUrl }}')">
                  <span class="tour-badge">Điểm đến hàng đầu</span>
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
                  {{ number_format($tour->price_adult, 0, ',', '.') }}<span class="currency">₫</span>
                </div>

                <ul class="tour-info list-unstyled">
                  <li><strong>Khởi hành:</strong> {{ $tour->departure_point }}</li>
                  <li><strong>Điểm đến:</strong> {{ $tour->destination }}</li>
                  <li><strong>Thời gian:</strong> {{ $tour->duration }}</li>
                </ul>

                <div class="clearfix">
                  <div class="pull-left">
                    <strong>Ngày đi:</strong>
                    @if($tour->schedules->isNotEmpty())
                    {{ $tour->schedules
        ->sortBy('start_date')
        ->map(fn($s) => \Carbon\Carbon::parse($s->start_date)->format('d/m'))
        ->implode(', ') }}
                    @else
                    Chưa có lịch khởi hành
                    @endif
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
          @if(!empty($regions['Miền Bắc']))
          @foreach($regions['Miền Bắc']->take(6) as $province)
          <li>
            <a href="{{ url('/tour-search?search_type=exact&destination=' . urlencode($province)) }}">
              {{ $province }}
            </a>
          </li>
          @endforeach
          @endif
        </ul>
      </div>
      <div class="fr">
        <div class="div-sub">
          <div class="link-more-3 dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <a href="javascript:void(0)">Tất cả</a>
          </div>
          <div class="dropdown-menu">
            <ul>
              @if(!empty($regions['Miền Bắc']))
              @foreach($regions['Miền Bắc'] as $province)
              <li>
                <a href="{{ url('/tour-search?search_type=exact&destination=' . urlencode($province)) }}">
                  {{ $province }}
                </a>
              </li>
              @endforeach
              @endif
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
        $imageUrl = asset('images/tours/' . $tour->image_main);
        @endphp
        <div class="col-md-3 col-sm-6">
          <div class="tour-card">
            <a href="{{ route('tour.detail', ['id' => $tour->tour_id]) }}">
              <div class="tour-image" style="background-image: url('{{ $imageUrl }}')">
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
                {{ number_format($tour->price_adult, 0, ',', '.') }}<span class="currency">₫</span>
              </div>

              <ul class="tour-info list-unstyled">
                <li><strong>Khởi hành:</strong> {{ $tour->departure_point }}</li>
                <li><strong>Điểm đến:</strong> {{ $tour->destination }}</li>
                <li><strong>Thời gian:</strong> {{ $tour->duration }}</li>
              </ul>

              <div class="clearfix">
                <div class="pull-left">
                  <strong>Ngày đi:</strong>
                  @if($tour->schedules->isNotEmpty())
                  {{ $tour->schedules
        ->sortBy('start_date')
        ->map(fn($s) => \Carbon\Carbon::parse($s->start_date)->format('d/m'))
        ->implode(', ') }}
                  @else
                  Chưa có lịch khởi hành
                  @endif
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
        <a href="javascript:void(0)" class="loadmore" data-offset="8" data-region="Miền Bắc">
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
          @if(!empty($regions['Miền Trung']))
          @foreach($regions['Miền Trung']->take(6) as $province)
          <li>
            <a href="{{ url('/tour-search?search_type=exact&destination=' . urlencode($province)) }}">
              {{ $province }}
            </a>
          </li>
          @endforeach
          @endif
        </ul>
      </div>
      <div class="fr">
        <div class="div-sub">
          <div class="link-more-3 dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <a href="javascript:void(0)">Tất cả</a>
          </div>
          <div class="dropdown-menu">
            <ul>
              @if(!empty($regions['Miền Trung']))
              @foreach($regions['Miền Trung'] as $province)
              <li>
                <a href="{{ url('/tour-search?search_type=exact&destination=' . urlencode($province)) }}">
                  {{ $province }}
                </a>
              </li>
              @endforeach
              @endif
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
        $imageUrl = asset('images/tours/' . $tour->image_main);
        @endphp
        <div class="col-md-3 col-sm-6">
          <div class="tour-card">
            <a href="{{ route('tour.detail', ['id' => $tour->tour_id]) }}">
              <div class="tour-image" style="background-image: url('{{ $imageUrl }}')">
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
                {{ number_format($tour->price_adult, 0, ',', '.') }}<span class="currency">₫</span>
              </div>

              <ul class="tour-info list-unstyled">
                <li><strong>Khởi hành:</strong> {{ $tour->departure_point }}</li>
                <li><strong>Điểm đến:</strong> {{ $tour->destination }}</li>
                <li><strong>Thời gian:</strong> {{ $tour->duration }}</li>
              </ul>

              <div class="clearfix">
                <div class="pull-left">
                  <strong>Ngày đi:</strong> @if($tour->schedules->isNotEmpty())
                  {{ $tour->schedules
        ->sortBy('start_date')
        ->map(fn($s) => \Carbon\Carbon::parse($s->start_date)->format('d/m'))
        ->implode(', ') }}
                  @else
                  Chưa có lịch khởi hành
                  @endif
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
        <a href="javascript:void(0)" class="loadmore" data-offset="8" data-region="Miền Trung">
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
          @if(!empty($regions['Miền Nam']))
          @foreach($regions['Miền Nam']->take(6) as $province)
          <li>
            <a href="{{ url('/tour-search?search_type=exact&destination=' . urlencode($province)) }}">
              {{ $province }}
            </a>
          </li>
          @endforeach
          @endif
        </ul>
      </div>
      <div class="fr">
        <div class="div-sub">
          <div class="link-more-3 dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <a href="javascript:void(0)">Tất cả</a>
          </div>
          <div class="dropdown-menu">
            <ul>
              @if(!empty($regions['Miền Nam']))
              @foreach($regions['Miền Nam'] as $province)
              <li>
                <a href="{{ url('/tour-search?search_type=exact&destination=' . urlencode($province)) }}">
                  {{ $province }}
                </a>
              </li>
              @endforeach
              @endif
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
        $imageUrl = asset('images/tours/' . $tour->image_main);
        @endphp
        <div class="col-md-3 col-sm-6">
          <div class="tour-card">
            <a href="{{ route('tour.detail', ['id' => $tour->tour_id]) }}">
              <div class="tour-image" style="background-image: url('{{ $imageUrl }}')">
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
                {{ number_format($tour->price_adult, 0, ',', '.') }}<span class="currency">₫</span>
              </div>

              <ul class="tour-info list-unstyled">
                <li><strong>Khởi hành:</strong> {{ $tour->departure_point }}</li>
                <li><strong>Điểm đến:</strong> {{ $tour->destination }}</li>
                <li><strong>Thời gian:</strong> {{ $tour->duration }}</li>
              </ul>

              <div class="clearfix">
                <div class="pull-left">
                  <strong>Ngày đi:</strong> @if($tour->schedules->isNotEmpty())
                  {{ $tour->schedules
        ->sortBy('start_date')
        ->map(fn($s) => \Carbon\Carbon::parse($s->start_date)->format('d/m'))
        ->implode(', ') }}
                  @else
                  Chưa có lịch khởi hành
                  @endif
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
        <a href="javascript:void(0)" class="loadmore" data-offset="8" data-region="Miền Nam">
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
@endsection

@section('scripts')
<script src="{{ asset('js/client/home.js') }}"></script>
<script src="{{ asset('js/client/plusone.js') }}"></script>
<script src="{{ asset('js/client/support_fixed.js') }}"></script>
<script src="{{ asset('js/client/monthSelect.js') }}"></script>
<script src="{{ asset('js/client/monthSelectindex.js') }}"></script>
<script src="{{ asset('js/client/Drog.js') }}"></script>
<script src="{{ asset('js/client/classList.min.js') }}"></script>
<script src="{{ asset('js/client/flatpickr.min.js') }}"></script>
<script src="{{ asset('js/client/rangePlugin.min.js') }}"></script>
<script src="{{ asset('js/client/scrollPlugin.min.js') }}"></script>
<!-- Locale Vietnamese phải load SAU flatpickr thì lịch mới hiện tiếng việt-->
<script src="{{ asset('js/client/vn.js') }}"></script>

{{-- Recommendation script - QUAN TRỌNG --}}
<script src="{{ asset('js/client/recommendations.js') }}"></script>
@endsection