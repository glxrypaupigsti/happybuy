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
<link rel="stylesheet" href="{$docroot}static/css/mobile.css" />
<script language="javascript" src="{$docroot}static/script/jquery-2.1.1.min.js"></script>
<script data-main="{$docroot}static/script/Wshop/shop_orderlist.js" src="{$docroot}static/script/require.min.js"></script>

</head>
<body style='background: #eeeeee;'>
<div id="uc-orderlist" class="uc-order-list">
</div>
<div id="list-loading" style="display: none;"><img src="{$docroot}static/images/icon/spinner-g-60.png" width="30"></div>

<div class="take-order" id="order" ><a class="order-sumbit"> 立即点单</a></div>
</body>
<script type="text/javascript">
  
  $('#order').click(function(){
      var url = "?/Index/index";
      window.location.href =url;
  });

</script>

</html>