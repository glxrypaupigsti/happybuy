<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
<link rel="stylesheet" href="../../static/font/fonticon.css" />
<link rel="stylesheet" href="../../static/css/mobile.css" />
<script language="javascript" src="../../static/script/jquery-2.1.1.min.js"></script>
<title>个人中心</title>
</head>

<body style=" background-color:#ffffff;">
<div class="center-body">
  <div class="user-img" style="background-image:url(../../static/img/imgpro3.png);    background-size: cover; background-position: center; background-repeat: no-repeat;"> <div class="mask-img"><img src="../../static/img/userimg-bg.png" /></div></div>
  <div class="center-footer">
  <div class="user-infor">
    <div class="user-head-img"><img src="../../static/img/imgpro.png" /></div>
    <div class="user-head-name">Adrew</div>
    <div class="user-head-phone">15267234567</div>
  </div>
  <div class="user-detail">
    <ul>
    <a>
      <li class="detail-01">
        <div class="coupon-detail-list"><span class="detail-iconimg"><i class="usericon icon-coupon"></i></span><span class="coupon-head">优惠券</span><span class="coupon-num">共<b>3</b>张</span></div>
      </li>
      </a>
      <a>
      <li class="detail-02">
        <div class="coupon-detail-list"><span class="detail-iconimg"><i class="usericon icon-money"></i></span><span class="coupon-head">余额充值</span><span class="coupon-num">余额：共<b>147</b>元</span></div>
      </li>
      </a>
      <a>
      <li class="detail-03">
        <div class="coupon-detail-list"><span class="detail-iconimg"><i class="usericon icon-order"></i></span><span class="coupon-head">订单</span><span class="coupon-num"><b>3</b>个</span></div>
      </li>
      </a>
    </ul>
  </div>
  </div>
<div class="take-order"><a class="order-sumbit"> 立即点单</a></div>

</div>
<div class="recharge-body" style="display:none;">
<div class="rcharge-bg"></div>
<div class="recharge">
<div class="charge-title">代金卡充值<span class="closeicon"><i class="usericon icon-close"></i></span></div>
<div class="recharge-code"><input type="text" placeholder="请输入充值码"/></div>
<div class="recharge-sub">立即充值</div>
</div>
</div>
</body>
</html>
<script type="application/javascript">
function close_recharge(){
	$(".closeicon").click(function(){
		$(".recharge-body").hide();
		})
	$(".coupon-detail-list").click(function(){
		$(".recharge-body").show();
		})
	}
function header_height(){
	var img_width=$(".user-head-img img").width();
	$(".user-head-img img").css('height',img_width);
	}
$(document).ready(function(){
	header_height();
	close_recharge();
	})
</script>