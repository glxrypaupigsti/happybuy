/* global wx, shoproot, parseFloat */

/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */

var priceHashId = 0;

require(['config'], function (config) {

    require(['util', 'jquery', 'Spinner', 'Cart', 'Tiping', 'touchSlider'], function (util, $, Spinner, Cart, Tiping, touchSlider) {

        Cart.init();

        // touchslider
        $('.touchslider-viewport').css({
            height: $(window).width(),
            overflow: 'hidden'
        });
        $('.touchslider-nav-item').eq(0).addClass('touchslider-nav-item-current');
        // 设置正方形宽高
        $('.touchslider-item img').width($(window).width()).height($(window).width());
        $(".touchslider").touchSlider({
            autoplay: true
        });

        /**
         * 微信图片预览接口
         */
        var imageList = [];

        $('.touchslider-item img').each(function () {
            imageList.push($(this).attr('src'));
        });

        $('.touchslider-viewport').on('click', function () {
            wx.previewImage({
                current: '', // 当前显示的图片链接
                urls: imageList // 需要预览的图片链接列表
            });
        });

        function callback(data){

           // alert(JSON.stringify(data.cartData));
           //Cart.doResultData(data);
          
                 window.localStorage.setItem('cart', '{}');
                 window.localStorage.removeItem('tmporder');
                 window.localStorage.removeItem('carthash');
                if(data.cartData != ""){

                  window.localStorage.setItem('cart', JSON.stringify(data.cartData));
                 
                }
                refreshCartCount();
        }

        /**
         * 添加至购物车
         * @param {type} redirect
         * @param {int} prom
         * @returns {undefined}
         */
        function addToCart(redirect, prom) {
            var productId = parseInt($('#iproductId').val());
            if (parseInt(prom) === 1 && redirect) {
                location = 'wxpay.php?id=p' + productId + 'm' + parseInt(priceHashId);
            } else {
               
                Cart.add(productId, 1, parseInt(priceHashId),callback);
                //Tiping.flas('已加入购物车');
                if (redirect) {
                     
                   window.location.href= "?/Cart/index_order";
                   
                }
            }
        }

        $('#pd-dsc1 .pd-spec-sx').click(fnDscTouch);

        $('#pd-dsc2 .pd-spec-sx').click(fnDscTouch2);

        /**
         * 商品价格表点击
         * @returns {undefined}
         */
        function fnDscTouch() {
            var node = $(this);
            $('#pd-dsc2 .pd-spec-sx.hover').removeClass('hover');
            $('#pd-dsc2 .pd-spec-sx.enable').removeClass('enable');
            $('#pd-dsc1 .pd-spec-sx.hover').removeClass('hover');
            // global
            detId = node.attr('data-det-id');
            var Havs = fnGetHav(detId);
            node.addClass('hover');
            if ($('#pd-dsc2').length === 0) {
                // 一维价格表
                showPriceHash(detId, 0);
            } else {
                // 二维价格表
                $.each(Havs, function (i, value) {
                    $('#pd-dsc2 .pd-spec-sx[data-det-id=' + value + ']').addClass('enable');
                });
                $('#pd-dsc2 .pd-spec-sx.enable').eq(0).click();
            }
        }

        // 初始化点击
        if ($('#pd-dsc1').length > 0) {
            $('#pd-dsc1 .pd-spec-sx').eq(0).click();
        }

        /**
         * 商品价格表点击
         * @returns {undefined}
         */
        function fnDscTouch2() {
            var node = $(this);
            if (node.hasClass('enable')) {
                $('#pd-dsc2 .pd-spec-sx.hover').removeClass('hover');
                node.addClass('hover');
                showPriceHash(detId, node.attr('data-det-id'));
            }
        }

        /**
         * 显示价格映射
         * @param {type} detId1
         * @param {type} detId2
         * @returns {undefined}
         */
        function showPriceHash(detId1, detId2) {
            var priceHash = $('.spec-hashs[value=' + detId1 + '-' + detId2 + ']');
            // 最终价
            $('#pd-sale-price').html('&yen;' + parseFloat(priceHash.attr('data-price')).toFixed(2));
            // 市场价
            if (priceHash.attr('data-market-price') > 0) {
                $('#pd-market-price').html('&yen;' + parseFloat(priceHash.attr('data-market-price')).toFixed(2));
            } else {
                $('#pd-market-price').html('&yen;' + parseFloat($('#mprice').val()).toFixed(2));
            }
            $('#pd-market-instock').html(priceHash.attr('data-instock'));
            // 价格表id
            priceHashId = parseInt(priceHash.attr('data-id'));
            priceHash = null;
        }

        function fnGetHav(detId) {
            var r = [];
            var nH = $('.spec-hashs[value^=' + detId + '-]');
            nH.each(function () {
                r.push(parseInt($(this).val().replace(detId + '-', '')));
            });
            return r;
        }

        var i = new Image();
        i.src = 'static/images/icon/iconfont-iconfontroundcheck-50x.png';
        i.src = 'static/images/icon/iconfont-iconfontroundcheck-100x.png';

        // 数量选择按钮
        $('.productCountMinus').bind({
            'touchend touchcancel mouseup': function (event) {
                event.preventDefault();
                var node = $(this).parent().find('.productCountNumi');
                node.val(parseInt(node.val()) === 1 ? 1 : node.val() - 1);
            }
        });

        $('.productCountPlus').bind({
            'touchend touchcancel mouseup': function (event) {
                event.preventDefault();
                var node = $(this).parent().find('.productCountNumi');
                node.val(parseInt(node.val()) + 1);
            }
        });

        var contentLoaded = false;

        $(window).scroll(function () {
            var totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop()) - 5;
            if ($(window).height() <= totalheight && !contentLoaded) {
                $('#vpd-content').html('');
                Spinner.spin($('#vpd-content').get(0));
                // ajax 加载商品详情
                $.ajax({
                    url: '/html/products/' + $('#iproductId').val() + '.html',
                    success: function (data) {
                        Spinner.stop();
                        $('#vpd-content').html(data);
                        contentLoaded = true;
                        $('#vpd-detail-header').show();
                        $('.notload').removeClass('notload');
                        $('#vpd-content').fadeIn();
                        // 调整图片
                        $('#vpd-content img').each(function () {
                            $(this).on('load', function () {
                                if ($(this).width() >= document.body.clientWidth) {
                                    $(this).css('display', 'block');
                                }
                                $(this).height('auto');
                            });
                        });
                        $('#vpd-content').find('div').width('auto');
                    },
                    error: function () {
                        $('#vpd-content').load('?/vProduct/ajaxGetContent/id=' + $('#iproductId').val(), function () {
                            Spinner.stop();
                            contentLoaded = true;
                            $('#vpd-detail-header').show();
                            $('.notload').removeClass('notload');
                            $('#vpd-content').fadeIn();
                            // 调整图片
                            $('#vpd-content img').each(function () {
                                $(this).on('load', function () {
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

        /**
         * 按钮点击
         */
        util.fnTouchEnd('a.button', function (node) {
            if (node.attr('data-add') === '1') {
                addToCart(false, node.attr('data-prom'));
            } else if (node.attr('data-add') === '0') {
                addToCart(true, node.attr('data-prom'));
            } else {
                // nothing
            }
        });
        
        
        
        

        util.onresize(function () {
            $('.pd-box-inner img').each(function (i, node) {
                $(node).height($(node).width());
            });
        });

        function refreshCartCount() {
            $('#toCart i').html(Cart.count());
        }

        // 加入收藏按钮点击
        $('.uc-add-like').click(function () {
            var node = $(this);
            var pid = parseInt($('#iproductId').val());
            if (node.hasClass('fill')) {
                pid = (-1) * pid;
            }
            // post
            $.post(shoproot + '?/Uc/ajaxAlterProductLike/', {id: pid}, function (r) {
                if (r > 0) {
                    if (!node.hasClass('fill')) {
                        Tiping.flas('收藏成功');
                    }
                    node.toggleClass('fill');
                }
            });
        });

        refreshCartCount();

    });

});