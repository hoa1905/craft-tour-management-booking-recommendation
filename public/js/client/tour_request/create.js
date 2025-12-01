$(document).ready(function () {
  // 1. Kích hoạt Chosen Select (Dropdown chọn nhiều)
  if ($('.chosen-select').length > 0) {
    $('.chosen-select').chosen({
      width: "100%",
      no_results_text: "Không tìm thấy kết quả!"
    });
  }

  // 2. Tự động cập nhật điểm đến
  // Khi thay đổi làng nghề -> Cập nhật ô "Điểm đến"
  $('#village_select').on('change', function (evt, params) {
    updateDestination();
  });

  function updateDestination() {
    var selectedProvinces = [];

    // Duyệt qua các option được chọn
    $('#village_select option:selected').each(function () {
      // Lấy tên tỉnh từ data-attribute
      var province = $(this).data('province');

      // Nếu có tên tỉnh và chưa có trong mảng thì thêm vào (tránh trùng lặp)
      if (province && !selectedProvinces.includes(province)) {
        selectedProvinces.push(province);
      }
    });

    // Nối các tỉnh lại bằng dấu phẩy và gán vào input
    if (selectedProvinces.length > 0) {
      $('#desired_destination').val(selectedProvinces.join(', '));
    } else {
      $('#desired_destination').val('');
    }
  }

  // Chạy 1 lần khi tải trang (để xử lý trường hợp form validation fail và load lại old data)
  updateDestination();

  // --- LOGIC FORMAT TIỀN TỆ (VNĐ) ---
  // Chọn các input có class 'currency'
  $('.currency').on('input', function () {
    // 1. Lấy giá trị hiện tại và xóa mọi ký tự không phải số
    let value = $(this).val().replace(/[^0-9]/g, '');

    // 2. Format thêm dấu chấm phân cách hàng nghìn
    if (value) {
      value = new Intl.NumberFormat('vi-VN').format(value);
    }

    // 3. Gán lại giá trị đã format vào ô input
    $(this).val(value);
  });
});