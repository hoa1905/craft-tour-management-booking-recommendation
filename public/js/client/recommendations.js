/**
 * Hỗ trợ cả khách đã đăng nhập và chưa đăng nhập
 */

const TourRecommendations = {

  /**
   * ===== 1. Hệ khuyến nghị cho trang chủ =====
   */
  loadHomeRecommendations() {
    $.ajax({
      // Route trỏ tới hàm controller
      url: '/api/recommendations/home',
      method: 'GET',
      beforeSend: function () {
        $('#recommended-loading').show();
      },
      success: (response) => {
        if (response.success && response.tours.length > 0) {
          this.displayRecommendedTours(
            response.tours,
            '#recommended-tours-list',
            response.message
          );
          $('#recommended-for-you-section').slideDown('slow');

          // Hiển thị thông tin trạng thái
          if (response.is_logged_in) {
            $('#recommendation-status').html(
              '<i class="fa fa-user-check"></i> Gợi ý dựa trên lịch sử của bạn'
            );
          } else {
            $('#recommendation-status').html(
              '<i class="fa fa-magic"></i> ' + response.message
            );
          }
        } else {
          $('#recommended-for-you-section').hide();
        }
        $('#recommended-loading').hide();
      },
      error: (xhr) => {
        console.error('Error loading home recommendations:', xhr);
        $('#recommended-loading').hide();
        $('#recommended-for-you-section').hide();
      }
    });
  },

  /**
   * ===== 2. GỢI Ý TOUR TƯƠNG TỰ (Trang chi tiết) =====
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
        $('#similar-tours-section').hide();
      }
    });
  },

  /**
   * ===== 3. GỢI Ý THEO TỪ KHÓA TÌM KIẾM =====
   */
  loadSearchRecommendations(keyword) {
    if (!keyword || keyword.trim() === '') {
      return;
    }

    $.ajax({
      url: '/api/recommendations/search',
      method: 'GET',
      data: { keyword: keyword },
      beforeSend: function () {
        $('#search-recommendations-loading').show();
      },
      success: (response) => {
        if (response.success && response.tours.length > 0) {
          this.displayRecommendedTours(
            response.tours,
            '#search-recommendations-list',
            `Gợi ý dựa trên từ khóa: "${response.keyword}"`
          );
          $('#search-recommendations-section').slideDown('slow');
        } else {
          $('#search-recommendations-section').hide();
        }
        $('#search-recommendations-loading').hide();
      },
      error: (xhr) => {
        console.error('Error loading search recommendations:', xhr);
        $('#search-recommendations-loading').hide();
      }
    });
  },

  /**
   * ===== 4. TRACKING TOUR ĐÃ XEM =====
   * Gọi khi user vào trang chi tiết tour
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
      success: function (response) {
        if (response.success) {
          console.log('Tour view tracked:', tourId);
        }
      },
      error: function (xhr) {
        console.error('Error tracking tour view:', xhr);
      }
    });
  },

  /**
   * ===== HIỂN THỊ DANH SÁCH TOUR =====
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
   * ===== FORMAT GIÁ TIỀN =====
   */
  formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price);
  },

  /**
   * ===== LẤY TOUR ID TỪ URL =====
   */
  getTourIdFromUrl() {
    const pathArray = window.location.pathname.split('/');
    const lastSegment = pathArray[pathArray.length - 1];
    return lastSegment && !isNaN(lastSegment) ? parseInt(lastSegment) : null;
  },

  /**
   * ===== LẤY TỪ KHÓA TÌM KIẾM TỪ URL =====
   */
  getSearchKeywordFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('destination') || urlParams.get('keyword') || '';
  }
};

// Export to window
window.TourRecommendations = TourRecommendations;

/**
 * ===== AUTO-INIT KHI DOM READY =====
 */
$(document).ready(function () {

  // Kiểm tra trang hiện tại và load recommendations phù hợp
  const currentPath = window.location.pathname;

  // === TRANG CHỦ ===
  if (currentPath === '/' || currentPath === '/home') {
    TourRecommendations.loadHomeRecommendations();
  }

  // === TRANG CHI TIẾT TOUR ===
  if (currentPath.includes('/tour-detail/')) {
    const tourId = TourRecommendations.getTourIdFromUrl();
    if (tourId) {
      // Track tour view (lưu vào cookie)
      TourRecommendations.trackTourView(tourId);

      // Load tour tương tự
      TourRecommendations.loadSimilarTours(tourId);
    }
  }

  // === TRANG TÌM KIẾM ===
  if (currentPath.includes('/tour-search')) {
    const keyword = TourRecommendations.getSearchKeywordFromUrl();
    if (keyword) {
      TourRecommendations.loadSearchRecommendations(keyword);
    }
  }
});