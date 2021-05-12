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


</head>
<body style='background: #eeeeee;'>
<div class="expressdetail-body">
  <div class="detail-header"> <span class="title-text">支付详情</span> </div>
<div class="empty-header" style="height:51px;"></div>






  
 <div class="send-detail">
 
 <div class="sendto sendhone">支付号：{$pay.serial_number}</div>
 <div class="sendto sendhone">支付时间：{$pay.order_time}</div>
 <div class="sendto sendname">支付人：{$uinfo['client_name']}</div>
 <div class="sendto sendhone">应付金额：{$pay.amount}</div>
 <div class="sendto sendhone">实付金额：{$pay.discount_amount}</div>


 </div>

 </div>
</div>

</body>
</html>
