@extends('layouts.app')

@section('title', 'Bản đồ Làng nghề Truyền thống Việt Nam')

@section('head')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<link rel="stylesheet" href="{{ asset('css/client/village-map.css') }}">
@endsection

@section('content')
<div class="map-container">
  <!-- Loading Overlay -->
  <div id="loading" class="loading-overlay" style="display: none;">
    <div class="spinner"></div>
  </div>

  <!-- Filter Sidebar -->
  <div class="filter-sidebar">
    <h2>Lọc Làng Nghề</h2>

    <form id="filterForm">
      <div class="filter-group">
        <label for="regionFilter">Vùng miền</label>
        <select id="regionFilter" name="region_id">
          <option value="">Tất cả</option>
          @foreach($region_list_filter as $region)
          <option value="{{ $region->region_id }}"
            {{ request('region_id') == $region->region_id ? 'selected' : '' }}>
            {{ $region->region_name }}
          </option>
          @endforeach
        </select>
      </div>

      <div class="filter-group">
        <label for="provinceFilter">Tỉnh/Thành phố</label>
        <select id="provinceFilter" name="province_id">
          <option value="">Tất cả</option>
          @foreach($provinces as $province)
          <option value="{{ $province->province_id }}"
            {{ request('province_id') == $province->province_id ? 'selected' : '' }}>
            {{ $province->province_name }}
          </option>
          @endforeach
        </select>
      </div>

      <button type="submit" class="btn-filter">
        Tìm kiếm
      </button>
    </form>
  </div>

  <!-- Stats Card -->
  <div class="stats-card">
    <div class="stats-item">
      <div class="stats-label">Tổng làng nghề</div>
      <div class="stats-value">{{ $villages->count() }}</div>
    </div>
  </div>

  <!-- Map Container -->
  <div id="map"></div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script>
  // Truyền dữ liệu từ PHP sang JS cho file village-map.js
  window.villageData = JSON.parse('@json($villages ?? [])');
</script>

<script src="{{ asset('js/client/village-map.js') }}"></script>
@endsection