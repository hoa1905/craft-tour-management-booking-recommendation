@extends('client.master')

@section('title', $village->village_name)

@section('body')
<div class="container mx-auto px-4 py-8" style="height: 400px;">
  <h1>
    {{ $village->village_name }}
  </h1>
</div>
<!-- <div class="container mx-auto px-4 py-8">
  <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
    <img src="{{ $village->image_main }}" alt="{{ $village->village_name }}"
      class="w-full h-96 object-cover">

    <div class="p-8">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-4xl font-bold text-gray-900">
          {{ $village->village_name }}
        </h1>
      </div>

      <div class="flex items-center text-gray-600 mb-6">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
        </svg>
        {{ $village->province->province_name }}, {{ $village->province->region->region_name }}
      </div>

      @if($village->history)
      <div class="mb-6">
        <h2 class="text-2xl font-bold mb-3">Lịch sử làng nghề</h2>
        <p class="text-gray-700 leading-relaxed">
          {{ $village->history }}
        </p>
      </div>
      @endif

      @if($village->main_product)
      <div class="mb-6">
        <h2 class="text-2xl font-bold mb-3">Sản phẩm chính</h2>
        <p class="text-gray-700">
          {{ $village->main_product }}
        </p>
      </div>
      @endif

      <div class="mb-6">
        <h2 class="text-2xl font-bold mb-3">Vị trí</h2>
        <div id="detailMap" style="height: 300px; border-radius: 8px;"></div>
      </div>
    </div>
  </div>

  @if($village->tours->count() > 0)
  <div class="bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-2xl font-bold mb-6">Tours tham quan {{ $village->village_name }}</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      @foreach($village->tours as $tour)
      <div class="border rounded-lg overflow-hidden hover:shadow-lg transition">
        <img src="{{ $tour->image_main }}" alt="{{ $tour->tour_name }}"
          class="w-full h-48 object-cover">
        <div class="p-4">
          <h3 class="font-bold text-lg mb-2">{{ $tour->tour_name }}</h3>
          <p class="text-gray-600 text-sm mb-3">{{ $tour->duration }}</p>
          <div class="flex justify-between items-center">
            <span class="text-blue-600 font-bold">
              {{ number_format($tour->price_adult) }}đ
            </span>
            <a href="/tours/{{ $tour->tour_id }}"
              class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
              Xem tour
            </a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif
</div> -->

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection