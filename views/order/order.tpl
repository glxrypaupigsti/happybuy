<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
    <meta name="format-detection" content="telephone=no" />  
    <meta name="format-detection" content="email=no" /> 
<link rel="stylesheet" type="text/css" href="{$docroot}static/city_select/mobile-select-area.css">
<link rel="stylesheet" type="text/css" href="{$docroot}static/city_select/dialog.css">
<script type="text/javascript" src="{$docroot}static/city_select/zepto.min.js"></script>
<script type="text/javascript" src="{$docroot}static/city_select/dialog.js"></script>
<script type="text/javascript" src="{$docroot}static/city_select/mobile-select-area.js"></script>
<link rel="stylesheet" href="{$docroot}static/layer/layer.css" />
<link rel="stylesheet" href="{$docroot}static/font/fonticon.css" />
<link rel="stylesheet" href="{$docroot}static/css/mobile.css" />
<link rel="stylesheet" href="{$docroot}static/css/commodity.css" />
<script language="javascript" src="{$docroot}static/script/jquery-2.1.1.min.js"></script>
<script language="javascript" src="{$docroot}static/script/fastclick.js"></script>
<script src="{$docroot}static/layer/layer.js"></script>
<title>U时光—小区快乐购商城</title>
</head>

<body style="background-color:#eeeeee;">
<input type="hidden" id="addressId" value="{$address.id}" />
<input type="hidden" id="userCouponId" value="{$couponId}" />
<input type="hidden" id="orderCouponId" value="{$orderCoupons.id}" />
<input type="hidden" id="isbalance" value="{$isbalance}" />


<div class="order-detail-body">
  <div class="detail-header"> <span class="backicon" id="back"><i class="proicon icon-back"></i></span> <span class="title-text">订单详情</span> </div>
  <div class="empty-header" style="height:51px;"></div>
  <div class="order-detail" >
    <div class="order-address">
      
      {if $address}
      <div class="sendto">送到这：</div>
      
      <div class="add-detail" id="update-address"><span class="modify-icon"><i class="proicon icon-right"></i></span> <span class="order-name">{$address.user_name}&nbsp;{$address.phone}</span>  <span class="order-add">{$address.area}{$address.address}</span> 
      </div>
      {else}
      <div class="link_add" id="update-address" style="text-align:center;" > <span class="modify-icon"><i class="proicon icon-right"></i></span><span style="line-height:34px; color:#ebe4d6; font-size:18px;">点击添加地址</span></div>
      {/if}
      </div>
    <div class="order-list">
      <ul>
         {strip}{section name=i loop=$product_list}
         
        <li><span class="pro-name">{$product_list[i].product_name}</span>
          <div class="num-price"><span class="num-commodity">×{$product_list[i].product_quantity}</span><span class="price-commodity">&yen; {$product_list[i]['pinfo'].sale_prices|string_format:"%.2f"}</span></div>
        </li>
       {/section}{/strip}
      </ul>
    </div>
    <div class="send-cost"><span class="send-cost-left">运送费</span><span class="send-cost-right">￥0.00</span></div>
  </div>
  <div class="order-detail1">
    <div class="detail-modify send-time"><span class="detail-modify-left">送达时间</span><span class="modify-icon"><i class="proicon icon-right"></i></span>
    <input class="select-time" id="time" value="{if $time ==''}请点此设置送达时间{else}{$time}{/if}" type="text" readonly="readonly">
   <input type="hidden" id="hd_time" value="1,1,1"/>
    </div>
    
    {if $coupon}
     <div class="detail-modify discount" id="discount_money"><span class="detail-modify-left">可扣除金额</span><span class="modify-icon"><i class="proicon icon-right"></i></span><span class="detail-modify-right" style=" color:#C9B68C;">
        
        {if $coupon.discount_type == 1}
          {$coupon.discount_val/10}折
        {else}
             -￥{$coupon.discount_val/100}
        {/if}
    
   
       </span>
       </div>
    {else}
        {if $userCoupons}
    <div class="detail-modify discount" id="discount_money"><span class="detail-modify-left">可用优惠券</span><span class="modify-icon"><i class="proicon icon-right"></i></span><span class="detail-modify-right">

    
           {count($userCoupons)}张可用


   
       </span>
    </div>
    {*
    {else}
      <div class="detail-modify discount"  ><span class="detail-modify-left">可用优惠券</span><span class="modify-icon"></span><span class="detail-modify-right">
          无 
       </span>
    </div>
    *}
     {/if}
    {/if}
   

  <div class="detail-modify discount"><span class="detail-modify-left">订单优惠   {if $orderCoupons}   |  {$orderCoupons.coupon_name}{/if}</span><span class="modify-icon"></span><span class="detail-modify-right"> {if $orderCoupons}
     <b style=" font-weight:400; color:#C9B68C;">-￥{$orderCoupons.coupon_value}</b>
    {else}
          无
       {/if}

       </span></div>
    <div class="balance"><span class="detail-modify-left">余额</span>&nbsp;<span class="balance-num">{$userInfo.balance}</span><span class="yes-no">{if $isbalance== 1}<img src="{$docroot}static/img/yes.png" />{else}<img src="{$docroot}static/img/no.png" />{/if}</span></div>
  </div>
  <div class="detail-footer">
    <div class="price-all">共计：&yen;{$amount|string_format:"%.2f"}</div>
    <div class="settle-btn" id="pay">结算</div>
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
                jsApiList: ['chooseWXPay']
            });

        </script>
        <script data-main="{$docroot}static/script/Wshop/order.js?v={$smarty.now}" src="{$docroot}static/script/require.min.js"></script> 
        
</body>
</html>


