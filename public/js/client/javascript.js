/** vnTAccout **/
var vnTAccout = function () {
  return {
    init: function () {
      var $this = this;
      // Khởi tạo biến toàn cục (đăng nhập vào Đặt tour theo yêu cầu)
      window.pendingRedirectUrl = null;

      $("html").on("click", ".vnt-popup-login", function () {
        // Nếu link đó có thuộc tính data-redirect, lưu nó lại
        var redirect = $(this).data('redirect');

        if (redirect) {
          // Lưu vào biến toàn cục window
          window.pendingRedirectUrl = redirect;
        } else {
          window.pendingRedirectUrl = null;
        }

        $this.popupLogin();
      });

      $(".vnt-popup-register").on('click', function () {
        $this.popupRegister();
      });

      $(".vnt-popup-forgetpass").on('click', function () {
        $this.popupForgetPass();
      });

      $(".a-login").on('click', function () {
        $("#tabforgetpass").removeClass('active');
        $("#tablogin").addClass('active');
      });

      $(".vnt-forgetpass").on('click', function () {
        $("#tablogin").removeClass('active');
        $("#tabforgetpass").addClass('active');
      });

      $(".vnt-form input").focus(function () {
        $(this).parent().removeClass("has-error");
      });


      $('.input-pass').each(function () {
        $(this).find('a').on('click', function () {
          if (!$(this).parents(".input-pass").hasClass("show")) {
            $(this).parents(".input-pass").addClass("show");
            $(this).parents('.input-pass').find('input').attr('type', 'text');
          } else {
            $(this).parents(".input-pass").removeClass("show");
            $(this).parents('.input-pass').find('input').attr('type', 'password');
          }
        })
      })

    },

    popupLogin: function () {
      $.fancybox.close();
      $("#vnt-popup-login").find(".loading-account").remove();
      $.fancybox.open({
        type: 'inline',
        src: "#vnt-popup-login",
        baseClass: 'vnt-popup',
        smallBtn: true,
        touch: false,
        clickSlide: false,
        clickOutside: false
      });
    },

    doLogin: function (obj_form) {
      var ok_login = 1;
      var msg_err = '';

      if (ok_login) {

        var params = obj_form.serialize();
        $.ajax({
          async: true,
          url: API_URL + '/login',
          dataType: 'json',
          type: "post",
          data: params,
          headers: {
            "csrf-token": $("#vnt_csrf_token").val()
          },
          beforeSend: function () {
            obj_form.addClass('vnt-loading');
          },
          success: function (rs) {
            obj_form.removeClass('vnt-loading');
            if (rs.success) {
              //obj_form[0].reset();

              //reset cart
              localStorage.removeItem("vnt_cart");
              let link_ref = obj_form.find(".login_ref").val();
              if (link_ref) {
                location.href = ROOT + link_ref;
              } else {
                location.reload();
              }
            } else {
              Swal.fire({ timer: 3000, icon: 'error', title: js_lang['error'], html: rs.error_msg, showConfirmButton: false });
            }
          }
        });
      } else {
        Swal.fire({ icon: 'error', title: js_lang['error'], html: msg_err });
      }
      return false;
    },
    popupForgetPass: function () {
      $.fancybox.close();
      $("#vnt-popup-forgetpass").find(".loading-account").remove();
      $.fancybox.open({
        type: 'inline',
        src: "#vnt-popup-forgetpass",
        baseClass: 'vnt-popup',
        smallBtn: true,
        toolsbar: false,
        touch: false,
        clickOutside: false,
      });
    },

    doForgetPass: function (obj_form) {
      ok_go = 1;
      var email = obj_form.find('.forget_email').val();
      var url_ajax = $("#url_forget").val();
      if (email == "") {
        obj_form.find('.forget_email').parent().addClass('has-error');
        ok_go = 0;
      }
      if (ok_go) {
        var mydata = "email=" + email;
        $.ajax({
          async: true,
          dataType: 'json',
          url: url_ajax,
          type: 'POST',
          data: mydata,
          beforeSend: function () {
            obj_form.addClass('vnt-loading');
          },
          success: function (data) {
            obj_form.removeClass('vnt-loading');
            obj_form.find('.forget_email').val('');
            if (data.ok) {
              $.fancybox.close();
              Swal.fire({ icon: 'success', title: js_lang['announce'], html: data.mess });

              //obj_form.find(".account-err").html('<div class="alert alert-success">' + data.mess + '</div>');
            } else {
              obj_form.find(".account-err").html('<div class="alert alert-danger">' + data.mess + '</div>');
            }
          }
        });
      }
    },

    popupRegister: function () {
      $.fancybox.close();
      $("#vnt-popup-register").find(".loading-account").remove();
      $.fancybox.open({
        type: 'inline',
        src: "#vnt-popup-register",
        baseClass: 'vnt-popup',
        smallBtn: true,
        toolsbar: false,
        touch: false,
        clickOutside: false,
      });
    },


    doRegister: function (obj_form) {
      var url_register = ROOT_MOD + '/ajax/ajax_register.html';
      if (obj_form.find('.url_register').val()) {
        url_register = obj_form.find('.url_register').val();
      }

      var params = obj_form.serialize();
      $.ajax({
        async: true,
        url: url_register,
        dataType: 'json',
        type: "post",
        data: params,
        success: function (rs) {
          obj_form.removeClass('vnt-loading');
          if (rs.ok == 1) {
            $.fancybox.close();

            obj_form[0].reset();
            obj_form.find('.div_input').removeClass('has-feedback');
            obj_form.find('.div_input').removeClass('has-success');
            obj_form.find('.form-control-feedback').remove();
            obj_form.find('em').remove();

            if (rs.link_ref) {
              location.href = rs.link_ref;
            } else {
              Swal.fire({ timer: 1000, icon: 'success', title: rs.mess }).then(() => {
                location.reload(true);
              });
            }


          } else {
            Swal.fire({ icon: 'error', title: js_lang['error'], html: rs.mess });
            //$(".error-register").html('<div class="alert alert-danger">' + rs.mess + '</div>');
          }
        }
      });
      return false;

    },
    doLogout: function () {
      if (lang == "vn") {
        mess = 'Bạn có muốn thoát khỏi hệ thống không ?'
      } else {
        mess = 'Do you want to logout account ?';
      }

      Swal.fire({
        title: mess,
        icon: 'question',
        showCancelButton: 1,
        confirmButtonText: "Đồng ý"
      }).then(function (result) {
        if (result.isConfirmed) {

          $.ajax({
            url: API_URL + '/logout',
            dataType: "json",
            type: "GET",
            headers: {
              "Authorization": "Bearer " + API_KEY,
              "csrf-token": $("#vnt_csrf_token").val()
            },
            success: function (rs) {
              if (rs.success) {
                location.reload();
              } else {
                Swal.fire({ position: "top-end", icon: 'error', title: js_lang['error'], html: rs.error_msg });
              }
            }
          });

        }

      });
      return false;
    }
  };
}();


