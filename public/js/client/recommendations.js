/**
 * Hỗ trợ hệ thống gợi ý tour (Recommendation System)
 * Tích hợp: Trang chủ, Chi tiết tour, Tìm kiếm tour
 */

const TourRecommendations = {

  /**
   * =================================================================
   * PHẦN 1: LOGIC GỌI API (CONTROLLER)
   * =================================================================
   */

  /**
   * 1. TRANG CHỦ: Load gợi ý chung hoặc theo lịch sử người dùng
   */
  loadHomeRecommendations() {
    $.ajax({
      url: '/api/recommendations/home',
      method: 'GET',
      beforeSend: function () {
        $('#recommended-loading').show();
      },
      success: (response) => {
        if (response.success && response.tours.length > 0) {
          // Sử dụng Layout GRID (dọc)
          this.displayRecommendedTours(
            response.tours,
            '#recommended-tours-list',
            response.message
          );
          $('#recommended-for-you-section').slideDown('slow');

          // Update trạng thái (Login/Guest)
          if (response.is_logged_in) {
            $('#recommendation-status').html('<i class="fa fa-user-check"></i> Gợi ý dựa trên lịch sử của bạn');
          } else {
            $('#recommendation-status').html('<i class="fa fa-magic"></i> ' + response.message);
          }
        } else {
          $('#recommended-for-you-section').hide();
        }
        $('#recommended-loading').hide();
      },
      error: (xhr) => {
        console.error('Error loading home recommendations:', xhr);
        $('#recommended-loading').hide();
      }
    });
  },

  /**
   * 2. TRANG CHI TIẾT: Load gợi ý tour tương tự (Content-based)
   */
  loadSimilarTours(tourId) {
    $.ajax({
      url: `/api/recommendations/similar/${tourId}`,
      method: 'GET',
      beforeSend: function () {
        $('#similar-tours-loading').show();
      },
      success: (response) => {
        if (response.success && response.tours.length > 0) {
          // Sử dụng Layout GRID dọc
          this.displayRecommendedTours(
            response.tours,
            '#similar-tours-list',
            'Tour tương tự bạn có thể thích'
          );
          $('#similar-tours-section').slideDown('slow');
        } else {
          $('#similar-tours-section').hide();
        }
        $('#similar-tours-loading').hide();
      },
      error: (xhr) => {
        console.error('Error loading similar tours:', xhr);
        $('#similar-tours-loading').hide();
      }
    });
  },

  /**
   * 3. TRANG TÌM KIẾM: Load gợi ý theo từ khóa
   * Logic: Nếu có gợi ý -> Ẩn kết quả tìm kiếm thường -> Hiện gợi ý
   */
  loadSearchRecommendations(keyword) {
    if (!keyword || keyword.trim() === '') return;

    $.ajax({
      url: '/api/recommendations/search',
      method: 'GET',
      data: { keyword: keyword },
      success: (response) => {
        // Kiểm tra xem có dữ liệu tour trả về không
        if (response.success && response.tours && response.tours.length > 0) {

          // A. Sử dụng layout GRID ngang vào div gợi ý
          this.displaySearchHorizontalTours(
            response.tours,
            '#search-recommendations-list',
            `Gợi ý dựa trên từ khóa: "${response.keyword}"`
          );

          // B. ẨN danh sách tour tìm kiếm thông thường (SQL)
          $('#standard-search-container').hide();

          // C. HIỆN danh sách tour gợi ý (AI)
          $('#search-recommendations-section').slideDown('fast');

        } else {
          // Nếu AI không tìm thấy, hiện lại thông báo
          $('#search-recommendations-list').html(`
            <div class="text-center py-5">
               <i class="fa fa-search" style="font-size:40px; color:#ddd;"></i>
               <p class="mt-3">Không tìm thấy tour nào cho từ khóa "<strong>${keyword}</strong>"</p>
            </div>
          `);
        }
      },
      error: (xhr) => {
        console.error('Error loading search recommendations:', xhr);
        // Gặp lỗi thì fallback về tìm kiếm thường
        $('#standard-search-container').show();
      }
    });
  },

  /**
   * 4. Tracking: Ghi nhận người dùng xem tour
   */
  trackTourView(tourId) {
    if (!tourId) return;
    $.ajax({
      url: '/api/recommendations/track-view',
      method: 'POST',
      data: {
        _token: $('meta[name="csrf-token"]').attr('content'),
        tour_id: tourId
      },
      success: function () { /* Console log if needed */ },
      error: function () { /* Handle error silently */ }
    });
  },


  /**
   * =================================================================
   * PHẦN 2: CÁC HÀM RENDER VIEW (HTML)
   * =================================================================
   */

  /**
   * LAYOUT 1: GRID (CỘT DỌC)
   * Dùng cho: Trang chủ, Trang chi tiết
   */
  displayRecommendedTours(tours, containerSelector, message = '') {
    let html = '';

    // Hiển thị message nếu có
    if (message) {
      $(containerSelector).closest('.wrapper').find('.recommendation-message').html(
        `<p class="text-muted text-center mb-3"><i class="fa fa-info-circle"></i> ${message}</p>`
      );
    }

    tours.forEach(tour => {
      const imageUrl = `/images/tours/${tour.image_main}`;
      const schedules = tour.schedules && tour.schedules.length > 0
        ? tour.schedules.map(s => s.start_date).join(', ')
        : 'Chưa có lịch';

      const transportIcon = tour.transport === 'Máy bay'
        ? '<i class="fa fa-plane"></i>'
        : '<i class="fa fa-bus"></i>';

      html += `
                <div class="col-md-3 col-sm-6 tour-recommendation-item">
                    <div class="tour-card">
                        <a href="${tour.url}">
                            <div class="tour-image" style="background-image: url('${imageUrl}')">
                                <span class="tour-badge">
                                    <i class="fa fa-magic"></i> Gợi ý
                                </span>
                                <span class="tour-region">
                                    <i class="glyphicon glyphicon-map-marker"></i> ${tour.destination}
                                </span>
                            </div>
                        </a>

                        <div class="tour-body">
                            <h4 class="tour-title">
                                <a href="${tour.url}">${tour.tour_name}</a>
                            </h4>

                            <div class="tour-price">
                                ${this.formatPrice(tour.price_adult)}<span class="currency">₫</span>
                            </div>

                            <ul class="tour-info list-unstyled">
                                <li><strong>Khởi hành:</strong> ${tour.departure_point}</li>
                                <li><strong>Điểm đến:</strong> ${tour.destination}</li>
                                <li><strong>Thời gian:</strong> ${tour.duration}</li>
                            </ul>

                            <div class="clearfix">
                                <div class="pull-left">
                                    <strong>Ngày đi:</strong> ${schedules}
                                </div>
                                <div class="pull-right">
                                    ${transportIcon}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
    });

    $(containerSelector).html(html);

    // Thêm animation fade-in
    $('.tour-recommendation-item').hide().fadeIn('slow');
  },

  /**
   * LAYOUT 2: HORIZONTAL (NGANG)
   * Dùng cho: Trang tìm kiếm
   */

  displaySearchHorizontalTours(tours, containerSelector, message = '') {

    // Update tiêu đề phần gợi ý (nếu có div tiêu đề)
    if (message && $('#recommendation-title').length > 0) {
      $('#recommendation-title').text(message);
    }

    let html = '';

    tours.forEach(tour => {
      const imageUrl = `/images/tours/${tour.image_main}`;
      const detailUrl = `/tour-detail/${tour.tour_id}`;

      // 1. Xử lý logic hiển thị Lịch khởi hành
      const minDate = tour.schedules && tour.schedules.length > 0
        ? tour.schedules.sort((a, b) => new Date(a.start_date) - new Date(b.start_date))[0].start_date
        : '---';

      const schedules = tour.schedules && tour.schedules.length > 0
        ? tour.schedules.map(s => s.start_date).join(', ')
        : 'Chưa có lịch';

      const scheduleText = schedules.length > 30 ? schedules.substring(0, 27) + '...' : schedules;

      // 2. Icon phương tiện
      const transportIcon = tour.transport === 'Máy bay' ? '<i class="fa fa-plane"></i>' : '<i class="fa fa-bus"></i>';

      html += `
        <div class="card mb-4 shadow-sm border-light">
          <div class="row g-0 align-items-center p-3">
            
            <a href="${detailUrl}">
              <div class="col-md-3 position-relative">
                <img src="${imageUrl}" class="img-fluid rounded" alt="${tour.tour_name}">
                
                <span class="tour-badge">
                                    <i class="fa fa-magic"></i> Gợi ý
                                </span>
              </div>
            </a>

            <div class="col-md-6 ps-3">
              <a href="${detailUrl}">
                <h5 class="fw-bold mb-2 tour-title">${tour.tour_name}</h5>
              </a>
              <p class="mb-1"><strong>Điểm đi:</strong> ${tour.departure_point || ''}</p>
              <p class="mb-1"><strong>Điểm đến:</strong> ${tour.destination || ''}</p>
              <p class="mb-1"><strong>Thời gian:</strong> ${tour.duration || ''}</p>
              <p class="mb-0">
                <strong>Ngày đi:</strong> ${scheduleText} ${transportIcon}
              </p>
            </div>

            <div class="col-md-3 text-end">
              <p class="mb-1"><i class="fa fa-calendar"></i> ${minDate}</p>
              <p class="mb-1">Giá 1 khách</p>
              <h4 class="fw-bold tour-price">${this.formatPrice(tour.price_adult)}₫</h4>
            </div>

          </div>
        </div>
      `;
    });

    $(containerSelector).html(html);
    $(containerSelector).hide().fadeIn();
  },

  /**
   * ===== CÁC HÀM TIỆN ÍCH =====
   */
  formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price);
  },

  getTourIdFromUrl() {
    const pathArray = window.location.pathname.split('/');
    const lastSegment = pathArray[pathArray.length - 1];
    return lastSegment && !isNaN(lastSegment) ? parseInt(lastSegment) : null;
  },

  getSearchKeywordFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    // Ưu tiên lấy từ destination (form name) hoặc keyword
    return urlParams.get('destination') || urlParams.get('keyword') || '';
  }
};

// Export ra window để gọi từ file view nếu cần
window.TourRecommendations = TourRecommendations;

/**
 * ===== KHỞI TẠO (DOCUMENT READY) =====
 */
$(document).ready(function () {
  const currentPath = window.location.pathname;

  // 1. TRANG CHỦ
  if (currentPath === '/' || currentPath === '/home') {
    TourRecommendations.loadHomeRecommendations();
  }

  // 2. TRANG CHI TIẾT
  if (currentPath.includes('/tour-detail/')) {
    const tourId = TourRecommendations.getTourIdFromUrl();
    if (tourId) {
      TourRecommendations.trackTourView(tourId);
      TourRecommendations.loadSimilarTours(tourId);
    }
  }

  // 3. TRANG TÌM KIẾM
  // Kiểm tra URL hoặc sự tồn tại của form tìm kiếm
  if (currentPath.includes('/tour-search') || $('#formModSearch').length > 0) {
    const keyword = TourRecommendations.getSearchKeywordFromUrl();
    // Chỉ chạy logic gợi ý nếu có từ khóa
    if (keyword) {
      TourRecommendations.loadSearchRecommendations(keyword);
    } else {
      // Nếu không có từ khóa (vào trang search), đảm bảo hiện list thường
      $('#standard-search-container').show();
      $('#search-recommendations-section').hide();
    }
  }
});