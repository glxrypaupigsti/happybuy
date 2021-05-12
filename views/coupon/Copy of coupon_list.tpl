<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv=Content-Type content="text/html;charset=utf-8" />
        <title>{$title} - {$settings.shopname}</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="format-detection" content="telephone=no">
        <link href="static/css/wshop_uc.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
        <script type="text/javascript" src="static/script/jquery-2.1.1.min.js?v={$cssversion}"></script>
   
            <!-- 微信JSSDK -->
        <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    </head>
    <body style='background: #f7f7f7;'>
        <input type="hidden" id="orderId" value="{$orderId}"  />
        <input type="hidden" id="status" value="unuse"  />

        {include file="../global/top_nav.tpl"}

        {include file="../global/ad/global_top.tpl"}

        <div class='clearfix' id='uc-order-sort-bar'>
            <div class='uc-order-sort {if $status eq ''}hover{/if}' data-status="unuse"><b>未使用</b></div>

            <!--
            <div class='uc-order-sort {if $status eq 'unpay'}hover{/if}' data-status="used"><b>已使用</b></div>
            <div class='uc-order-sort {if $status eq 'payed'}hover{/if}' data-status="expire"><b>已过期</b></div>

            -->
         
        </div>

        <div id="uc-orderlist"></div>
        <div id="list-loading" style="display: none;"><img src="{$docroot}static/images/icon/spinner-g-60.png" width="30"></div>


        <script data-main="{$docroot}static/script/Wshop/shop_couponlist.js" src="{$docroot}static/script/require.min.js"></script>
        
    

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
        <script type="text/javascript" src="static/script/main.js?v={$cssversion}"></script>
    </body>
</html>