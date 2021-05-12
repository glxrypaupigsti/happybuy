/* global wx, shoproot, parseFloat */

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

require(['config'], function(config) {

	require(['util', 'jquery', 'Spinner', 'Cart', 'Tiping', 'touchSlider'], function(util, $, Spinner, Cart, Tiping, touchSlider) {

		Cart.init();

		$('#buy').click(function() {

			if (Cart.count() == 0) {

				layer.open({
                 content: '购物车空空的！',
                 time: 3 //3秒后自动关闭
            });
				return;
			}
			$.post(shoproot + "?/Cart/add_product_to_cart", {

				cartData: window.localStorage.getItem('cart'),

			}, function result(data) {

				window.location.href = shoproot + "?/Cart/index_order";

			});

		})

		function doCallback(data) {

			if (data != "") {
				// alert(JSON.stringify(data));
				// val total = data.total;
				// alert(data.total);
				$(".cart-price ").html("&yen;" + data.total.toFixed(2));
				if (data.topCats != "") {

				}

			}
			$(".number").html(Cart.count());
		}

		function empty_cart() {
			$(".header-right").click(function() {
				layer.open({
    content: '确认清空购物车？',
    btn: ['确认', '取消'],
    shadeClose: false,
    yes: function(){
					$.post(shoproot + '?/Cart/ajaxDelCart/', function(Res) {

						//$(".cart-list ul").remove();
						Cart.clear();
						$(".number").html(Cart.count());
						window.location.reload();
						layer.closeAll();
					});
				}
				});
			});
		}

		function cart_list_show() {
			$(".cart-list img").click(function() {
				$(".cart-list").hide();
			});
			$(".shopping-cart").click(function() {
				var count = Cart.count();
				if ($(".cart-list").hasClass('active')) {
					$(".cart-list").hide();
					$(".cart-list").removeClass('active');
					fnLoadCatlist(cat);

				} else if (count > 0) {
					$(".cart-list").show();
					$(".cart-list").addClass('active');
					$.post(shoproot + '?/Index/ajaxGetCartProducts/', clearResult);
				}
			})
		}

		function clearResult(data) {

			$('#cart_list').html(data);
			cart_proname_wd();
			cart_plus_click();
			cart_minus_click();

		}

		// $(".number").html(Cart.count());

		function cart_proname_wd() {
			var win_wd = $(window).width();
			var win_hg = $(window).height();
			$(".title-pro").css({
				'max-width': win_wd - 175
			});
			$(".cart-list ul").css({
				'max-height': win_hg / 2
			});
		}

		function cart_plus_click() {

			$('.prolist #cart_plus').click(function() {
				var hash = $(this).parents('li').find(".pro-buy-num").attr('data-hash');
				var p = $(this).parents('li').find(".pro-buy-num").attr('data-p');

				var numNode = $(this).parents('li').find('.num');

				
				var instock = $(".commodity-body").attr('data-instock');
				if(parseInt(numNode.text()) + 1 > parseInt(instock)){
                    layer.open({
                 content:'无法选择更多',
                 time: 3 //3秒后自动关闭
            });
					return;
				}
				Cart.set(hash, parseInt(numNode.text()) + 1, doCallback);

				if (p == $('#iproductId').val()) {

					var localNode = $('.minus-hidden').find('.num');

					localNode.html(parseInt(localNode.text()) + 1);
					//alert(num);

				}
				$(this).parents(".buy-num").find(".hidden").show();

				numNode.html(parseInt(numNode.text()) + 1);

			});
		}

		function cart_minus_click() {

			$('.prolist #cart_minus').click(function() {
				var count = Cart.count();
				var hash = $(this).parents('li').find(".pro-buy-num").attr('data-hash');
				var numNode = $(this).parents('li').find('.num');
				var p = $(this).parents('li').find(".pro-buy-num").attr('data-p');
				var sp = $(this).parents('li').find(".pro-buy-num").attr('data-sp');

				var num = parseInt(numNode.text()) - 1;
				if (num < 1) {
					num = 0;
					Cart.del(p, sp);
					$(".minus-hidden").hide();
					var localNode = $('.minus-hidden').find('.num');

					localNode.html(0);
					$(this).parents('li').remove();
					$.post('?/Index/ajaxRemoveProduct/', {
						product_id: p,
						spec_id: sp
					}, function(data) {


						//clearResult(data);

						//alert(data);
					});
					Cart.sync(doCallback);

				} else {
					Cart.set(hash, parseInt(numNode.text()) - 1, doCallback);

					if (p == $('#iproductId').val()) {

						var localNode = $('.minus-hidden').find('.num');

						localNode.html(parseInt(localNode.text()) - 1);
						$(".minus-hidden").show();
						//alert(num);

					}

				}
				numNode.html(num);
				if (count < 2) {
					$(".cart-list").hide();
				}
			});
		}

		function detail_plus() {

			$(".icon-plus").click(function() {

				var hash = $(".commodity-body").attr('data-hash');
				$(".minus-hidden").show();

				var num = parseInt($(".minus-hidden .num").text()) + 1;
				
				var instock = $(".commodity-body").attr('data-instock');
				if(num > parseInt(instock)){
                    layer.open({
                 content:'无法选择更多',
                 time: 3 //3秒后自动关闭
            });
					return;
				}

				$(".minus-hidden .num").html(num);
				$(".number").html(num);
				Cart.set(hash, num, doCallback);

			});
			$(".icon-minus").click(function() {
				var num_minus = $(".minus-hidden .num").text();
				var hash = $(".commodity-body").attr('data-hash');
				var p = $(".commodity-body").attr('data-p');
				var sp = $(".commodity-body").attr('data-sp');

				var num = parseInt(num_minus) - 1;
				if (num < 1) {
					num = 0;
					Cart.del(p, sp);
					Cart.sync(doCallback);
					$(".minus-hidden").hide();
				} else {
					Cart.set(hash, num, doCallback);

				}
				$(".minus-hidden .num").html(num);

			});
		}


		$(document).ready(function() {
			detail_plus();
			cart_list_show();
			empty_cart();

			ajaxGetContent();

		});

		function ajaxGetContent() {

			$('#vpd-content').html('');
			Spinner.spin($('#vpd-content').get(0));
			$.ajax({
				url: '/html/products/' + $('#iproductId').val() + '.html',
				success: function(data) {
					Spinner.stop();
					$('#vpd-content').html(data);
					contentLoaded = true;
					$('#vpd-detail-header').show();
					$('.notload').removeClass('notload');
					$('#vpd-content').fadeIn();
					// 调整图片
					$('#vpd-content img').each(function() {
						$(this).on('load', function() {
							if ($(this).width() >= document.body.clientWidth) {
								$(this).css('display', 'block');
							}
							$(this).height('auto');
						});
					});
					$('#vpd-content').find('div').width('auto');
				},
				error: function() {
					$('#vpd-content').load('?/vProduct/ajaxGetContent/id=' + $('#iproductId').val(), function() {
						Spinner.stop();
						contentLoaded = true;
						$('#vpd-detail-header').show();
						$('.notload').removeClass('notload');
						$('#vpd-content').fadeIn();
						// 调整图片
						$('#vpd-content img').each(function() {
							$(this).on('load', function() {

								if ($(this).width() >= document.body.clientWidth) {
									$(this).css('display', 'block');
								}
								$(this).height('auto');
							});
						});
						$('#vpd-content').find('div').width('auto');
					});
				}
			});
		}

	});

});