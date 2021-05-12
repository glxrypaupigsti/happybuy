<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv=Content-Type content="text/html;charset=utf-8" />
<title>订单中心</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<link rel="stylesheet" href="{$docroot}static/layer/layer.css" />
<link rel="stylesheet" href="{$docroot}static/font/fonticon.css" />
<link rel="stylesheet" href="{$docroot}static/css/mobile.css" />
<script language="javascript" src="{$docroot}static/script/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="static/script/main.js?v={$cssversion}"></script>
        <!-- 微信JSSDK -->
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="{$docroot}static/layer/layer.js"></script>
</head>
<body style='background: #eeeeee;'>
<div class="expressdetail-body">
  <div class="detail-header"> <span class="backicon" onclick="location = '{$docroot}?/Uc/orderlist/';"><i class="proicon icon-back"></i></span> <span class="title-text">订单详情</span> </div>
<div class="empty-header" style="height:51px;"></div>
 {if $orderdetail.status == "unpay"}
 <div class="express-body">
  <div class="order-status"><span class="order-sub"><img src="{$docroot}static/img/notpay.png"><!--<i class="ordericon icon-notpay"></i>--></span>
  <div class="orderstate"><span class="statetext">订单未支付</span><span class="wait-order">请快快支付哦，1小时后订单自动失效！</span></div>
  </div>
 {/if}

 {if $orderdetail.status == "delivering"}
  <div class="order-status"><span class="order-sub"><i class="ordericon icon-send"></i></span>
  <div class="orderstate"><span class="statetext">订单配送中</span><span class="wait-order">订单正在火速配送中，请耐心等待！</span></div>
  </div>
 {/if}
 
 {if $orderdetail.status == "received"}
  <div class="order-status order-complete"><span class="order-sub"><i class="ordericon icon-complete"></i></span>
  <div class="orderstate"><span class="statetext">订单已完成</span><span class="wait-order">订单已完成，欢迎下次订购！</span></div>
  </div>
 {/if}
 
  {if $orderdetail.status == "payed"}
  <div class="order-status"><span class="order-sub"><i class="ordericon icon-sub"></i></span>
  <div class="orderstate"><span class="statetext">订单已提交</span><span class="wait-order">我们将尽快为您配送，请耐心等待！</span></div>
  </div>
 {/if}



 <div class="express-detail">
 <div class="order-pro-list">
            {section name=pi loop=$productlist}
          <p><span class="title-pro">{$productlist[pi].product_name}</span><span class="pro-price">&yen;{$productlist[pi].product_discount_price}</span><span class="pro-num">×{$productlist[pi].product_count}</span></p>
          {/section}
        </div>
<div class="order-send-price"><span class="pay-left">配送费</span><span class="pay-right">&yen;0</span></div>
<div class="order-minus"><span class="pay-left">优惠券</span><span class="pay-right">-&yen;{$orderdetail.order_amount-$orderdetail.pay_amount}</span></div>   
<div class="order-pay"><span class="all-pay-left">总计</span><span class="pay-right">{$orderdetail.pay_amount}</span></div>       
 </div> 
  
 <div class="send-detail">
 
 <div class="sendto sendhone">下单时间：{$orderdetail.order_time}</div>
  <div class="sendto sendhone">送达时间：{$orderdetail.exptime}</div>
 <div class="sendto sendname">联系人：{$address.user_name}</div>
 <div class="sendto sendhone">联系电话：{$address.phone}</div>
 <div class="sendto sendadd">联系地址：{$address.area}{$address.address}</div>

 </div>
 <!-- 未支付-->
  {if $orderdetail.status == "unpay"}
 <div class="payment-btn" onclick="javascript:Orders.reWePay({$orderdetail.order_id});">立即支付</div>
 </div>
 {else if $orderdetail.status == "received" && $isComment == '0'}
    <a class="payment-btn" href="http://survey.qiezilife.com/Weixin/FrontIndex/login?id=3&orderId={$orderdetail.order_id}">评价领取优惠</a>
 {/if}
 </div>
</div>
  <script type="text/javascript">
            WeixinJSBridgeReady(ExpressDetailOnload);
        </script>
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