/** initLocalStorage **/
vnTRUST.initLocalStorage = function () {

  var hours = 1; // Reset when storage is more than  x hours
  var now = Date.now();
  var vnt_expire_time = localStorage.getItem('vnt_expire_time');
  if (vnt_expire_time == null) {
    localStorage.clear();
    localStorage.setItem('vnt_expire_time', now);
  } else if ((now - vnt_expire_time) > hours * 60 * 60 * 1000) {
    localStorage.clear();
    localStorage.setItem('vnt_expire_time', now);
  }


  //load data location
  var local_city = localStorage.getItem("arr_city");
  var local_state = localStorage.getItem("arr_state");
  var isload = false;
  //console.log(JSON.parse(local_city));
  if (!vnTRUST.isJson(local_city)) {
    isload = true;
  } else {
    var arrp = JSON.parse(local_city);
    if (vnTRUST.isJson(local_city) && arrp != null && typeof arrp[0].name === "undefined") {
      isload = true;
    }
  }
  if (isload || !local_city || !local_state) {
    $.ajax({
      url: ROOT + "api/getlocations",
      method: "GET",
      success: function (rs) {
        if (rs.success) {
          localStorage.setItem("arr_city", JSON.stringify(rs.data.city));
          localStorage.setItem("arr_state", JSON.stringify(rs.data.state));
        }
      },
    });
  }


};


/** Top Nav
 **************************************************************** **/
vnTRUST.topNav = function () {
  $(window).scroll(function (e) {
    var _scroll = $(window).scrollTop();
    if (_scroll > $(".w-fixed-menu").offset().top) {
      $(".fixed-menu").addClass("active");
    } else {
      $(".fixed-menu").removeClass("active");
    }
  });

  /*2023*/
  vnTRUST.initLocalStorage();
  vnTAccout.init();
};


