@extends('client.master')
@section('title', 'Trang tìm kiếm tour')
@section('body')
<div id="vnt-content">
  <div id="vnt-navation" class="breadcrumb">
    <div class="wrapper">
      <div class="navation">
        <ul>
          <li><a href="http://127.0.0.1:8000/">Tour du lịch</a></li>
          <li><a href="#">Tìm kiếm tour</a></li>
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
                  <div class="box-filter">
                    <div class="filter-title">TÌM KIẾM TOUR</div>
                    <div class="item-filter">
                      <div class="tpfindtour">
                        <div class="fichose">
                          <div class="itfindtour">
                            <div class="chtitle">Điểm Khởi Hành</div>
                            <div class="chconts">
                              <select class="form-control chosen-select"
                                id="departure" name="departure">
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
                          <div class="itfindtour">
                            <div class="chtitle">Điểm đến</div>
                            <div class="chconts">
                              <select class="form-control chosen-select"
                                id="destination" name="destination">
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
                          <div class="itfindtour">
                            <div class="chtitle">Ngày khởi hành</div>
                            <div class="chconts fa-date">
                              <input class="flatpickr-input" type="text"
                                name="startdate" id="startdate"
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
            <link href="css/flatpickr.min.css" rel="stylesheet">
            <link href="css/flatpickrcus.css" rel="stylesheet">
            <link href="css/monthSelect.min.css" rel="stylesheet">

            <script src="js/classList.min.js"></script>
            <script src="js/flatpickr.min.js"></script>
            <script src="js/rangePlugin.min.js"></script>
            <script src="js/scrollPlugin.min.js"></script>
            <script src="js/monthSelect.js"></script>
            <script src="js/monthSelectindex.js"></script>
            <script src="js/vn.js"></script>
            <script type="text/javascript" src="js/jquery.number.min.js"></script>
            <script language="javascript">
              vnTTour.initFilter();
            </script>
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
                                  <a href="#" class="sort-link" data-sort="0">Mới nhất</a>
                                </li>
                                <li class="ac-sort {{ request('sort') == 1 ? 'active' : '' }}">
                                  <a href="#" class="sort-link" data-sort="1">Giá tăng dần</a>
                                </li>
                                <li class="ac-sort {{ request('sort') == 2 ? 'active' : '' }}">
                                  <a href="#" class="sort-link" data-sort="2">Giá giảm dần</a>
                                </li>
                                <li class="ac-sort {{ request('sort') == 3 ? 'active' : '' }}">
                                  <a href="#" class="sort-link" data-sort="3">Ngày khởi hành gần nhất</a>
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
                      <img src="{{ asset('images/' . $tour->image_url) }}" class="img-fluid rounded" alt="{{ $tour->tour_name }}">
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
                      <strong>Ngày đi:</strong> {{ \Carbon\Carbon::parse($tour->start_date)->format('d/m') }}
                      @if ($tour->transport === 'Máy bay')
                      <i class="fa fa-plane"></i>
                      @else
                      <i class="fa fa-bus"></i>
                      @endif
                    </p>
                  </div>

                  {{-- Giá --}}
                  <div class="col-md-3 text-end">
                    <p class="mb-1"><i class="fa fa-calendar"></i> {{ $tour->start_date ?? '---' }}</p>
                    <p class="mb-1">Giá 1 khách</p>
                    <h4 class="fw-bold tour-price">{{ number_format($tour->price, 0, ',', '.') }} đ</h4>
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

{{-- AJAX Sắp xếp --}}
<script>
  $(document).ready(function() {
    $('.sort-link').on('click', function(e) {
      e.preventDefault();
      const sortValue = $(this).data('sort');
      const url = new URL(window.location.href);
      url.searchParams.set('sort', sortValue);

      $.ajax({
        url: url.toString(),
        type: 'GET',
        beforeSend: function() {
          $('#tour-list').html('<div class="text-center py-4">Đang tải...</div>');
        },
        success: function(response) {
          // Lấy nội dung #tour-list từ HTML trả về
          const newContent = $(response).find('#tour-list').html();
          $('#tour-list').html(newContent);

          // Cập nhật URL mà không reload trang
          window.history.pushState({}, '', url);

          // Cập nhật trạng thái active
          $('.sort-link').parent().removeClass('active');
          $(`.sort-link[data-sort="${sortValue}"]`).parent().addClass('active');
        },
        error: function() {
          alert('Lỗi khi tải dữ liệu.');
        }
      });
    });

    // Xử lý quay lại trình duyệt (back/forward)
    window.onpopstate = function() {
      $.get(window.location.href, function(response) {
        const newContent = $(response).find('#tour-list').html();
        $('#tour-list').html(newContent);
      });
    };
  });
