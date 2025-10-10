vnTMain = {


  loadPlaceTemplate: function (data) {
    let html = `<div class="item effect_up">
                <div class="item-place">
                    <div class="i-img">
                        <a href="${data.link}" style="background-image: url(${data.src});"></a>
                    </div>
                    <div class="i-text">
                        <div class="t-title">${data.title}</div>
                    </div>
                </div>
            </div>`;

    return html ;
  },


  loadFocusTemplate: function (data) {


    let text_price = 'Liên hệ' ;
    let text_price_old  ='';
    if(parseInt(data.price) > 0){
      text_price = vnTRUST.numberFormat(data.price)+' đ' ;
    }
    if(parseInt(data.price_old) > 0){
      text_price_old = '<div class="i-price-line">'+vnTRUST.numberFormat(data.price_old)+' đ</div>' ;
    }

    let html = `<div class="colt" >
        <div class="item-tour">
            <div class="i-img">
              <a href="${data.link}"><img  src="${data.src}" alt="${data.title}" /><span class="i-address">${data.last_destination}</span></a>            
               ${data.text_status}
            </div>
            <div class="i-desc">
                <div class="i-title">
                   <a href="${data.link}">${data.title}</a>
                </div>
                 <div class="price-star">
                    <div class="div-price">
                        <div class="i-price">${text_price}</div>
                        ${text_price_old} 
                    </div>
                    <div class="div-star">${data.img_star}</div>
                </div>
            </div>
        </div>
    </div>`;

    return html ;
  },



  loadTemplate: function (data) {


    let text_price = 'Liên hệ' ;
    let text_price_old  ='';
    if(parseInt(data.price) > 0){
      text_price = vnTRUST.numberFormat(data.price)+' đ' ;
    }
    if(parseInt(data.price_old) > 0){
      text_price_old = '<div class="i-price-line">'+vnTRUST.numberFormat(data.price_old)+' đ</div>' ;
    }


    let html = `<div class="colt" ${data.page} data-id="${data.id}" >
        <div class="item-tour">
            <div class="i-img">
                <a href="${data.link}" title="${data.title}">
                    <img src="${data.src}" alt="${data.title}" />
                    <span class="i-address">${data.last_destination}</span>
                </a>
                ${data.text_status}
            </div>
            <div class="i-desc">
                <div class="i-title">
                    <a href="${data.link}">${data.title}</a>
                </div>
                <div class="mb5 text-overflow">
                    <div class="price-star">
                        <div class="div-price">
                            <div class="i-price">${text_price}</div>
                            ${text_price_old}
                        </div>
                        <div class="div-star">${data.img_star}</div>
                    </div>


                </div>
                <div class="i-content">
                    <div class="text-overflow"><strong>${js_lang['departure']}:</strong> ${data.text_departure}</div>
                    <div class="text-overflow"><strong>${js_lang['destination']}:</strong> ${data.text_destination}</div>
                    <div class="text-overflow"><strong>${js_lang['duration']}:</strong> ${data.text_duration}</div>
                    <div class="text-overflow"><strong>${js_lang['department']}:</strong> ${data.date_department}</div>
                    <div class="i-right">
                        <div class="i-icon">
                            <ul>
                               ${data.list_transport}
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>`;

    return html ;

  },


  loadMoreHome:function(objBox)
  {

    var objHtml = objBox.find('.html_content') ;
    var objNav = objBox.find('.html_nav') ;

    let num_page = parseInt(objNav.attr("data-num_page")) ;
    let page_cur = parseInt(objNav.attr("data-page")) ;
    let page_next = page_cur+1;

    objHtml.find('.colt[data-page="'+page_next+'"]').show();
    if(page_next==3 || page_next==num_page){
      objNav.hide();
    }else{
      objNav.attr("data-page", page_next );
    }


  },

  buildItemHome:function(objBox,items)
  {

    var objHtml = objBox.find('.html_content') ;
    var objNav = objBox.find('.html_nav') ;

    let n =  parseInt(objNav.attr("data-page_size")) ;
    let num_item = items.length;

    objBox.find(".loading-grid").hide();

    let page = 1;
    let i = 0;
    let html = '';

    items.forEach(function(item)
    {

      if(i==n) {
        page++;
        i=0;
      }
      let info_page =  'data-page="'+page+'"';
      if(page>1) {
        info_page += ' style="display:none"'
      }

      item.page = info_page;
      html +=  vnTMain.loadTemplate(item);

      i++;
    });
    objHtml.html(html);

    if(num_item > n) {
      objNav.show();
      objNav.attr("data-num_page", page );
    }else{
      objNav.hide();
    }
  },


  loadItemHome: function ()
  {


    var mydata = 'limit=24&lang='+lang;
    $.ajax({
      async: true,
      dataType: 'json',
      url: API_URL + "/tour/home",
      type: 'GET',
      data: mydata,
      headers: {
        "Authorization": "Bearer "+API_KEY,
        "csrf-token": $("#vnt_csrf_token").val()
      },
      beforeSend: function () {

      },
      success: function (rs) {
        if(rs.success)
        {

          var home_items = rs.data.items ;
          var num = Object.keys(home_items).length;
          //console.log(items);

          if(num>0)
          {
            $.each(home_items, function( key, items )
            {
             // console.log('key = '+key+':');
             // console.log(items);

              let objBox = $("#box_home-"+key);
              vnTMain.buildItemHome(objBox,items);

            });
          }

        }

      }
    });


    return false;
  },




  loadTour:function(objBox)
  {

    var objHtml = objBox.find('.html_content') ;
    var objNav = objBox.find('.html_nav') ;


    var pType = objBox.attr('data-type');
    var pTypeId = objBox.attr('data-id');
    var page = parseInt(objNav.attr("page"));
    var page_size = parseInt(objNav.attr("page_size"));
    var filterParam = objNav.attr("filterParam");

    var mydata = 'page='+page +'&page_size='+page_size;
    if(filterParam) mydata += "&"+filterParam ;

    var request = $.ajax({
      url: ROOT+'api/tour/lists?pType='+pType+'&pTypeId='+pTypeId,
      async: true,
      dataType: 'json' ,
      type: 'GET',
      data: mydata,
      headers: {
        "Authorization": "Bearer "+API_KEY,
        "csrf-token": $("#vnt_csrf_token").val()
      },
      beforeSend: function () {
        objBox.find(".loading-grid").show();
      }
    });
    $.when( request ).then( function(rs){
      setTimeout(function()
      {
        objBox.find(".loading-grid").hide();

        if(rs.success)
        {


          var items = rs.data.items;
          var paging = rs.data.paging;
          var num_item =  parseInt(items.length) ;
          if(num_item>0)
          {
            for (var i = 0; i < num_item ; i++) {

              if(pType=='focus'){
                var html = vnTMain.loadFocusTemplate(items[i]) ;
              }else{
                var html =  vnTMain.loadTemplate(items[i]) ;
              }
              objHtml.append(html);
            }

            let pageNext = page+1;
            objNav.attr("page", pageNext );
            if(pageNext <= paging.num_pages) {
              objNav.show();
            }else{
              objNav.hide();
            }
          }else{
            objBox.remove();
          }

        }
      },100)
    });



  },

  loadPlaceFocus:function()
  {

    var objBox = $("#place-focus");

    var objHtml = objBox.find('.html_content') ;
    var objNav = objBox.find('.html_nav') ;

    var page = parseInt(objNav.attr("page"));
    var page_size = parseInt(objNav.attr("page_size"));
    var mydata = 'page='+page +'&page_size='+page_size;

    var request = $.ajax({
      url: ROOT+'api/tour/place?pType=focus&pTypeId=0' ,
      async: true,
      dataType: 'json' ,
      type: 'GET',
      data : mydata,
      headers: {
        "Authorization": "Bearer "+API_KEY,
        "csrf-token": $("#vnt_csrf_token").val()
      },
      beforeSend : function(){
        objBox.find(".loading-grid").show();
      }
    });
    $.when( request ).then( function(rs){
      setTimeout(function(){

        objBox.find(".loading-grid").hide();
        if(rs.success)
        {
          var items = rs.data.items;
          var paging = rs.data.paging;

          for (var i = 0; i < items.length; i++) {
            var html = vnTMain.loadPlaceTemplate(items[i]) ;
            objHtml.append(html);
          }

          let  pageNext = page+1 ;
          objNav.attr("page",pageNext);
          if(pageNext <= paging.num_pages) {
            objNav.show();
          }else{
            objNav.hide();
          }

        }
      },300)
    });



  },



  init:function()
  {


    $(".box-tour").each(function(){
      vnTMain.loadTour($(this)) ;
    });
    $(".box-tour .loadmore").click(function (e)
    {
      var objBox = $(this).closest('.box-tour');
      vnTMain.loadTour(objBox) ;
      return false;
    });


    vnTMain.loadItemHome();
    $(".box-tour-home .loadmore").click(function (e)    {
      var objBox = $(this).closest('.box-tour-home');
      vnTMain.loadMoreHome(objBox) ;
      return false;
    });

    $("#place-focus .load-more").click(function (e)
    {
      vnTMain.loadPlaceFocus() ;
      return false;
    });




  }

};

$(document).ready(function(){

  vnTMain.init();
});