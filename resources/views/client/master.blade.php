<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <title>@yield('title')</title>
  <meta http-equiv="content-language" content="vi">
  <meta name="robots" content="index, follow">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="{{ asset('images/hanh-trinh-vang-icon.png') }}" type="image/gif">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/all.min.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/jquery.fancybox.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/slick.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/slick-theme.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/jquery-ui.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/menu.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/jquery.mCustomScrollbar.min.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/chosen.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/screen.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/main.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/update.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/detail.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/tour-search.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/booking.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/ai-chatbox.css') }}" type="text/css">
  <script language="javascript">
    var ROOT = "";
    var lang = "vn";
    var API_URL = "{{ route('api.chat') }}";
    var CSRF_TOKEN = "{{ csrf_token() }}";
    var API_KEY = '';
  </script>
  <script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/jquery-migrate.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/jquery.fancybox.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/slick.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/jquery.validate.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/jquery-ui.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/jquery.ui.datepicker-vi.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/menu.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/jquery.mCustomScrollbar.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/mnfixed.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/chosen.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/sweetalert2.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/toastr.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/jquery.form.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/pagination.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/core.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/javascript.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/main.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/update.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/detail.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/tour-search.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/booking.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/ai-chatbox.js') }}"></script>

  @yield('head')
</head>

