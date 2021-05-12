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
        <link rel="stylesheet" href="{$docroot}static/font/fonticon.css" />
        <link rel="stylesheet" href="{$docroot}static/css/mobile.css" />
        <script language="javascript" src="{$docroot}static/script/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="{$docroot}static/script/Wdmin/update_dist.js"></script>
    </head>

<body style='background: #eeeeee;'>
<div class="expressdetail-body" style="margin-bottom:10px;" >
    <div class="detail-header">
        <span class="title-text">订单详情</span>
    </div>
    <div class="empty-header" style="height:51px;"></div>
    <div style="height: 40px;line-height: 40px;text-align: center;font-size: 20px;color: #666;">{$distri_status}</div>

    <div class="express-detail" style="margin-top:0px;">
        <div class="order-pro-list">
            {section name=pi loop=$productlist}
            <p>
                <span class="title-pro">{$productlist[pi].product_name}</span>
                <span class="pro-num" style="position:inherit;">×{$productlist[pi].product_count}</span>
            </p>
            {/section}
        </div>

        <div class="send-detail">
            <div class="sendto sendhone">下单时间：{$orderdetail.order_time}</div>
            <div class="sendto sendhone">送达时间：{$orderdetail.exptime}</div>
            <div class="sendto sendadd" style="border-bottom:1px solid #efefef;" >送货地址：{$orderdetail.address.area}{$orderdetail.address.address}</div>
            <div class="sendto sendname">联系人：{$orderdetail.address.user_name}</div>
            <div class="sendto sendhone">联系电话：{$orderdetail.address.phone}</div>
        </div>
    </div>
</div>

{section name=opi loop=$next_op}
<div class="take-order-now" style="margin-top: 20px;width: 90%;margin-left: 5%;" data-id="{$distri_id}" data-status="{$next_op[opi].status}" onclick="update_dist_status(this);">
    <a class="order-sumbit" >{$next_op[opi].title}</a>
</div>
{/section}

</body>
</html>