vnTRUST.load_NewsApi = function (objShow, api_url, param, slide) {

  $.ajax({
    async: true,
    dataType: 'json',
    url: api_url,
    type: 'GET',
    data: param,
    headers: {
      "Authorization": "Bearer " + API_KEY,
      "csrf-token": $("#vnt_csrf_token").val()
    },
    beforeSend: function () {
      objShow.find(".loading-grid").show();
    },
    success: function (data) {
      objShow.find(".loading-grid").hide();

      if (data.length) {
        var html = '';

        data.forEach(function (item) {
          html += `<div class="item">
                      <div class="iNews">
                          <div class="i-img">
                            <a href="${item.link}"><img src="${item.src}" alt="${item.title}" /></a>
                          </div>
                          <div class="i-desc">
                              <div class="w-desc">
                                  <h3 class="i-title">
                                    <a  href="${item.link}" >${item.title}</a>
                                  </h3>
                                  <div class="i-content">${item.short}</div>
                              </div>
                          </div>
                      </div>
                  </div>`;
        });

        if (slide) {
          html = '<div class="slide-news">' + html + '</div>';
        }

        objShow.find('.ext_list').html(html);

        setTimeout(function () {
          objShow.find(".slide-news").slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 5000,
            speed: 800,
            swipeToSlide: true,
            pauseOnHover: true,
            responsive: [
              {
                breakpoint: 992,
                settings: {
                  slidesToShow: 3
                }
              },
              {
                breakpoint: 768,
                settings: {
                  slidesToShow: 2
                }
              },
              {
                breakpoint: 600,
                settings: {
                  slidesToShow: 1
                }
              }
            ]
          });

        }, 100);

      } else {
        objShow.remove();
      }

    }
  });

};


/** init Load
 **************************************************************** **/
vnTRUST.init = function () {
  var Xwidth = $(window).width();
  if (Xwidth < 1100) {
    $(".floating-left").hide();
    $(".floating-right").hide()
  }


  $('[data-toggle="tooltip"]').tooltip();

  $(".alert-autohide").delay(5000).slideUp(200, function () {
    $(this).alert('close');
  });

  $(".load_state").change(function () {
    var ext_display = $(this).attr("data-state");

    var mydata = "do=option_state&city=" + $(this).val();
    $.ajax({
      type: "GET",
      url: ROOT + 'load_ajax.php',
      data: mydata,
      success: function (html) {
        $("#" + ext_display).html(html);
      }
    });
  });


  $(".load_ward").change(function () {
    var ext_display = $(this).attr("data-ward");

    var mydata = "do=option_ward&state=" + $(this).val();
    $.ajax({
      type: "GET",
      url: ROOT + 'load_ajax.php',
      data: mydata,
      success: function (html) {
        $("#" + ext_display).html(html);
      }
    });
  });

  $('.chosen-select').chosen();

  $('#vnt_banner').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    fade: true,
    autoplay: true,
    autoplaySpeed: 5000,
    speed: 800
  });
  $('#vnt_banner').addClass("active");
  $("#banner_img").slick({
    dots: false,
    speed: 800,
    fade: true,
    arrows: false,
    cssEase: 'linear',
    autoplay: true,
    //pauseOnHover: true,
    autoplaySpeed: 3000
  });
  $("#banner_main").slick({
    dots: false,
    speed: 800,
    fade: true,
    arrows: false,
    cssEase: 'linear',
    autoplay: true,
    //pauseOnHover: true,
    autoplaySpeed: 8000
  });
  $("#banner_main").addClass("active");


  $(".vnt-support .s-title").click(function (e) {
    if (!$(this).parents(".vnt-support").hasClass("active")) {
      $(this).parents(".vnt-support").addClass("active");
    } else {
      $(this).parents(".vnt-support").removeClass("active");
    }
  });
  $(".wrap_menu_cat .menu_cat_title").click(function (e) {
    if (!$(this).parents(".wrap_menu_cat").hasClass("active")) {
      $(this).parents(".wrap_menu_cat").addClass("active");
    } else {
      $(this).parents(".wrap_menu_cat").removeClass("active");
    }
  });
  $(".menu-qldp .menu-title").click(function (e) {
    if (!$(this).parents(".menu-qldp").hasClass("active")) {
      $(this).parents(".menu-qldp").addClass("active");
    } else {
      $(this).parents(".menu-qldp").removeClass("active");
    }
  });
  $(".link-tour-footer .link-title").click(function (e) {
    if (!$(this).parents(".link-tour-footer").hasClass("active")) {
      $(this).parents(".link-tour-footer").addClass("active");
    } else {
      $(this).parents(".link-tour-footer").removeClass("active");
    }
  });


  $(".menu-category .mc-title").click(function (e) {
    if (!$(this).parents(".menu-category").hasClass("active")) {
      $(this).parents(".menu-category").addClass("active");
    } else {
      $(this).parents(".menu-category").removeClass("active");
    }
  });
  $(window).bind("click", function (e) {
    var $clicked = $(e.target);
    if (!$clicked.parents().hasClass("menu-category")) {
      $(".menu-category").removeClass("active");
    }
  });


  vnTRUST.topNav();
  vnTRUST.ZoomImage();
  vnTRUST.goTopStart();

  $(window).resize(function () {
  });

};

/* Init */
jQuery(window).ready(function () {
  vnTRUST.init();
});