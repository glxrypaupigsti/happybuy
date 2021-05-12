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

require([ 'config' ], function(config) {

    require([ 'util', 'jquery', 'Spinner', 'Cart', 'Slider', 'Tiping' ], function(util, $, Spinner, Cart, Slider, Tiping) {
          $('#charge').click(function(){
	          var code = $('#code').val();
	          $.post(shoproot + '?/ChargeManage/charge_by_charge_code/charge_code='+$('#code').val(), function (r) {
		           if(r.ret_code > 0){
		        	   window.location.reload()
		           }else{
		              layer.open({
                 content: r.ret_msg,
                 time: 3 //3秒后自动关闭
            });
		           }
	            });
          });    

         function close_recharge(){
                  $(".closeicon").click(function(){
                  $(".recharge-body").hide();
              });
              $(".recharge-btn").click(function(){
                $(".recharge-body").show();
                });
            }
          
              function header_height(){
                var img_width=$(".user-head-img img").width();
                $(".user-head-img img").css('height',img_width);
              }

              $(document).ready(function(){
                header_height();
                close_recharge();
              });

        });

});
