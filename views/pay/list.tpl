<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
<link rel="stylesheet" href="{$docroot}static/css/bootstrap.css" />
<link rel="stylesheet" href="{$docroot}static/css/payment.css" />
<title>CheersLife结算统计</title>
</head>
<style>

</style>
<body>
<div class="payment_bg">
<div class="activity_top1 show_img">
<img src="{$docroot}static/img/p1.png" />
</div>


<div style="text-align: center;font-size: 18px;color: #fff;margin-top: 20px;width: 100%;">
    <div style="margin-top: 10px;width: 300px;margin-left: auto;margin-right: auto;height: 30px;line-height: 30px;">
    总计：{$total_transactions}笔&nbsp;&nbsp;&nbsp;&nbsp￥{$total_amount}
    </div>
</div>
<div style="text-align: center;font-size: 16px;color: #fff;margin-top: 20px;width: 100%;">
    <div style="margin-top: 10px;width: 300px;margin-left: auto;margin-right: auto;height: 26px;line-height: 26px;">
        <div style="width: 120px;float: left;">日期</div>
        <div style="width: 50px;float: left;">笔数</div>
        <div style="width: 130px;float: left;">金额</div>
    </div>
    {foreach from=$daily_stat item=day}
    <div style="width: 300px;margin-left: auto;margin-right: auto;height: 26px;line-height: 26px;">
        <div style="width: 120px;float: left;">{$day.date}</div>
        <div style="width: 50px;float: left;">{$day.num}</div>
<div style="width: 130px;float: left;text-align:left;padding-left:20px;">￥{$day.sum}</div>
    </div>
    {/foreach}
</div>
<div class="activity_top3 show_img">
<img src="{$docroot}static/img/p3.png" />
</div>
</div>
</body>
</html>
