@extends('client.master')

@section('title', $village->village_name)
<link rel="stylesheet" href="{{ asset('css/village-detail.css') }}">
@section('head')

@endsection

@section('content')
<div id="vnt-content">
  <div class="village-detail">
    <div class="wrapper">
      <div class="village-header">
        <h1>{{ $village->village_name }}</h1>
        @if(isset($village->province->province_name))
        <p><i class="fa fa-map-marker-alt"></i> {{ $village->province->province_name }}</p>
        @endif
      </div>

      @php
      $villageImage = $village->image_main
      ? asset('images/' . $village->image_main)
      : asset('images/banner-1.jpg'); // Fallback
      @endphp

      <div class="village-image"
        style="background-image: url('{{ $villageImage }}');">
      </div>

      <div class="village-content">
        <h2 class="text-2xl font-bold mb-3">Lịch sử làng nghề</h2>
        <p class="text-gray-700 leading-relaxed">{{ $village->history }}</p>
      </div>

      <ul class="village-info mt-4">
        <li><i class="fa fa-gem"></i> <strong>Giá trị truyền thống:</strong> {{ $village->traditional_value }}</li>
        <li><i class="fa fa-industry"></i> <strong>Ngành nghề chính:</strong> {{ $village->main_product }}</li>
      </ul>
    </div>
  </div>

  <div class="vnt-tintuc vnt-tour-load box-tour" data-id="0" data-type="focus">
    <div class="wrapper">
      <div class="style-title-1 lazyloading">
        <div class="fl">
          <h2 class="stTitle">Tour liên quan</h2>
        </div>
        <div class="clear"></div>
      </div>

      <div class="vhtourkm loadBottom">
        <div id="related-tour-list" class="row g-4">
          @forelse ($village->tours as $tour)
          @php
          $tourImage = asset('images/' . $tour->image_main);
          @endphp
          <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="tour-card">
              <a href="{{ route('tour.detail', ['id' => $tour->tour_id]) }}">
                <div class="tour-image"
                  style="background-image: url('{{ $tourImage }}');">
                  <span class="tour-region">
                    <i class="fa fa-map-marker-alt"></i> {{ $tour->destination }}
                  </span>
                </div>
              </a>

              <div class="tour-body">
                <h4 class="tour-title">
                  <a href="{{ route('tour.detail', ['id' => $tour->tour_id]) }}">
                    {{ $tour->tour_name }}
                  </a>
                </h4>
                <div class="tour-price">
                  {{ number_format($tour->price_adult, 0, ',', '.') }} <span class="currency">đ</span>
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
          @empty
          <div class="col-12 text-center text-muted">Chưa có tour liên quan.</div>
          @endforelse
        </div>
      </div>

      @if($village->tours->count() > 8)
      <div class="viewall">
        <a href="#" class="btn btn-outline-primary px-4">Xem thêm</a>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection