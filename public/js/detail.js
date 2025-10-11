vnTCart = {


  do_Booking: function (id,price_id) {
    $this = this;

    var ok_add = 1;
    var msg_err ='';

    if(id==0){
      ok_add = 0 ;
      msg_err = 'Vui lòng chọn 1 tour' ;
    }
    if(price_id==0){
      ok_add = 0 ;
      msg_err = 'Vui lòng chọn 1 lịch khởi hành' ;
    }


    if (ok_add)
    {

      var params = 'sid=' +sid +'&item_id=' + id + '&price_id=' + price_id +'&quantity=1&child_quantity=0&lang='+lang;
      $.ajax({
        url: API_URL + "/booking/addcart",
        dataType: "json",
        type: "post",
        data: params,
        headers: {
          "Authorization": "Bearer "+API_KEY,
          "csrf-token": $("#vnt_csrf_token").val()
        },
        beforeSend :function (){
          $('body').addClass('loading');
        },
        success: function (rs) {
          $('body').removeClass('loading');
          if (rs.success)
          {
            location.href = ROOT_MOD + '/booking.html';
          }else {
            Swal.fire({ icon: 'error',title: js_lang['error'],html: rs.error_msg});
          }
        }
      });


    }else{
      Swal.fire({ icon: 'error',title: js_lang['error'],html: msg_err});
    }


    return false;
  },



  do_AddCart: function (id,obj_form) {
    $this = this;

    var ok_add = 1;
    var msg_err ='';
    var price_id =  parseInt($("#pid").val());

    var quantity = parseInt(obj_form.find(".adult_quantity").val());
    var child_quantity = parseInt(obj_form.find(".child_quantity").val());

    if(id==0){
      ok_add = 0 ;
      msg_err = 'Vui lòng chọn 1 tour' ;
    }
    if(price_id==0){
      ok_add = 0 ;
      msg_err = 'Vui lòng chọn 1 lịch khởi hành' ;
    }
    if(quantity==0){
      ok_add = 0 ;
      msg_err = 'Vui lòng chọn số người lớn' ;
    }


    if (ok_add)
    {

      var params = 'sid=' +sid +'&item_id=' + id + '&price_id=' + price_id +'&quantity='+quantity+'&child_quantity='+child_quantity +'&lang='+lang;
      //alert('params = '+params);
      $.ajax({
        url: API_URL + "/booking/addcart",
        dataType: "json",
        type: "post",
        data: params,
        headers: {
          "Authorization": "Bearer "+API_KEY,
          "csrf-token": $("#vnt_csrf_token").val()
        },
        beforeSend :function (){
          $('body').addClass('loading');
        },
        success: function (rs) {
          $('body').removeClass('loading');
          if (rs.success)
          {
            location.href = ROOT_MOD + '/booking.html';
          }else {
            Swal.fire({ icon: 'error',title: js_lang['error'],html: rs.error_msg});
          }
        }
      });


    }else{
      Swal.fire({ icon: 'error',title: js_lang['error'],html: msg_err});
    }


    return false;
  },




  caculate_total_price: function ()
  {


    let departure_date = $("#departure_date").val();
    let key_day =  departure_date.replaceAll("/", "-").trim();

    //alert('key_day= '+key_day);

    if (Object.keys(info_price_items).indexOf(key_day) != -1)
    {
      var data = info_price_items[key_day];

      let price = parseInt(data['price']);
      let price_old = parseInt(data['price_old']);

      let price_child = parseInt(data['price_child']);
      let price_old_child = parseInt(data['price_old_child']);


      let adult_quantity = parseInt($(".adult_quantity").val());
      if (adult_quantity == 0) {
        adult_quantity = 1;
      }

      let child_quantity = parseInt($(".child_quantity").val());

      let total_price = (adult_quantity * price);
      if (price_child > 0 && child_quantity > 0) {
        total_price += (child_quantity * price_child);
      }

      let total_price_old = (adult_quantity * price_old);
      if (child_quantity > 0) {
        if(price_old_child > 0 ) {
          total_price_old += (child_quantity * price_old_child);
        }else{
          total_price_old += (child_quantity * price_child);
        }
      }

      //alert('price = '+price);
      text_total_price = 'Liên hệ';
      if (total_price > 0) {
        text_total_price = vnTRUST.numberFormat(total_price) + ' đ';
      }
      text_total_price_old = '';
      if (total_price_old > 0) {
        text_total_price_old = vnTRUST.numberFormat(total_price_old) + ' đ';
      }


      $(".total-price").html(text_total_price);
      $(".total-price-old").html(text_total_price_old);
    }
    return false;
  },

  load_data_price: function ()
  {

    let departure_date = $("#departure_date").val();

    let key_day =  departure_date.replaceAll("/", "-").trim();
    var pid = 0 ;

    if (Object.keys(info_price_items).indexOf(key_day) != -1)
    {
      var data = info_price_items[key_day];
      pid = parseInt(data['id']);


      //console.log(data);

      let price = parseInt(data['price']);
      let price_old = parseInt(data['price_old']);
      let text_price = 'Liên hệ' ;
      if(price>0){
        text_price =  vnTRUST.numberFormat(price)+' đ' ;
      }

      let text_price_old = '';
      if(price_old>0){
        text_price_old = vnTRUST.numberFormat(price_old)+' đ' ;
      }

      let price_child = parseInt(data['price_child']);
      let price_old_child = parseInt(data['price_old_child']);
      let text_price_child =  'Liên hệ' ;
      if(price_child>0) {
        text_price_child =  vnTRUST.numberFormat(price_child)+' đ' ;
      }
      let text_price_old_child = '';
      if(price_old_child>0){
        text_price_old_child = vnTRUST.numberFormat(price_old_child)+' đ' ;
      }

      let adult_quantity = parseInt($(".adult_quantity").val()) ;
      if(adult_quantity==0){
        adult_quantity = 1;
      }

      let child_quantity = parseInt($(".child_quantity").val());

      let total_price = (adult_quantity * price) ;
      if(price_child > 0 && child_quantity> 0){
        total_price +=  (child_quantity * price_child) ;
      }

      let total_price_old = (adult_quantity * price_old) ;
      if (child_quantity > 0) {
        if(price_old_child > 0 ) {
          total_price_old += (child_quantity * price_old_child);
        }else{
          total_price_old += (child_quantity * price_child);
        }
      }


      //alert('price = '+price);
      text_total_price = 'Liên hệ';
      if(total_price>0){
        text_total_price =  vnTRUST.numberFormat(total_price)+' đ' ;
      }
      text_total_price_old = '';
      if(total_price_old>0){
        text_total_price_old =  vnTRUST.numberFormat(total_price_old)+' đ' ;
      }



      $(".i-price").html(text_price);
      $(".i-price_old").html(text_price_old);
      $(".i-price_child").html(text_price_child);
      $(".i-price_old_child").html(text_price_old_child);


      $(".total-price").html(text_total_price);
      $(".total-price-old").html(text_total_price_old);

      $("#pid").val(pid);

    }



  },

  loaddatadate: function (arr)
  {

    var textli = '';
    if(arr.length > 0){
      for(k = 0; k < arr.length; k++){
        var artext = arr[k].split('/');
        var liactive = (k == nuactive)? "class='active'":'';
        textli += "<li "+liactive+" data-date='"+artext[0].trim()+"/"+artext[1].trim()+"/"+artext[2].trim()+"'> "+artext[0]+"/"+artext[1]+"</li>";
        if(k == nuactive){
          date_active = artext;
          $('.ckdateshow input').val(arr[k]).trigger('change');
        }
      }
    }
    $('.ckchose ul').html(textli)
  },

  load_data_datepicker : function ()
  {
    setTimeout(() => {
      $('.ui-datepicker td a').removeClass("ui-state-active")
      if (Object.keys(day_show).length > 0) {
        Object.keys(day_show).forEach(function(key) {
          if (day_show[key]) {
            keynew = key.split("|");
            var _arr = keynew[0].split("-");
            if(keynew[1]==1){
              $('.ui-datepicker td[data-month="'+(_arr[1]-1)+'"][data-year="'+_arr[2]+'"]').children('a[data-date="'+_arr[0]+'"]').addClass('green_color')
            }
            if(keynew[1]==2){
              $('.ui-datepicker td[data-month="'+(_arr[1]-1)+'"][data-year="'+_arr[2]+'"]').children('a[data-date="'+_arr[0]+'"]').addClass('red_color')
            }
            $('.ui-datepicker td[data-month="'+(_arr[1]-1)+'"][data-year="'+_arr[2]+'"]').children('a[data-date="'+_arr[0]+'"]').append("<span class='txtmdy'>"+day_show[key]+"</span>");
          }
        });
      }

      if(date_active.length > 0){
        $('.ui-datepicker td[data-month="'+(parseInt(date_active[1].trim())-1)+'"][data-year="'+date_active[2].trim()+'"]').children('a[data-date="'+date_active[0].trim()+'"]').addClass('ui-state-active');
      }
      $("#ui-datepicker-div").append('<div class="note-calenda"><span class="green_color">* Màu xanh lá: ngày khởi hành giá tốt nhất</span><span class="red_color">* Màu đỏ: ngày khởi hành giá cao điểm</span><span>* Giá hiển thị trên 1 khách</span></div>');
    }, 100);

  },

  loadyearat:function(){
    var $this = this;
    var arraydli = [],
    i = 0;
    if( $('.tbinfos .ifbody .tritem').length > 0)
    {
      $('.tbinfos .ifbody .tritem').each(function(){
        if(!$(this).hasClass('hidden')){
          //var vstexst = $(this).find('.i-date').html().trim().split(",")[1];
          var vstexst = $(this).find('.i-date').html().trim().split(",")[1];
          var arrtexst = vstexst.split("/");
          $(this).attr({
            "data-number": i,
            "data-date": arrtexst[0].trim()+"/"+arrtexst[1].trim()+"/"+arrtexst[2].trim(),
          });
          if( i < 3){
            arraydli.push(vstexst);
          }
          i++;
        }
      })
      $this.loaddatadate(arraydli);

    }
  },

  init:function()
  {
    var $this = this;

    var i = 0;
    if( $('.tbinfos .ifbody .tritem').length > 0){
      $('.tbinfos .ifbody .tritem').each(function(){
       // var vstexst = $(this).find('.i-date').html().trim().split(",")[1];
        var vstexst = $(this).find('.i-date').html().trim().split(",")[1];
        var arrtexst = vstexst.split("/");
        $(this).attr({
          "data-number": i,
          "data-date": arrtexst[0].trim()+"/"+arrtexst[1].trim()+"/"+arrtexst[2].trim(),
        });
        if( i < 3){
          arraydli.push(vstexst);
        }
        i++;
      })
      $this.loaddatadate(arraydli);


    }


    // click  calender
    $(".iddatepicker").datepicker({
      dateFormat: "dd/mm/yy",
      changeMonth: true,
      changeYear: true,
      language: "vi",
      beforeShowDay: function(date) {
        var month = date.getMonth()+1;

        var year = date.getFullYear();
        var day = date.getDate();
        var newdate = day+"-"+month+'-'+year;
        var tooltip_text = "Ngày chọn " + newdate;
        var dis_tooltip_text = "Không có ngày " + newdate;
        var _ishas = false;
        if (Object.keys(day_show).length > 0) {
          Object.keys(day_show).forEach(function(key) {
            var _arr = key.split("|")[0].split("-"),
              mydate = parseInt(_arr[0]) +"-"+parseInt(_arr[1])+'-'+_arr[2];
            if (mydate.trim() == newdate.trim() && !_ishas) {
              _ishas = true;
            }
          });
        }
        if(_ishas){
          return [true, "active", tooltip_text ];
        }else{
          return [true, "disactive", dis_tooltip_text];
        }
        return [true];
      },

      beforeShow: function(input, inst) {
        $('#advRight').removeClass('vactive')
        $this.load_data_datepicker();
        var widget = $(input).datepicker('widget');
        var marginLeft = $(input).outerWidth() - widget.outerWidth();
        if ($(input).offset().left + $(widget).outerWidth() > $(window).width()) {
          marginLeft += ($(input).offset().left + $(widget).outerWidth()) - $(window).width();
        }
        marginLeft = marginLeft > 0 ? 0: marginLeft;
        widget.css('margin-left', marginLeft);
      },
      onChangeMonthYear: function() {
        $this.load_data_datepicker();
      },
      onSelect: function(date) {
        var datenew = date.split("/");
        $('[data-myear="'+datenew[1]+"/"+datenew[2]+'"]').trigger('click');
        $('.tbinfos .tritem[data-date="'+date+'"]').trigger('click');
        $('.ckdateshow input').val(date);
        $('#advRight').removeClass('vactive');
      },
    });


    // khi click danh sach lich khoi hanh
    $("html").on("click", "[data-click]", function(e){
      $(this).addClass('active');
      $(this).siblings().removeClass('active');

      $('#advRight').addClass('vactive');
      $('.vntinffix').addClass('active')
     // var vtext = $(this).find('.i-date').html().trim().split(",");
      var vtext = $(this).find('.i-date').html().trim().split(",");
      date_active = vtext[1].split('/');
      var vnumber = parseInt($(this).attr("data-number"));
      $('.ckdates input').val(vtext[1]).trigger('change');
      arraydli = [];
      if(vnumber == 0){
        arraydli.push($(this).find(".i-date").html().trim().split(",")[1]);
        if($(this).next().find(".i-date").length > 0){
          arraydli.push($(this).next().find(".i-date").html().trim().split(",")[1]);;
        }
        if($(this).next().next().find(".i-date").length > 0){
          arraydli.push($(this).next().next().find(".i-date").html().trim().split(",")[1]);
        }
        nuactive = 0;
      }
      else if(vnumber == (i - 1)){
        arraydli.push($(this).prev().find(".i-date").html().trim().split(",")[1]);
        arraydli.push($(this).find(".i-date").html().trim().split(",")[1]);
        nuactive = 1;
      }
      else{
        arraydli.push($(this).prev().find(".i-date").html().trim().split(",")[1]);
        arraydli.push($(this).find(".i-date").html().trim().split(",")[1]);
        arraydli.push($(this).next().find(".i-date").html().trim().split(",")[1]);
        nuactive = 1;
      }

      console.log(arraydli);
      $this.loaddatadate(arraydli);
    });

    // click tab year
    var datadatetour = $('.vheight').html();
    $("[data-myear]").click(function(){
      $('.vheight').html(datadatetour);
      $(this).parent("li").addClass('active');
      $(this).parent("li").siblings().removeClass('active');
      var vtext =  $(this).attr("data-myear");
      if( $('.tbinfos .ifbody .tritem').length > 0){
        $('.tbinfos .ifbody .tritem').each(function(){
          var result = $(this).attr("data-date").includes(vtext);
          if(!result){
            $(this).remove();
          }
        })
        $this.loadyearat();
        $('.ckdateshow input').val($('.tbinfos .ifbody .tritem:first-child').attr("data-date").trim());
        $('.ckchose li').removeClass('active');
      }
    })

    // khi click tab 3 ngay
    $("html").on("click", ".clchoose li", function(e){
      $("#advRight").removeClass('vactive')
      var that = $(this);
      that.addClass("active")
      that.siblings().removeClass("active");
      date_active = $(this).attr("data-date").trim().split("/");
      $('.ckdateshow input').val($(this).attr("data-date").trim()).trigger('change');
      $('.tbinfos .ifbody .tritem').removeClass('active');
      $('.tbinfos .ifbody .tritem[data-date="'+$(this).attr("data-date").trim()+'"]').addClass('active');

    });



    $('.vbackpop, .box-detail-tour').on("click", function(){
      $(this).parents("#advRight").removeClass("vactive")
    })


    $(".iquan .btn-down").click(function() {

      var obj_qty = $(this).parents(".iquan") ;
      var $value = parseInt(obj_qty.find(".ipt-qua").val());
      let min_val = 0;
      if(obj_qty.hasClass('q-adult')) {
        min_val = 1;
      }
      if (parseInt($value) > min_val) {
        $value = parseInt($value) - 1;
        obj_qty.find(".ipt-qua").val($value).trigger('change');
      }
    });

    $(".iquan .btn-up").click(function() {
      var $value = parseInt($(this).parents(".iquan").find(".ipt-qua").val());
      $value = parseInt($value) + 1;
      $(this).parents(".iquan").find(".ipt-qua").val($value).trigger('change');
    });


    $("html").on("change", "#departure_date", function(e){
      setTimeout(function () {
        $this.load_data_price();
      }, 300);
    });

    $("html").on("change", ".ipt-qua", function(e){
      setTimeout(function () {
        $this.caculate_total_price();
      }, 100);
    });

    $("html").on("click", ".addCart", function(e){
      let obj_form = $(this).closest('.boxCart');
      let id = parseInt($(this).attr('data-id'));
      $this.do_AddCart(id,obj_form);
    });


    $("html").on("click", ".btn-booking", function(e){

      let id = parseInt($(this).attr('data-id'));
      let price_id = parseInt($(this).attr('data-price'));
      $this.do_Booking(id,price_id);
    });



  }
};

