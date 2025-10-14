var vnTTour = function () {

  var objResult = 'load_tour';
  return {

    ShowWaiting: function () {
      $("body").addClass("loading");
    },
    HideWaiting: function () {
      $("body").removeClass('loading');
    },

    buil_data_calendar: function (day_show) {
      setTimeout(() => {

        $('.ui-datepicker td a').removeClass("ui-state-active");
        if (day_show.length) {
          day_show.forEach(function (item) {
            let price = item.price;
            let status = item.status;
            let arr_date = item.date.split("/");
            let cmonth = parseInt(arr_date[1].trim()) - 1;
            let cyear = parseInt(arr_date[2].trim());
            let cday = parseInt(arr_date[0].trim());


            if (status == 1) {
              $('.ui-datepicker td[data-month="' + cmonth + '"][data-year="' + cyear + '"]').children('a[data-date="' + cday + '"]').addClass('green_color')
            }
            if (status == 2) {
              $('.ui-datepicker td[data-month="' + cmonth + '"][data-year="' + cyear + '"]').children('a[data-date="' + cday + '"]').addClass('red_color')
            }
            $('.ui-datepicker td[data-month="' + cmonth + '"][data-year="' + cyear + '"]').children('a[data-date="' + cday + '"]').append("<span class='txtmdy'>" + price + "</span>");

          });
        }

      }, 100);
      return false;
    },

    popupCalendar: async function (id) {
      var $this = this;
      const response = await fetch(API_URL + "/tour/calendar?id=" + id + "&show=data", {
        method: "GET",
        headers: {
          "Authorization": "Bearer " + API_KEY,
          "csrf-token": $("#vnt_csrf_token").val()
        }
      });
      var data = await response.json();
      var obj_calendar = $("#item_" + id + " .i-datepicker");

      if (data.length) {
        let i = 0;
        data.forEach(function (item) {
          i++;
          if (i == 1) {
            obj_calendar.val(item.date)
          }
        });
      }


      obj_calendar.datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy',
        yearRange: "1900:+200",
        beforeShowDay: function (date) {
          var month = date.getMonth() + 1;
          var year = date.getFullYear();
          var day = date.getDate();
          var newdate = day + "-" + month + '-' + year;
          var tooltip_text = "Ngày chọn " + newdate;
          var dis_tooltip_text = "Không có ngày " + newdate;
          var _ishas = false;

          if (data.length) {
            data.forEach(function (item) {
              let arr_date = item.date.split("/");
              let mydate = parseInt(arr_date[0]) + "-" + parseInt(arr_date[1]) + '-' + parseInt(arr_date[2]);
              if (mydate.trim() == newdate.trim() && !_ishas) {
                _ishas = true;
              }
            });
          }

          if (_ishas) {
            return [true, "active", tooltip_text];
          } else {
            return [true, "disactive", dis_tooltip_text];
          }
          return [true];
        },
        beforeShow: function () {
          $this.buil_data_calendar(data);
        },
        onChangeMonthYear: function () {
          $this.buil_data_calendar(data);
        },
        onSelect: function (date) {
          let arr_date = date.split("/");
          let date_url = arr_date[0] + '-' + arr_date[1] + '-' + arr_date[2];
          let link_ref = $("#item_" + id).find('.i-title a').attr('href') + '/?day=' + date_url;
          location.href = link_ref;
        }

      });


      obj_calendar.datepicker("show");

      return false;
    },

    buildNumFilter: function () {

      return false;
    },

    changeUrl: function () {

      let new_url = ROOT_MOD + '.html';
      let objForm = $("#formModSearch");
      let params = objForm.serialize();
      if (params) {
        new_url += '/?' + params;
      }
      history.pushState({}, null, new_url);
    },

    initFilter: function () {
      var $this = this;
      $this.buildNumFilter();

      if (($(window).innerWidth() <= 992)) {
        var fp1 = flatpickr(".flatpickr-input", {
          showMonths: 1,
          mode: "range",
          dateFormat: "d/m/Y",
          minDate: "today",
          locale: "vn"
        })
      }
      else {
        var fp2 = flatpickr(".flatpickr-input", {
          showMonths: 3,
          mode: "range",
          dateFormat: "d/m/Y",
          minDate: "today",
          locale: "vn"
        })
      }

      // Price slider
      $(function () {
        $("#slider_price").slider({
          range: true,
          min: 0,
          max: 50000000,
          step: 100000,
          values: [0, 50000000],
          slide: function (event, ui) {
            // Cập nhật text hiển thị
            $("#price_min").html(ui.values[0].toLocaleString('vi-VN'));
            $("#price_max").html(ui.values[1].toLocaleString('vi-VN'));

            // Gán lại giá trị vào input hidden
            $("#s_price_min").val(ui.values[0]);
            $("#s_price_max").val(ui.values[1]);
          }
        });

        // Hiển thị giá mặc định khi load trang
        $("#price_min").html($("#slider_price").slider("values", 0).toLocaleString('vi-VN'));
        $("#price_max").html($("#slider_price").slider("values", 1).toLocaleString('vi-VN'));
      });




      $("#sort").on("change", function (e) {
        location.href = $(this).val();
      });


      $("#btnModSearch").click(function () {
        $this.onSubmit();
        //$this.onSearch();
      });


    },

    loadTemplate: function (items, slide) {


      var html = '';
      var item_class = 'item';
      if (slide == 1) {
        item_class = 'item';
      }
      items.forEach(function (item) {

        let text_price = 'Liên hệ';
        if (item.price) {
          text_price = vnTRUST.numberFormat(item.price) + ' đ';
        }

        let text_price_old = '';
        if (item.price_old) {
          text_price_old = '<div class="i-price-line">' + vnTRUST.numberFormat(item.price_old) + ' đ</div>';
        }

        html += `<div class="${item_class}">
                <div class="item-tour">
                  <div class="i-img">
                      <a href="${item.link}" title="${item.title}">
                          <img src="${item.src}" alt="${item.title}" />
                          <span class="i-address">${item.last_destination}</span>
                      </a>
                      ${item.text_status}
                  </div>
                  <div class="i-desc">
                      <div class="i-title">
                          <a href="${item.link}">${item.title}</a>
                      </div>
                      <div class="mb5 text-overflow">
                          <div class="price-star">
                              <div class="div-price">
                                  <div class="i-price">${text_price}</div>
                                  ${text_price_old}
                              </div>
                              <div class="div-star">${item.img_star}</div>
                          </div>
                      </div>
                      <div class="i-content">
                          <div class="text-overflow"><strong>${js_lang['departure']}:</strong> ${item.text_departure}</div>
                          <div class="text-overflow"><strong>${js_lang['destination']}:</strong> ${item.text_destination}</div>
                          <div class="text-overflow"><strong>${js_lang['duration']}:</strong> ${item.text_duration}</div>
                          <div class="text-overflow"><strong>${js_lang['department']}:</strong> ${item.date_department}</div>
                          <div class="i-right">
                              <div class="i-icon">
                                  <ul>
                                     ${item.list_transport}
                                  </ul>
                              </div>
                          </div>      
                      </div>
                  </div>
              </div>
             </div>`;
      });

      if (slide == 1) {
        html_out = '<div class="slide-tour">';
        html_out += html;
        html_out += '</div>';
      } else {
        html_out = '<div class="list-tour">';
        html_out += html;
        html_out += '</div>';
      }



      return html_out;
    },

    loadItem: function (ptype, params, slide) {

      var obj_show = $("#vnt-load_" + ptype);

      $.ajax({
        async: true,
        dataType: 'json',
        url: ROOT + "api/tour/items?ptype=" + ptype,
        type: 'GET',
        data: params,
        headers: {
          "Authorization": "Bearer " + API_KEY,
          "csrf-token": $("#vnt_csrf_token").val()
        },
        beforeSend: function () {

        },
        success: function (rs) {
          if (rs.success) {
            if (rs.data.num) {
              var html = vnTTour.loadTemplate(rs.data.items, slide);
              obj_show.find('.ext_item').html(html);
              if (slide == 1) {
                setTimeout(function () {
                  obj_show.find('.slide-tour').slick({
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
              }

            } else {
              obj_show.remove();
            }
          } else {
            obj_show.remove();
          }

        }
      });
      return false;

    },

    goPager: function (p) {
      $("#page").val(p);

      var targetOffset = $("#" + this.objResult).offset().top - 200;
      jQuery('html,body').animate({ scrollTop: targetOffset }, 100);


      let cur_url = window.location.href;
      let new_url = vnTRUST.updateQueryStringParameter(cur_url, 'p', p);
      // alert('cur_url = '+cur_url);

      //window.location.href =

      history.pushState({}, null, new_url);

      this.onSearch();
    },

    BindDataItem: function (items) {
      var $this = this;
      var objShow = $("#" + $this.objResult).find('.ext_html');

      var html = '';
      var num_item = parseInt(items.length);
      if (num_item > 0) {

        items.forEach(function (item) {

          let text_price = 'Liên hệ';
          if (item.price) {
            text_price = vnTRUST.numberFormat(item.price) + ' đ';
          }

          let text_price_old = '';
          if (item.price_old) {
            text_price_old = '<div class="i-price-line">' + vnTRUST.numberFormat(item.price_old) + ' đ</div>';
          }

          let text_note = '';
          if (item.note) {
            text_note = ' <div><strong>Ghi chú:</strong> ' + item.note + '</div>';
          }

          let text_date_department = item.date_department;
          text_date_department += '<span class="vdateall i-choose-date" data-id="' + item.id + '"><input class="i-datepicker" type="text"  data-id="' + item.id + '"><i class="far fa-calendar"></i> Ngày khác</span>';

          html += `<div class="item" id="item_${item.id}" >
                    <div class="tour-detail">
                        <div class="i-img">
                            <a href="${item.link}">
                                <img src="${item.src}" alt="${item.title}" />
                            </a>
                            ${item.text_status}
                        </div>
                        <div class="i-desc">
                            <div class="i-title">
                                <a href="${item.link}">${item.title} </a>
                                ${item.img_star}
                            </div>
                            <div class="i-content">
                                <div><strong>`+ js_lang['departure'] + `:</strong> ${item.text_departure}</div>
                                <div><strong>`+ js_lang['destination'] + `:</strong> ${item.text_destination}</div>
                                ${text_note}
                                <div><strong>`+ js_lang['duration'] + `:</strong> ${item.text_duration}</div>
                                <div class="vchange"><strong>`+ js_lang['department'] + `:</strong> ${text_date_department}</div>
                            </div>
                        </div>
                        <div class="i-info">
                            <div class="i-dates">
                                <span>${item.text_departure_date}</span>
                            </div>
                            <div>`+ js_lang['price_per_guest'] + `</div>
                            <div class="g-price">${text_price_old}<div class="i-price">${text_price}</div></div>
                            <div class="i-link">
                                <a href="${item.link}">` + js_lang['detail'] + ` <i class="fa fa-chevron-right"></i></a>
                            </div>
                            <div class="i-icon">
                                <ul>
                                    ${item.list_transport}
                                </ul>
                            </div>
                        </div>
                
                        <div class="clear"></div>
                    </div>
                </div>`;
        });
      } else {
        html = '<div class="noItem"><div class="alert alert-danger">' + js_lang['no_have_item'] + '</div></div>';
      }





      objShow.html(html);

      // init after load
      setTimeout(function () {

      }, 100);

    },

    loadResult: function (params) {
      var $this = this;

      var objShow = $("#" + $this.objResult).find('.ext_html');
      var objNav = $("#" + $this.objResult).find('.ext_nav');


      $.ajax({
        url: API_URL + '/tour/lists',
        dataType: "json",
        type: "post",
        data: params,
        headers: {
          "Authorization": "Bearer " + API_KEY,
          "csrf-token": $("#vnt_csrf_token").val()
        },
        beforeSend: function () {
          $("#" + $this.objResult).find(".loading-list").show()
        },
        success: function (rs) {
          $("#" + $this.objResult).find(".loading-list").hide()

          if (rs.success) {

            var items = rs.data.items;
            var paging = rs.data.paging;
            $this.BindDataItem(items);

            if (paging.num_pages > 1) {
              objNav.find('.js-pagination').pagination({
                total: paging.totals,
                current: paging.cur_page,
                length: paging.page_size,
                size: 3,
                prev: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>',
                click: function (options, $target) {
                  $this.goPager(options.current);
                }
              });
            } else {
              objNav.find('.js-pagination').html('');
            }

            $('.num_tour').html(paging.totals);
          } else {
            Swal.fire({ icon: 'error', title: js_lang['error'], html: rs.error_msg });
          }
        }
      });

      return true;

    },

    onSearch: function () {

      var objForm = $("#formModSearch");
      let _sort = parseInt($("#sort").val());
      let _page = parseInt($("#page").val());
      let _page_size = parseInt($("#page_size").val());
      let _filterParam = $("#filterParam").val();

      var params = objForm.serialize();
      params += '&sort=' + _sort + '&page=' + _page + '&page_size=' + _page_size + '&lang=' + lang;
      params += _filterParam;

      this.loadResult(params);

      return true;
    },

    onSubmit: function () {
      var $this = this;

      let url = ROOT_MOD + ".html";
      let dataForm = $("#formModSearch").serializeArray();
      let params = '';
      $.each(dataForm, function (i, field) {
        if (field.value != '' && field.value != 0) {

          if (field.name == 'startdate') {
            //field.value = field.value.replaceAll("/", "-");
            let arr_date = field.value.split("-").map(s => s.trim());
            let date_from = arr_date[0].replaceAll("/", "-");
            let date_to = arr_date[1].replaceAll("/", "-");
            params += 'date_from=' + date_from + '&date_to=' + date_to;

          } else {
            params += field.name + '=' + field.value + '&';
          }

        }
      });

      if (params) {
        let _sort = $("#sort").val();
        params += '&sort=' + _sort;

        url += '/?' + params;
      }
      location.href = url;

    },

    initLoad: function () {
      this.onSearch();

    },

    init: function () {
      var $this = this;

      $("html").on("click", ".i-choose-date", function () {
        let id = $(this).attr('data-id');
        $this.popupCalendar(id);
      });

      $("html").on("click", ".ac-sort", function () {
        let sort = $(this).attr('data-value');
        $("#sort").val(sort);

        $this.onSearch();
      });

      $("html").on("click", ".vnt-box-select__text", function () {
        $('.vnt-box-select').not($(this).parent()).removeClass('active');
        $(this).closest('.vnt-box-select').toggleClass('active');
      });

      $("html").on("click", ".vnt-box-select__list li", function () {
        let objbox = $(this).closest('.vnt-box-select');
        objbox.removeClass('active');
        objbox.find('.vnt-box-select__text').html($(this).html());
      });

      $(window).bind("click", function (e) {
        var $clicked = $(e.target);
        if (!$clicked.parents().hasClass("vnt-box-select")) {
          $(".vnt-box-select").removeClass("active");
        }
      });

    }

  };
}();

/* Init */
$(document).ready(function () {
  vnTTour.init();
});
