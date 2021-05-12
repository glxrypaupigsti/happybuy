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

<body style=" background-color:#eeeeee;">
<div class="coupon-body">
  <div class="detail-header"> <span class="backicon"><i class="proicon icon-back"></i></span> <span class="title-text">优惠券</span> </div>
  <div class="coupon-cat">
    <ul>
      <li class="not-used active">未使用</li>
      <li class="has-use">已使用</li>
      <li class="expired">已过期</li>
    </ul>
  </div>
  <div class="coupon-list">
    <ul>
      <li class="not-used-coupon">
        <div class="coupon-amount"><span class="amount-money">30</span><span class="yuan">元</span></div>
        <div class="coupon-detail"><span class="condition">满100减50</span><span class="superposition">不可与其他优惠券共享</span><span class="expire-time">有效期至：2015.12.01</span></div>
         <span class="select-btn active"><i class="proicon icon-select"></i></span>
      </li>
      
            <li class="not-used-coupon">
        <div class="coupon-amount"><span class="amount-money">30</span><span class="yuan">元</span></div>
        <div class="coupon-detail"><span class="condition">满100减50</span><span class="superposition">不可与其他优惠券共享</span><span class="expire-time">有效期至：2015.12.01</span></div>
         <span class="select-btn"><i class="proicon icon-select"></i></span>
      </li>
      
      <li class="has-use-coupon" style="display:none;">
        <div class="coupon-amount"><span class="amount-money">30</span><span class="yuan">元</span></div>
        <div class="coupon-detail"><span class="condition">满100减50</span><span class="superposition">不可与其他优惠券共享</span><span class="expire-time">有效期至：2015.12.01</span></div>
       <!-- 已使用图标-->  <img src="../../static/img/hasuse.png" class="failure-img" />
      </li>
      
      <li class="expired-coupon" style="display:none;">
        <div class="coupon-amount"><span class="amount-money">30</span><span class="yuan">元</span></div>
        <div class="coupon-detail"><span class="condition">满100减50</span><span class="superposition">不可与其他优惠券共享</span><span class="expire-time">有效期至：2015.12.01</span></div>
       <!-- 已失效图标-->  <img src="../../static/img/failure.png" class="failure-img" />
      </li>
    </ul>
  </div>
</div>
</body>
</html>
<script type="application/javascript">
function select_coupon(){
	$(".not-used-coupon .select-btn").click(function(){
		if($(this).hasClass('active')){}
		else{
			$(".not-used-coupon .select-btn").removeClass('active');
			$(this).addClass('active');
			}
		})
	}
function coupon_select(){
	$(".coupon-cat ul li").click(function(){
	if($(this).hasClass("active")){
		}
		else{
			$(".coupon-cat ul li").removeClass("active");
			$(this).addClass('active');}
		});
	$(".not-used").click(function(){
		$(".coupon-list ul li").hide();
		$(".not-used-coupon").show();
		})
	$(".has-use").click(function(){
		$(".coupon-list ul li").hide();
		$(".has-use-coupon").show();
		})
	$(".expired").click(function(){
		$(".coupon-list ul li").hide();
		$(".expired-coupon").show();
		})
	}
	
$(document).ready(function(){
select_coupon();
coupon_select();
	})
</script>
