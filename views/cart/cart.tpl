<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv=Content-Type content="text/html;charset=utf-8" />
        <title>{$title} - {$settings.shopname}</title>
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
        <meta name="format-detection" content="telephone=no" />
        <link href="{$docroot}static/css/wshop_cart.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
        <script type="text/javascript" src="{$docroot}static/script/crypto-md5.js"></script>
    </head>
    <body>



        {include file="../global/ad/global_top.tpl"}

        <div id="addrPick"></div>
        <input type="hidden" id="promId" value="{$promId}" />
        <input type="hidden" id="promAva" value="{$promAva}" />
        <input type="hidden" id="payOn" value="{if !$config.order_nopayment}1{else}0{/if}" />
        <input type="hidden" id="addrOn" value="{if $config.wechatVerifyed and $config.useWechatAddr}1{else}0{/if}" />
        <input type="hidden" id="paycallorderurl" value="{$docroot}?/Order/ajaxCreateOrder" />

     

        <header class="Thead">商品信息</header>

        <div id="orderDetailsWrapper" data-minheight="68px"></div>




        <div class="button green" style='display: none' id="wechat-payment-btn">去结算</div>
        

        <script data-main="{$docroot}static/script/Wshop/shop_cart.js?v={$smarty.now}" src="{$docroot}static/script/require.min.js"></script>

      
		{include file="../global/footer.tpl"}
    </body>
</html>