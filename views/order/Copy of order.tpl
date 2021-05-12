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

        {include file="../global/top_nav.tpl"}

        {include file="../global/ad/global_top.tpl"}

        <input type="hidden" id="addressId" value="{$address.id}" />

        <header class="Thead">收货信息</header>

        <!-- 收货地址 -->
        <div id="express-bar"></div>
        <div id="express_address" href="javascript:;">
          
          {if $address}

            <div class="express-person-info clearfix">
                <div class="express-person-name">

                    <span id="express-name"></span><span id="express-person-phone">{$address.user_name}</span>
                </div>
            </div>
            <div class="express-address-info">
                <span id="express-address">{$address.city},{$address.address}</span>
            </div>
            {else}
              <div id="wrp-btn"><a href="?/UserAddress/edit_address/orderId={$orderInfo.order_id}">点击填写配送地址</a></div>
             {/if}
        </div>

        <header class="Thead">订单信息</header>

        <div id="orderDetailsWrapper" data-minheight="68px">
        
        {strip}{section name=i loop=$product_list}
        <section class="cartListWrap clearfix" id="cartsec{$product_list[i].product_id}">
         
            <img alt="{$product_list[i].product_name}" width="100" height="100" src="{if $config.usecdn}{$config.imagesPrefix}product_hpic/{$product_list[i].catimg}_x120{else}static/Thumbnail/?w=120&h=120&p={$config.productPicLink}{$product_list[i].catimg}{/if}" />
            <div class="cartListDesc">
                <p class="title">
                    {$product_list[i].product_name}
                </p>
               
                <p class="count">
                    <span class="spec Elipsis">
                        {if $product_list[i]['pinfo'].det_name1} 
                            [{$product_list[i]['pinfo'].det_name1} {$product_list[i]['pinfo'].det_name2}]
                        {else}
                            默认规格
                        {/if}
                    </span>
                    <span class="dprice prices" 
                          data-expfee="{$product_list[i]['pinfo'].product_expfee}"
                          data-price="{$product_list[i]['pinfo'].sale_prices}"
                          data-weight="{if $product_list[i].product_weight neq ''}{$product_list[i]['pinfo'].product_weight}{else}0{/if}" 
                          data-count="{$product_list[i].product_count}">&yen; {$product_list[i]['pinfo'].sale_prices}
                    </span>
                </p>
                <dl class="pd-dsc clearfix">
                    <dt class="productCount clearfix">

                    <span class="productCountNum"><input  disabled="disabled" type='tel' data-mhash="p{$product_list[i].product_id}m{$product_list[i].spid}" data-prom-limit="{$product_list[i].product_prom_limit}" value='{$product_list[i].product_quantity}' class="dcount productCountNumi" /></span>
             
                    </dt>
                </dl>
                </p>
            </div>
        </section>
{/section}{/strip}
        
        </div>


       
                <header class="Thead">发票信息</header>
                <div id="userReciInfo">
                <input type="text" id="reciTex" placeholder="发票内容" />
                    
         </div>


        <section class="orderopt">
            <span class="label">配送时间</span>
            <span id = "time" class="value">随时可以</span>
            <input type="date" id="exptime" name="date" />
        </section>

         <section class="orderopt">
            <a href="{$docroot}?/Coupon/coupon_list">
            <span class="label">优惠券</span>
             {if $coupons}
                 <span class="value">{count($coupons)}</span>
               {else}
                <span class="value">暂无优惠券</span>
                {/if}
            </a>
              
        </section>

         {if $useCoupons}
           <section class="orderopt">
            <span style="color:red">{$useCoupons.coupon_name}</span>
             <input type="hidden" id="couponId" value="{$useCoupons.id}" />
            
           </section>
        {/if}


            <!-- 订单总额 -->
        <div id="orderSummay" >
            <div >
                 余额 <b id="cart-balance-pay">{$userInfo.balance}
            </div>
            <div>
                运费 : <b class="prices font13" id="order_yunfei">&yen;0.00</b>
            </div>
   
          
            <div>
                总计 : <b class="prices" id="order_amount">&yen;{$amount}</b>
            </div>
             <div>
           
               需支付 :&yen;<b class="prices" id="pay_amount">{$amount}</b>
             
            </div>
        </div>


        <div class="button green" id="wechat-payment-btn">提交订单</div>
        <div class="button green" style='display: none'  id="money-payment-btn">余额支付</div>
          
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
        <script type="text/javascript">
           
        </script>

        <script data-main="{$docroot}static/script/Wshop/order.js?v={$smarty.now}" src="{$docroot}static/script/require.min.js"></script> 
    </body>
</html>