document.addEventListener("DOMContentLoaded", function () {

  // =========================================================
  // 1. KHỞI TẠO CÁC THƯ VIỆN & CONSTANTS
  // =========================================================

  const STORAGE_NEW_SCHEDULES = 'tour_add_new_schedules';
  const STORAGE_NEW_ITINERARIES = 'tour_add_new_itineraries';

  // Init CKEditor
  if (document.getElementById('description')) {
    CKEDITOR.replace('description');
  }

  if (document.getElementById('workshop')) {
    CKEDITOR.replace('workshop');
  }

  // Init Select2
  try {
    $('.select2-basic').select2({ theme: "bootstrap-5", width: '100%' });
  } catch (e) { console.error("Lỗi khởi tạo Select2:", e); }

  // =========================================================
  // 2. FORMAT GIÁ TIỀN
  // =========================================================
  function formatMoney(v) {
    if (!v) return "";
    return v.toString().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }
  function unformat(v) { return v ? v.toString().replace(/\./g, "") : ""; }

  document.querySelectorAll(".price-format").forEach(input => {
    if (input.value) input.value = formatMoney(input.value);
    input.addEventListener("input", () => {
      input.value = formatMoney(input.value);
      const hidden = document.getElementById(input.dataset.hidden);
      if (hidden) hidden.value = unformat(input.value);
    });
  });

  // =========================================================
  // 3. CHỌN LÀNG NGHỀ VÀ CẬP NHẬT MIỀN/ĐIỂM ĐẾN
  // =========================================================

  const villagePickerContainer = document.querySelector(".village-picker-container");
  const villageSource = document.getElementById("village-source");
  const addVillageBtn = document.getElementById("add-village-btn");
  const selectedVillagesList = document.getElementById("selected-villages-list");
  let allVillagesByProvince = {};

  // Parse dữ liệu JSON từ Blade
  if (villagePickerContainer && villagePickerContainer.dataset.villagesByProvince) {
    try {
      allVillagesByProvince = JSON.parse(villagePickerContainer.dataset.villagesByProvince);
    } catch (e) { console.error("Lỗi parse JSON làng nghề:", e); }
  }

  // Hàm điền danh sách làng nghề vào ô select
  function populateVillageSource(provinceId) {
    villageSource.innerHTML = "";
    if (provinceId && allVillagesByProvince[provinceId]) {
      allVillagesByProvince[provinceId].forEach(village => {
        const option = document.createElement("option");
        option.value = village.village_id;
        option.textContent = village.village_name;
        option.dataset.province = village.province.province_name;
        option.dataset.region = village.province.region.region_name;
        villageSource.appendChild(option);
      });
    }
  }

  // Sự kiện khi chọn Tỉnh (Select2)
  $('#province-source').on('select2:select', function (e) {
    populateVillageSource(e.params.data.id);
  });

  // Sự kiện nút Thêm làng nghề
  if (addVillageBtn) {
    addVillageBtn.addEventListener("click", function () {
      // Lấy ID tỉnh đang chọn để dùng (sửa lỗi biến không tồn tại)
      const selectedProvinceId = $('#province-source').val();

      Array.from(villageSource.selectedOptions).forEach(option => {
        const villageId = option.value;
        // Kiểm tra trùng lặp
        if (!selectedVillagesList.querySelector(`input[value="${villageId}"]`)) {
          addVillageToSelectedList(
            villageId,
            option.textContent,
            option.dataset.province,
            option.dataset.region,
            selectedProvinceId // Truyền ID tỉnh vào
          );
        }
      });
      updateTourRegions();
      updateTourDestinations();
    });
  }

  // Render HTML làng nghề đã chọn
  function addVillageToSelectedList(id, name, province, region, provinceId) {
    const html = `
            <div class="selected-village-item" 
                 data-region="${region}" 
                 data-province="${province}"
                 data-province-id="${provinceId}">
                <div class="village-info">
                    <strong>${name}</strong>
                    <small>${province} - ${region}</small>
                </div>
                <button type="button" class="btn-close remove-village-btn"></button>
                <input type="hidden" name="villages[]" value="${id}">
            </div>`;
    selectedVillagesList.insertAdjacentHTML("beforeend", html);
  }

  // Xóa làng nghề (Event Delegation)
  if (selectedVillagesList) {
    selectedVillagesList.addEventListener("click", function (e) {
      if (e.target.classList.contains("remove-village-btn")) {
        e.target.closest(".selected-village-item").remove();

        updateTourRegions();
        updateTourDestinations();

        // Logic thông minh: Tự động chọn lại tỉnh của làng nghề còn lại
        const firstRemaining = selectedVillagesList.querySelector('.selected-village-item');
        if (firstRemaining) {
          const pid = firstRemaining.dataset.provinceId;
          $('#province-source').val(pid).trigger('change');
          populateVillageSource(pid);
        } else {
          $('#province-source').val("").trigger('change');
          populateVillageSource("");
        }
      }
    });
  }

  // Cập nhật ô Miền
  function updateTourRegions() {
    const display = document.getElementById('tour-regions-display');
    if (!display) return;
    const items = selectedVillagesList.querySelectorAll('.selected-village-item');
    const regions = [...new Set(Array.from(items).map(i => i.dataset.region))];
    display.value = regions.join(', ');
  }

  // Cập nhật ô Điểm đến (Tỉnh)
  function updateTourDestinations() {
    const display = document.getElementById('tour-destinations-display');
    if (!display) return;
    const items = selectedVillagesList.querySelectorAll('.selected-village-item');
    const provinces = [...new Set(Array.from(items).map(i => i.dataset.province))];
    display.value = provinces.join(', ');
  }


  // =========================================================
  // 4 & 5. LỊCH KHỞI HÀNH & LỊCH TRÌNH (LOCALSTORAGE & AUTO-ADD)
  // =========================================================
  const schedulesContainer = document.getElementById("schedules-container");
  const addScheduleBtn = document.getElementById("add-schedule");
  const itinerariesContainer = document.getElementById("itineraries-container");
  const addItineraryBtn = document.getElementById("add-itinerary");

  // Biến đếm (Counter) lấy từ data attribute (nếu có old data thì > 0)
  let scheduleCounter = schedulesContainer ? parseInt(schedulesContainer.dataset.counter || 0) : 0;
  let itineraryCounter = itinerariesContainer ? parseInt(itinerariesContainer.dataset.counter || 0) : 0;

  // --- Render Schedule HTML ---
  function renderScheduleRow(index, data = {}) {
    return `
            <div class="row mb-2 schedule-row new-row">
                <div class="col-md-4">
                    <label class="form-label">Ngày bắt đầu</label>
                    <input type="date" name="schedules[${index}][start_date]" class="form-control start-date" value="${data.start_date || ''}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ngày kết thúc</label>
                    <input type="date" name="schedules[${index}][end_date]" class="form-control end-date" value="${data.end_date || ''}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Số ghế</label>
                    <input type="number" name="schedules[${index}][seats_available]" class="form-control seats" value="${data.seats_available || ''}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-schedule">Xóa</button>
                </div>
            </div>`;
  }

  // --- Render Itinerary HTML ---
  function renderItineraryRow(index, data = {}) {
    return `
            <div class="row mb-3 pt-2 border-top itinerary-row new-row">
                <div class="col-md-1">
                    <label class="form-label">Ngày</label>
                    <input type="number" name="itineraries[${index}][day_number]" class="form-control day-number" value="${data.day_number || ''}">
                </div>
                <div class="col-md-11">
                    <label class="form-label">Hoạt động</label>
                    <textarea name="itineraries[${index}][activities]" class="form-control activities mb-1">${data.activities || ''}</textarea>
                    
                    <label class="form-label">Ăn uống</label>
                    <textarea name="itineraries[${index}][meals]" class="form-control meals mb-1">${data.meals || ''}</textarea>
                    
                    <label class="form-label">Chỗ ở</label>
                    <textarea name="itineraries[${index}][accommodation]" class="form-control accommodation">${data.accommodation || ''}</textarea>
                    
                    <button type="button" class="btn btn-danger btn-sm mt-2 remove-itinerary">Xóa</button>
                </div>
            </div>`;
  }

  // --- Save to LocalStorage ---
  function saveSchedules() {
    const rows = document.querySelectorAll("#schedules-container .schedule-row");
    const data = Array.from(rows).map(row => ({
      start_date: row.querySelector('.start-date').value,
      end_date: row.querySelector('.end-date').value,
      seats_available: row.querySelector('.seats').value
    }));
    localStorage.setItem(STORAGE_NEW_SCHEDULES, JSON.stringify(data));
  }

  function saveItineraries() {
    const rows = document.querySelectorAll("#itineraries-container .itinerary-row");
    const data = Array.from(rows).map(row => ({
      day_number: row.querySelector('.day-number').value,
      activities: row.querySelector('.activities').value,
      meals: row.querySelector('.meals').value,
      accommodation: row.querySelector('.accommodation').value
    }));
    localStorage.setItem(STORAGE_NEW_ITINERARIES, JSON.stringify(data));
  }

  // =========================================================
  // 6. LOGIC TẢI DỮ LIỆU VÀ TỰ ĐỘNG THÊM HÀNG
  // =========================================================

  // --- Xử lý Lịch Khởi Hành ---
  if (schedulesContainer && schedulesContainer.dataset.hasOld === 'false') {
    const data = JSON.parse(localStorage.getItem(STORAGE_NEW_SCHEDULES) || "[]");

    if (data.length > 0) {
      // Case 1: Có dữ liệu trong LocalStorage -> Load ra
      data.forEach(s => {
        schedulesContainer.insertAdjacentHTML("beforeend", renderScheduleRow(scheduleCounter++, s));
      });
    } else {
      // Case 2: Mới tinh -> Tự động thêm 1 hàng trống
      schedulesContainer.insertAdjacentHTML("beforeend", renderScheduleRow(scheduleCounter++));
    }
  }

  // --- Xử lý Lịch Trình ---
  if (itinerariesContainer && itinerariesContainer.dataset.hasOld === 'false') {
    const data = JSON.parse(localStorage.getItem(STORAGE_NEW_ITINERARIES) || "[]");

    if (data.length > 0) {
      // Case 1: Có dữ liệu trong LocalStorage -> Load ra
      data.forEach(i => {
        itinerariesContainer.insertAdjacentHTML("beforeend", renderItineraryRow(itineraryCounter++, i));
      });
    } else {
      // Case 2: Mới tinh -> Tự động thêm 1 hàng trống (Ngày 1)
      itinerariesContainer.insertAdjacentHTML("beforeend", renderItineraryRow(itineraryCounter++, { day_number: 1 }));
    }
  }

  // --- Sự kiện nút Thêm ---
  if (addScheduleBtn) {
    addScheduleBtn.addEventListener("click", () => {
      schedulesContainer.insertAdjacentHTML("beforeend", renderScheduleRow(scheduleCounter++));
      saveSchedules();
    });
  }
  if (addItineraryBtn) {
    addItineraryBtn.addEventListener("click", () => {
      // Tự động tăng ngày (nếu muốn thông minh hơn, lấy ngày cuối + 1)
      // Ở đây đơn giản là tăng counter
      itinerariesContainer.insertAdjacentHTML("beforeend", renderItineraryRow(itineraryCounter++, { day_number: itineraryCounter }));
      saveItineraries();
    });
  }

  // --- Sự kiện Xóa & Nhập liệu (Delegation) ---
  if (schedulesContainer) {
    schedulesContainer.addEventListener('click', e => {
      if (e.target.classList.contains('remove-schedule')) {
        e.target.closest('.schedule-row').remove();
        saveSchedules();
      }
    });
    schedulesContainer.addEventListener('input', saveSchedules);
  }
  if (itinerariesContainer) {
    itinerariesContainer.addEventListener('click', e => {
      if (e.target.classList.contains('remove-itinerary')) {
        e.target.closest('.itinerary-row').remove();
        saveItineraries();
      }
    });
    itinerariesContainer.addEventListener('input', saveItineraries);
  }

  // =========================================================
  // 7. KHỞI TẠO TRẠNG THÁI BAN ĐẦU
  // =========================================================

  // Nếu có tỉnh được chọn sẵn (từ old input), load làng nghề
  if (villagePickerContainer) {
    const defaultProvinceId = villagePickerContainer.dataset.defaultProvinceId;
    if (defaultProvinceId) {
      $('#province-source').val(defaultProvinceId).trigger('change');
      populateVillageSource(defaultProvinceId);
    }
  }

  // Cập nhật hiển thị miền/điểm đến ngay khi tải trang
  updateTourRegions();
  updateTourDestinations();

  // 8. Xử lý kéo thả và upload ảnh
  setupImageUpload('image_main', 'image-preview', false); // false = chỉ 1 ảnh
  setupImageUpload('images', 'description-image-preview', true); // true = nhiều ảnh
});

