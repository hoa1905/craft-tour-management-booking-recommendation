document.addEventListener("DOMContentLoaded", function () {
  function fetchChartData(url, callback) {
    fetch(url)
      .then(response => response.json())
      .then(callback)
      .catch(error => console.error("Lỗi tải dữ liệu:", error));
  }

  // Doanh thu theo tháng
  fetchChartData("/admin/dashboard/data/monthly-revenue", data => {
    new Chart(document.getElementById("monthlyRevenueChart"), {
      type: "bar",
      data: {
        labels: data.map(item => `Tháng ${item.month}`),
        datasets: [{
          label: "Doanh thu (VNĐ)",
          data: data.map(item => item.revenue),
          backgroundColor: "#4facfe"
        }]
      },
      options: { responsive: true }
    });
  });

  // Doanh thu theo tuần
  fetchChartData("/admin/dashboard/data/weekly-revenue", data => {
    new Chart(document.getElementById("weeklyRevenueChart"), {
      type: "line",
      data: {
        labels: data.map(item => `Tuần ${item.week}`),
        datasets: [{
          label: "Doanh thu tuần (VNĐ)",
          data: data.map(item => item.revenue),
          borderColor: "#FF6384",
          fill: false,
          tension: 0.3
        }]
      },
      options: { responsive: true }
    });
  });

  // Doanh thu theo ngày
  fetchChartData("/admin/dashboard/data/daily-revenue", data => {
    new Chart(document.getElementById("dailyRevenueChart"), {
      type: "line",
      data: {
        labels: data.map(item => item.date),
        datasets: [{
          label: "Doanh thu ngày (VNĐ)",
          data: data.map(item => item.revenue),
          borderColor: "#36A2EB",
          fill: true, // tô nền dưới đường
          tension: 0.3 // làm cong nhẹ đường nối các điểm
        }]
      },
      options: { responsive: true }
    });
  });

  // Trạng thái đơn hàng
  fetchChartData("/admin/dashboard/data/order-status", data => {
    new Chart(document.getElementById("orderStatusChart"), {
      type: "pie",
      data: {
        labels: data.map(item => item.status),
        datasets: [{
          data: data.map(item => item.count),
          backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0"]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });
  });

  // Top 5 tour doanh thu cao nhất
  fetchChartData("/admin/dashboard/data/top-tours", data => {
    new Chart(document.getElementById("topToursChart"), {
      type: "bar",
      data: {
        labels: data.map(item => item.label),
        datasets: [{
          label: "Doanh thu (VNĐ)",
          data: data.map(item => item.value),
          backgroundColor: "#f6d365"
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
      }
    });
  });

  // Top 5 tour được đặt nhiều nhất
  fetchChartData("/admin/dashboard/data/top-booked-tours", data => {
    new Chart(document.getElementById("topBookedToursChart"), {
      type: "bar",
      data: {
        labels: data.map(item => item.label),
        datasets: [{
          label: "Số lượt đặt (lượt)",
          data: data.map(item => item.value),
          backgroundColor: "#8e5ea2"
        }]
      },
      options: { responsive: true }
    });
  });

  // Tỷ lệ phương thức thanh toán 
  fetchChartData("/admin/dashboard/data/payment-methods", data => {
    new Chart(document.getElementById("paymentMethodsChart"), {
      type: "doughnut",
      data: {
        labels: data.map(item => item.label),
        datasets: [{
          data: data.map(item => item.value),
          backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56"]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });
  });
});