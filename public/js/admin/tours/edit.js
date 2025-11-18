document.addEventListener("DOMContentLoaded", function () {

  // =========================================================
  // 1. Khởi tạo các thư viện
  // =========================================================

  // Khóa để lưu trong localStorage
  const STORAGE_NEW_SCHEDULES = 'tour_edit_new_schedules';
  const STORAGE_NEW_ITINERARIES = 'tour_edit_new_itineraries';

  // Hàm tạo ID duy nhất cho các hàng mới
  function uuid() {
    return "xxxx-4xxx-yxxx".replace(/[xy]/g, c => {
      const r = Math.random() * 16 | 0;
      const v = c === "x" ? r : (r & 0x3 | 0x8);
      return v.toString(16);
    });
  }

  // Khởi tạo CKEditor
  if (document.getElementById('description')) {
    CKEDITOR.replace('description');
  }

  // Khởi tạo Select2 (jQuery $ đã được tải từ master)
  try {
    $('.select2-basic').select2({
      theme: "bootstrap-5",
      width: '100%'
    });
  } catch (e) {
    console.error("Lỗi khởi tạo Select2:", e);
  }

  // =========================================================
  // 2. Format giá tiền
  // =========================================================
  function formatMoney(v) {
    if (!v) return "";
    return v.toString().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }

  function unformat(v) {
    if (!v) return "";
    return v.toString().replace(/\./g, "");
  }

  document.querySelectorAll(".price-format").forEach(input => {
    // Format ngay khi load
    input.value = formatMoney(input.value);

    // Format khi nhập
    input.addEventListener("input", () => {
      input.value = formatMoney(input.value);
      // Cập nhật input ẩn ngay lập tức
      const hidden = document.getElementById(input.dataset.hidden);
      if (hidden) {
        hidden.value = unformat(input.value);
      }
    });
  });

  // =========================================================
  // 3. Chọn làng nghề và cập nhật miền
  // =========================================================

  // Đọc dữ liệu từ data attribute
  const villagePickerContainer = document.querySelector(".village-picker-container");
  let allVillagesByProvince = {}; // Khởi tạo biến rỗng

  if (villagePickerContainer) {
    const villagesData = villagePickerContainer.dataset.villagesByProvince;
    if (villagesData) {
      try {
        // Đọc attribute và parse JSON
        allVillagesByProvince = JSON.parse(villagesData);
      } catch (e) {
        console.error("Lỗi parse JSON làng nghề:", e, villagesData);
      }
    }
  }

  const villageSource = document.getElementById("village-source");
  const addVillageBtn = document.getElementById("add-village-btn");
  const selectedVillagesList = document.getElementById("selected-villages-list");

  // ----- 3.1. Hàm để load làng nghề -----
  function populateVillageSource(provinceId) {
    villageSource.innerHTML = ""; // Xóa các làng nghề cũ
    if (provinceId && allVillagesByProvince[provinceId]) {
      const villages = allVillagesByProvince[provinceId];
      villages.forEach(village => {
        const option = document.createElement("option");
        option.value = village.village_id;
        option.textContent = village.village_name;
        option.dataset.province = village.province.province_name;
        option.dataset.region = village.province.region.region_name;
        villageSource.appendChild(option);
      });
    }
  }

  // ----- 3.2. Khi thay đổi Tỉnh (Dùng sự kiện 'select2:select' của JQUERY) -----
  $('#province-source').on('select2:select', function (e) {
    const provinceId = e.params.data.id;
    populateVillageSource(provinceId);
  });

  // ----- 3.3. Khi bấm nút "Thêm vào tour" -----
  if (addVillageBtn) {
    addVillageBtn.addEventListener("click", function () {
      Array.from(villageSource.selectedOptions).forEach(option => {
        const villageId = option.value;
        const isAlreadyAdded = selectedVillagesList.querySelector(`input[value="${villageId}"]`);
        const selectedProvinceId = $('#province-source').val();

        if (!isAlreadyAdded) {
          addVillageToSelectedList(
            villageId,
            option.textContent,
            option.dataset.province,
            option.dataset.region,
            selectedProvinceId // Truyền ID tỉnh vào
          );
        }
      });
      updateTourRegions(); // Cập nhật miền
      updateTourDestinations(); // Cập nhật điểm đến
    });
  }

  // ----- 3.4. Hàm render 1 làng nghề đã chọn -----
  function addVillageToSelectedList(id, name, province, region, provinceId) {
    const itemHTML = `
            <div class="selected-village-item"
                data-region="${region}"
                data-province="${province}"
                data-province-id="${provinceId}">
              <div class="village-info">
                  <strong>${name}</strong>
                  <small>${province} - ${region}</small>
              </div>
              <button type="button" class="btn-close remove-village-btn" aria-label="Close"></button>
              <input type="hidden" name="villages[]" value="${id}">
            </div>
        `;
    selectedVillagesList.insertAdjacentHTML("beforeend", itemHTML);
  }

  // ----- 3.5. Khi bấm nút icon "Xóa" làng nghề (dùng event delegation) -----
  if (selectedVillagesList) {
    selectedVillagesList.addEventListener("click", function (e) {
      if (e.target.classList.contains("remove-village-btn")) {
        // Xóa hàng
        e.target.closest(".selected-village-item").remove();
        // Cập nhật lại 2 ô "Miền" và "Điểm đến"
        updateTourRegions();
        updateTourDestinations();

        // Tìm làng nghề đầu tiên còn lại
        const firstRemainingVillage = selectedVillagesList.querySelector('.selected-village-item');

        if (firstRemainingVillage) {
          // Nếu còn làng nghề:
          // Lấy ID tỉnh của làng nghề đầu tiên đó
          const provinceIdToSelect = firstRemainingVillage.dataset.provinceId;

          // 1. Cập nhật ô "1. Chọn tỉnh" (dùng jQuery)
          $('#province-source').val(provinceIdToSelect).trigger('change');

          // 2. Tải lại danh sách làng nghề (ô "2. Chọn (các) làng nghề")
          populateVillageSource(provinceIdToSelect);

        } else {
          // Nếu không còn làng nghề nào:
          // 1. Reset ô "1. Chọn tỉnh" về mặc định
          $('#province-source').val("").trigger('change'); // Giá trị rỗng là "-- Chọn tỉnh --"

          // 2. Xóa sạch danh sách làng nghề (ô "2. Chọn (các) làng nghề")
          populateVillageSource("");
        }
      }
    });
  }

  // ----- 3.6. Cập nhật ô hiển thị miền -----
  function updateTourRegions() {
    const regionDisplay = document.getElementById('tour-regions-display');
    // Thoát nếu không tìm thấy
    if (!regionDisplay) return;

    // Lấy tất cả làng nghề (cả từ Blade và JS)
    const items = selectedVillagesList.querySelectorAll('.selected-village-item[data-region]');
    // Lấy giá trị data-region từ mỗi item
    const regions = Array.from(items).map(item => item.dataset.region);
    // Lọc ra các miền duy nhất
    const uniqueRegions = [...new Set(regions)];
    // Hiển thị
    regionDisplay.value = uniqueRegions.join(', ');
  }

  // ----- 3.7. Hàm cập nhật ô "Điểm đến (Tỉnh)" -----
  function updateTourDestinations() {
    const destinationDisplay = document.getElementById('tour-destinations-display');
    if (!destinationDisplay) return;

    // Lấy tất cả làng nghề (cả từ Blade và JS)
    const items = selectedVillagesList.querySelectorAll('.selected-village-item[data-province]');
    // Lấy giá trị data-province từ mỗi item
    const provinces = Array.from(items).map(item => item.dataset.province);
    // Lọc ra các tỉnh duy nhất
    const uniqueProvinces = [...new Set(provinces)];
    // Hiển thị
    destinationDisplay.value = uniqueProvinces.join(', ');
  }

  // =========================================================
  // 4. Lịch khởi hành - Tích hơp LocalStorage
  // =========================================================
  const schedulesContainer = document.getElementById("schedules-container");
  const addScheduleBtn = document.getElementById("add-schedule");
  let scheduleCounter = schedulesContainer ? parseInt(schedulesContainer.dataset.counter, 10) : 0;

  // ----- Hàm render HTML -----
  function renderScheduleRow(index, id, data = {}) {
    const startDate = data.start_date || '';
    const endDate = data.end_date || '';
    const seats = data.seats_available || '';

    return `
            <div class="row mb-2 schedule-row" data-id="${id}">
                <div class="col-md-4">
                    <label class="form-label">Ngày bắt đầu</label>
                    <input type="date" name="schedules[${index}][start_date]" class="form-control start-date" value="${startDate}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Ngày kết thúc</label>
                    <input type="date" name="schedules[${index}][end_date]" class="form-control end-date" value="${endDate}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Số ghế còn trống</label>
                    <input type="number" name="schedules[${index}][seats_available]" class="form-control seats" value="${seats}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-schedule">Xóa</button>
                </div>
            </div>
        `;
  }

  // ----- Hàm lưu và LocalStorage -----
  function saveNewSchedules() {
    const rows = document.querySelectorAll("#schedules-container .schedule-row[data-id]");
    const data = Array.from(rows).map(row => ({
      id: row.dataset.id,
      start_date: row.querySelector('.start-date').value,
      end_date: row.querySelector('.end-date').value,
      seats_available: row.querySelector('.seats').value
    }));
    localStorage.setItem(STORAGE_NEW_SCHEDULES, JSON.stringify(data));
  }

  // ----- Hàm tải từ LocalStorage (Khi load trang) -----
  function loadNewSchedules() {
    const data = JSON.parse(localStorage.getItem(STORAGE_NEW_SCHEDULES) || "[]");
    data.forEach(s => {
      const html = renderScheduleRow(scheduleCounter, s.id, s);
      schedulesContainer.insertAdjacentHTML("beforeend", html);
      scheduleCounter++;
    });
  }

  // ----- Gắn sự kiện -----
  if (addScheduleBtn) {
    addScheduleBtn.addEventListener("click", () => {
      const id = uuid(); // Tạo ID mới
      const html = renderScheduleRow(scheduleCounter, id, {});
      schedulesContainer.insertAdjacentHTML("beforeend", html);
      scheduleCounter++;
      saveNewSchedules(); // Lưu ngay sau khi thêm
    });
  }

  if (schedulesContainer) {
    // Xóa
    schedulesContainer.addEventListener("click", e => {
      if (e.target.classList.contains("remove-schedule")) {
        const row = e.target.closest(".schedule-row");
        row.remove();
        // Nếu là hàng mới (có data-id), lưu lại danh sách
        if (row.dataset.id) {
          saveNewSchedules();
        }
      }
    });
    // Tự động lưu khi gõ
    schedulesContainer.addEventListener('input', e => {
      if (e.target.closest('.schedule-row[data-id]')) {
        saveNewSchedules();
      }
    });
  }

  // =========================================================
  // 5. Lịch trình - Tích hợp LocalStorage
  // =========================================================
  const itinerariesContainer = document.getElementById("itineraries-container");
  const addItineraryBtn = document.getElementById("add-itinerary");
  let itineraryCounter = itinerariesContainer ? parseInt(itinerariesContainer.dataset.counter, 10) : 0;

  // ----- Hàm render HTML -----
  function renderItineraryRow(index, id, data = {}) {
    const day = data.day_number || '';
    const activities = data.activities || '';
    const meals = data.meals || '';
    const accommodation = data.accommodation || '';

    return `
            <div class="row mb-3 pt-2 border-top itinerary-row" data-id="${id}">
                <div class="col-md-1">
                    <label class="form-label">Ngày</label>
                    <input type="number" name="itineraries[${index}][day_number]" class="form-control day-number" value="${day}">
                </div>
                <div class="col-md-11">
                    <label class="form-label">Hoạt động</label>
                    <textarea name="itineraries[${index}][activities]" class="form-control activities mb-1" placeholder="Hoạt động">${activities}</textarea>

                    <label class="form-label">Ăn uống</label>
                    <textarea name="itineraries[${index}][meals]" class="form-control meals mb-1" placeholder="Bữa ăn">${meals}</textarea>

                    <label class="form-label">Chỗ ở</label>
                    <textarea name="itineraries[${index}][accommodation]" class="form-control accommodation" placeholder="Chỗ ở">${accommodation}</textarea>
                    <button type="button" class="btn btn-danger btn-sm mt-2 remove-itinerary">Xóa</button>
                </div>
            </div>
        `;
  }

  // ----- Hàm lưu vào LocalStorage -----
  function saveNewItineraries() {
    const rows = document.querySelectorAll("#itineraries-container .itinerary-row[data-id]");
    const data = Array.from(rows).map(row => ({
      id: row.dataset.id,
      day_number: row.querySelector('.day-number').value,
      activities: row.querySelector('.activities').value,
      meals: row.querySelector('.meals').value,
      accommodation: row.querySelector('.accommodation').value
    }));
    localStorage.setItem(STORAGE_NEW_ITINERARIES, JSON.stringify(data));
  }

  // ----- Hàm tải từ LocalStorage (Khi load trang) -----
  function loadNewItineraries() {
    const data = JSON.parse(localStorage.getItem(STORAGE_NEW_ITINERARIES) || "[]");
    data.forEach(i => {
      const html = renderItineraryRow(itineraryCounter, i.id, i);
      itinerariesContainer.insertAdjacentHTML("beforeend", html);
      itineraryCounter++;
    });
  }

  // ----- Gắn sự kiện -----
  if (addItineraryBtn) {
    addItineraryBtn.addEventListener("click", () => {
      const id = uuid();
      const html = renderItineraryRow(itineraryCounter, id, {});
      itinerariesContainer.insertAdjacentHTML("beforeend", html);
      itineraryCounter++;
      saveNewItineraries(); // Lưu ngay
    });
  }

  if (itinerariesContainer) {
    // Xóa
    itinerariesContainer.addEventListener("click", e => {
      if (e.target.classList.contains("remove-itinerary")) {
        const row = e.target.closest(".itinerary-row");
        row.remove();
        if (row.dataset.id) {
          saveNewItineraries();
        }
      }
    });
    // Tự động lưu khi gõ
    itinerariesContainer.addEventListener('input', e => {
      if (e.target.closest('.itinerary-row[data-id]')) {
        saveNewItineraries();
      }
    });
  }


  // =========================================================
  // 6. Chạy các hàm tải và dọn dẹp
  // =========================================================

  // Chỉ tải từ localStorage nếu Blade không render 'old()' data
  if (schedulesContainer && schedulesContainer.dataset.hasOld === 'false') {
    loadNewSchedules();
  }
  if (itinerariesContainer && itinerariesContainer.dataset.hasOld === 'false') {
    loadNewItineraries();
  }

  // Cập nhật miền và điểm đến ngay khi tải trang
  updateTourRegions();
  updateTourDestinations();

  // ----- Tự động chọn tỉnh đến ban đầu -----
  function initializeDefaultProvince() {
    if (villagePickerContainer) {
      // Đọc ID tỉnh mặc định từ data attribute
      const defaultProvinceId = villagePickerContainer.dataset.defaultProvinceId;
      if (defaultProvinceId) {
        try {
          // 1. Báo cho Select2 chọn giá trị này
          $('#province-source').val(defaultProvinceId).trigger('change');

          // 2. Tự gọi hàm load làng nghề cho tỉnh đó
          populateVillageSource(defaultProvinceId);
        } catch (e) {
          console.error("Lỗi tự động chọn tỉnh đến:", e);
        }
      }
    }
  }
  // Chạy hàm 
  initializeDefaultProvince();

  // Dọn dẹp localStorage sau khi submit form thành công ở list.blade.php
});