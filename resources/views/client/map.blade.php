@extends('client.master')

@section('title', 'Bản đồ Làng nghề Truyền thống Việt Nam')

@section('head')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

<style>
  #map {
    height: calc(100vh - 50px);
    width: 100%;
    position: relative;
    z-index: 1;
  }

  .map-container {
    position: relative;
  }

  /* Custom Marker Avatar */
  .custom-marker {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s ease;
  }

  .custom-marker:hover {
    transform: scale(1.2);
    border-color: #f59e0b;
    z-index: 1000 !important;
  }

  /* Popup Custom */
  .village-popup {
    min-width: 280px;
  }

  .village-popup .popup-image {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
  }

  .village-popup h3 {
    margin: 0 0 8px 0;
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
  }

  .village-popup .craft-badge {
    display: inline-block;
    background: #f59e0b;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 8px;
  }

  .village-popup .location {
    color: #6b7280;
    font-size: 14px;
    margin-bottom: 8px;
  }

  .village-popup .btn-view {
    display: inline-block;
    background: #3b82f6;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    margin-top: 8px;
    transition: background 0.3s;
  }

  .village-popup .btn-view:hover {
    background: #2563eb;
  }

  /* Filter Sidebar */
  .filter-sidebar {
    position: absolute;
    top: 85px;
    left: 13px;
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    max-width: 320px;
  }

  .filter-sidebar h2 {
    margin: 0 0 16px 0;
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
  }

  .filter-group {
    margin-bottom: 16px;
  }

  .filter-group label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 14px;
  }

  .filter-group select {
    width: 100%;
    padding: 10px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    transition: border 0.3s;
  }

  .filter-group select:focus {
    border-color: #3b82f6;
  }

  .btn-filter {
    width: 100%;
    background: #10b981;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
  }

  .btn-filter:hover {
    background: #059669;
  }

  .btn-reset {
    width: 100%;
    background: #6b7280;
    color: white;
    padding: 10px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 8px;
    transition: background 0.3s;
  }

  .btn-reset:hover {
    background: #4b5563;
  }

  /* Stats Card */
  .stats-card {
    position: absolute;
    top: 200px;
    right: 12px;
    background: white;
    padding: 16px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    min-width: 200px;
  }

  .stats-item {
    margin-bottom: 12px;
  }

  .stats-item:last-child {
    margin-bottom: 0;
  }

  .stats-label {
    font-size: 12px;
    color: #6b7280;
    text-transform: uppercase;
    font-weight: 600;
  }

  .stats-value {
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
  }

  /* Loading */
  .loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2000;
  }

  .spinner {
    border: 4px solid #f3f4f6;
    border-top: 4px solid #3b82f6;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
  }

  /* 🌾 Node làng nghề dạng hình tròn có ảnh */
  .village-node {
    width: 25px;
    height: 25px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.3);
    background-size: cover;
    background-position: center;
    cursor: pointer;
    transition: transform 0.2s ease;
  }

  .village-node:hover {
    transform: scale(1.15);
    border-color: #ff6b00;
  }

  /* Tùy chỉnh popup */
  .leaflet-popup-content {
    text-align: center;
    font-family: 'Arial', sans-serif;
  }

  .leaflet-popup-content h4 {
    margin-bottom: 4px;
    font-size: 15px;
    font-weight: bold;
  }

  .leaflet-popup-content img {
    width: 120px;
    height: 80px;
    border-radius: 8px;
    object-fit: cover;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }

  /* Mobile Responsive */
  @media (max-width: 768px) {
    .filter-sidebar {
      position: fixed;
      top: auto;
      bottom: 0;
      left: 0;
      right: 0;
      max-width: 100%;
      border-radius: 20px 20px 0 0;
      max-height: 70vh;
      overflow-y: auto;
    }

    .stats-card {
      top: auto;
      bottom: 10px;
      right: 10px;
      padding: 12px;
      min-width: 150px;
    }

    .stats-value {
      font-size: 20px;
    }
  }
</style>
@endsection

