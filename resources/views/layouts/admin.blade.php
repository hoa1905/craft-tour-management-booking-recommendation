<!DOCTYPE html>
<html lang="vn">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title')</title>
  <link rel="icon" href="{{ asset('images/client/hanh-trinh-vang-icon.png') }}" type="image/png">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.3/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="//cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/admin/master.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/status.css') }}">

  @yield('head')
</head>

<body>
  <div class="d-flex">
    <!-- Sidebar -->
    <nav class="sidebar scrollbar-overlay" id="sidebar">
      <div class="header">
        <i class="header-icon bi bi-person-circle"></i>
        <div class="header-text">
          <div class="greeting">Xin chào,</div>
          <div class="role">Quản trị viên</div>
        </div>
      </div>

      <div class="sidebarMenu nav flex-column mt-2">
        <li class="nav-item">
          <a href="{{ route('admin.dashboard') }}" class="nav-link">
            <i class="bi bi-grid-fill me-2"></i> <span>Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('admin.client.index') }}" class="nav-link">
            <i class="bi bi-person-lines-fill me-2"></i> <span>Quản lý khách hàng</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('admin.village.index') }}" class="nav-link">
            <i class="bi bi-houses-fill me-2"></i> <span>Quản lý làng nghề</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('admin.tour.index') }}" class="nav-link">
            <i class="bi bi-map-fill me-2"></i> <span>Quản lý tour</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('admin.booking.index') }}" class="nav-link">
            <i class="bi bi-cart-check me-2"></i> <span>Quản lý đơn đặt tour</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('admin.tour_request.index') }}" class="nav-link">
            <i class="bi bi-envelope-paper-fill me-2"></i> <span> Quản lý tour theo yêu cầu </span>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('admin.review.index') }}" class="nav-link ">
            <i class="bi bi-star left-text me-2"></i>
            <span class="text-gray-700">Quản lý đánh giá</span>
          </a>
        </li>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="content w-100" id="main-content">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-light bg-light px-3">
        <button class="btn btn-outline-secondary" id="toggle-btn">
          <i id="toggle-icon" class="bi bi-list"></i>
        </button>
        <div class="ms-auto dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
            @php
            $admin = Auth::guard('admin')->user();
            @endphp

            @if($admin && $admin->avatar)
            <img src="{{ asset('images/admin/avatar/' . $admin->avatar) }}"
              alt="{{ $admin->user_name }}"
              class="img-thumbnail rounded-circle border"
              style="width: 40px; height: 40px; object-fit: cover;">
            @else
            <i class="bi bi-person-circle fs-3 text-secondary"></i>
            @endif
            <span class="fw-semibold text-dark">{{ $admin ? $admin->user_name : 'Admin' }}</span>
          </a>


          <ul class="dropdown-menu dropdown-menu-end shadow-sm">
            <li>
              <a class="dropdown-item" href="{{ route('admin.profile.index') }}">
                <i class="bi bi-person me-2"></i>
                Thông tin cá nhân
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item text-danger" href="#"
                onclick="event.preventDefault(); 
                if (confirm('Bạn có chắc chắn muốn đăng xuất?')) { 
                    document.getElementById('logout-form').submit(); 
                }"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a></li>
            <form id="logout-form" action="{{ route('admin.profile.logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
          </ul>
        </div>
      </nav>

      <!-- Page Content -->
      @yield('content')

    </div>
  </div>
  <script src="{{ asset('js/admin/admin.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="//cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>

  @yield('scripts')
</body>

</html>