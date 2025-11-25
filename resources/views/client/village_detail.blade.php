@extends('layouts.app')

@section('title', $village->village_name)

@section('head')
<link rel="stylesheet" href="{{ asset('css/client/village-detail.css') }}">
@endsection

@section('content')
<div id="vnt-content">
  @php
  $bgImage = $village->image_main ? asset('images/villages/' . $village->image_main) : asset('images/client/banner-default.jpg');
  @endphp
  <div class="village-hero" style="background-image: url('{{ $bgImage }}');">
    <div class="container">
      <div class="hero-content">
        <h1 class="hero-title">{{ $village->village_name }}</h1>
        <div class="hero-meta">
          {{-- Địa chỉ --}}
          <span class="info-value me-4">
            <i class="fas fa-map-marker-alt me-1 text-warning"></i> {{ $village->address }}
          </span>

          {{-- Miền --}}
          <span class="info-value">
            <i class="fas fa-globe-asia me-1 text-info"></i> {{ $village->province->region->region_name }}
          </span>

        </div>
      </div>
    </div>
  </div>

  <div class="vnt-tintuc vnt-tour-load box-tour" data-id="0" data-type="focus">
    <div class="wrapper">
      <div class="container">
        <div class="row g-4">
          {{-- 1. Cột trái --}}
          <div class="col-lg-10">
            <div class="mb-5">
              <h2 class="section-title">Giới thiệu làng nghề</h2>
              <div class="village-description">
                {!! $village->description !!}
              </div>
            </div>
          </div>

          {{-- 2. Cột phải --}}
          <div class="col-lg-2">
            @if(!empty($village->gallery) && is_array($village->gallery))
            <div class="mb-5">
              <h3 class="section-title">Thư viện ảnh</h3>
              <div class="gallery-grid">
                @foreach($village->gallery as $img)
                <div class="gallery-item">
                  <a href="{{ asset('images/villages/' . $img) }}" data-fancybox="gallery" data-caption="{{ $village->village_name }}">
                    <img src="{{ asset('images/villages/' . $img) }}" alt="Ảnh làng nghề">
                  </a>
                </div>
                @endforeach
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>
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
          $tourImage = asset('images/tours/' . $tour->image_main);
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
    </div>
  </div>
  @endsection