document.addEventListener("DOMContentLoaded", function () {
  // --- 1. Khởi tạo các lớp bản đồ  ---

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

  // --- 2. Khởi tạo các lớp phủ (Overlays) ---
  // Khởi tạo LayerGroup trước để nạp vào map
  const tinhLayer = L.layerGroup();
  const xaLayer = L.layerGroup();

  // --- 3. Khởi tạo bản đồ ---
  const map = L.map('map', {
    // Trung tâm Việt Nam
    center: [16.047, 107.824],
    zoom: 6,
    // Khai báo street và tinhLayer ở đây để mặc định hiện
    layers: [street, tinhLayer]
  });

  // --- 4. Tải dữ liệu GEOJSON ---

  // Tải Tỉnh/Thành (Thêm vào tinhLayer, đã được bật mặc định)
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
    }).catch(err => console.error("Lỗi tải GeoJSON Tỉnh/Thành:", err));

  /// Tải Xã/Phường (Thêm vào xaLayer, mặc định đang tắt)
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
    }).catch(err => console.error("Lỗi tải GeoJSON Xã/Phường:", err));

  // --- 5. Hiển thị các làng nghề dưới dạng nodes có ảnh ---
  const villages = window.villageData || [];
  console.log("Đang hiển thị số lượng làng nghề:", villages.length);

  villages.forEach(village => {
    if (village.latitude && village.longitude) {
      const iconHTML = `
        <div class="village-node"
          style="background-image: url('/images/villages/${village.image_main}');"
        ></div>`;

      const customIcon = L.divIcon({
        html: iconHTML,
        // Để trống để dùng class trong HTML
        className: "",
        // Kích thước phải khớp với(class village-node, Width CSS () + Border * 2)
        iconSize: [30, 30],
        // Tâm của icon (để mũi nhọn ghim đúng vị trí)
        // Là một nửa của iconSize
        iconAnchor: [15, 15]
      });

      const marker = L.marker([village.latitude, village.longitude], {
        icon: customIcon
      }).addTo(map);

      // Nội dung popup với ảnh và tên
      const popupContent = `
        <div style="text-align: center; min-width: 200px;">
          <h4 style="margin: 0 0 10px 0; font-size: 16px; color: #1f2937;">${village.village_name}</h4>
          <img src="/images/villages/${village.image_main}"
            alt="${village.village_name}"
            style="width:100%;
            max-width:250px;
            height:150px;
            object-fit:cover;
            border-radius:8px;">
        </div>`;

      /* Khi người dùng Click chuột vào marker đó trên bản đồ, Leaflet sẽ tự động:
        * Tạo ra cái bong bóng chat (popup).
        * Đổ nội dung popupContent vào trong.
        * Hiển thị nó ngay phía trên marker.
      */
      marker.bindPopup(popupContent);

      // Sự kiện chuột

      // Hiển thị popup khi hover
      marker.on('mouseover', () => marker.openPopup());
      // Đóng popup khi rời chuột (optional)
      marker.on('mouseout', () => marker.closePopup());
      // Click vào marker sẽ chuyển đến trang chi tiết
      marker.on('click', () => window.location.href = `/village-detail/${village.village_id}`);
    }
  });

  // --- 6. Thêm lá cờ Việt Nam cho quần đảo Hoàng Sa và Trường Sa ---
  const vietnamIcon = L.icon({
    iconUrl: "/images/client/vietnam-flag.jpg",
    // Tỷ lệ lá cờ 3:2
    iconSize: [45, 30],
    // Tâm icon để ghim đúng vị trí(Một nửa của iconSize)
    iconAnchor: [22, 15]
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
    "Tỉnh/Thành": tinhLayer, // Đã được bật mặc định
    "Phường/Xã": xaLayer,
  };

  L.control.layers(baseMaps, overlayMaps, {
    collapsed: true // Hiển thị icon layers thay vì danh sách mở
  }).addTo(map);
});
