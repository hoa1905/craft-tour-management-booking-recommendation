var vnTTour = function () {
  return {
    initFilter: function () {
      /**
       * Nếu chiều rộng cửa sổ nhỏ hơn hoặc bằng 992px → gán showMonths = 1. (chỉ hiển thị 1 tháng trong lịch)
       * Ngược lại (màn hình lớn hơn 992px, ví dụ như laptop, desktop) → gán showMonths = 3. (hiển thị 3 tháng cùng lúc trong lịch)
       */
      const showMonths = $(window).innerWidth() <= 992 ? 1 : 3;
      flatpickr(".flatpickr-input", {
        showMonths: showMonths,
        mode: "range",
        dateFormat: "d/m/Y",
        minDate: "today",
        locale: "vn"
      });

      // Price slider
      $(function () {
        $("#slider_price").slider({
          range: true,
          min: 0,
          max: 50000000,
          step: 100000,
          values: [0, 50000000],
          slide: function (event, ui) {
            // Cập nhật text hiển thị
            $("#price_min").html(ui.values[0].toLocaleString('vi-VN'));
            $("#price_max").html(ui.values[1].toLocaleString('vi-VN'));

            // Gán lại giá trị vào input hidden
            $("#s_price_min").val(ui.values[0]);
            $("#s_price_max").val(ui.values[1]);
          }
        });

        // Hiển thị giá mặc định khi load trang
        $("#price_min").html($("#slider_price").slider("values", 0).toLocaleString('vi-VN'));
        $("#price_max").html($("#slider_price").slider("values", 1).toLocaleString('vi-VN'));
      });
    },

    init: function () {
      $("html").on("click", ".vnt-box-select__text", function () {
        $('.vnt-box-select').not($(this).parent()).removeClass('active');
        $(this).closest('.vnt-box-select').toggleClass('active');
      });

      $("html").on("click", ".vnt-box-select__list li", function () {
        let objbox = $(this).closest('.vnt-box-select');
        objbox.removeClass('active');
        objbox.find('.vnt-box-select__text').html($(this).html());
      });
    }
  };
}();

// Gọi khởi tạo
$(document).ready(function () {
  vnTTour.init();
});

// Ajax sắp xếp tour
$(document).ready(function () {
  $('.sort-link').on('click', function (e) {
    e.preventDefault();
    const sortValue = $(this).data('sort');
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sortValue);

    $.ajax({
      url: url.toString(),
      type: 'GET',
      beforeSend: function () {
        $('#tour-list').html('<div class="text-center py-4">Đang tải...</div>');
      },
      success: function (response) {
        // Lấy nội dung #tour-list từ HTML trả về
        const newContent = $(response).find('#tour-list').html();
        $('#tour-list').html(newContent);

        // Cập nhật URL mà không reload trang
        window.history.pushState({}, '', url);

        // Cập nhật trạng thái active
        $('.sort-link').parent().removeClass('active');
        $(`.sort-link[data-sort="${sortValue}"]`).parent().addClass('active');
      },
      error: function () {
        alert('Lỗi khi tải dữ liệu.');
      }
    });
  });

  // Xử lý quay lại trình duyệt (Back/Forward)
  window.onpopstate = function () {
    $.get(window.location.href, function (response) {
      const newContent = $(response).find('#tour-list').html();
      $('#tour-list').html(newContent);
    });
  };
});

// Ajax phân trang tour được tìm thấy
$(document).on('click', '.pagination a', function (e) {
  e.preventDefault();

  let url = new URL($(this).attr('href'), window.location.origin);
  const currentSort = new URL(window.location.href).searchParams.get('sort');
  if (currentSort) url.searchParams.set('sort', currentSort);

  $.ajax({
    url: url.toString(),
    type: 'GET',
    beforeSend: function () {
      // Hiển thị trạng thái đang tải
      $('#tour-list').html('<div class="text-center py-4">Đang tải...</div>');
    },
    success: function (response) {
      // Cập nhật danh sách tour và phân trang mới
      const newTours = $(response).find('#tour-list').html();
      const newPagination = $(response).find('.pagination-wrapper').html();

      $('#tour-list').html(newTours);
      $('.pagination-wrapper').html(newPagination);

      // Cập nhật URL trên trình duyệt mà không load lại trang
      window.history.pushState({}, '', url);
    },
    error: function () {
      alert('Không thể tải dữ liệu. Vui lòng thử lại.');
    }
  });
});

// Khi người dùng nhấn nút Back/Forward
window.onpopstate = function () {
  $.get(window.location.href, function (response) {
    const newTours = $(response).find('#tour-list').html();
    const newPagination = $(response).find('.pagination-wrapper').html();

    $('#tour-list').html(newTours);
    $('.pagination-wrapper').html(newPagination);
  });
};

// TOUR Ajax tìm kiếm tour
$(document).ready(function () {
  $('#formModSearch').on('submit', function (e) {
    e.preventDefault();

    let params = []; // Mảng chứa các cặp key=value

    // Duyệt qua tất cả input/select có name
    $(this).find('input[name], select[name]').each(function () {
      let name = $(this).attr('name');
      let value = $(this).val()?.trim();

      // Chỉ thêm nếu có giá trị
      if (value) {
        params.push(encodeURIComponent(name) + '=' + encodeURIComponent(value));
      }
    });

    // Tạo query string
    let queryString = params.join('&');
    let url = $(this).attr('action');
    if (queryString) {
      url += '?' + queryString;
    }

    $.ajax({
      url: url,
      type: 'GET',
      beforeSend: function () {
        $('#tour-list').html('<p>Đang tìm kiếm tour...</p>');
      },
      success: function (response) {
        // Xóa message cũ (nếu có)
        $('.alert-warning').remove();

        // Lấy message mới từ response (nếu có)
        let newMessage = $(response).find('.alert-warning');
        if (newMessage.length > 0) {
          $('.mid-content').prepend(newMessage);
        }

        // Cập nhật phần hiển thị thông báo kết quả (tlfind)
        let newResultInfo = $(response).find('.tlfind').html();
        if (newResultInfo && newResultInfo.trim() !== '') {
          $('.tlfind').html(newResultInfo);
        } else {
          $('.tlfind').empty();
        }

        // Cập nhật danh sách tour và phân trang
        $('#tour-list').html($(response).find('#tour-list').html());
        $('.pagination-wrapper').html($(response).find('.pagination-wrapper').html());

        // Cập nhật URL mà không reload
        window.history.pushState({}, '', url);
      },
      error: function () {
        $('#tour-list').html('<p class="text-danger">Lỗi khi tìm kiếm tour.</p>');
      }
    });
  });
});
