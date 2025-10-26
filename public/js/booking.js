vnTBooking = {

	ShowWaiting: function () {
		$("#fBooking").addClass('vnt-loading');
	},

	HideWaiting: function () {
		$("#fBooking").removeClass('vnt-loading');
	},


	updateCart: function ()
	{

		var ok_submit = 1;
		var msg_err ='';

		var sid = $("#sid").val() ;
		var obj_qty = $(".adult_quantity") ;
		var quantity = parseInt(obj_qty.val());
		var child_quantity = parseInt($(".child_quantity").val());
		var baby_quantity = parseInt($(".baby_quantity").val());

		if(quantity==0){
			ok_submit = 0 ;
			msg_err = 'Vui lòng chọn số người lớn' ;
		}

		if(ok_submit)
		{

			var params =  'sid='+sid +'&quantity=' +quantity +'&child_quantity='+child_quantity +'&baby_quantity='+baby_quantity+'&lang='+lang;
			$.ajax({
				async: true,
				dataType: 'json',
				url: API_URL + "/booking/update_cart",
				type: 'POST',
				data: params,
				headers: {
					"Authorization": "Bearer "+API_KEY,
					"csrf-token": $("#vnt_csrf_token").val()
				},
				beforeSend: function () {

					$(".boxCart").addClass('vnt-loading');
				},
				success: function (rs) {
					$(".boxCart").removeClass('vnt-loading');
					if(rs.success)
					{
						let total_price = parseInt(rs.data.total_price);

						let text_total_price = 'Liên hệ';
						if(total_price>0){
							text_total_price =  vnTRUST.numberFormat(total_price) + ' đ';
						}

						$(".total-price").html(text_total_price) ;
						$(".ext_numcart").html(rs.data.quantity) ;
					}else{
						Swal.fire({icon: 'error', title: js_lang['error'], html: rs.error_msg});
					}
				}
			});
		}else{
			Swal.fire({ icon: 'error',title: js_lang['error'],html:msg_err});
			var quantity_prev = obj_qty.data('val');
			obj_qty.val(quantity_prev);
		}

		return false;


	},

	do_Process: function (obj_form) {

		var ok_submit = 1;
		var msg_err ='';

		if(ok_submit)
		{

			var params =  obj_form.serialize();
			params+= '&lang='+lang;

			$.ajax({
				async: true,
				dataType: 'json',
				url: API_URL + "/booking/process",
				type: 'POST',
				data: params,
				headers: {
					"Authorization": "Bearer "+API_KEY,
					"csrf-token": $("#vnt_csrf_token").val()
				},
				beforeSend: function () {
					$('body').addClass('loading');
					obj_form.find(".btn-loading").removeClass("hidden");
					obj_form.find(".btn-submit").prop("disabled", true);
					obj_form.find(".btn-submit span").html('Đang xử lý ...');

				},
				success: function (rs) {
					$('body').removeClass('loading');
					obj_form.find(".btn-loading").addClass("hidden");
					obj_form.find(".btn-submit").prop("disabled", false);
					obj_form.find(".btn-submit span").html(js_lang['btn_booking']);


					if(rs.success) {
						let token_code = rs.data.token_code;
						location.href =  ROOT_MOD+"/booking/process.html/?vnt_code=" + token_code ;
					}else{
						Swal.fire({icon: 'error', title: js_lang['error'], html: rs.error_msg});
					}
				}
			});
		}else{
			Swal.fire({ icon: 'error',title: js_lang['error'],html:msg_err});
		}

		return false;
	},

	loadJsTemplate: function (item) {
		let html = `<div class="ifrow">
                  <div class="ifitem">
                    <div class="vvgrip">
                    <div class="mcol">
                      <div class="vroom">${item.title}</div>
                    </div>
                    <div class="hcol">
                      <div class="vtime">`+js_lang['booking_duration']+` : ${row.duration} `+js_lang['hours']+`</div>
                    </div>
                    <div class="mcol">
                      <div class="vmony">${row.text_price}</div>
                    </div>
                    </div>
                  </div>
                </div>`;

		return html ;
	},

	ShowCart: function (data)
	{

		//load data
		var boxCart = $("#boxCart");
		var objShow = $("#ext_item").html('');



		//console.log(data);
		var num_item = parseInt(data.num_item) ;
		if(num_item>0)
		{

			var items = data.items;
			var total_price =0;
			items.forEach(function(item)
			{

				total_price += parseInt(item.price);

				let text_price = js_lang['no_price'] ;
				if(parseInt(item.price) > 0){
					text_price = vnTRUST.format_number(item.price)+' USD' ;
				}

				item.text_price = text_price ;
				item.text_item_price = vnTRUST.format_number(item.item_price)+' USD' ;
				let html = vnTBooking .loadJsTemplate(item) ;

				objShow.append(html);

			});

			let text_total_price = vnTRUST.format_number(total_price)+' USD' ;

			boxCart.find('.total_price').html(text_total_price);

			$(".ext_numcart").html(num_item) ;
		}

	},



	initCart: function () {


		var data_cart = {};
		var local_cart = localStorage.getItem("vnt_cart");
		var isload = false;
		if (!vnTRUST.isJson(local_cart)) {
			isload = true;
		}
		isload = true;
		if (isload || !local_cart || mem_id>0)
		{
			var params = 'sid='+sid+'&lang='+lang;
			$.ajax({
				async: true,
				dataType: 'json',
				url: API_URL + "/info_cart",
				type: 'GET',
				data: params,
				headers: {
					"Authorization": "Bearer "+API_KEY,
					"csrf-token": $("#vnt_csrf_token").val()
				},
				success: function (rs) {
					data_cart =  rs.data ;
					localStorage.setItem("vnt_cart", JSON.stringify(data_cart));
					vnTBooking.ShowCart(data_cart);
				}
			});

		}else{
			data_cart = JSON.parse(localStorage.getItem("vnt_cart"));
			vnTBooking.ShowCart(data_cart);
		}

	},


	init:function()
	{
		$this = this;

		$("html").on("change", ".ipt-qua", function(e){
			setTimeout(function () {
				$this.updateCart();
			}, 100);
		});


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


	}
};