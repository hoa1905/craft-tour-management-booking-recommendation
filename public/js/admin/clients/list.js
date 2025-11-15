function filterTable() {
  let input = document.getElementById("searchBrand").value.toLowerCase();
  let rows = document.querySelectorAll("#searchUser tbody tr");

  rows.forEach(row => {
    let name = row.cells[2].textContent.toLowerCase();
    let email = row.cells[3].textContent.toLowerCase();
    if (name.includes(input) || email.includes(input)) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}
document.addEventListener('DOMContentLoaded', function () {
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  });
});