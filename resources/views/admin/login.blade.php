<!DOCTYPE html>
<html lang="vn">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng nhập Quản trị viên</title>
  <link rel="icon" href="{{ asset('images/client/hanh-trinh-vang-icon.png') }}" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
</head>

<body>
  <div class="background-overlay"></div>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h2>Chào mừng quay trở lại</h2>
        <p>Đăng nhập Quản trị viên</p>
      </div>

      <div class="login-body">
        <form action="{{ route('admin.login.post') }}" method="POST">
          @csrf
          <div class="form-group">
            <label for="email">Email</label>
            <div class="input-with-icon">
              <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email" value="{{ old('email') }}">
              <i class="fas fa-envelope input-icon"></i>
            </div>
            @error('email')
            <div class="error-message">
              <i class="fas fa-exclamation-circle"></i>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="form-group">
            <label for="password">Mật khẩu</label>
            <div class="input-with-icon">
              <input type="password" class="form-control password" id="password" name="password" placeholder="Nhập mật khẩu">
              <i class="fas fa-lock input-icon"></i>
              <i class="fas fa-eye password-toggle" id="togglePassword"></i>
            </div>
            @error('password')
            <div class="error-message">
              <i class="fas fa-exclamation-circle"></i>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="form-footer">
            <div class="remember-me">
              <input type="checkbox" name="remember" id="remember" class="custom-checkbox">
              <label for="remember" class="checkbox-label">Ghi nhớ tôi</label>
            </div>
            <button type="submit" class="btn-login">Đăng nhập</button>
          </div>

          <a href="#" class="forgot-password">Quên mật khẩu?</a>

          <div class="divider">hoặc tiếp tục với</div>

          <div class="social-login">
            <div class="social-btn google">
              <i class="fab fa-google"></i>
            </div>
            <div class="social-btn facebook">
              <i class="fab fa-facebook-f"></i>
            </div>
            <div class="social-btn twitter">
              <i class="fab fa-twitter"></i>
            </div>
          </div>
        </form>

        <div class="login-footer">
          &copy; 2025 Hành Trình Vàng.
        </div>
      </div>
    </div>
  </div>

  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="{{ asset('js/admin/login.js') }}"></script>
</body>

</html>