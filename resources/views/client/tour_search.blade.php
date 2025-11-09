@extends('client.master')

@section('title', 'Trang tìm kiếm tour')

@section('head')
<link rel="stylesheet" href="{{ asset('css/tour-search.css') }}">
<link rel="stylesheet" href="css/flatpickr.min.css">
<link rel="stylesheet" href="css/flatpickrcus.css">
<link rel="stylesheet" href="css/monthSelect.min.css">
@endsection

@section('content')
<div id="vnt-content">
  <div id="vnt-navation" class="breadcrumb">
    <div class="wrapper">
      <div class="navation">
        <ul>
          <li><a href="http://127.0.0.1:8000/">Tour du lịch</a></li>
          <li><a href="javascript:void(0)">Tìm kiếm tour</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="box_mid">
    <div class="wrapper">
      <div class="mid-title">
        <div class="fl">
          <h1 class="title-main vmargin">Tour</h1>
        </div>
        <div class="fr">
          <div class="hidden-lg hidden-md visible-sm visible-xs">
            <div class="popup_mobile" id="sapxep">
              <div class="pmTitle">Sắp xếp</div>
              <div class="pmClose"><i class="fa fa-times"></i></div>
              <div class="pmContent">
                <div class="list-filter">
                  <ul>
                    <li class="ac-sort active" data-value="0"><a href="javascript:void(0)"
                        class="current active">Mặc định</a></li>
                    <li class="ac-sort" data-value="1"><a href="javascript:void(0)">Mới nhất</a>
                    </li>
                    <li class="ac-sort" data-value="2"><a href="javascript:void(0)">Giá tăng
                        dần</a></li>
                    <li class="ac-sort" data-value="3"><a href="javascript:void(0)">Giá giảm
                        dần</a></li>
                    <li class="ac-sort" data-value="4"><a href="javascript:void(0)">Ngày khởi
                        hành gần nhất</a></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="clear"></div>
      </div>
      <div class="mid-content">
        @if(!empty($message))
        <div class="alert alert-warning text-center">{{ $message }}</div>
        @endif
        <div class="tptooltour hidden-md hidden-lg">
          <div class="tlfind">
            @if ($tours->total() > 0)
            Kết quả: Tìm thấy <strong class="num_tour">{{ $tours->total() }}</strong> tour phù hợp.
            @endif
          </div>
        </div>
        <div class="hidden-lg hidden-md visible-sm visible-xs">
          <div class="filter-order">
            <ul>
              <li data-id="boloc"><i class="fa fa-search"></i>Tìm tour</li>
              <li data-id="sapxep"><i class="fa fa-sort-alpha-down"></i> Sắp xếp</li>
            </ul>
          </div>
        </div>
        <div class="row cel-p10">
          <div class="col-md-3 col-sm-12 col-xs-12">
            <div class="popup_mobile" id="boloc">
              <div class="pmTitle">Bộ lọc</div>
              <div class="pmClose"><i class="fa fa-times"></i></div>
              <div class="pmContent">
                <form id="formModSearch" name="formModSearch" action="{{ route('tour.search') }}" method="get">
                  <!-- Tham số phân biệt form -->
                  <input type="hidden" name="search_type" value="exact">
                  <div class="box-filter">
                    <div class="filter-title">TÌM KIẾM TOUR</div>
                    <div class="item-filter">
                      <div class="tpfindtour">
                        <div class="fichose">
                          <div class="itfindtour">
                            <div class="chtitle">Điểm Khởi Hành</div>
                            <div class="chconts">
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
                          <div class="itfindtour">
                            <div class="chtitle">Điểm đến</div>
                            <div class="chconts">
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

                          <div class="itfindtour">
                            <div class="chtitle">Ngày khởi hành</div>
                            <div class="chconts fa-date">
                              <input class="flatpickr-input" type="text"
                                name="start_date" id="startdate"
                                placeholder="Chọn ngày, tháng ---" value="">
                            </div>
                          </div>
                          <div class="itfindtour">
                            <div class="chtitle">Thời lượng tour</div>
                            <div class="chconts">
                              <select class="form-control chosen-select"
                                name="duration" id="duration">
                                <option value="">Thời lượng tour ---</option>
                                <option value="1 ngày">1 ngày</option>
                                <option value="2 ngày">2 ngày</option>
                                <option value="3 ngày">3 ngày</option>
                                <option value="4 ngày">4 ngày</option>
                                <option value="5 ngày">5 ngày</option>
                                <option value="6 ngày">6 ngày</option>
                                <option value="7 ngày">7 ngày</option>
                                <option value="8 ngày">8 ngày</option>
                                <option value="9 ngày">9 ngày</option>
                                <option value="10 ngày">10 ngày</option>
                                <option value="11 ngày">11 ngày</option>
                                <option value="12 ngày">12 ngày</option>
                                <option value="13 ngày">13 ngày</option>
                                <option value="14 ngày">14 ngày</option>
                                <option value="15 ngày">15 ngày</option>
                              </select>
                            </div>
                          </div>
                          <div class="itfindtour">
                            <div class="chtitle">Giá tour</div>
                            <div class="chconts">
                              <div class="box-filter-price">
                                <div id="slider_price"></div>

                                <div class="slider-info">
                                  <div class="s-item">
                                    <span id="price_min" class="number_vnd"></span> đ
                                  </div>
                                  <div class="s-item">
                                    <span id="price_max" class="number_vnd"></span> đ
                                  </div>
                                </div>

                                <input type="hidden" name="s_price_min" id="s_price_min" value="0">
                                <input type="hidden" name="s_price_max" id="s_price_max" value="50000000">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="filinks">
                          <button type="submit" id="btnModSearch">
                            <i class="fab fa-sistrix"></i>
                            <span>Tìm tour</span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="col-md-9 col-sm-12 col-xs-12">
            <div class="tptooltour hidden-xs hidden-sm">
              <div class="tlgrip">
                <div class="xcol">
                  <div class="tlfind">
                    @if ($tours->total() > 0)
                    Kết quả: Tìm thấy <strong class="num_tour">{{ $tours->total() }}</strong> tour phù hợp.
                    @endif
                  </div>
                </div>
                <div class="ycol">
                  <div class="tlsort">
                    <div class="ssgrip">
                      <div class="lcol">
                        <div class="sttext">Sắp xếp:</div>
                      </div>
                      <div class="rcol">
                        <div class="sinfo">
                          <div class="vnt-box-select">
                            <div class="vnt-box-select__text"><span>Mới nhất</span>
                            </div>
                            <div class="vnt-box-select__list">
                              <ul>
                                <li class="ac-sort {{ request('sort') == 0 ? 'active' : '' }}">
                                  <a href="javascript:void(0)" class="sort-link" data-sort="0">Mới nhất</a>
                                </li>
                                <li class="ac-sort {{ request('sort') == 1 ? 'active' : '' }}">
                                  <a href="javascript:void(0)" class="sort-link" data-sort="1">Giá tăng dần</a>
                                </li>
                                <li class="ac-sort {{ request('sort') == 2 ? 'active' : '' }}">
                                  <a href="javascript:void(0)" class="sort-link" data-sort="2">Giá giảm dần</a>
                                </li>
                                <li class="ac-sort {{ request('sort') == 3 ? 'active' : '' }}">
                                  <a href="javascript:void(0)" class="sort-link" data-sort="3">Ngày khởi hành gần nhất</a>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Danh sách tour --}}
            <div id="tour-list" class="tour-list">
              @foreach ($tours as $tour)
              <div class="card mb-4 shadow-sm border-light">
                <div class="row g-0 align-items-center p-3">

                  {{-- Ảnh --}}
                  <a href="{{ route('tour.detail', $tour->tour_id) }}">
                    <div class="col-md-3 position-relative">
                      <img src="{{ asset('images/' . $tour->image_main) }}" class="img-fluid rounded" alt="{{ $tour->tour_name }}">
                    </div>
                  </a>

                  {{-- Nội dung --}}
                  <div class="col-md-6 ps-3">
                    <a href="{{ route('tour.detail', $tour->tour_id) }}">
                      <h5 class="fw-bold mb-2 tour-title">{{ $tour->tour_name }}</h5>
                    </a>
                    <p class="mb-1"><strong>Điểm đi:</strong> {{ $tour->departure_point}}</p>
                    <p class="mb-1"><strong>Điểm đến:</strong> {{ $tour->destination }}</p>
                    <p class="mb-1"><strong>Thời gian:</strong> {{ $tour->duration }}</p>
                    <p class="mb-0">
                      <strong>Ngày đi:</strong> @if($tour->schedules->isNotEmpty())
                      {{ $tour->schedules
        ->sortBy('start_date')
        ->map(fn($s) => \Carbon\Carbon::parse($s->start_date)->format('d/m'))
        ->implode(', ') }}
                      @else
                      Chưa có lịch khởi hành
                      @endif


                      @if ($tour->transport === 'Máy bay')
                      <i class="fa fa-plane"></i>
                      @else
                      <i class="fa fa-bus"></i>
                      @endif
                    </p>
                  </div>

                  {{-- Giá --}}
                  <div class="col-md-3 text-end">
                    <p class="mb-1">
                      <i class="fa fa-calendar"></i>
                      {{ $tour->schedules->isNotEmpty() 
      ? \Carbon\Carbon::parse($tour->schedules->min('start_date'))->format('d/m/Y') 
      : '---' 
  }}
                    </p>

                    <p class="mb-1">Giá 1 khách</p>
                    <h4 class="fw-bold tour-price">{{ number_format($tour->price_adult, 0, ',', '.') }} đ</h4>
                  </div>
                </div>
              </div>
              @endforeach
            </div>

            {{-- Phân trang --}}
            <div class="pagination-wrapper mt-4">
              {{ $tours->links('vendor.pagination.custom-pagination') }}
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/tour-search.js') }}"></script>
<script src="{{ asset('js/classList.min.js') }}"></script>
<script src="{{ asset('js/jquery.number.min.js') }}"></script>
<script src="{{ asset('js/flatpickr.min.js') }}"></script>
<script src="{{ asset('js/rangePlugin.min.js') }}"></script>
<script src="{{ asset('js/scrollPlugin.min.js') }}"></script>
<script src="{{ asset('js/monthSelect.js') }}"></script>
<script src="{{ asset('js/monthSelectindex.js') }}"></script>
<!-- Locale Vietnamese phải load SAU flatpickr thì lịch mới hiện tiếng việt-->
<script src="{{ asset('js/vn.js') }}"></script>
<!-- Các JS custom gọi flatpickr -->
<script>
  // Khởi tạo lịch và Price slider
  vnTTour.initFilter();
</script>
@endsection