
var vnTDetail = function () {
  return {
    init: function () {
      $('.itschedule').each(function () {
        var that = $(this);
        if (that.hasClass("active")) {
          that.find(".scconts").show();
        }
        else {
          that.find(".scconts").hide();
        }
      })

      $(".itschedule .sctitle").click(function () {
        if (!$(this).parents(".itschedule").hasClass("active")) {
          $(this).parents(".itschedule").addClass("active");
          $(this).parents(".itschedule").find(".scconts").stop().slideDown();
        }
        else {
          $(this).parents(".itschedule").removeClass("active");
          $(this).parents(".itschedule").find(".scconts").stop().slideUp();
        }
      });

      $(".clviewsmore").click(function () {
        if (!$(this).parents(".tview").hasClass("active")) {
          $(this).parents(".tview").addClass("active");
          $(this).parents(".vvtitle").siblings(".vvcont").find(".itschedule").addClass("active");
          $(this).parents(".vvtitle").siblings(".vvcont").find(".itschedule .scconts").stop().slideDown();
        }
        else {
          $(this).parents(".tview").removeClass("active");
          $(this).parents(".vvtitle").siblings(".vvcont").find(".itschedule").removeClass("active");
          $(this).parents(".vvtitle").siblings(".vvcont").find(".itschedule .scconts").stop().slideUp();
          $(this).parents(".vvtitle").siblings(".vvcont").find('.itschedule:first').addClass("active")
          $(this).parents(".vvtitle").siblings(".vvcont").find('.itschedule:first .scconts').stop().slideDown();
        }
      });
    }
  };
}();

$(document).ready(function () {
  vnTDetail.init();
});


