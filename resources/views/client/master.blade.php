<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <title>@yield('title')</title>
  <meta http-equiv="content-language" content="vi">
  <meta name="robots" content="index, follow">
  <link rel="icon" href="{{ asset('images/hanh-trinh-vang-icon.png') }}" type="image/gif">
  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('css/all.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('css/jquery.fancybox.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('css/slick.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('css/slick-theme.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('css/jquery-ui.css') }}" type="text/css" rel="stylesheet">
  <link href="{{ asset('css/menu.css') }}" type="text/css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/jquery.mCustomScrollbar.min.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/chosen.css') }}" type="text/css">
  <link href="{{ asset('css/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('css/screen.css') }}" rel="stylesheet" type="text/css">
  <script language="javascript">
    var ROOT = "";
    var lang = "vn";
    var API_URL = '';
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
  <link rel="stylesheet" href="{{ asset('css/main.css') }}" type="text/css">
  <script type="text/javascript" src="{{ asset('js/main.js') }}"></script>
  <link type="text/css" href="{{ asset('css/update.css') }}" rel="stylesheet">
  <script type="text/javascript" src="{{ asset('js/update.js') }}"></script>

  <link rel="stylesheet" href="{{ asset('css/detail.css') }}" type="text/css">
  <script type="text/javascript" src="{{ asset('js/detail.js') }}"></script>
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
                        <li>
                          <a href="#" target="_self">Về chúng tôi</a>
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
                                          <a href="{{ url('/tours/' . Str::slug($province)) }}" class="d-flex justify-content-between align-items-center py-1 text-decoration-none text-dark">
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
                        <li class="submenu hassub"><a href="#" target="_self">Cách đặt tour</a>
                          <ul>
                            <li><a href="#">Hướng dẫn đặt tour</a></li>
                            <li>
                              <a href="#">Hướng dẫn thanh toán </a>
                              <ul>
                                <li><a href="#">Điều kiện & điều khoản</a></li>
                              </ul>
                            </li>
                          </ul>
                        </li>
                        <li>
                          <a href="javascript:void(0)" target="_self">Tour theo yêu cầu</a>
                        </li>
                        <li>
                          <a href="javascript:void(0)" target="_self">Đăng ký trở thành nhà cung cấp</a>
                        </li>
                        <li>
                          <div class="vnt-search hiddenmb">
                            <form name="formSearch" action="/tour/" method="get" class="box_search">
                              <div class="seponsi">
                                <input name="keyword" type="text" class="text_search" value="" placeholder="Bạn muốn đi đâu?">
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
                          <div class="tpcarthead">
                            <a href="/tour/tour/booking.html">
                              <i class="icon-icart"></i>
                              <span class="ext_numcart hidden">0</span>
                            </a>
                          </div>
                        </div>
                        <div class="xcol">
                          <div class="tpaccounts">
                            <div class="acnolog ">
                              <div class="nntitle">
                                <i class="icon-cuser"></i>
                              </div>
                              <div class="nnconts">
                                <ul>
                                  <li><a class="fa-key vnt-popup-login" href="javascript:void(0)">Đăng nhập</a></li>
                                  <li><a class="fa-lock vnt-popup-register" href="javascript:void(0);">Đăng ký</a></li>
                                </ul>
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
            <div class="error-login"></div>
            <div class="form-group">
              <input type="text" placeholder="Email" id="popup-login-user" name="login_user" class="login_user" autocomplete="off">
            </div>
            <div class="form-group">
              <div class="input-pass">
                <input type="password" placeholder="Mật khẩu" id="popup-login-password" name="login_pass" class="login_pass" autocomplete="off">
                <div class="i-change"><a href="javascript:void(0);" title="hide/show"></a></div>
              </div>
            </div>
            <div class="form-group df">
              <div class="checkbox"><label for="ch_remember"><input type="checkbox" name="ch_remember" id="ch_remember" value="1" class="login_save"> Ghi nhớ đăng nhập</label></div>
              <div class="linkForget"><a href="javascript:void(0)" class="vnt-popup-forgetpass"><span>Bạn quên mật khẩu?</span></a></div>
            </div>
            <div class="form-btn">
              <input type="hidden" class="login_ref" name="login_ref" value="">
              <input type="hidden" class="url_login" name="url_login" id="url_login" value="/tour/member/ajax/ajax_login.html">
              <button type="submit" id="vnt-login" onclick="vnTAccout.doLogin($(this).parents('form'))"><span>Đăng nhập</span></button>
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
          <div class="error-register"></div>
          <div class="vnt-popup-form">
            <div class="form-group">
              <div class="div_input"><input type="text" class="form-control required" name="rName" id="rName" placeholder="Họ tên của bạn *" title="Vui lòng nhập họ tên của bạn" value=""></div>
            </div>

            <div class="form-group">
              <div class="div_input"><input type="text" class="form-control" name="rEmail" id="rEmail" placeholder="Email *" value=""></div>
            </div>

            <div class="form-group">
              <div class="input-pass div_input">
                <input type="password" class="form-control required" name="rPassWord" id="rPassWord" autocomplete="off" placeholder="Mật khẩu *" value="">
                <div class="i-change"><a href="javascript:void(0);" title="hide/show"></a></div>
              </div>
            </div>
            <div class="form-group">
              <div class="input-pass div_input">
                <input type="password" class="form-control required" name="rConfirm_PassWord" id="rConfirm_PassWord" autocomplete="off" placeholder="Xác nhận mật khẩu *" value="">
                <div class="i-change"><a href="javascript:void(0);" title="hide/show"></a></div>
              </div>
            </div>

            <div class="form-group df">
              <div class="checkbox div_input"><label for="r_agree"><input type="checkbox" id="r_agree" name="r_agree" value="1" class="required">Tôi đồng ý với <a href="/qui-dinh-ve-dang-tin.html" target="_blank">Điều khoản dịch vụ</a> và <a href="/chinh-sach-bao-mat.html" target="_blank">Chính sách bảo mật</a> của công ty</label></div>
            </div>
            <div class="form-btn">
              <input type="hidden" name="csrf_token" value="a4ea56d767b8b7db3ab0747c0c98b553">
              <input type="hidden" name="do_submit" id="do_submit" value="1">
              <input type="hidden" name="url_register" id="url_register" class="url_register" value="/tour/member/ajax/ajax_register.html">
              <button type="submit"><span>Đăng ký</span></button>
            </div>
          </div>
        </form>

        <script>
          jQuery(window).ready(function() {

            var valid_reg = $("#formPopupReg").validate({
              rules: {
                rEmail: {
                  required: true,
                  email: true,
                  remote: '/tour/member/ajax/check_email.html'
                },
                rPassWord: {
                  required: true,
                  minlength: 6
                },
                rConfirm_PassWord: {
                  required: true,
                  equalTo: "#rPassWord"
                },
                r_agree: {
                  required: true
                }
              },
              messages: {
                rEmail: {
                  required: "Nhập email",
                  email: "Email không hợp lệ",
                  remote: "Rất tiếc! Đã có người sử dụng email này"
                },
                rPassWord: {
                  required: "Nhập mật khẩu",
                  minlength: "Mật khẩu phải có ít nhất 6 ký tự."
                },
                rConfirm_PassWord: {
                  required: "Nhập lại mật khẩu",
                  equalTo: "Xác nhận mật khẩu phải giống mật khẩu."
                },
                r_agree: {
                  required: "Vui lòng đánh dấu vào ô đồng ý"
                }

              },
              errorElement: "em",
              errorPlacement: function(error, element) {
                // Add the `help-block` class to the error element
                error.addClass("help-block");

                // Add `has-feedback` class to the parent div.form-group
                // in order to add icons to inputs
                element.parents(".div_input").addClass("has-feedback");

                if (element.prop("type") === "checkbox") {
                  error.insertAfter(element.parent("label"));
                } else {
                  error.insertAfter(element);
                }

                // Add the span element, if doesn't exists, and apply the icon classes to it.
                if (!element.next("span")[0]) {
                  $("<span class='glyphicon glyphicon-remove form-control-feedback'></span>").insertAfter(element);
                }
              },
              success: function(label, element) {
                // Add the span element, if doesn't exists, and apply the icon classes to it.
                if (!$(element).next("span")[0]) {
                  $("<span class='glyphicon glyphicon-ok form-control-feedback'></span>").insertAfter($(element));
                }
              },
              highlight: function(element, errorClass, validClass) {
                $(element).parents(".div_input").addClass("has-error").removeClass("has-success");
                $(element).next("span").addClass("glyphicon-remove").removeClass("glyphicon-ok");
              },
              unhighlight: function(element, errorClass, validClass) {
                $(element).parents(".div_input").addClass("has-success").removeClass("has-error");
                $(element).next("span").addClass("glyphicon-ok").removeClass("glyphicon-remove");
              },
              submitHandler: function(form) {
                vnTAccout.doRegister($(form));
                return false;
              }
            });
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
              <button type="submit" class="btnColo" onclick=" vnTAccout.doForgetPass($(this).parents('form'));">
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
                    <div class="mb5"><a href="#" target="_self">Tour trọn gói</a></div>
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
</body>

</html>