<body>

  <head>
    <input type="hidden" name="vnt_csrf_token" id="vnt_csrf_token" value="a4ea56d767b8b7db3ab0747c0c98b553">
    <div id="vnt-wrapper">
      <div id="vnt-container">
        <div class="w-fixed-menu hidden-sm hidden-xs">
          <div class="fixed-menu">
            <div class="wrapper">
              <div class="hptoolhead">
                <div class="tlgrip">
                  <div class="tlcol">
                    <div class="vnt-fixed-menu">
                      <ul>
                        <li class="current">
                          <a href="http://127.0.0.1:8000/" target="_self">
                            <i class="fas fa-map-marked-alt"></i>HanhTrinhVang
                          </a>
                        </li>
                        <li class="submenu"><a href="javascript:void(0)" target="_self">Tour</a>
                          <div class="tpsubmnhead">
                            <div class="mncroll">
                              <div class="mngrip">
                                @foreach ($regions as $regionName => $provinces)
                                <div class="mcol" style="width: 33.33%;">
                                  <div class="itsubmnhead">
                                    <div class="vtitle">{{ $regionName }}</div>
                                    <div class="vconts">
                                      <ul>
                                        @foreach ($provinces as $province)
                                        <li>
                                          <a href="{{ url('/tour-search?destination=' . urlencode($province)) }}" class="d-flex justify-content-between align-items-center py-1 text-decoration-none text-dark">
                                            <span>{{ $province }}</span>
                                            <img src="{{ asset('images/hot.png') }}" alt="HOT">
                                          </a>
                                        </li>
                                        @endforeach
                                      </ul>
                                    </div>
                                  </div>
                                </div>
                                @endforeach
                              </div>
                            </div>
                          </div>
                        </li>
                        <li class="submenu hassub"><a href="javascript:void(0)" target="_self">Cách đặt tour</a>
                          <ul>
                            <li><a href="javascript:void(0)">Hướng dẫn đặt tour</a></li>
                            <li>
                              <a href="javascript:void(0)">Hướng dẫn thanh toán </a>
                              <ul>
                                <li><a href="javascript:void(0)">Điều kiện & điều khoản</a></li>
                              </ul>
                            </li>
                          </ul>
                        </li>
                        <li>
                          <a href="{{ route('villages.map') }}" target="_self">Bản đồ làng nghề</a>
                        </li>
                        <li>
                          <a href="javascript:void(0)" target="_self">Tour theo yêu cầu</a>
                        </li>
                        <li>
                          <a href="javascript:void(0)" target="_self">Đăng ký trở thành nhà cung cấp</a>
                        </li>
                        <li>
                          <div class="vnt-search hiddenmb">
                            <form name="formSearch" action="{{ route('tour.search') }}" method="get" class="box_search">
                              <div class="seponsi">
                                <input name="destination" type="text" class="text_search" value="" placeholder="Bạn muốn đi đâu?">
                                <button type="submit">
                                  <span><i class="fa fa-search"></i></span>
                                </button>
                              </div>
                            </form>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="trcol">
                    <div class="tptoolhead">
                      <div class="mmgrip">
                        <div class="xcol">
                          <div class="tpaccounts">
                            <div class="acnolog">
                              <div class="nntitle">
                                @if(Auth::guard('client')->check())
                                <i class="fas fa-user-check"></i>
                                @else
                                <i class="icon-cuser"></i>
                                @endif
                              </div>
                              <div class="nnconts @if(Auth::guard('client')->check()) logged-in @endif">
                                @if(Auth::guard('client')->check())
                                <ul>
                                  <li class=" text-center">
                                    <div style="font-size: 13px;">Xin chào,</div>
                                    <strong style="font-size: 15px;">{{ Auth::guard('client')->user()->user_name }}</strong>
                                  </li>
                                  <li><a href="{{ route('account.myBookings') }}"><i class="fas fa-list"></i> Đơn đặt tour</a></li>
                                  <li><a href="{{ route('account.info') }}"><i class="fas fa-user"></i> Thông tin tài khoản</a></li>
                                  <li><a href="{{ route('account.changePassword') }}"><i class="fas fa-lock"></i> Đổi mật khẩu</a></li>
                                  <li>
                                    <a href="{{ route('account.logout') }}"
                                      onclick="event.preventDefault(); 
                if (confirm('Bạn có chắc chắn muốn đăng xuất?')) { 
                    document.getElementById('logout-form').submit(); 
                }">
                                      <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                    </a>
                                  </li>
                                </ul>
                                <form id="logout-form" action="{{ route('account.logout') }}" method="POST" style="display: none;">
                                  @csrf
                                </form>
                                @else
                                <ul>
                                  <li><a class="fa-key vnt-popup-login" href="javascript:void(0)">Đăng nhập</a></li>
                                  <li><a class="fa-lock vnt-popup-register" href="javascript:void(0);">Đăng ký</a></li>
                                </ul>
                                @endif
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
  </head>

  <!-- Menu Mobile (Chỉ hiện trên tablet và mobile) -->
  <div class="mobile-menu-header hidden-md hidden-lg">
    <div class="mobile-header-wrap">
      <div class="mobile-logo">
        <a href="{{ url('/') }}">
          <img src="{{ asset('images/hanh-trinh-vang-icon.png') }}" alt="Logo">
        </a>
      </div>
      <div style="display: flex; align-items: center; gap: 15px;">
        @if(Auth::guard('client')->check())
        <a href="{{ route('account.info') }}" class="mobile-user-icon">
          <i class="fas fa-user-circle"></i>
        </a>
        @else
        <a href="javascript:void(0)" class="mobile-user-icon vnt-popup-login">
          <i class="fas fa-user-circle"></i>
        </a>
        @endif
        <button class="mobile-menu-toggle">
          <i class="fas fa-bars"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- Sidebar Menu Mobile -->
  <div class="mobile-menu-sidebar">
    <div class="mobile-menu-close">
      <button><i class="fas fa-times"></i></button>
    </div>
    <ul class="mobile-menu-list">
      <li><a href="{{ url('/') }}"><i class="fas fa-home"></i> Trang chủ</a></li>

      <li class="has-submenu">
        <a href="javascript:void(0)"><i class="fas fa-map-marked-alt"></i> Tour</a>
        <ul class="mobile-submenu">
          @foreach ($regions as $regionName => $provinces)
          <li class="submenu-title">{{ $regionName }}</li>
          @foreach ($provinces as $province)
          <li><a href="{{ url('/tour-search?destination=' . urlencode($province)) }}">{{ $province }}</a></li>
          @endforeach
          @endforeach
        </ul>
      </li>

      <li class="has-submenu">
        <a href="javascript:void(0)"><i class="fas fa-question-circle"></i> Cách đặt tour</a>
        <ul class="mobile-submenu">
          <li><a href="javascript:void(0)">Hướng dẫn đặt tour</a></li>
          <li><a href="javascript:void(0)">Hướng dẫn thanh toán</a></li>
          <li><a href="javascript:void(0)">Điều kiện & điều khoản</a></li>
        </ul>
      </li>

      <li><a href="{{ route('villages.map') }}"><i class="fas fa-map"></i> Bản đồ làng nghề</a></li>
      <li><a href="javascript:void(0)"><i class="fas fa-clipboard-list"></i> Tour theo yêu cầu</a></li>
      <li><a href="javascript:void(0)"><i class="fas fa-user-plus"></i> Đăng ký nhà cung cấp</a></li>

      @if(Auth::guard('client')->check())
      <li style="border-top: 2px solid #ddd; margin-top: 10px; padding-top: 10px;">
        <div style="padding: 10px 20px; background: #f0f0f0;">
          <div style="font-size: 12px; color: #666;">Xin chào,</div>
          <strong style="font-size: 14px; color: #d82727;">{{ Auth::guard('client')->user()->user_name }}</strong>
        </div>
      </li>
      <li><a href="{{ route('account.myBookings') }}"><i class="fas fa-list"></i> Đơn đặt tour</a></li>
      <li><a href="{{ route('account.info') }}"><i class="fas fa-user"></i> Thông tin tài khoản</a></li>
      <li><a href="{{ route('account.changePassword') }}"><i class="fas fa-lock"></i> Đổi mật khẩu</a></li>
      <li>
        <a href="{{ route('account.logout') }}"
          onclick="event.preventDefault(); if(confirm('Bạn có chắc muốn đăng xuất?')) document.getElementById('logout-form-mobile').submit();">
          <i class="fas fa-sign-out-alt"></i> Đăng xuất
        </a>
      </li>
      <form id="logout-form-mobile" action="{{ route('account.logout') }}" method="POST" style="display: none;">
        @csrf
      </form>
      @else
      <li><a href="javascript:void(0)" class="vnt-popup-login"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a></li>
      <li><a href="javascript:void(0)" class="vnt-popup-register"><i class="fas fa-user-plus"></i> Đăng ký</a></li>
      @endif
    </ul>
  </div>

  <!-- Overlay khi mở menu -->
  <div class="mobile-menu-overlay"></div>

  @yield('body')
  <div id="vnt-popup-login" style="display: none">
    <div class="vnt-popup-member">
      <div class="content">
        <div class="tab-title">
          <ul>
            <li class="active">Đăng Nhập</li>
            <li><a class="vnt-popup-register" href="javascript:void(0)">ĐĂNG KÝ</a></li>
          </ul>
        </div>
        <form action="" method="post" class="vnt-form" onsubmit="return false">
          <div class="vnt-popup-form">
            <div class="error-login" style="color: red"></div>
            <div class="form-group">
              <input type="text" placeholder="Email" id="popup-login-user" name="login_user" class="login_user" autocomplete="off">
              <div class="error-message error-login-user"></div>
            </div>

            <div class="form-group">
              <div class="input-pass">
                <input type="password" placeholder="Mật khẩu" id="popup-login-password" name="login_pass" class="login_pass" autocomplete="off">
                <div class="i-change"><a href="javascript:void(0);" title="hide/show"></a></div>
                <div class="error-message error-login-pass"></div>
              </div>
            </div>

            <div class="form-group df">
              <div class="checkbox"><label for="ch_remember"><input type="checkbox" name="ch_remember" id="ch_remember" value="1" class="login_save"> Ghi nhớ đăng nhập</label></div>
              <div class="linkForget"><a href="javascript:void(0)" class="vnt-popup-forgetpass"><span>Bạn quên mật khẩu?</span></a></div>
            </div>

            <div class="form-btn">
              <button type="submit" id="vnt-login"><span>Đăng nhập</span></button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="vnt-popup-register" style="max-width: 430px; width: 100%; display: none">
    <div class="vnt-popup-member">
      <div class="content">
        <div class="tab-title">
          <ul>
            <li><a class="vnt-popup-login" href="javascript:void(0)">Đăng Nhập</a></li>
            <li class="active">ĐĂNG KÝ</li>
          </ul>
        </div>
        <form id="formPopupReg" name="formPopupReg" method="POST" action="" class="form validate">
          <div class="vnt-popup-form">
            <div class="form-group">
              <div class="div_input">
                <input type="text" class="form-control required" name="rName" id="rName" placeholder="Họ tên của bạn *" title="Vui lòng nhập họ tên của bạn" value="">
                <div class="error-message text-danger" id="error-rName"></div>
              </div>
            </div>

            <div class="form-group">
              <div class="div_input">
                <input type="text" class="form-control" name="rEmail" id="rEmail" placeholder="Email *" value="">
                <div class="error-message text-danger" id="error-rEmail"></div>
              </div>
            </div>

            <div class="form-group">
              <div class="input-pass div_input">
                <input type="password" class="form-control required" name="rPassWord" id="rPassWord" autocomplete="off" placeholder="Mật khẩu *" value="">
                <div class="i-change"><a href="javascript:void(0);" title="hide/show"></a></div>
                <div class="error-message text-danger" id="error-rPassWord"></div>
              </div>
            </div>
            <div class="form-group">
              <div class="input-pass div_input">
                <input type="password" class="form-control required" name="rConfirm_PassWord" id="rConfirm_PassWord" autocomplete="off" placeholder="Xác nhận mật khẩu *" value="">
                <div class="i-change"><a href="javascript:void(0);" title="hide/show"></a></div>
                <div class="error-message text-danger" id="error-rConfirm_PassWord"></div>
              </div>
            </div>

            <div class="form-group df">
              <div class="checkbox div_input">
                <label for="r_agree">
                  <input type="checkbox" id="r_agree" name="r_agree" value="1" class="required">Tôi đồng ý với <a href="javascript:void(0)" target="_blank">Điều khoản dịch vụ</a> và <a href="javascript:void(0)" target="_blank">Chính sách bảo mật</a> của công ty
                </label>
                <div class="error-message text-danger" id="error-r_agree"></div>
              </div>
            </div>
            <div class="form-btn">
              <button type="submit"><span>Đăng ký</span></button>
            </div>
          </div>
        </form>
        <script>
          $(document).ready(function() {
            // Đăng ký
            $('#formPopupReg').on('submit', function(e) {
              e.preventDefault();

              // Ẩn toàn bộ lỗi cũ
              $('.error-message').text('');

              $.ajax({
                url: "{{ route('account.register.post') }}",
                type: "POST",
                data: {
                  _token: "{{ csrf_token() }}",
                  rName: $('#rName').val(),
                  rEmail: $('#rEmail').val(),
                  rPassWord: $('#rPassWord').val(),
                  rConfirm_PassWord: $('#rConfirm_PassWord').val(),
                  r_agree: $('#r_agree').is(':checked') ? 1 : 0
                },
                success: function(res) {
                  if (res.success) {
                    toastr.success(res.message);
                    $('#formPopupReg')[0].reset();
                    $('.error-message').text('');
                    $('.vnt-popup-login').trigger('click');
                  }
                },
                error: function(xhr) {
                  if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;

                    // Hiển thị lỗi cho input tương ứng
                    $.each(errors, function(key, val) {
                      $('#error-' + key).text(val[0]);
                    });
                  }
                }
              });
            });

            // Xóa lỗi khi người dùng bắt đầu nhập lại trong form
            $('input').on('input change', function() {
              const inputId = $(this).attr('id');
              $('#error-' + inputId).text('');
            });

            // Đăng nhập
            $('#vnt-login').on('click', function(e) {
              e.preventDefault();

              // Xóa lỗi cũ trước khi gửi request
              $('.error-message').html('');

              $.ajax({
                url: "{{ route('account.login.post') }}",
                type: "POST",
                data: {
                  _token: "{{ csrf_token() }}",
                  login_user: $('#popup-login-user').val(),
                  login_pass: $('#popup-login-password').val(),
                  ch_remember: $('#ch_remember').is(':checked') ? 1 : 0
                },
                success: function(res) {
                  if (res.success) {
                    // Chủ động cập nhật trạng thái đăng nhập toàn cục ngay trong trình duyệt, không phụ thuộc vào việc reload lại trang sau khi đăng nhập
                    window.isLoggedIn = true;
                    toastr.success(res.message);
                    $.fancybox.close(); // Đóng FancyBox chuẩn
                    // Thay đổi giao diện user ngay sau đăng nhập
                    updateUserHeader(res.username);
                  }
                },
                error: function(xhr) {
                  if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.login_user) {
                      $('.error-login-user').html(errors.login_user[0]);
                    }
                    if (errors.login_pass) {
                      $('.error-login-pass').html(errors.login_pass[0]);
                    }
                  }
                  // Lỗi xác thực sai email hoặc mật khẩu (401)
                  else if (xhr.status === 401) {
                    $('.error-login').html(xhr.responseJSON.message);
                  }
                }
              });
            });

            // Xóa lỗi khi người dùng bắt đầu nhập lại trong form đăng nhập
            $('#popup-login-user, #popup-login-password').on('input', function() {
              const name = $(this).attr('name');
              if (name === 'login_user') $('.error-login-user').html('');
              if (name === 'login_pass') $('.error-login-pass').html('');
            });


            function updateUserHeader(username) {
              const content = `
    <ul>
      <li class="text-center">
        <div style="font-size: 13px;">Xin chào,</div>
        <strong style="font-size: 15px;">${username}</strong>
      </li>
      <li><a href="{{ route('account.myBookings') }}"><i class="fas fa-list"></i> Đơn đặt tour</a></li>
      <li><a href="{{ route('account.info') }}"><i class="fas fa-user"></i> Thông tin tài khoản</a></li>
      <li><a href="{{ route('account.changePassword') }}"><i class="fas fa-lock"></i> Đổi mật khẩu</a></li>
      <li>
        <a href="{{ route('account.logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
           <i class="fas fa-sign-out-alt"></i> Đăng xuất
        </a>
      </li>
    </ul>
    <form id="logout-form" action="{{ route('account.logout') }}" method="POST" style="display: none;">
      @csrf
    </form>
  `;

              // Cập nhật nội dung và thêm class 'logged-in' để áp CSS
              $('.tpaccounts .acnolog .nnconts')
                .html(content)
                .addClass('logged-in');

              // Thay icon bên trên
              $('.tpaccounts .acnolog .nntitle').html('<i class="fas fa-user-check"></i>');
            }
          });
        </script>

      </div>
    </div>
  </div>

  <div id="vnt-popup-forgetpass" style="max-width: 430px; width: 100%; display: none">
    <div class="vnt-popup-member">
      <div class="content">
        <div class="title"> Bạn quên mật khẩu?</div>
        <div class="vnt-popup-form">

          <form action="" method="post" class="vnt-form" onsubmit="return false">
            <div class="account-err">
              <div class="form-mess">Vui lòng nhập số điện thoại để lấy lại mật khẩu</div>
            </div>

            <div class="form-group">
              <input type="email" name="forget_email" id="forget_email" class="form-control forget_email" required="" placeholder="Email *">
            </div>
            <div class="form-btn">
              <input type="hidden" name="url_forget" id="url_forget" value="/tour/member/ajax/forget_pass.html">
              <button type="submit" class="btnColo">
                <span>Gửi lại mật khẩu</span>
              </button>
            </div>

            <div class="txt-register"> <a class="vnt-popup-register" href="javascript:void(0)"> </a></div>

          </form>
        </div>
      </div>
    </div>
  </div>
  <footer>
    <div id="vnt-footer">
      <div class="footer-main">
        <div class="wrapper">
          <div class="row cel-p10">
            <div class="col-md-5 col-sm-12 col-xs-12 ">
              <div class="mb30">
                <div class="title-footer">Du lịch làng nghề truyền thống Việt Nam</div>
                <div class="content-footer">
                  <div class="web-address">
                    <div class="wa-item"><span class="i-icon"><i class="fa fa-user"></i></span> <span class="i-text"><strong>Người đại diện: <a href="https://www.facebook.com/hoa.phamtan.102" target="_blank">Phạm Tấn Hòa</a></strong></span></div>

                    <div class="wa-item"><span class="i-icon"><i class="fas fa-file-alt"></i></span> <span class="i-text"><strong>Mã số thuế: 8832410066</strong></span></div>

                    <div class="wa-item"><span class="i-icon"><i class="fas fa-map-marker-alt"></i></span> <span class="i-text"><strong>VP Hà Nội:</strong> abc, def</span></div>

                    <div class="wa-item"><span class="i-icon"><i class="fas fa-map-marker-alt"></i></span> <span class="i-text"><strong>VP Đà Nẵng:</strong> abc, def</span></div>

                    <div class="wa-item"><span class="i-icon"><i class="fas fa-map-marker-alt"></i></span> <span class="i-text"><strong>VP TP.HCM:</strong> abc, def</span></div>
                  </div>
                </div>
              </div>

            </div>
            <div class="col-md-2 col-sm-12 col-xs-12 hidden-sm hidden-xs">
              <div class="mb30">
                <div class="title-footer">Thông Tin</div>
                <div class="content-footer">
                  <div class="mb5">
                    <div class="mb5"><a href="/tour/ve-chung-toi.html" target="_self">Về chúng tôi</a></div>
                    <div class="mb5"><a href="/tour/cach-dat-tour.html" target="_self">Hướng dẫn đặt tour</a></div>
                    <div class="mb5"><a href="/tour/cach-thanh-toan.html" target="_self">Hướng dẫn thanh toán </a></div>
                    <div class="mb5"><a href="/tour/dieu-khoan.html" target="_self">Điều khoản chung</a></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-2 col-sm-12 col-xs-12 hidden-sm hidden-xs">
              <div class="mb30">
                <div class="title-footer">DỊCH VỤ</div>
                <div class="content-footer">
                  <div class="mb5">
                    <div class="mb5"><a href="javascript:void(0)" target="_self">Tour trọn gói</a></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-12 col-xs-12">
              <div class="mb30">
                <div class="title-footer">Liên hệ</div>
                <div class="content-footer">
                  <div class="mb10">Email:<strong> <a href="mailto:abc@dulich.com.vn" rel="nofollow">abc@dulich.com.vn</a> </strong></div>
                </div>
                <div class="vnt-social">
                  <ul>
                    <li><a href="https://www.facebook.com/hoa.phamtan.102" target="_blank" rel="nofollow"><i class="fab fa-facebook-f"></i></a></li>
                  </ul>
                  <div class="clear"></div>
                </div>
              </div>
            </div>
          </div>
          <hr class="fhr">
          <div class="copyright">
            <div>Copyright © 2025 - <strong>Bản quyền thuộc về Phạm Tấn Hòa</strong></div>
          </div>
        </div>
      </div>
    </div>
  </footer>

  {{--Chatbox AI --}}
  <div id="ai-chatbox-widget">
    <button id="chatbox-toggle-button">
      <i class="fas fa-robot"></i>
    </button>

    {{-- Khung thông báo chào mời --}}
    <div id="chatbox-greeting" style="display: none;"> {{-- Ban đầu ẩn --}}
      <span class="greeting-text"></span> {{-- Nơi hiển thị text --}}
      <button class="greeting-close">&times;</button> {{-- Nút X --}}
    </div>

    {{-- Khung chatbox (ban đầu bị ẩn) --}}
    <div id="chatbox-window" style="display: none;">
      {{-- Header của chatbox --}}
      <div class="chatbox-header">
        <span>AI Assistant</span>
        <div>
          <button class="chatbox-toggle-fullscreen" title="Toàn màn hình">
            <i class="fas fa-expand"></i>
          </button>
          <button class="chatbox-close">&times;</button>
        </div>
      </div>

      {{-- Phần hiển thị nội dung chat --}}
      <div class="chatbox-body" id="chatbox-message-body">
        <p class="ai-message">Xin chào! Tôi là trợ lý AI, tôi có thể giúp gì cho bạn?</p>
        {{-- Tin nhắn sẽ được thêm vào đây --}}
      </div>

      {{-- Phần nhập liệu và nút gửi --}}
      <div class="chatbox-footer">
        <input type="text" id="chatbox-input" placeholder="Nhập tin nhắn..." />
        <button id="chatbox-send-button"><i class="fas fa-paper-plane"></i></button>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const menuToggle = document.querySelector('.mobile-menu-toggle');
      const menuSidebar = document.querySelector('.mobile-menu-sidebar');
      const menuClose = document.querySelector('.mobile-menu-close button');
      const menuOverlay = document.querySelector('.mobile-menu-overlay');

      // Mở menu
      menuToggle?.addEventListener('click', function() {
        menuSidebar.classList.add('active');
        menuOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
      });

      // Đóng menu
      function closeMenu() {
        menuSidebar.classList.remove('active');
        menuOverlay.classList.remove('active');
        document.body.style.overflow = '';
      }

      menuClose?.addEventListener('click', closeMenu);
      menuOverlay?.addEventListener('click', closeMenu);

      // Toggle submenu
      document.querySelectorAll('.mobile-menu-list li.has-submenu > a').forEach(item => {
        item.addEventListener('click', function(e) {
          e.preventDefault();
          const parent = this.parentElement;
          const submenu = parent.querySelector('.mobile-submenu');

          parent.classList.toggle('active');
          if (submenu.style.display === 'block') {
            submenu.style.display = 'none';
          } else {
            submenu.style.display = 'block';
          }
        });
      });
    });
  </script>

  @yield('scripts')
</body>

</html>