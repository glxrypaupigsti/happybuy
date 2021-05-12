<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
<link rel="stylesheet" href="{$docroot}static/font/fonticon.css" />
<link rel="stylesheet" href="{$docroot}static/css/bootstrap.css" />
<link rel="stylesheet" href="{$docroot}static/css/mobile.css" />

<script data-main="{$docroot}static/script/Wshop/user_coupon.js" src="{$docroot}static/script/require.min.js"></script>

<title>个人中心</title>
</head>
<style>
.coupon-list ul li.not-used-coupon{ padding-right:10px;}
</style>
<body style=" background-color:#eeeeee;">
<div class="coupon-body">
<div class="detail-header"><a href="" onclick="window.history.back();return false;" > <span class="backicon"><i class="proicon icon-back"></i></span> </a><span class="title-text">优惠券</span> </div>
<div class="empty-header" style="height:51px;"></div>
  <div class="coupon-cat">
    <ul>
      <li class="not-used active" data-state="0">未使用</li>
      <li class="has-use" data-state="1">已使用</li>
      <li class="expired" data-state="2">已过期</li>
    </ul>
  </div>
  
  <div id="uc-orderlist" class="coupon-list">
	  	<ul id="not-used-coupon-wrapper"></ul>
	  	<ul id="used-coupon-wrapper"></ul>
	  	<ul id="expired-coupon-wrapper"></ul>
  </div>
  <div id="list-loading" style="display: none;"><img src="{$docroot}static/images/icon/spinner-g-60.png" width="30"></div>
  
</div>
</body>
</html>