// --- Hàm xử lý upload ảnh chung ---
function setupImageUpload(inputId, previewId, isMultiple) {
  const input = document.getElementById(inputId);
  const previewContainer = document.getElementById(previewId);

  if (!input || !previewContainer) return;

  const instructionDiv = input.closest('.upload-zone').querySelector('.upload-instruction');

  // DataTransfer dùng để giả lập một danh sách file mới
  // để cập nhật lại input khi người dùng xóa ảnh
  let dataTransfer = new DataTransfer();

  input.addEventListener('change', function (e) {
    const files = e.target.files;

    if (!isMultiple) {
      // Nếu chỉ được chọn 1 ảnh (Avatar), reset danh sách cũ
      dataTransfer = new DataTransfer();
      previewContainer.innerHTML = "";
    }

    // Duyệt qua các file vừa chọn
    for (let i = 0; i < files.length; i++) {
      const file = files[i];

      // Thêm vào DataTransfer
      dataTransfer.items.add(file);

      // Tạo giao diện xem trước
      const reader = new FileReader();
      reader.onload = function (event) {
        const imgWrapper = document.createElement('div');
        imgWrapper.className = 'position-relative m-2 preview-item';

        // Ảnh
        const img = document.createElement('img');
        img.src = event.target.result;
        img.className = 'img-fluid rounded shadow-sm border';
        img.style.height = isMultiple ? '100px' : '120px';
        img.style.width = isMultiple ? '100px' : 'auto';
        img.style.objectFit = 'cover';

        // Nút Xóa
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 p-0 d-flex justify-content-center align-items-center rounded-circle';
        removeBtn.style.width = '20px';
        removeBtn.style.height = '20px';
        removeBtn.style.transform = 'translate(30%, -30%)';
        removeBtn.innerHTML = '<i class="bi bi-x" style="font-size: 14px;"></i>';

        // Sự kiện Xóa ảnh
        removeBtn.addEventListener('click', function () {
          // 1. Xóa khỏi giao diện
          imgWrapper.remove();

          // 2. Xóa khỏi DataTransfer (danh sách file thực tế)
          // Ta cần tạo một DataTransfer mới, copy những file KHÔNG bị xóa sang
          const newDataTransfer = new DataTransfer();
          for (let j = 0; j < dataTransfer.files.length; j++) {
            // So sánh tên và kích thước để xác định file cần xóa
            if (dataTransfer.files[j] !== file) {
              newDataTransfer.items.add(dataTransfer.files[j]);
            }
          }
          dataTransfer = newDataTransfer;

          // 3. Cập nhật lại input
          input.files = dataTransfer.files;

          // 4. Nếu hết ảnh thì hiện lại hướng dẫn
          if (input.files.length === 0) {
            instructionDiv.style.display = 'block';
          }
        });

        imgWrapper.appendChild(img);
        imgWrapper.appendChild(removeBtn);
        previewContainer.appendChild(imgWrapper);
      }
      reader.readAsDataURL(file);
    }

    // Cập nhật input với danh sách file mới (đã cộng dồn)
    input.files = dataTransfer.files;

    // Ẩn hướng dẫn
    if (files.length > 0) {
      instructionDiv.style.display = 'none';
    }
  });
}