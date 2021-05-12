<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
<link rel="stylesheet" href="{$docroot}static/font/fonticon.css" />
<link rel="stylesheet" href="{$docroot}static/css/bootstrap.css" />
<link rel="stylesheet" href="{$docroot}static/css/mobile.css" />

<script data-main="{$docroot}static/script/Wshop/shop_couponlist.js" src="{$docroot}static/script/require.min.js"></script>

<title>Cheerslife暖心下午茶</title>
</head>

<body style=" background-color:#eeeeee;">
<input type="hidden" id="couponId" value="{$couponId}" />
<input type="hidden" id="time" value="{$time}" />
<input type="hidden" id="isbalance" value="{$isbalance}" />
<div class="coupon-body" style="padding-bottom:50px;">
  <div class="detail-header"> <span class="backicon" id="back"><i class="proicon icon-back"></i></span> <span class="title-text">优惠券</span> </div>
  <div class="empty-header" style="height:51px;"></div>
  <div class="coupon-cat coupon-select" style="display:none;">
    <ul>
      <li class="can-use active">今日优惠</li>
      <li class="canot-use">我的优惠券</li>
    </ul>
  </div>
  <div class="coupon-list">
 
    <ul class="tody_coupon">
    
     
     
       
       {foreach $couponList as $key => $val}
       
           
           {if $couponId == ''}
            {if $key == 0}
      <li class="not-used-coupon">
        <div class="coupon-amount" ><span class="amount-money">{$val.coupon_value}</span><span class="yuan">{$val.unit}</span></div>
        <div class="coupon-detail"><span class="condition">{$val.coupon_name}</span><span class="superposition">不可与同类优惠券共享</span><span class="expire-time">有效期至：{date("Y-m-d H:i",{$val.effective_end})}</span></div>
         <span class="select-btn" data-id="{$val.coupon_id}"><i class="proicon icon-select"></i></span>
      </li>
       {else}
       <li class="not-used-coupon">
        <div class="coupon-amount" data-id="{$couponList[oi].coupon_id}"><span class="amount-money">{$val.coupon_value}</span><span class="yuan">{$val.unit}</span></div>
        <div class="coupon-detail"><span class="condition">{$val.coupon_name}</span><span class="superposition">不可与同类优惠券共享</span><span class="expire-time">有效期至：{date("Y-m-d H:i",{$val.effective_end})}</span></div>
         <span class="select-btn" data-id="{$val.coupon_id}"><i class="proicon icon-select"></i></span>
      </li>
       {/if}
           {else}
             {if $val.select}
      <li class="not-used-coupon">
        <div class="coupon-amount" ><span class="amount-money">{$val.coupon_value}</span><span class="yuan">{$val.unit}</span></div>
        <div class="coupon-detail"><span class="condition">{$val.coupon_name}</span><span class="superposition">不可与同类优惠券共享</span><span class="expire-time">有效期至：{date("Y-m-d H:i",{$val.effective_end})}</span></div>
         <span class="select-btn active" data-id="{$val.coupon_id}"><i class="proicon icon-select"></i></span>
      </li>
       {else}
       <li class="not-used-coupon">
        <div class="coupon-amount" data-id="{$couponList[oi].coupon_id}"><span class="amount-money">{$val.coupon_value}</span><span class="yuan">{$val.unit}</span></div>
        <div class="coupon-detail"><span class="condition">{$val.coupon_name}</span><span class="superposition">不可与同类优惠券共享</span><span class="expire-time">有效期至：{date("Y-m-d H:i",{$val.effective_end})}</span></div>
         <span class="select-btn" data-id="{$val.coupon_id}"><i class="proicon icon-select"></i></span>
      </li>
       {/if}
           
           {/if}
               
       {/foreach}

    </ul>
   
  </div>

</div>
  <div class="take-order" id="coupon_notuse" ><a class="notuse-sumbit"> 不使用优惠券</a></div>
</body>
</html>
