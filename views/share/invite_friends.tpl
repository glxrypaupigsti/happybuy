<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
<meta name="format-detection" content="telephone=no" />  
<meta name="format-detection" content="email=no" /> 
<link rel="stylesheet" href="{$docroot}static/font/fonticon.css" />
<link rel="stylesheet" href="{$docroot}static/css/mobile.css" />

<title>Cheerslife暖心下午茶</title>
</head>

<body style="background-color:#eeeeee;">
<div class="invite-body">
<div class="detail-header"> <span class="backicon" onClick="location.href='?/Uc/home'" id="select_back"><i class="proicon icon-back"></i></span> <span class="title-text">邀请好友</span>
 <span onClick="location.href='?/Share/share_note_view'" class="modify-add">邀请说明</span>
 </div>
  <div class="empty-header" style="height:51px;"></div>
<div class="invite-note"><img src="{$docroot}static/img/invite_bg.png" />
<div class="invite-note-text">

<div class="invite-text"><div class="text-1"><img src="{$docroot}static/img/share.png" />分享本页面给其他好友,</div><div class="text-2">可赢取一张<b>{$couponInfo.coupon_value}</b>元优惠券!</div></div>
</div>
</div> 

<div class="invite-friends">
<div class="list-header"><span class="list-header-text">邀请好友列表</span><span class="iconopen"><i class="icon-open iconfont"></i></span></div>

 <div class="friend-title"><span class="left-name">朋友(ID)</span><span class="right-num">赢得优惠券金额</span></div>
<div class="friends-list">
<ul>
 {section name=oi loop=$list}
<li><div><span class="leftname">{$list[oi]['uinfo'].client_name}</span><span class="rightmoney">￥{$list[oi].coupon_value}</span></div></li>
   {/section}
</ul>
</div>
</div>

</div>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

        <script type="text/javascript">

            wx.config({
                debug: false,
                appId: '{$signPackage.appId}',
                timestamp: {$signPackage.timestamp},
                nonceStr: '{$signPackage.nonceStr}',
                signature: '{$signPackage.signature}',
                jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage']
            });
             wx.ready(function () {
    // 在这里调用 API

            wx.onMenuShareTimeline({
  				  title: '【福利】快来领优惠券~享用CheersLife健康下午茶', // 分享标题
    			  link: '{$base_url}?/Share/share_wallet_view/type=0&uid={$uid}&from_uid={$uid}&time={$time}', // 分享链接
   				  imgUrl: '{$base_url}/static/img/headimg.jpeg', // 分享图标
    			  success: function () { 
      			    $.post('{$base_url}?/Share/ajaxCreateShare',
       			     function(data){
       			      
       			   });
  				  }
  				 });
    	   wx.onMenuShareAppMessage({
    		  title: '【福利】快来领优惠券~享用CheersLife健康下午茶', // 分享标题
    			 desc: '吃过下午茶，吃过这么健康的下午茶么？', // 分享描述
   				 link: '{$base_url}?/Share/share_wallet_view/type=0&uid={$uid}&from_uid={$uid}&time={$time}', // 分享链接
    			 imgUrl: '{$base_url}/static/img/headimg.jpeg', // 分享图标
  			     success: function () { 
       			   $.post('{$base_url}?/Share/ajaxCreateShare',
       			     function(data){
       			 		
       			   });
  			 }
   				 
        });
      });
    			 
      
</script>
     <script data-main="{$docroot}static/script/Wshop/share.js?v={$smarty.now}" src="{$docroot}static/script/require.min.js"></script> 
        
</body>
</html>
