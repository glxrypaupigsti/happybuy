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
    require(['util', 'jquery', 'Spinner', 'Cart', 'Slider', 'Tiping'], function(util, $, Spinner, Cart, Slider, Tiping) {
        $('#list-loading').show();
        Cart.check();
        var cat = $('#cat').val();
        plus_click();
        cart_list_show();
        select_pro();
        empty_cart();
        prolist_scroll();
        fnLoadCatlist(cat);
        select_time();
        mask_height();
            
        load_avaliable_deliver_day();

        function mask_height() {
            var mask_top_h = parseInt($(".top-mask").height());
            $(".header-time").css({
                'line-height': mask_top_h + 'px'
            });
            $(".header-time").css({
                'height': mask_top_h + 'px'
            });
            $(".header-empty").css({'height':mask_top_h+ 'px'});
            $(".mid-mask").css('top', mask_top_h);
            $(".product-body").click(function() {
                $(".mid-mask").hide();
                $(".top-mask").hide();
            })
        }

        function select_time() {
            $(".sec-btn").click(function() {
                if ($(".select-time").hasClass('active')) {
                    $(".select-time").slideUp();
                    $(".select-time").removeClass('active');
                    $(".bot-mask").hide();
                } else {
                    $(".select-time").slideDown();
                    $(".select-time").addClass('active');
                    $(".bot-mask").show();
                }
            })
            $(".bot-mask").click(function() {
                $(this).hide();
                $(".select-time").slideUp();
                $(".select-time").removeClass('active');
            })
            $(".select-time ul li").click(function() {
                sec_this = $(this).html();
                $('.sec-send').html(sec_this);
                $(".select-time").slideUp();
                $(".select-time").removeClass('active');
                $(".bot-mask").hide();

            });
        }

        function select_pro() {
            $(".left-cat ul li").click(function() {
                var catId = $(this).find('.cat-btn').attr('data-catid');
                cat = catId;
                fnLoadCatlist(catId);
                $(".left-cat ul li.active").removeClass('active');
                $(this).addClass('active');
            })
        }

        function prolist_scroll() {
            var left_height = $(window).height();
            $(".left-cat").css('height', left_height + 'px');
            $(".right-pro").css('height', left_height + 'px');
        }

        function empty_cart() {
            $(".header-right").click(function() {
               layer.open({
    content: '确认清空购物车？',
    btn: ['确认', '取消'],
    shadeClose: false,
    yes: function(){

        $.post(shoproot + '?/Cart/ajaxDelCart/', function (Res) {
                        //$(".cart-list ul").remove();
                        Cart.clear();
                        $(".number").html(Cart.count());
                        $(".num").html("0");
                        $(".hidden").hide();
                        $('.cat-num').hide();
                        $("#cart_list").html("");
                        $(".cart-list").hide();
                        $(".cart-price").html("￥0");
                        layer.closeAll();
                    });
    }
});

            });
        }

        function cart_list_show() {
            $(".cart-list img").click(function() {
                $(".cart-list").hide();
                fnLoadCatlist(cat);
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

        function plus_click() {
            $(".icon-plus").click(function() {
                var hash = $(this).parents('li').find(".pro-intru").attr('data-hash');
                var numNode = $(this).parents('li').find('.num');
                var num = parseInt($(this).parents('li').find('.num').html());
                var stock = parseInt($(this).parents('li').attr('data-stock'));
                if ((num + 1) > stock) {
                    layer.open({
                 content:'无法选择更多',
                 time: 3 //3秒后自动关闭
            });
                    return false;
                }
                Cart.set(hash, parseInt(numNode.text()) + 1, doCallback);
                $(this).parents(".buy-num").find(".hidden").show();
                numNode.html(parseInt(numNode.text()) + 1);
            })
        }

        function minus_click() {
            $(".icon-minus").click(function() {
                var hash = $(this).parents('li').find(".pro-intru").attr('data-hash');
                var numNode = $(this).parents('li').find('.num');
                var p = $(this).parents('li').find(".pro-intru").attr('data-p');
                var sp = $(this).parents('li').find(".pro-intru").attr('data-sp');
                var num = parseInt(numNode.text()) - 1;
                if (num < 1) {
                    num = 0;
                    Cart.del(p, sp);
                    $.post('?/Index/ajaxRemoveProductUPdateData/', {
                        pid: p,
                        sid: sp
                    }, doCallback);
                    $(this).parents(".buy-num").find(".hidden").hide();
                } else {
                    Cart.set(hash, parseInt(numNode.text()) - 1, doCallback);
                }
                numNode.html(num);
            })
        }

        function doCallback(data) {
            if (data != "") {
                // alert(JSON.stringify(data));
                //val total = data.total;
                //alert(data.total);
                $(".cart-price ").html("&yen;" + data.total.toFixed(2));
                if (data.topCats != "") {
                    var cat = data.topCats;
                    for (var i = 0; i < cat.length; i++) {
                        $(".left-cat li").each(function() {
                            var v = $(this).find('.cat-btn').attr('data-catid');
                            if (cat[i].cat_id == v) {
                                if (typeof(cat[i].count) != 'undefined') {
                                    $(this).find('.cat-num').html(cat[i].count);
                                    $(this).find(".cat-num").show();
                                } else {
                                    $(this).find('.cat-num').hide();
                                }
                            }
                        });
                    }
                }
                $(".number").html(Cart.count());
            }
        }
        // 购买
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
        });

        function cart_plus_click() {
            $('.prolist #cart_plus').click(function() {
                var clicked_li = $(this).closest('li');
                var p = clicked_li.find(".pro-buy-num").attr('data-p');
                var hash = clicked_li.find(".pro-buy-num").attr('data-hash');
                var numNode = clicked_li.find('.num');
                var num = parseInt(clicked_li.find('.num').html());
                var stock = parseInt(clicked_li.attr('data-stock'));
                if ((num + 1) > stock) {
                    layer.open({
                 content:'无法选择更多',
                 time: 3 //3秒后自动关闭
            });
                    return false;
                }
                $(".right-pro ul li").each(function(i) {
                    var pro_p = $(".right-pro ul li").eq(i).find(".pro-intru").attr('data-p');
                    var plus_num = $(".right-pro ul li").eq(i).find(".num");
                    if (pro_p == p) {
                        plus_num.html(parseInt(plus_num.text()) + 1);
                    }
                });
                Cart.set(hash, parseInt(numNode.text()) + 1, doCallback);
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
                $(".right-pro ul li").each(function(i) {
                    var pro_p = $(".right-pro ul li").eq(i).find(".pro-intru").attr('data-p');
                    var minus_num = $(".right-pro ul li").eq(i).find(".num");
                    if (pro_p == p) {
                        minus_num.html(parseInt(minus_num.text()) - 1);
                        if (parseInt(minus_num.text()) == 0) {
                            minus_num.parents(".buy-num").find(".hidden").hide();
                        }
                    }
                });
                var num = parseInt(numNode.text()) - 1;
                if (num < 1) {
                    num = 0;
                    Cart.del(p, sp);
                    $(this).parents('li').remove();
                    $.post('?/Index/ajaxRemoveProductUPdateData/', {
                        pid: p,
                        sid: sp
                    }, doCallback);
                } else {
                    Cart.set(hash, parseInt(numNode.text()) - 1, doCallback);
                }
                numNode.html(num);
                if (count < 2) {
                    $(".cart-list").hide();
                }
            });
        }
        /*function touch_left_right() {
			$(".left-cat").on('touchstart', function () {
				$(".left-cat").css('position', 'absolute');
				$(".right-pro").css('position', 'fixed');
			});
			$(".right-pro").on('touchstart', function () {
				$(".right-pro").css('position', 'absolute');
				$(".left-cat").css('position', 'fixed');
			});
		}
*/
        function fnLoadCatlist(cat) {
            $('#rightContainer').load('?/Index/ajax_list_item/id=' + cat, function() {
                plus_click();
                minus_click();
                //touch_left_right();
            });
        }
            
        function set_date()
        {
            // get selected day and week
            day = $(this).find('.time-day').html();
            week = $(this).find('.time-weak').html();
            // update selected day info
            $('#selected_day .time-day').html(day);
            $('#selected_day .time-weak').html(week);
            $(".select-time").slideUp();
            $(".select-time").removeClass('active');
            $(".bot-mask").hide();
            // clean up shopping cart
            $.post(shoproot + '?/Cart/ajaxDelCart/', function(Res) {
                   Cart.clear();
                   $(".number").html(Cart.count());
                   $(".num").html("0");
                   $(".hidden").hide();
                   $('.cat-num').hide();
                   $("#cart_list").html("");
                   $(".cart-list").hide();
                   $(".cart-price").html("￥0");
            });
            // reload stock info of given date
            $.get('?/Index/setDeliverDate/date='+day, function(data){
                  var result = $.parseJSON(data);
                  if (result.err == 0) {
                        cat_id = $('.left-cat').find('.active .cat-btn').attr('data-catid');
                        // refresh current cat stock info for selected date
                        fnLoadCatlist(cat_id);
                  }
            });
        }
            
        function show_tip()
        {
            shouldShow = $('input[name=show_tip]').val();
            if (shouldShow) {
                $(".mid-mask").show();
                $(".top-mask").show();
            }
        }
        
        function load_avaliable_deliver_day()
        {
            $.get('?/Index/getDeliverDateList', function(data){
                  var result = eval(data);
                  
                  // update days list
                  days = '<ul>';
                  for (var i=0; i<result.length; i++) {
                        days += '<li><div class="text-left"><span class="time-icon"><i class="usericon icon-cale"></i></span><span class="time-day">'+result[i].date+'</span><span class="time-weak">'+result[i].weekday+'</span></div></li>'
                  }
                  days += '</ul>';
                  $('.select-time').html(days);
                  $('.select-time li').click(set_date);
                  
                  show_tip();
            });
        }
    });
});
