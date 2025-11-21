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

  // 4. Xử lý xóa ảnh cũ (Đã có trong DB)
  const oldGalleryContainer = document.getElementById('old-gallery-container');
  if (oldGalleryContainer) {
    oldGalleryContainer.addEventListener('click', function (e) {
      const btn = e.target.closest('.remove-old-image-btn');
      if (btn) {
        const item = btn.closest('.old-image-item');
        if (item) {
          item.style.transition = 'opacity 0.3s';
          item.style.opacity = '0';
          setTimeout(() => {
            item.remove();
            if (oldGalleryContainer.children.length === 0) {
              const note = document.getElementById('old-gallery-note');
              if (note) note.style.display = 'none';
            }
          }, 300);
        }
      }
    });
  }

  // 5. Xử lý xem trước & xóa ảnh MỚI (Upload)
  // Tái sử dụng logic giống hệt trang Add
  setupImageUpload('image_main', 'image-preview', false); // Avatar (1 ảnh)
  setupImageUpload('images', 'description-image-preview', true); // Gallery (Nhiều ảnh)
});

// --- Hàm xử lý upload (Giống hệt add.js) ---
function setupImageUpload(inputId, previewId, isMultiple) {
  const input = document.getElementById(inputId);
  const previewContainer = document.getElementById(previewId);

  if (!input || !previewContainer) return;

  const instructionDiv = input.closest('.upload-zone').querySelector('.upload-instruction');
  let dataTransfer = new DataTransfer();

  input.addEventListener('change', function (e) {
    const files = e.target.files;

    if (!isMultiple) {
      dataTransfer = new DataTransfer();
      previewContainer.innerHTML = "";
    }

    for (let i = 0; i < files.length; i++) {
      const file = files[i];
      dataTransfer.items.add(file);

      const reader = new FileReader();
      reader.onload = function (event) {
        const imgWrapper = document.createElement('div');
        imgWrapper.className = 'position-relative m-2 preview-item';

        const img = document.createElement('img');
        img.src = event.target.result;
        img.className = 'img-fluid rounded shadow-sm border';
        img.style.height = isMultiple ? '100px' : '120px';
        img.style.width = isMultiple ? '100px' : 'auto';
        img.style.objectFit = 'cover';

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 p-0 d-flex justify-content-center align-items-center rounded-circle shadow-sm';
        removeBtn.style.width = '22px';
        removeBtn.style.height = '22px';
        removeBtn.style.transform = 'translate(30%, -30%)';
        removeBtn.innerHTML = '<i class="bi bi-x" style="font-size: 16px; line-height: 1;"></i>';

        removeBtn.addEventListener('click', function () {
          imgWrapper.remove();
          const newDataTransfer = new DataTransfer();
          for (let j = 0; j < dataTransfer.files.length; j++) {
            if (dataTransfer.files[j] !== file) {
              newDataTransfer.items.add(dataTransfer.files[j]);
            }
          }
          dataTransfer = newDataTransfer;
          input.files = dataTransfer.files;

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
    input.files = dataTransfer.files;
    if (files.length > 0) {
      instructionDiv.style.display = 'none';
    }
  });
}