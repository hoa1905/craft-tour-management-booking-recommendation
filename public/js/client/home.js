// Nút xem thêm
$(function () {
  $('.loadmore').on('click', function () {
    // chỉ lấy nút được click
    var button = $(this);
    var offset = parseInt(button.attr('data-offset'));
    var region = button.attr('data-region');

    // Tạo slug: "Tour Miền Bắc" => "mien-bac"
    // loại bỏ tiền tố "Tour"
    const slug = region.replace(/^Tour\s*/i, '')
      .normalize("NFD").replace(/[\u0300-\u036f]/g, '') // loại bỏ dấu tiếng Việt
      .replace(/\s+/g, '-') // thay khoảng trắng bằng dấu gạch ngang
      .toLowerCase(); // chuyển về chữ thường

    $.ajax({
      url: "/load-more-tours",
      method: "GET",
      data: {
        offset: offset,
        region: region
      },
      dataType: "json",
      beforeSend: function () {
        button.find('span').html('<i class="fa fa-spinner fa-spin"></i> Đang tải...');
      },
      success: function (data) {
        if (data && data.length > 0) {
          data.forEach(function (tour) {
            const imageUrl = `/images/tours/${tour.image_main}`;
            const detailUrl = `/tour-detail/${tour.tour_id}`; // route detail
            // Thêm dữ liệu mới vào vùng đúng theo slug
            $('#' + slug + '-tour-list').append(`
          <div class="col-md-3 col-sm-6">
            <div class="tour-card">
              <div class="tour-image" style="background-image: url('${imageUrl}');">
                <span class="tour-region"><i class="glyphicon glyphicon-map-marker"></i> ${tour.destination}</span>
              </div>

              <div class="tour-body">
                <h4 class="tour-title">
                  <a href="${detailUrl}">${tour.tour_name}</a>
                </h4>

                <div class="tour-price">
                  ${Number(tour.price_adult).toLocaleString('vi-VN').replaceAll('.', ',')}<span class="currency">₫</span>
                </div>

                <ul class="tour-info list-unstyled">
                  <li><strong>Khởi hành:</strong> ${tour.departure_point}</li>
                  <li><strong>Điểm đến:</strong> ${tour.destination}</li>
                  <li><strong>Thời gian:</strong> ${tour.duration}</li>
                </ul>

                <div class="clearfix">
                  <div class="pull-left">
                    <strong>Ngày đi:</strong>
    ${tour.schedules && tour.schedules.length
                ? tour.schedules
                  .sort((a, b) => new Date(a.start_date) - new Date(b.start_date))
                  .map(s => {
                    const d = new Date(s.start_date);
                    return d.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' }).replace(/-/g, '/');;
                  })
                  .join(', ')
                : 'Chưa có lịch khởi hành'
              }
                  </div>
                  <div class="pull-right">
                    ${tour.transport === 'Máy bay' ? '<i class="fa fa-plane"></i>' : '<i class="fa fa-bus"></i>'}
                  </div>
                </div>
              </div>
            </div>
          </div>
            `);
          });

          // Cập nhật offset riêng nút được click
          button.attr('data-offset', offset + data.length);

          // Nếu trả về ít hơn 4 tour → đã hết
          if (data.length < 4) {
            button.hide();
            button.closest('.viewall').next('.no-more').show();
          } else {
            button.find('span').html('<i class="fa fa-chevron-down"></i> Xem thêm');
          }
        } else {
          button.hide();
          button.closest('.viewall').next('.no-more').show();
        }
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        button.find('span').html('<i class="fa fa-exclamation-triangle"></i> Lỗi tải dữ liệu');
      }
    });
  });
});

// Thông báo đổi mật khẩu thành công
document.addEventListener('DOMContentLoaded', function () {
  const flashSuccess = document.querySelector('meta[name="flash-success"]')?.content || '';
  if (flashSuccess) toastr.success(flashSuccess);
});

var vnTSearch = function () {
  return {

    init: function () {


    }

  };
}();

jQuery(window).ready(function () {
  vnTSearch.init();
  var fpOptions = {
    showMonths: $(window).innerWidth() <= 992 ? 1 : 3,
    mode: "range",
    dateFormat: "d/m/Y",
    minDate: "today",
    locale: $(window).innerWidth() <= 992 ? undefined : "vn"
  };
  flatpickr(".flatpickr-input", fpOptions);
});