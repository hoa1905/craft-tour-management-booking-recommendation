@extends('layouts.app')

@section('title', 'Trang chi tiết tour')

@section('head')
<link rel="stylesheet" href="{{ asset('css/client/tour-detail.css') }}">
@endsection

@section('content')
<div id="vnt-content">
  <div id="vnt-navation" class="breadcrumb">
    <div class="wrapper">
      <div class="navation">
        <ul>
          <li><a href="http://127.0.0.1:8000/">Tour du lịch</a></li>
          @if($tour->villages->isNotEmpty())
          <li>
            <a href="javascript:void(0)">
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
                      <td>{{ $tour->departure_point }}</a>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="i-label fa-route"><strong>Điểm Đến:</strong></div>
                      </td>
                      <td>{{ $tour->destination }}</a></td>
                    </tr>
                    <tr>
                      <td>
                        <div class="i-label fa-map-marked-alt">Tham quan:</div>
                      </td>
                      <td>
                        @if($tour->villages->isNotEmpty())
                        @foreach($tour->villages as $village)
                        <a href="{{ route('villages.detail', ['id' => $village->village_id]) }}" target="_blank">
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
                  <!-- Đưa dữ liệu từ Blade sang JS bằng data-* attributes -->
                  <div class="box-detail-tour hasBg"
                    id="tourBookingBox"
                    data-price-adult="{{ $tour->price_adult }}"
                    data-price-child="{{ $tour->price_child }}"
                    data-tour-id="{{ $tour->tour_id }}"
                    data-tour-name="{{ $tour->tour_name }}"
                    data-tour-image="{{ $tour->image_main }}"
                    data-booking-url="{{ route('booking.create') }}"
                    data-csrf-token="{{ csrf_token() }}">
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
                                    end-date="{{ \Carbon\Carbon::parse($schedule->end_date)->format('d/m/Y') }}"
                                    start-date="{{ \Carbon\Carbon::parse($schedule->start_date)->format('d/m/Y') }}">
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
                                <img src="{{ asset('images/client/ihotline.png') }}" alt="ihotline">
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
                            {{-- 1. Hiển thị Ảnh chính --}}
                            @if($tour->image_main)
                            <div class="colsl">
                              <div class="itupgradenma clpopalbum">
                                <img src="{{ asset('images/tours/' . $tour->image_main) }}" alt="Ảnh chính">
                              </div>
                            </div>
                            @endif

                            {{-- 2. Hiển thị danh sách ảnh từ Gallery --}}
                            @if($tour->gallery && is_array($tour->gallery))
                            @foreach($tour->gallery as $img)
                            <div class="colsl">
                              <div class="itupgradenma clpopalbum">
                                <img src="{{ asset('images/tours/' . $img) }}" alt="Ảnh thư viện">
                              </div>
                            </div>
                            @endforeach
                            @endif
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
                                <p style="text-align: justify;">{!! $tour->description !!}</p>
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
                          <div class="txt"><b> Lịch trình tour chi tiết</b></div>
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
                      {{-- Form gửi đánh giá --}}

                      @php
                      $reviewed = false;
                      if (Auth::guard('client')->check()) {
                      $reviewed = \App\Models\Review::where('tour_id', $tour->tour_id)
                      ->where('user_id', Auth::guard('client')->id())
                      ->exists();
                      }
                      @endphp

                      @if(!$reviewed)
                      <div class="panel panel-default" id="reviewFormBox">
                        <div class="panel-heading"><strong>Gửi đánh giá của bạn</strong></div>
                        <div class="panel-body">

                          @if(Auth::guard('client')->check())
                          <form id="reviewForm">
                            <input type="hidden" name="tour_id" value="{{ $tour->tour_id }}">
                            @csrf

                            <div class="form-group">
                              <div class="rating">
                                @for ($i = 5; $i >= 1; $i--)
                                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}">
                                <label for="star{{ $i }}" title="{{ $i }} sao">★</label>
                                @endfor
                              </div>
                            </div>

                            <div class="form-group">
                              <label for="comment">Nội dung đánh giá:</label>
                              <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                              <label>Ảnh (tối đa 5 ảnh):</label>
                              <input type="file" name="images[]" class="form-control" multiple>
                            </div>

                            <button type="submit" class="btn btn-primary">
                              <i class="fa fa-paper-plane"></i> Gửi đánh giá
                            </button>
                          </form>
                          @else
                          <p>Bạn cần đăng nhập và tour đã đi du lịch để đánh giá.</p>
                          @endif
                        </div>
                      </div>
                      <h3 class="text-success" id="alreadyReviewed" style="font-size:18px; display:none;">
                        Bạn đã đánh giá tour này.
                      </h3>
                      @else
                      {{-- Nếu đã đánh giá từ trước --}}
                      <h3 class="text-success" id="alreadyReviewed" style="font-size:18px;">
                        Bạn đã đánh giá tour này.
                      </h3>
                      @endif

                      {{-- Danh sách đánh giá --}}
                      <div class="panel panel-default">
                        <div class="panel-heading">
                          <strong>Đánh giá tour</strong>
                        </div>

                        <ul class="list-group" id="reviewList">
                          @forelse($reviews as $review)
                          <li class="list-group-item">
                            <div style="display: flex; align-items: flex-start; gap: 15px;">

                              {{-- Avatar --}}
                              @if($review->user->avatar)
                              <img src="{{ asset('images/client/avatar/' . $review->user->avatar) }}"
                                style="width:50px;height:50px;border-radius:50%;object-fit:cover;">
                              @else
                              <div style="width:50px;height:50px;border-radius:50%;background:#ddd;display:flex;
                        align-items:center;justify-content:center;font-size:22px;">
                                <i class="fa fa-user"></i>
                              </div>
                              @endif

                              <div style="flex: 1;">
                                <strong>{{ $review->user->user_name }}</strong>
                                <br>
                                <span class="rated">{!! str_repeat('★', $review->rating) !!}</span>
                                <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>

                                <p>{{ $review->comment }}</p>

                                {{-- Images --}}
                                @if(!empty($review->images))
                                <div class="row">
                                  @foreach($review->images as $img)
                                  <div class="col-xs-4">
                                    <img src="{{ asset('images/reviews/' . $img) }}"
                                      class="img-responsive img-thumbnail"
                                      style="max-height:120px;">
                                  </div>
                                  @endforeach
                                </div>
                                @endif
                              </div>

                            </div>
                          </li>
                          @empty
                          <li class="list-group-item">Chưa có đánh giá nào.</li>
                          @endforelse
                        </ul>

                        <div class="text-center" id="reviewPagination">
                          {!! $reviews->links() !!}
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
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/client/tour-detail.js') }}"></script>
<script src="{{ asset('js/client/plusone.js') }}"></script>
<script src="{{ asset('js/client/support_fixed.js') }}"></script>
<script src="{{ asset('js/client/Drog.js') }}"></script>
@endsection