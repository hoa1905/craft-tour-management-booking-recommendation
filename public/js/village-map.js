document.addEventListener("DOMContentLoaded", function () {
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
  fetch("/geojson/vietnam_tinh.geojson")
    .then(res => res.json())
    .then(data => {
      L.geoJSON(data, {
        style: { color: "#3388ff", weight: 1, fillOpacity: 0.1 },
        onEachFeature: (feature, layer) => {
          if (feature.properties?.name)
            layer.bindPopup(`<b>${feature.properties.name}</b>`);
        }
      }).addTo(tinhLayer);
      tinhLayer.addTo(map);
    });

  // Tải GeoJSON phường/xã
  fetch("/geojson/vietnam_xa.geojson")
    .then(res => res.json())
    .then(data => {
      L.geoJSON(data, {
        style: { color: "#ff7800", weight: 0.5, fillOpacity: 0 },
        onEachFeature: (feature, layer) => {
          if (feature.properties?.name)
            layer.bindTooltip(feature.properties.name);
        }
      }).addTo(xaLayer);
      xaLayer.addTo(map);
    });

  // Hiển thị các làng nghề dưới dạng node có ảnh
  const villages = window.villageData || [];

  villages.forEach(village => {
    if (village.latitude && village.longitude) {
      const iconHTML = `
        <div class="village-node"
          style="background-image: url('/images/${village.image_main}');"
        ></div>`;

      const customIcon = L.divIcon({
        html: iconHTML,
        className: "",
        iconSize: [25, 25],
        iconAnchor: [22, 22]
      });

      const marker = L.marker([village.latitude, village.longitude], {
        icon: customIcon
      }).addTo(map);

      // Nội dung popup với ảnh và tên
      const popupContent = `
        <div style="text-align: center; min-width: 200px;">
          <h4 style="margin: 0 0 10px 0; font-size: 16px; color: #1f2937;">${village.village_name}</h4>
          <img src="/images/${village.image_main}"
            alt="${village.village_name}"
            style="width:100%;
            max-width:250px;
            height:150px;
            object-fit:cover;
            border-radius:8px;">
        </div>`;

      // Bind popup (click vẫn hoạt động)
      marker.bindPopup(popupContent);
      // Hiển thị popup khi hover
      marker.on('mouseover', () => marker.openPopup());
      // Đóng popup khi rời chuột (optional)
      marker.on('mouseout', () => marker.closePopup());
      // Click vào marker sẽ chuyển đến trang chi tiết
      marker.on('click', () => window.location.href = `/village-detail/${village.village_id}`);
    }
  });

  // Thêm lá cờ Việt Nam cho quần đảo Hoàng Sa và Trường Sa
  const vietnamIcon = L.icon({
    iconUrl: "/images/vietnam-flag.jpg",
    iconSize: [24, 24]
  });

  // Hover hiển thị popup
  const hoangSaMarker = L.marker([16.5, 112.0], { icon: vietnamIcon })
    .addTo(map)
    .bindPopup('<b>QĐ. HOÀNG SA</b><br>(TP. Đà Nẵng - Việt Nam)', { closeButton: false });

  const truongSaMarker = L.marker([10.0, 114.0], { icon: vietnamIcon })
    .addTo(map)
    .bindPopup('<b>QĐ. TRƯỜNG SA</b><br>(Tỉnh Khánh Hòa - Việt Nam)', { closeButton: false });

  // Hover mở popup, rời chuột đóng popup
  [hoangSaMarker, truongSaMarker].forEach(marker => {
    marker.on('mouseover', () => marker.openPopup());
    marker.on('mouseout', () => marker.closePopup());
  });

  // Bộ chọn lớp bản đồ
  const baseMaps = {
    "Đường phố (OpenStreetMap)": street,
    "Vệ tinh (Esri Satellite)": satellite,
    "Địa hình (OpenTopoMap)": topo
  };

  const overlayMaps = {
    "Tỉnh/Thành": tinhLayer,
    "Phường/Xã": xaLayer,
  };

  L.control.layers(baseMaps, overlayMaps, {
    collapsed: true // Hiển thị icon layers thay vì danh sách mở
  }).addTo(map);
});