@section('body')
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
        <select id="regionFilter" name="region">
          <option value="">Tất cả</option>

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
    <div class="stats-item">
      <div class="stats-label">Loại nghề</div>
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
  // Lớp Đường phố (OpenStreetMap)
  const street = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
  });

  // Lớp Vệ tinh (Esri Satellite) + Nhãn (Labels Overlay)
  const satelliteBase = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    attribution: 'Tiles © Esri'
  });

  const satelliteLabels = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
    attribution: 'Labels © Esri',
    opacity: 0.9
  });

  const satellite = L.layerGroup([satelliteBase, satelliteLabels]);

  // Lớp Địa hình (OpenTopoMap)
  const topo = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
    maxZoom: 17,
    attribution: 'Map data: © OpenTopoMap contributors'
  });

  // Khởi tạo bản đồ trung tâm Việt Nam
  const map = L.map('map', {
    center: [16.047, 107.824],
    zoom: 6,
    layers: [street]
  });

  // Các lớp hành chính Việt Nam
  const tinhLayer = L.layerGroup();
  const xaLayer = L.layerGroup();

  // Tải GeoJSON tỉnh/thành
  fetch("{{ asset('geojson/vietnam_tinh.geojson') }}")
    .then(res => res.json())
    .then(data => {
      L.geoJSON(data, {
        style: {
          color: "#3388ff",
          weight: 1,
          fillOpacity: 0.1
        },
        onEachFeature: (feature, layer) => {
          if (feature.properties && feature.properties.name) {
            layer.bindPopup(`<b>${feature.properties.name}</b>`);
          }
        }
      }).addTo(tinhLayer);
      tinhLayer.addTo(map);
    });

  // Tải GeoJSON phường/xã
  fetch("{{ asset('geojson/vietnam_xa.geojson') }}")
    .then(res => res.json())
    .then(data => {
      L.geoJSON(data, {
        style: {
          color: "#ff7800",
          weight: 0.5,
          fillOpacity: 0
        },
        onEachFeature: (feature, layer) => {
          if (feature.properties && feature.properties.name) {
            layer.bindTooltip(feature.properties.name);
          }
        }
      }).addTo(xaLayer);
      xaLayer.addTo(map);
    });

  // Hiển thị các làng nghề dưới dạng node có ảnh
  // const villages = @json($villages); bị gạch đỏ nhưng vẫn chạy được
  const villages = JSON.parse('@json($villages ?? [])');

  villages.forEach(village => {
    if (village.latitude && village.longitude) {
      const iconHTML = `
        <div class="village-node"
          style="background-image: url('{{ asset('images') }}/${village.image_main}');"
        </div>
      `;

      const customIcon = L.divIcon({
        html: iconHTML,
        className: "",
        iconSize: [25, 25],
        iconAnchor: [22, 22],
      });

      const marker = L.marker([village.latitude, village.longitude], {
        icon: customIcon
      }).addTo(map);

      // Nội dung popup với ảnh và tên
      const popupContent = `
        <div style="text-align: center; min-width: 200px;">
          <h4 style="margin: 0 0 10px 0; font-size: 16px; color: #1f2937;">${village.village_name}</h4>
          <img src="{{ asset('images') }}/${village.image_main}" 
               alt="${village.village_name}" 
               style="width: 100%; max-width: 250px; height: 150px; object-fit: cover; border-radius: 8px;">
        </div>
      `;

      // Bind popup (click vẫn hoạt động)
      marker.bindPopup(popupContent, {
        maxWidth: 300,
        closeButton: true
      });

      // Hiển thị popup khi HOVER
      marker.on('mouseover', function() {
        this.openPopup();
      });

      // Đóng popup khi rời chuột (optional)
      marker.on('mouseout', function() {
        this.closePopup();
      });

      // Click vào marker sẽ chuyển đến trang chi tiết
      marker.on('click', () => {
        window.location.href = `/village-detail/${village.village_id}`;
      });
    }
  });

  // Thêm lá cờ Việt Nam cho quần đảo Hoàng Sa và Trường Sa
  const vietnamIcon = L.icon({
    iconUrl: "{{ asset('images/vietnam-flag.jpg') }}",
    iconSize: [24, 24]
  });

  // hover hiển thị popup
  const hoangSaMarker = L.marker([16.5, 112.0], {
      icon: vietnamIcon
    })
    .addTo(map)
    .bindPopup('<b>QĐ. Hoàng Sa</b><br>(TP. Đà Nẵng - Việt Nam)', {
      closeButton: false
    });

  const truongSaMarker = L.marker([10.0, 114.0], {
      icon: vietnamIcon
    })
    .addTo(map)
    .bindPopup('<b>QĐ. Trường Sa</b><br>(Tỉnh Khánh Hòa - Việt Nam)', {
      closeButton: false
    });

  // Hover mở popup, rời chuột đóng popup
  [hoangSaMarker, truongSaMarker].forEach(marker => {
    marker.on('mouseover', function() {
      this.openPopup();
    });
    marker.on('mouseout', function() {
      this.closePopup();
    });
  });

  // Bộ chọn lớp bản đồ
  const baseMaps = {
    "🛣️ Đường phố (OpenStreetMap)": street,
    "🛰️ Vệ tinh (Esri Satellite)": satellite,
    "⛰️ Địa hình (OpenTopoMap)": topo
  };

  const overlayMaps = {
    "Tỉnh/Thành": tinhLayer,
    "Phường/Xã": xaLayer,
  };

  L.control.layers(baseMaps, overlayMaps, {
    collapsed: true // Hiển thị icon layers thay vì danh sách mở
  }).addTo(map);
</script>

@endsection