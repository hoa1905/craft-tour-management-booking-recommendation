function filterTable() {
  let input = document.getElementById("searchVillage").value.toLowerCase();
  let rows = document.querySelectorAll("#searchVillage tbody tr");

  rows.forEach(row => {
    let villageName = row.cells[2].textContent.toLowerCase();

    if (villageName.includes(input)) {
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