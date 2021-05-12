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

	require(['util', 'jquery', 'Spinner'], function(util, $, Spinner) {

	    $('#update_phone').click(update_phone);
		$('#bind_phone').click(bind_phone);
        friend_name();
		function update_phone(){
				var m_tel = $('#modify_phone_txt').val();
				var shareType = $('#type').val();
				var shareUid = $('#share_uid').val();
				var fromUid = $('#from_uid').val();
				var reg = /^1[3|4|5|7|8][0-9]\d{8}$/;
				if(m_tel == ''|| !reg.test(m_tel)){
					
					layer.open({
                 content: '请填写正确的手机号码！',
                 time: 3 //3秒后自动关闭
                  });
					return;
				}

				var url = shoproot+"?/Share/ajaxBindPhone/phone="+m_tel;

			    $.get(url,function(data){
					window.location.href="?/Share/share_wallet_view/type="+shareType+"&uid="+shareUid+"&from_uid="+fromUid;
			    });
			}
		function bind_phone(){

				var tel = $('#bind_phone_txt').val();
				var shareType = $('#type').val();
				var shareUid = $('#share_uid').val();
				var fromUid = $('#from_uid').val();
				var time = $('#time').val();
				var reg = /^1[3|4|5|7|8][0-9]\d{8}$/;

				if(tel == ''|| !reg.test(tel)){
					layer.open({
                 content: '请输入正确的手机号！',
                 time: 3 //3秒后自动关闭
                  });
					return;
				}
				var url = shoproot+"?/Share/ajaxBindPhone/phone="+tel;

			    $.get(url,function(data){
					window.location.href="?/Share/share_wallet_view/type="+shareType+"&time="+time+"&uid="+shareUid+"&from_uid="+fromUid;
			    });
		}
			$(".right-modify").click(modify_phone);
				function modify_phone(){
				     $("#has-wallet").hide();
				     $("#modify-phone").show();
				}
			
	});


});

                $(document).ready(function(){
                   friend_name();
                      });
				function friend_name(){
					var friendtop = $(".friend-top").width();
					name_wd=friendtop-140;
					$(".f-name").css({'max-width':name_wd});
					}