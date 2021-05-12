
require([ 'config' ], function(config) {

	require([ 'util', 'jquery', 'Spinner', 'Cart' ], function(util, $, Spinner,
			Cart) {
		var isbalance= 1;
		var b = $('#isbalance').val();
		if(b != ''){
			isbalance = b;
			
		}
		$('#back').click(function() {

			var url = "?/Index/index";
			window.location.href = url;
		});
		
		$('#discount_money').click(function(){
			
            var deliver_time = $('#time').val();
            var couponId = $('#userCouponId').val(); 
            var url = "?/Coupon/coupon_list/couponId="+couponId+"&time="+deliver_time+"&isbalance="+isbalance;
            window.location.href = url;
		});

		var orderId;
		var isPaying = false; 
		$('#update-address').click(function() {
			var addressId = $('#addressId').val();
		    var deliver_time = $('#time').val();
            var couponId = $('#userCouponId').val(); 
			var url = shoproot + "?/UserAddress/list_address/couponId="+couponId+"&time="+deliver_time+"&isbalance="+isbalance;

			if(addressId == ''){
				
				url =  shoproot + "?/UserAddress/edit_address/couponId="+couponId+"&time="+deliver_time+"&isbalance="+isbalance;
			}
			window.location.href = url;

		});

		function payOrder() {
			if(isPaying){
				return;
			}
			if(Cart.count() == 0)
				return;
			var addressId = $('#addressId').val();

			if (addressId == '') {
				 layer.open({
                 content: '请选择要派送的地址！',
                 time: 3 //3秒后自动关闭
            });
				return;
			}
            
            // check deliver time
            var deliver_time = $('#time').val();
            if (deliver_time == '请点此设置送达时间') {
				layer.open({
                 content: '请设置送达时间！',
                 time: 3 //3秒后自动关闭
            });
                return;
            } else {
                var currentDate = new Date();
                var year = currentDate.getFullYear();
                var res = deliver_time.match(/[1-9]\d{0,1}/g);
                var time_string = res[2]+':00-'+res[3]+':00';
                // need to check if target date which combined with year is not correct
                // say: today is 2015-12-31, order is "1-1 15:00-16:00"
                // combine time_string with year from currentDate is 2015-1-1 15:00-16:00, which is wrong
                if (res[0]<10) res[0] = '0'+res[0];
                if (res[1]<10) res[1] = '0'+res[1];
                checkDateStr = year+'/'+res[0]+'/'+res[1]+' '+res[3]+':00';
                checkDate = new Date(checkDateStr);
                if (checkDate > currentDate)
                    full_time= year+'-'+res[0]+'-'+res[1]+' '+time_string;
                else
                    full_time= (year+1)+'-'+res[0]+'-'+res[1]+' '+time_string;
            }

			var reciTex = '';
			var rcHead = '';
            var time = full_time;//$('#time').val();
			var couponId = $('#orderCouponId').val()+","+$('#userCouponId').val();
			
			var url = shoproot + "?/Order/createOrder";
			isPaying = true;
			$.post(url, {
				cartData : window.localStorage.getItem('cart'),
				addrData : addressId,
				reciHead : rcHead,
				reciTex : reciTex,
				time : time,
				coupon : couponId,
				isbalance:	isbalance

			}, orderGenhandleReq);
		}

		function orderGenhandleReq(id) {
			if(id.ret_code < 0){
				layer.open({
                 content:id.ret_msg,
                 time: 3 //3秒后自动关闭
            });
				return;
			}
			
			
			// alert(JSON.stringify(id));
			orderId = parseInt(id.ret_msg);
			Cart.clear();
			
			if (orderId > 0) {
				$.post(shoproot + "?/Order/ajaxOrderPay/", {
					orderId : orderId
				}, function(bizPackage) {
					isPaying = false;
				
						var state = bizPackage.ret_code;
						var msg = bizPackage.ret_msg;
					

							if (state == 1){
		// 全额支付
						        history.replaceState(null, "CheersLife 下午茶", "?/Index/index");
								location.href = '?/Uc/orderlist/';

							}
							else if(state == -1)
							{
				            layer.open({
                              content:msg,
                             time: 3 //3秒后自动关闭
				              });
							} else {
								// alert(JSON.stringify(bizPackage));
								// 订单映射
								msg.success = wepayCallback;
								msg.cancel = wepayCancelCallback;
								// 发起微信支付
								wx.chooseWXPay(msg);

							}

				});

			} else {
				layer.open({
                 content:'订单无效或者已过期',
                 time: 3 //3秒后自动关闭
				 });
				isPaying = false;
			}
		}
		/**
		 * 微信支付回调
		 * 
		 * @param {type}
		 *            res
		 * @returns {undefined}
		 */
		function wepayCallback(res) {
	        history.replaceState(null, "CheersLife 下午茶", "?/Index/index");
			window.payed = true;
			window.location.href = shoproot
					+ '?/Order/expressDetail/?order_id=' + orderId;
			$('#wechat-payment-btn').removeClass('disable').html('微信安全支付');
		}

		function wepayCancelCallback(res) {
	        history.replaceState(null, "CheersLife 下午茶", "?/Index/index");
			window.location.href = shoproot
					+ '?/Order/expressDetail/?order_id=' + orderId;

		}

		$('#pay').click(payOrder);
	
		function balance_change() {
			$(".yes-no").click(
					function() {
						var yes = $(".yes-no img").attr("src").indexOf("yes");
						if (yes > 0) {
							var replace_yes = $(".yes-no img").attr("src")
									.replace('yes', 'no');
							$(".yes-no img").attr("src", replace_yes);
							isbalance = 0;
						} else if (yes <= 0) {
							var replace_no = $(".yes-no img").attr("src")
									.replace('no', 'yes');
							$(".yes-no img").attr("src", replace_no);
							
							isbalance = 1;
						}
					})
		}
		
		function order_title_wd(){
			var web_win_wd=$(window).width();
			order_name_wd=web_win_wd-103;
			$(".pro-name").css({'max-width':order_name_wd});
			}
         function express_title_wd(){
			var web_win_wd=$(window).width();
			order_name_wd=web_win_wd-103;
			$(".order-pro-list p .title-pro").css({'max-width':order_name_wd});
			}

            var time_data;
		$(document).ready(function() {

			balance_change();
			order_title_wd();
			express_title_wd();
    
    FastClick.attach(document.body);
		var selectArea = new MobileSelectArea();
         selectArea.init({trigger:'#time',value:$('#hd_time').val(),data:'deliverTime.php',level:2});
		});


	});

});

