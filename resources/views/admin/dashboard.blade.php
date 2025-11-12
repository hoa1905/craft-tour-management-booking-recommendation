@extends('admin.master')

@section('head')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endsection

@section('content')
{{-- Thông báo --}}
@if(session('status'))
<div class="alert custom-alert alert-success alert-dismissible fade show border-0 shadow" role="alert" id="status-alert">
  <div class="d-flex align-items-center">
    <div class="alert-icon-container me-3">
      <i class="fas fa-check-circle"></i>
    </div>
    <div>
      <h5 class="alert-heading mb-1">Thành công!</h5>
      <p class="mb-0">{{ session('status') }}</p>
    </div>
  </div>
  <div class="progress mt-2" style="height: 3px;">
    <div id="alert-progress-bar" class="progress-bar bg-white" style="width: 100%;"></div>
  </div>
  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
<div class="container-fluid p-4">
  <div class="row g-3">
    <div class="col-lg-3 col-md-6">
      <div class="dashboard-card card-gradient-primary">
        <i class="bi bi-people card-icon"></i>
        <div>
          <h5>Tổng số người dùng</h5>
          <p class="fs-4 fw-bold">{{ $totalUsers }}</p>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="dashboard-card card-gradient-success">
        <i class="bi bi-map-fill card-icon"></i>
        <div>
          <h5>Tổng số tour</h5>
          <p class="fs-4 fw-bold">{{ $totalTours }}</p>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="dashboard-card card-gradient-warning">
        <i class="bi bi-cart-check card-icon"></i>
        <div>
          <h5>Tổng số đơn hoàn tất</h5>
          <p class="fs-4 fw-bold">{{ $totalBookings }}</p>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6">
      <div class="dashboard-card card-gradient-danger">
        <i class="bi bi-cash-coin card-icon"></i>
        <div>
          <h5>Tổng doanh thu</h5>
          <p class="fs-4 fw-bold">{{ number_format($totalRevenue, 0, ',', '.') }} VNĐ</p>
        </div>
      </div>
    </div>
  </div>
</div>

<hr class="my-1">

<!-- Biểu đồ thống kê -->
<div class="container mt-4">
  <h2 class="text-center mb-4">Biểu đồ thống kê</h2>
  <div class="row">
    <div class="col-lg-6 col-md-12">
      <div class="card shadow p-3 mb-4">
        <h5 class="text-center">Doanh thu theo tháng</h5>
        <canvas id="monthlyRevenueChart"></canvas>
      </div>
    </div>
    <div class="col-lg-6 col-md-12">
      <div class="card shadow p-3 mb-4">
        <h5 class="text-center">Doanh thu theo tuần</h5>
        <canvas id="weeklyRevenueChart"></canvas>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-6 col-md-12">
      <div class="card shadow p-3 mb-4">
        <h5 class="text-center">Doanh thu theo ngày</h5>
        <canvas id="dailyRevenueChart"></canvas>
      </div>
    </div>
    <div class="col-lg-6 col-md-12">
      <div class="card shadow p-3 mb-4">
        <h5 class="text-center">Trạng thái đơn hàng</h5>
        <canvas id="orderStatusChart"></canvas>
      </div>
    </div>
    <div class="col-lg-6 col-md-12">
      <div class="card shadow p-3 mb-4">
        <h5 class="text-center">Top 5 tour có doanh thu cao nhất</h5>
        <canvas id="topToursChart"></canvas>
      </div>
    </div>
    <div class="col-lg-6 col-md-12">
      <div class="card shadow p-3 mb-4">
        <h5 class="text-center">Top 5 tour được đặt nhiều nhất</h5>
        <canvas id="topBookedToursChart"></canvas>
      </div>
    </div>
    <div class="col-lg-6 col-md-12">
      <div class="card shadow p-3 mb-4">
        <h5 class="text-center">Tỷ lệ phương thức thanh toán</h5>
        <canvas id="paymentMethodsChart"></canvas>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/admin/dashboard.js') }}"></script>
@endsection