var vnTDetail = function () {

  return {
    loadTemplate: function (items,slide) {


      var html='';
      var item_class = 'item';
      if(slide == 1) {
        item_class = 'item';
      }
      items.forEach(function(item)
      {

        let text_price = 'Liên hệ';
        if(item.price) {
          text_price = vnTRUST.numberFormat(item.price)+' đ' ;
        }

        let text_price_old = '';
        if(item.price_old) {
          text_price_old = '<div class="i-price-line">'+vnTRUST.numberFormat(item.price_old)+' đ</div>';
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

      if(slide == 1){
        html_out = '<div class="slide-tour">';
        html_out += html ;
        html_out += '</div>';
      }else{
        html_out = '<div class="list-tour">';
        html_out += html ;
        html_out += '</div>';
      }



      return html_out ;
    },

    loadItem: function (ptype,params,slide)
    {
      var $this = this;

      var obj_show = $("#vnt-load_"+ptype);

      $.ajax({
        async: true,
        dataType: 'json',
        url: ROOT + "api/tour/items?ptype="+ptype ,
        type: 'GET',
        data: params,
        headers: {
          "Authorization": "Bearer "+API_KEY,
          "csrf-token": $("#vnt_csrf_token").val()
        },
        beforeSend: function () {

        },
        success: function (rs) {
          if(rs.success)
          {
            if(rs.data.num)
            {
              var html = $this.loadTemplate(rs.data.items,slide) ;
              obj_show.find('.ext_item').html(html);
              if(slide == 1)
              {
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

            }else{
              obj_show.remove();
            }
          }else{
            obj_show.remove();
          }

        }
      });
      return false;

    },

    init:function()
    {

      $('.itschedule').each(function(){
        var that = $(this);
        if(that.hasClass("active")){
          that.find(".scconts").show();
        }
        else{
          that.find(".scconts").hide();
        }
      })
      $(".itschedule .sctitle").click(function(){
        if(!$(this).parents(".itschedule").hasClass("active")){
          $(this).parents(".itschedule").addClass("active");
          $(this).parents(".itschedule").find(".scconts").stop().slideDown();
        }
        else{
          $(this).parents(".itschedule").removeClass("active");
          $(this).parents(".itschedule").find(".scconts").stop().slideUp();
        }
      });
      //
      $(".clviewsmore").click(function(){
        if(!$(this).parents(".tview").hasClass("active")){
          $(this).parents(".tview").addClass("active");
          $(this).parents(".vvtitle").siblings(".vvcont").find(".itschedule").addClass("active");
          $(this).parents(".vvtitle").siblings(".vvcont").find(".itschedule .scconts").stop().slideDown();
        }
        else{
          $(this).parents(".tview").removeClass("active");
          $(this).parents(".vvtitle").siblings(".vvcont").find(".itschedule").removeClass("active");
          $(this).parents(".vvtitle").siblings(".vvcont").find(".itschedule .scconts").stop().slideUp();
          $(this).parents(".vvtitle").siblings(".vvcont").find('.itschedule:first').addClass("active")
          $(this).parents(".vvtitle").siblings(".vvcont").find('.itschedule:first .scconts').stop().slideDown();
        }
      });
      //
      $('.itthenotes').each(function(){
        var that = $(this);
        if(that.hasClass("active")){
          that.find(".mcconts").show();
        }
        else{
          that.find(".mcconts").hide();
        }
        $(".itthenotes .mctitle").click(function(){
          if(!$(this).parents(".itthenotes").hasClass("active")){
            $(this).parents(".itthenotes").addClass("active");
            $(this).parents(".itthenotes").find(".mcconts").stop().slideDown();
          }
          else{
            $(this).parents(".itthenotes").removeClass("active");
            $(this).parents(".itthenotes").find(".mcconts").stop().slideUp();
          }
        });
      })

      vnTRUST.vnTAdvFixed('advRight');
    }

  };
}();

/* Init */
$(document).ready(function(){
  vnTDetail.init();
});
