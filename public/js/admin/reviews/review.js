function filterTable() {
  // Lấy giá trị của ô tìm kiếm và chuyển thành chữ thường
  let input = document.querySelector("input[type='text']").value.toLowerCase();
  let rows = document.querySelectorAll("#reviewTable tbody tr");

  rows.forEach(row => {
    let client_name = row.cells[1].textContent.toLowerCase();     // Cột Khách hàng
    let tour_name = row.cells[2].textContent.toLowerCase();  // Cột tour

    if (client_name.includes(input) || tour_name.includes(input)) {  // Kiểm tra cả tên và sản phẩm
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}

// Xử lý tải hình ảnh đánh giá tương ứng
document.addEventListener('DOMContentLoaded', function () {
  // Lấy tất cả các carousel review trong trang
  var carousels = document.querySelectorAll('[id^="carouselReview"]');

  carousels.forEach(function (carousel) {
    // Lấy ID của review từ ID của carousel (VD: carouselReview123 -> 123)
    var reviewId = carousel.id.replace('carouselReview', '');
    var downloadBtn = document.querySelector('#downloadBtn' + reviewId);

    if (downloadBtn) {
      // Lắng nghe sự kiện chuyển slide
      carousel.addEventListener('slid.bs.carousel', function (event) {
        var activeItem = event.relatedTarget;
        var img = activeItem.querySelector('img');
        if (img) {
          downloadBtn.setAttribute('href', img.src);
        }
      });
    }
  });
});