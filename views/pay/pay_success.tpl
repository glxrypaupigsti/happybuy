<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
<link rel="stylesheet" href="../../static/css/bootstrap.css" />
<link rel="stylesheet" href="../../static/css/payment.css" />
<title>Cheerslife暖心下午茶</title>
</head>

<body>
<div class="payment_bg">
<div class="activity_top1 show_img">
<img src="../../static/img/p1.png" />
</div>
<div class="pay-success">
<div class="success-text">您已支付￥{$amount}</div>
<div class="code-img"><img src="{$docroot}?/CashPay/genOrderTrackQRCode/id={$id}" style=" width:50%;margin-bottom: 10px;"/></div>
<div class="pay-voucher">支付凭证：{$code}</div>
<div class="pay-voucher2">保存图片以作凭证哦！</div>
</div>
<div class="activity_top3 show_img">
<img src="../../static/img/p3.png" />
</div>
</div>

</body>
</html>
