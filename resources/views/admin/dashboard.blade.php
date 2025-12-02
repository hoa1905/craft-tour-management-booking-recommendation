@extends('layouts.admin')

@section('title', 'Dashboard')

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

  {{-- Hàng 1: Tổng quan hệ thống --}}
  <h5 class="mb-3 text-secondary fw-bold"><i class="bi bi-graph-up-arrow me-2"></i>Tổng quan hệ thống</h5>
  <div class="row g-3 mb-4">

    {{-- 1. Tổng khách hàng --}}
    <div class="col-lg-3 col-md-6">
      <div class="card stat-card">
        <div class="card-body p-3">
          <div class="d-flex align-items-center">
            <div class="icon-box icon-primary me-3">
              <i class="bi bi-people-fill"></i>
            </div>
            <div>
              <p class="stat-label">Tổng khách hàng</p>
              <h3 class="stat-value">{{ $totalUsers }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- 2. Tổng Tour --}}
    <div class="col-lg-3 col-md-6">
      <div class="card stat-card">
        <div class="card-body p-3">
          <div class="d-flex align-items-center">
            <div class="icon-box icon-success me-3">
              <i class="bi bi-map-fill"></i>
            </div>
            <div>
              <p class="stat-label">Tour (hoạt động)</p>
              <h3 class="stat-value">{{ $totalTours }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- 3. Tour đã thực hiện --}}
    <div class="col-lg-3 col-md-6">
      <div class="card stat-card">
        <div class="card-body p-3">
          <div class="d-flex align-items-center">
            <div class="icon-box icon-warning me-3">
              <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
              <p class="stat-label">Tour đã thực hiện</p>
              <h3 class="stat-value">{{ $totalBookings }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- 4. Tổng doanh thu --}}
    <div class="col-lg-3 col-md-6">
      <div class="card stat-card">
        <div class="card-body p-3">
          <div class="d-flex align-items-center">
            <div class="icon-box icon-danger me-3">
              <i class="bi bi-cash-stack"></i>
            </div>
            <div>
              <p class="stat-label">Tổng doanh thu</p>
              {{-- Đặt font-size nhỏ hơn một chút --}}
              <h3 class="stat-value text-danger">
                {{ number_format($totalRevenue, 0, ',', '.') }}
                <small class="fs-6">VND</small>
              </h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Hàng 2: Cần xử lý và Theo dõi --}}
  <h5 class="mb-3 text-secondary fw-bold"><i class="bi bi-list-task me-2"></i>Cần xử lý và Theo dõi</h5>
  <div class="row g-3">

    {{-- 1. Yêu cầu Tour mới --}}
    <div class="col-lg-3 col-md-6">
      <div class="card border-0 shadow-sm border-start border-info border-4 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted small mb-1 text-uppercase fw-bold">Yêu cầu Tour mới</p>
              <h4 class="mb-0 text-info fw-bold">{{ $pendingRequests }}</h4>
            </div>
            <div class="bg-info bg-opacity-10 rounded-circle p-3 text-info">
              <i class="bi bi-envelope-paper-fill fs-4"></i>
            </div>
          </div>
          <a href="{{ route('admin.tour_request.index', ['status' => 'pending']) }}" class="text-decoration-none small mt-3 d-block text-info">
            Xử lý báo giá ngay <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>

    {{-- 2. Tổng Làng Nghề --}}
    <div class="col-lg-3 col-md-6">
      <div class="card border-0 shadow-sm border-start border-warning border-4 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted small mb-1 text-uppercase fw-bold">Làng nghề (hoạt động)</p>
              <h4 class="mb-0 text-warning fw-bold">{{ $totalVillages }}</h4>
            </div>
            <div class="bg-warning bg-opacity-10 rounded-circle p-3 text-warning">
              <i class="bi bi-houses-fill fs-4"></i>
            </div>
          </div>
          <a href="{{ route('admin.village.index') }}" class="text-decoration-none small mt-3 d-block text-secondary">
            Quản lý làng nghề <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>

    {{-- 3. Sắp khởi hành --}}
    <div class="col-lg-3 col-md-6">
      <div class="card border-0 shadow-sm border-start border-primary border-4 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted small mb-1 text-uppercase fw-bold">Sắp khởi hành (7 ngày)</p>
              <h4 class="mb-0 text-primary fw-bold">{{ $upcomingTours }}</h4>
            </div>
            <div class="bg-primary bg-opacity-10 rounded-circle p-3 text-primary">
              <i class="bi bi-calendar-week fs-4"></i>
            </div>
          </div>
          <div class="small mt-3 text-muted">Chuẩn bị hậu cần/Liên hệ</div>
        </div>
      </div>
    </div>

    {{-- 4. Đơn đã hủy --}}
    <div class="col-lg-3 col-md-6">
      <div class="card border-0 shadow-sm border-start border-danger border-4 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted small mb-1 text-uppercase fw-bold">Đơn đã hủy</p>
              <h4 class="mb-0 text-danger fw-bold">{{ $canceledBookings }}</h4>
            </div>
            <div class="bg-danger bg-opacity-10 rounded-circle p-3 text-danger">
              <i class="bi bi-x-circle fs-4"></i>
            </div>
          </div>
          <a href="{{ route('admin.booking.index', ['status' => 'canceled']) }}" class="text-decoration-none small mt-3 d-block text-danger">
            Xem lý do hủy <i class="bi bi-arrow-right"></i>
          </a>
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