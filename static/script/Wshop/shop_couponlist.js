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

require([ 'config' ], function(config) {

	require([ 'util', 'jquery' ], function(util, $) {

		function select_coupon() {
			$(".tody_coupon li").click(function() {
				if ($(this).find('.select-btn').hasClass('active')) {
				} else {
					$(".tody_coupon .select-btn").removeClass('active');
					$(this).find('.select-btn').addClass('active');
					
					var id = $('.select-btn.active').attr('data-id');
					var time = $("#time").val();
					var isbalance = $('#isbalance').val();
					var url = "?/Cart/index_order/couponId="+id+"&time="+time+"&isbalance="+isbalance;
					window.location.href=url;
					
				}
				
				
			})
		    $(".my_coupon .select-btn").click(function() {
				if ($(this).hasClass('active')) {
				} else {
					$(".my_coupon .select-btn").removeClass('active');
					$(this).addClass('active');
				}
			})
		
		}
		function coupon_select() {
			$(".coupon-cat ul li").click(function() {
				if ($(this).hasClass("active")) {
				} else {
					$(".coupon-cat ul li").removeClass("active");
					$(this).addClass('active');
				}
			});
			$(".can-use").click(function(){
				$(".tody_coupon").show();
				$(".my_coupon").hide();
				})
			$(".canot-use").click(function(){
				$(".tody_coupon").hide();
				$(".my_coupon").show();
				})
		
		}
        function coupon_notused(){
			$("#coupon_notuse").click(function(){
				$(".coupon-list ul li .select-btn").removeClass("active");
				})
			}
		$(document).ready(function() {
			select_coupon();
			coupon_select();
			coupon_notused();
		})
		
		$('#back').click(function(){
			

			var id = $('.select-btn.active').attr('data-id');
		
			var time = $("#time").val();
			var isbalance = $('#isbalance').val();
		   	if(id == undefined){
	       		id = -1;
	       	}
			var url = "?/Cart/index_order/couponId="+id+"&time="+time+"&isbalance="+isbalance;
			window.location.href=url;

			
			
		});
	$('#coupon_notuse').click(function(){

		
		var time = $("#time").val();
		var isbalance = $('#isbalance').val();
		var url = "?/Cart/index_order/couponId=-1"+"&time="+time+"&isbalance="+isbalance;
	
			window.location.href=url;
			
			
		});

	});

});