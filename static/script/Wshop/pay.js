
require([ 'config' ], function(config) {

	require([ 'util', 'jquery', 'Spinner', 'Cart' ], function(util, $, Spinner,
			Cart) {
		
		$(document).ready(function(){
			$(".empty-btn").click(function(){
				$(".input-price input").val("");
				$(".actual-price input").val("");
				})
		});
		
		$("#submit").click(function(){
			
			var price = $('#number').val();
		
			if(price == ''){
				alert('请填入你要支付的金额');
				return;
			}
			var url = shoproot + "?/CashPay/ajaxCreatePay";
	
			$.post(url, {
				amount : price

			}, orderGenhandleReq);
			
			
		});
		
		var orderId = '';
		function orderGenhandleReq(id) {
			if(id.ret_code < 0){
			  
				alert('失败');
            }
			orderId = parseInt(id.ret_msg);
			
			if (orderId > 0) {
				$.post(shoproot + "?/CashPay/ajaxPay/", {
					orderId : orderId
				}, function(bizPackage) {
					isPaying = false;
				
						var state = bizPackage.ret_code;
						var msg = bizPackage.ret_msg;
					

						if(state == -1)
					    {
				             alert('支付失败');
					   } else {
								// alert(JSON.stringify(bizPackage));
								// 订单映射
								msg.success = wepayCallback;
							
								// 发起微信支付
								wx.chooseWXPay(msg);

							}

				});

			} else {
			  
				alert('订单无效');
			}
		}
		
		function wepayCallback(res) {
			window.location.href = '?/CashPay/deal_view/id='+orderId;

		}

		function wepayCancelCallback(res) {

			window.location.href = 'http://www.icheerslife.com'
					

		}
		$("input").keyup(function(){
			   //code
			   var discountMode = $('#discount').val();
			   var price = $('#number').val();
		       var discountPrice = price*discountMode;
		      $('#discount_value').val(discountPrice.toFixed(2));
	    });  
	});

});

