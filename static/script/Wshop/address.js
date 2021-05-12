/**
 * Desc
 *
 * @description Holp You Do Good But Not Evil
 * @copyright Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author Chenyong Cai <ycchen@iwshop.cn>
 * @package Wshop
 * @link http://www.iwshop.cn
 */

var priceHashId = 0;

require(['config'], function(config) {

	require(['util', 'jquery', 'Spinner', 'Cart', 'Slider', 'Tiping'],
		function(util, $, Spinner, Cart, Slider, Tiping) {

			$('#select_back').click(function() {

				
				var couponId = $('#couponId').val();
			    var time = $('#time').val();
			    var isbalance =$('#isbalance').val();
                var url = shoproot + "?/Cart/index_order/couponId="+couponId+"&time="+time+"&isbalance="+isbalance;
                window.location.href = url;

			});
			
			$('#back').click(function() {

				
				var couponId = $('#couponId').val();
			    var time = $('#time').val();
			    var isbalance =$('#isbalance').val();
                var url = shoproot + "?/Cart/index_order/couponId="+couponId+"&time="+time+"&isbalance="+isbalance;
                window.location.href = url;

			});
			
			$('#submit').click(
				function() {

					var name = $('#name').val();
					var area = $('#txt_area').val();
					var phone = $('#phone').val();
					var address = $('#address').val();
					var city = '';
					var reg = /^1[3|4|5|7|8][0-9]\d{8}$/;
					if (name == '' || area == '' || phone == '' || address == '') {
						layer.open({
                 content: '请填写完整信息！',
                 time: 3 //3秒后自动关闭
            });
						return;
					}
					if (!reg.test(phone)) {
						layer.open({
                 content: '请填写正确的手机号！',
                 time: 3 //3秒后自动关闭
            });
						return;
					}
                    
					var url = shoproot + "?/UserAddress/add_address";
					$.post(url, {
						user_name: name,
						phone: phone,
						city: city,
						area: area,
						address: address

					}, addAddressResult);

				});

			function addAddressResult(data) {

				if (data.ret_code < 0) {

					layer.open({
                 content: data.ret_msg,
                 time: 3 //3秒后自动关闭
                 });
					return;
				}
				var couponId = $('#couponId').val();
			    var time = $('#time').val();
			    var isbalance =$('#isbalance').val();
				var url = shoproot + "?/UserAddress/list_address/couponId="+couponId+"&time="+time+"&isbalance="+isbalance;
				window.location.href = url;
			}

			//增加地址
			$('#add').click(function() {
				var couponId = $('#couponId').val();
			    var time = $('#time').val();
			    var isbalance =$('#isbalance').val();
				var url = shoproot + "?/UserAddress/edit_address/couponId="+couponId+"&time="+time+"&isbalance="+isbalance;
				window.location.href = url;
			});
			
			//删除地址
			function delete_address() {
				$(".delete-btn").click(function() {
					var value = $(this).parents('li').find(
						'.address-detail').attr('data-id');
					layer.open({
    content: '确认删除地址？',
    btn: ['确认', '取消'],
    shadeClose: false,
    yes: function(){
                            $.post('?/UserAddress/remove_address',{

                            id:value
                            },function(data){
                                if(data.ret_code > 0){
                                    $(this).parents("li").remove();

                                }

                                var couponId = $('#couponId').val();
                			    var time = $('#time').val();
                			    var isbalance =$('#isbalance').val();
                				var url = shoproot + "?/UserAddress/list_address/couponId="+couponId+"&time="+time+"&isbalance="+isbalance;
                                window.location.href = url;


                            });

    }
});

                    })
                }

			function modify_address() {
				$(".modify-add").click(
					function() {
						if ($(this).text() == "编辑") {
							$(".select-btn").hide();
							$(".delete-btn").show();
							$(this).html("完成")

						} else {
							$(".select-btn").show();
							$(".delete-btn").hide();
							$(this).html("编辑")
							var address_list = $(".address-list ul li").find(".select-btn");
							if (address_list.hasClass('active')) {} else {
								$(".address-list ul li:first").find(
									".select-btn").addClass('active');
							}
						}
					})
			}

			function select_address() {
				$(".address-list ul li").click(
					function() {

						var value = $(this).find(
							'.address-detail').attr('data-id');
						var url = shoproot + "?/UserAddress/ajaxEditAddress";

						$.post(url, {
							id: value

						}, function(data) {

						});
						
						var couponId = $('#couponId').val();
					    var time = $('#time').val();
					    var isbalance =$('#isbalance').val();
						
						if ($(this).find('.select-btn').hasClass('active')) {} else {
							$(".address-list ul li .select-btn")
								.removeClass('active');
							$(this).find('.select-btn').addClass('active');
						}
						if($(".delete-btn").is(":hidden")) {
                                    var url = shoproot + "?/Cart/index_order/couponId="+couponId+"&time="+time+"&isbalance="+isbalance;
                                window.location.href = url;
						}
					})
			}
			$(document).ready(function() {
				select_address();
				delete_address();
				modify_address();
			})

		});
});