</script>

{{-- AJAX phân trang --}}
<script>
  $(document).ready(function() {
    $(document).on('click', '.pagination a', function(e) {
      e.preventDefault();

      let url = new URL($(this).attr('href'), window.location.origin);
      const currentSort = new URL(window.location.href).searchParams.get('sort');
      if (currentSort) url.searchParams.set('sort', currentSort);

      $.ajax({
        url: url.toString(),
        type: 'GET',
        beforeSend: function() {
          // Hiển thị trạng thái đang tải
          $('#tour-list').html('<div class="text-center py-4">Đang tải...</div>');
        },
        success: function(response) {
          // Cập nhật danh sách tour và phân trang mới
          const newTours = $(response).find('#tour-list').html();
          const newPagination = $(response).find('.pagination-wrapper').html();

          $('#tour-list').html(newTours);
          $('.pagination-wrapper').html(newPagination);

          // Cập nhật URL trên trình duyệt mà không load lại trang
          window.history.pushState({}, '', url);
        },
        error: function() {
          alert('Không thể tải dữ liệu. Vui lòng thử lại.');
        }
      });
    });

    // Khi người dùng nhấn nút quay lại (Back/Forward)
    window.onpopstate = function() {
      $.get(window.location.href, function(response) {
        const newTours = $(response).find('#tour-list').html();
        const newPagination = $(response).find('.pagination-wrapper').html();

        $('#tour-list').html(newTours);
        $('.pagination-wrapper').html(newPagination);
      });
    };
  });
</script>

{{-- AJAX tìm kiếm --}}
<script>
  $(document).ready(function() {
    // Khi submit form tìm kiếm
    $('#formModSearch').on('submit', function(e) {
      e.preventDefault();

      let params = []; // Mảng chứa các cặp key=value

      // Duyệt qua các input/select có name
      $(this).find('input[name], select[name]').each(function() {
        let name = $(this).attr('name');
        let value = $(this).val()?.trim();

        // Chỉ thêm nếu có giá trị
        if (value) {
          params.push(encodeURIComponent(name) + '=' + encodeURIComponent(value));
        }
      });

      // Ghép query string thủ công
      let queryString = params.join('&');
      let url = $(this).attr('action');
      if (queryString) {
        url += '?' + queryString;
      }

      $.ajax({
        url: url,
        type: 'GET',
        beforeSend: function() {
          $('#tour-list').html('<p>Đang tìm kiếm tour...</p>');
        },
        success: function(response) {
          // Xóa message cũ (nếu có)
          $('.alert-warning').remove();

          // Lấy message mới từ response (nếu có)
          let newMessage = $(response).find('.alert-warning');
          if (newMessage.length > 0) {
            $('.mid-content').prepend(newMessage);
          }

          // Cập nhật phần hiển thị thông báo kết quả (tlfind)
          let newResultInfo = $(response).find('.tlfind').html();
          if (newResultInfo && newResultInfo.trim() !== '') {
            $('.tlfind').html(newResultInfo);
          } else {
            $('.tlfind').empty();
          }

          // Cập nhật danh sách tour và phân trang
          $('#tour-list').html($(response).find('#tour-list').html());
          $('.pagination-wrapper').html($(response).find('.pagination-wrapper').html());

          // ✅ Cập nhật URL mà không load lại trang
          window.history.pushState({}, '', url);
        },
        error: function() {
          $('#tour-list').html('<p class="text-danger">Lỗi khi tìm kiếm tour.</p>');
        }
      });
    });
  });
</script>

@endsection