document.addEventListener('DOMContentLoaded', function () {
  // 1. Tìm thẻ cha chứa dữ liệu
  const bookingBox = document.getElementById('tourBookingBox');

  // 2. Đọc dữ liệu từ data-* attributes
  const priceAdult = parseFloat(bookingBox.dataset.priceAdult);
  const priceChild = parseFloat(bookingBox.dataset.priceChild);
  const tourId = parseInt(bookingBox.dataset.tourId);
  const tourName = bookingBox.dataset.tourName;
  const tourImage = bookingBox.dataset.tourImage;
  const bookingUrl = bookingBox.dataset.bookingUrl; // Đường dẫn URL để fetch
  const csrfToken = bookingBox.dataset.csrfToken;

  // Lấy các elements
  const adultQuantityInput = document.querySelector('.adult_quantity');
  const childQuantityInput = document.querySelector('.child_quantity');
  const totalPriceElement = document.querySelector('.total-price');
  const bookTourBtn = document.querySelector('.book-tour');

  // Element hiển thị số chỗ
  const seatsDisplay = document.getElementById('seats-display');
  // Biến lưu số chỗ tối đa của ngày hiện tại
  let maxSeats = 0;

  // --- HÀM CẬP NHẬT SỐ CHỖ ---
  function updateSeatStatus() {
    const selectedDateElement = document.querySelector('.ckchose ul li.active');

    if (selectedDateElement) {
      // Lấy số chỗ từ data-seats
      const seats = parseInt(selectedDateElement.getAttribute('data-seats')) || 0;
      maxSeats = seats; // Cập nhật biến toàn cục để dùng validate lúc nhập số lượng

      if (seats > 0) {
        seatsDisplay.textContent = `Còn ${seats} chỗ nhận`;
        seatsDisplay.style.color = '#28a745'; // Màu xanh
        document.querySelector('.book-tour').classList.remove('disabled');
      } else {
        seatsDisplay.textContent = 'Đã hết chỗ';
        seatsDisplay.style.color = '#d82727'; // Màu đỏ
        // Tùy chọn: Disable nút đặt tour nếu hết chỗ
        // document.querySelector('.book-tour').classList.add('disabled');
      }
    } else {
      seatsDisplay.textContent = 'Vui lòng chọn ngày';
      maxSeats = 0;
    }
  }

  // --- GỌI HÀM KHI LOAD TRANG ---
  updateSeatStatus();

  // Hàm format số tiền
  function formatMoney(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + '₫';
  }

  // Hàm tính tổng giá
  function calculateTotal() {
    const adultQty = parseInt(adultQuantityInput.value) || 0;
    const childQty = parseInt(childQuantityInput.value) || 0;
    const totalPeople = adultQty + childQty;

    // Kiểm tra nếu tổng số người vượt quá số chỗ còn lại
    if (totalPeople > maxSeats && maxSeats > 0) {
      alert(`Ngày khởi hành này chỉ còn nhận ${maxSeats} khách!`);

      // Reset về giá trị hợp lệ (Ví dụ: ưu tiên người lớn)
      if (adultQty > maxSeats) {
        adultQuantityInput.value = maxSeats;
        childQuantityInput.value = 0;
      } else {
        childQuantityInput.value = maxSeats - adultQty;
      }
      return calculateTotal(); // Tính lại
    }

    const totalAdult = adultQty * priceAdult;
    const totalChild = childQty * priceChild;
    const grandTotal = totalAdult + totalChild;

    totalPriceElement.textContent = formatMoney(grandTotal);

    return {
      adultQty: parseInt(adultQuantityInput.value) || 0,
      childQty: parseInt(childQuantityInput.value) || 0,
      grandTotal
    };
  }

  // Xứ lý nút tăng/giảm số lượng người lớn
  const adultBtnUp = document.querySelector('.q-adult .btn-up');
  const adultBtnDown = document.querySelector('.q-adult .btn-down');

  adultBtnUp.addEventListener('click', function (e) {
    e.preventDefault();
    let currentValue = parseInt(adultQuantityInput.value) || 0;
    adultQuantityInput.value = currentValue + 1;
    calculateTotal();
  });

  adultBtnDown.addEventListener('click', function (e) {
    e.preventDefault();
    let currentValue = parseInt(adultQuantityInput.value) || 1;
    if (currentValue > 1) {
      adultQuantityInput.value = currentValue - 1;
      calculateTotal();
    }
  });

  // Xứ lý nút tăng/giảm số lượng trẻ em
  const childQuantityContainer = document.querySelectorAll('.iquan')[1];
  const childBtnUp = childQuantityContainer.querySelector('.btn-up');
  const childBtnDown = childQuantityContainer.querySelector('.btn-down');

  childBtnUp.addEventListener('click', function (e) {
    e.preventDefault();
    let currentValue = parseInt(childQuantityInput.value) || 0;
    childQuantityInput.value = currentValue + 1;
    calculateTotal();
  });

  childBtnDown.addEventListener('click', function (e) {
    e.preventDefault();
    let currentValue = parseInt(childQuantityInput.value) || 0;
    if (currentValue > 0) {
      childQuantityInput.value = currentValue - 1;
      calculateTotal();
    }
  });

  // Xứ lý khi nhập số lượng trực tiếp
  adultQuantityInput.addEventListener('blur', function () {
    let value = parseInt(this.value) || 1;
    if (value < 1) value = 1;
    this.value = value;
    calculateTotal();
  });

  // Chỉ cho phép nhập số ô số lượng người lớn
  adultQuantityInput.addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
  });

  childQuantityInput.addEventListener('blur', function () {
    let value = parseInt(this.value) || 0;
    if (value < 0) value = 0;
    this.value = value;
    calculateTotal();
  });

  // Chỉ cho phép nhập số ô số lượng trẻ em
  childQuantityInput.addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
  });

  // Xử lý chọn ngày khởi hành
  const dateItems = document.querySelectorAll('.ckchose ul li[start-date]');
  dateItems.forEach(function (item) {
    item.addEventListener('click', function () {
      dateItems.forEach(function (i) {
        i.classList.remove('active');
      });
      this.classList.add('active');
      // Gọi hàm cập nhật số chỗ khi click
      updateSeatStatus();
    });
  });

  // Xử lý nút đặt tour
  bookTourBtn.addEventListener('click', function (e) {
    e.preventDefault();

    const result = calculateTotal();

    // Kiểm tra số lượng
    if (result.adultQty === 0) {
      alert('Vui lòng chọn số lượng người lớn!');
      return;
    }

    // Lấy id của ngày đã chọn
    const selectedDateElement = document.querySelector('.ckchose ul li.active');
    const scheduleId = selectedDateElement ? selectedDateElement.getAttribute('data-schedule-id') : null;
    const selectedStartDate = selectedDateElement ? selectedDateElement.getAttribute('start-date') : null;
    const endDate = selectedDateElement ? selectedDateElement.getAttribute('end-date') : null;
    const seatsAvailable = selectedDateElement ? selectedDateElement.getAttribute('data-seats') : null;

    if (!window.isLoggedIn) {
      if ($('.vnt-popup-login').length) {
        $('.vnt-popup-login').trigger('click');
      } else {
        alert('Vui lòng đăng nhập để đặt tour!');
      }
      return;
    }

    // Chuẩn bị data
    const bookingData = {
      tour_id: tourId,
      schedule_id: scheduleId,
      tour_name: tourName,
      tour_image: tourImage,
      start_date: selectedStartDate,
      end_date: endDate,
      seats_available: seatsAvailable,
      adult_quantity: result.adultQty,
      child_quantity: result.childQty,
      price_adult_at_booking: priceAdult,
      price_child_at_booking: priceChild,
      total_price: result.grandTotal,
      _token: csrfToken
    };

    console.log(bookingData);

    fetch(bookingUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify(bookingData)
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          window.location.href = data.redirect_url;
        } else {
          toastr.error(data.message || 'Có lỗi xảy ra!');
        }
      })
      .catch(err => {
        console.error(err);
        toastr.error('Không thể đặt tour. Vui lòng thử lại!');
      });
  });

  // Khởi chạy tính toán ban đầu
  calculateTotal();

  // Kích hoạt slide
  $('.slupgradenm').slick({
    autoplay: true,
    autoplaySpeed: 4000,
    speed: 1000,
    arrows: true,
    dots: false,
    slidesToShow: 1,
    pauseOnHover: false,
  });

  // Gửi đánh giá
  $('#reviewForm').submit(function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
      url: "/review/create",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (res) {
        // Ẩn form gửi đánh giá
        document.getElementById("reviewFormBox").style.display = "none";

        // Hiện thông báo "Bạn đã đánh giá tour này"
        document.getElementById("alreadyReviewed").style.display = "block";

        loadReviews();

        $('#reviewForm')[0].reset();
        $('input[type=file]').val('');
      },
      error: function (err) {
        alert(err.responseJSON?.message || "Lỗi không xác định");
      }
    });
  });

  // Load review
  function loadReviews(page = 1) {
    $.get(`/review/list/${tourId}?page=${page}`, function (res) {
      $('#reviewList').html(res.html);
      $('#reviewPagination').html(res.pagination);
    });
  }

  // Phân trang AJAX
  $(document).on('click', '#reviewPagination .pagination a', function (e) {
    e.preventDefault();
    let page = $(this).attr('href').split('page=')[1];
    loadReviews(page);
  });
});
