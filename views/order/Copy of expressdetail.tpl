<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv=Content-Type content="text/html;charset=utf-8" />
        <title>订单详情</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="format-detection" content="telephone=no">
        <link href="static/css/wshop_uc.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
        <script type="text/javascript" src="static/script/jquery-2.1.1.min.js?v={$cssversion}"></script>
        <script type="text/javascript" src="static/script/main.js?v={$cssversion}"></script>
        <!-- 微信JSSDK -->
        <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    </head>
    <body style='background: #f7f7f7;overflow-x:hidden;'>

        <input type="hidden" value="{$orderdetail.express_code}" id="expresscode" />
        <input type="hidden" value="{$orderdetail.express_com}" id="expresscom" />

        {include file="../global/top_nav.tpl"}

        {include file="../global/ad/global_top.tpl"}

        <div class="exp-item-info clearfix" style="margin-top:0;">
            <span class="order-status">{$orderdetail.statusX}</span>
            <span class="order-id">订单号：{$orderdetail.serial_number}</span>
        </div>

        <div class="exp-item-info" style="background:url({$docroot}static/images/icon/od-exp-bh.png) left top repeat-x #fff;padding:0;padding-top:15px;">
            <div style="padding:0 10px;">
                <div class="clearfix">
                    <span class="od-name">{$address.user_name}</span>
                    <span class="od-tel">{$address.phone}</span>
                </div>
                <div class="od-address">{$address.city},{$address.address}</div>
            </div>
            <div style="height:15px;background:url({$docroot}static/images/icon/od-exp-bh.png) left bottom repeat-x #fff;"></div>
        </div>

        {if $orderdetail.express_code neq ''}
            <div class="exp-head">
                <div id="exp-comname">{$orderdetail.express_com1}</div>
                <div id="exp-code">运单编号：{$orderdetail.express_code}</div>
            </div>
        {/if}

        {if $orderdetail.express_code neq ''}
            <div class="exp-item-info">
                <div class="exp-item-caption">物流跟踪</div>
                <ul id="express-dt"></ul>
                <div id="loading-wrap"></div>
            </div>
        {/if}

        <div class="exp-item-info">
            <div class="exp-item-caption">物品信息</div>
            {section name=pi loop=$productlist}
                <div class="clearfix items" onclick="location = '{$docroot}?/vProduct/view/id={$productlist[pi].product_id}/showwxpaytitle=1';">
                    <img class="ucoi-pic" src="{$docroot}static/Thumbnail/?w=100&h=100&p=/uploads/product_hpic/{$productlist[pi].catimg}">
                    <div class="ucoi-con">
                        <span class="title">{$productlist[pi].product_name}</span>
                        <span class="price"><span class="prices">&yen;{$productlist[pi].product_discount_price}</span> x <span class="dcount">{$productlist[pi].product_count}</span></span>
                    </div>
                </div>
            {/section}
        </div>

        <div class="exp-item-info">
            <div class="exp-item-caption">订单信息</div>
            <div class="exp-payinfo clearfix">
                <span class="left">订单总额(包括运费)：</span>
                <span class="right prices">&yen;{$orderdetail.order_amount}</span>
            </div>
            <div class="exp-payinfo clearfix">
                <span class="left">实际支付：</span>
                <span class="right prices">&yen;{$orderdetail.pay_amount}</span>
            </div>
            <div class="exp-payinfo clearfix">
                <span class="left">运费：</span>
                <span class="right prices">&yen;{$orderdetail.order_yunfei}</span>
            </div>
            {if $orderdetail.wepay_serial neq ''}
                <div class="exp-payinfo clearfix">
                    <span class="left">微信支付编号：</span>
                    <span class="right" style="color:#777;">{$orderdetail.wepay_serial}</span>
                </div>
            {/if}
            {if $orderdetail.bank_billno neq ''}
                <div class="exp-payinfo clearfix">
                    <span class="left">银行扣款号：</span>
                    <span class="right" style="color:#777;">{$orderdetail.bank_billno}</span>
                </div>
            {/if}
            <div class="exp-payinfo clearfix">
                <span class="left">备注：</span>
                <span class="right">{$orderdetail.leword}</span>
            </div>
              <div class="exp-payinfo clearfix">
                <span class="left">送达时间：</span>
                <span class="right">{$orderdetail.exptime}</span>
            </div>
            <div class="exp-payinfo clearfix">
                <span class="left">下单时间：</span>
                <span class="right">{$orderdetail.order_time}</span>
            </div>
            {if $orderdetail.reci_head != ""}
                <div class="exp-payinfo clearfix">
                    <span class="left">发票抬头：</span>
                    <span class="right">{$orderdetail.reci_head}</span>
                </div>
                <div class="exp-payinfo clearfix">
                    <span class="left">发票内容：</span>
                    <span class="right">{$orderdetail.reci_cont}</span>
                </div>
                <div class="exp-payinfo clearfix">
                    <span class="left">发票税额：</span>
                    <span class="right">&yen;{$orderdetail.order_amount}</span>
                </div>
            {/if}
            {if $orderdetail.status == "delivering"}
                <div id="expressapi-cop"><a id="express-confirm" href="javascript:Orders.confirmExpress({$orderdetail.order_id});">确认收货</a></div>
            {/if}
            {if $orderdetail.status == "unpay"}
                <div id="expressapi-cop"><a id="express-confirm" href="javascript:Orders.reWePay({$orderdetail.order_id});">微信支付</a></div>
            {/if}
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
        {include file="../global/footer.tpl"}
    </body>
</html>