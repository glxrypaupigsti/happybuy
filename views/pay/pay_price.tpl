<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
<link rel="stylesheet" href="{$docroot}static/css/bootstrap.css" />
<link rel="stylesheet" href="{$docroot}static/css/payment.css" />
 <script src="{$docroot}static/script/jquery-2.1.1.min.js"></script>
<title>Cheerslife暖心下午茶</title>
</head>

<body style=" background-color:#eeeeee;">
<div class="price-body">
<input type="hidden" id="discount" value={$discountMode} />
<div class="input-price"> <input type="number" id="number" placeholder="输入金额(元)"/><span class="empty-btn"><img src="{$docroot}static/img/empty.png"/></span></div>
<div class="actual-price"><span class="actual-text">实付金额</span><input type="text" id="discount_value" readonly="readonly" value="" /></div>
<div class="paynow" id="submit">立即支付</div>
</div>

<div style="position: absolute;bottom: 20px;font-size: 17px;color: #f00;text-align: center;width: 100%;">
    本支付仅限在点沁餐厅用餐结账付款使用<br>不可用于下午茶付款
</div>
</body>
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
<script data-main="{$docroot}static/script/Wshop/pay.js?v={$smarty.now}" src="{$docroot}static/script/require.min.js"></script> 

</html>
