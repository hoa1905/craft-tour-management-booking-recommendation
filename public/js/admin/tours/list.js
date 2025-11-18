function filterTable() {
  let input = document.getElementById("searchProduct").value.toLowerCase();
  let rows = document.querySelectorAll("#searchProduct tbody tr");

  rows.forEach(row => {
    let color = row.cells[1].textContent.toLowerCase();

    if (color.includes(input)) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}
// Khởi tạo chú giải công cụ
document.addEventListener('DOMContentLoaded', function () {
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  });
});