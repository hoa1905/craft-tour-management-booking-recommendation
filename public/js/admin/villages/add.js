document.addEventListener("DOMContentLoaded", function () {

  // 1. Khởi tạo CKEditor
  if (document.getElementById('description')) {
    CKEDITOR.replace('description');
  }

  // 2. Khởi tạo Select2
  try {
    $('.select2-basic').select2({ theme: "bootstrap-5", width: '100%' });
  } catch (e) { console.error("Lỗi khởi tạo Select2:", e); }

  // 3. Tự động cập nhật Miền khi chọn Tỉnh
  $('#province_id').on('select2:select', function (e) {
    var regionName = $(this).find(':selected').data('region');
    if (regionName) {
      $('#village-regions-display').val(regionName);
    }
  });

  // Trigger change ban đầu nếu có old data
  if ($('#province_id').val()) {
    var regionName = $('#province_id').find(':selected').data('region');
    $('#village-regions-display').val(regionName);
  }

  // 4. Xử lý kéo thả và upload ảnh
  setupImageUpload('image_main', 'image-preview', false); // false = chỉ 1 ảnh
  setupImageUpload('images', 'description-image-preview', true); // true = nhiều ảnh
});


// --- HÀM XỬ LÝ UPLOAD CHUNG ---
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