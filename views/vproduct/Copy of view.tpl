<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv=Content-Type content="text/html;charset=utf-8" />
        <title>{$title} - {$settings.shopname}</title>
        <link href="favicon.ico" rel="Shortcut Icon" />
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
        <meta name="format-detection" content="telephone=no" />
        <link href="{$docroot}static/css/wshop_vproduct.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
    </head>
    <body>

        {include file="../global/top_nav.tpl"}

        {include file="../global/ad/global_top.tpl"}

        <input type="hidden" value="{$comid}" id="comid" />
        <input type="hidden" value="{$productInfo.product_name}" id="sharetitle" />
        <input type="hidden" value="{$productid}" id="iproductId" />
        <input type="hidden" value="{$productInfo.market_price|string_format:"%.2f"}" id="mprice" />

        <!-- touchslider -->
        {strip}
            <div class="touchslider" id="touchslider">
                <div class="touchslider-viewport">
                    {section name=ii loop=$images}
                        <div class="touchslider-item">
                            <img style="max-width: 100%;" data-big="{if $config.usecdn}{$config.imagesPrefix}product_hpic/{$images[ii].image_path}{else}{$config.productPicLink}{$images[ii].image_path}{/if}" 
                                 src="{if $config.usecdn}{$config.imagesPrefix}product_hpic/{$images[ii].image_path}_x500{else}static/Thumbnail/?w=500&h=500&p={$config.productPicLink}{$images[ii].image_path}{/if}" />
                        </div>
                    {/section}
                </div>
                <div class="touchslider-nav">
                    {section name=ii loop=$images}
                        <span class="touchslider-nav-item"></span>
                    {/section}
                </div>
            </div>
            <script type="text/javascript">document.querySelector('#touchslider').style.height = document.documentElement.clientWidth + 'px';</script>
        {/strip}
        <!-- touchslider -->

        <div id="container">
            {if $productInfo.product_online eq 1}
                <div class="uc-add-like{if $isLiked} fill{/if}">??????</div>
                <p class="vpd-title" style='height:auto;'>
                    {$productInfo.product_name}
                </p>
                <p class="vpd-subtitle">{$productInfo.product_subtitle}</p>
                <!-- ???????????? -->
                {if $productInfo.product_prom eq 1}
                    <dl class="pd-dsc clearfix">
                        <dt>????????????</dt>
                        <dt id="pd-market-price" class="prices marketPrice">&yen;{$productInfo.market_price|string_format:"%.2f"}</dt>
                    </dl>
                    <dl class="pd-dsc clearfix">
                        <dt>????????????</dt>
                        <dt class="prices marketPrice" id="pd-market-price2">&yen;{($productInfo.sale_prices)|string_format:"%.2f"}</dt>
                    </dl>
                    <dl class="pd-dsc clearfix">
                        <dt>????????????</dt>
                        <dt class="prices" id="pd-sale-price">
                        &yen;{($productInfo.sale_prices * ($productInfo.product_prom_discount / 100))|string_format:"%.2f"}
                        </dt>
                    </dl>
                    <dl class="pd-dsc clearfix">
                        <dt>???????????????</dt>
                        <dt>{$productInfo.product_prom_limitdate}</dt>
                    </dl>
                {else}
                    {if $productInfo.sale_prices ne '0.00'}
                    
                    	<!--
                        {if $productInfo.market_price > 0}
                            <dl class="pd-dsc clearfix">
                                <dt>????????????</dt>
                                <dt id="pd-market-price" class="prices marketPrice">&yen;{$productInfo.market_price|string_format:"%.2f"}</dt>
                            </dl>
                        {/if}
                        
                        -->
                        
                       
                         <dl class="pd-dsc clearfix">
                                <dt>?????????</dt>
                                <dt class="prices" id="pd-sale-price">
                                &yen;{($productInfo.sale_prices)|string_format:"%.2f"}
                                </dt>
                         </dl>
                       <!--
                        {if $discount eq 1}
                            <dl class="pd-dsc clearfix">
                                <dt>????????????</dt>
                                <dt class="prices" id="pd-sale-price">
                                &yen;{($productInfo.sale_prices * $discount)|string_format:"%.2f"}
                                </dt>
                            </dl>
                        {else}
                            <dl class="pd-dsc clearfix">
                                <dt>????????????</dt>
                                <dt class="prices marketPrice" id="pd-market-price2">&yen;{($productInfo.sale_prices)|string_format:"%.2f"}</dt>
                            </dl>
                            <dl class="pd-dsc clearfix">
                                <dt>????????????</dt>
                                <dt class="prices" id="pd-sale-price">&yen;{($productInfo.sale_prices * $discount)|string_format:"%.2f"}</dt>
                            </dl>
                        {/if}
                        
                        -->
                    {/if}
                    {if $prominfo}
                        <dl class="pd-dsc clearfix">
                            <dt>?????????</dt>
                            <dt class="prominfo">
                            <b>??????</b>???{$prominfo.req_amount}???{$prominfo.dis_amount}
                            </dt>
                        </dl>
                    {/if}
                {/if}

                <!-- ???????????? -->
                {if $specsDistinct.a.spd1name neq ''}
                    <div>
                        {if $productInfo.product_prom eq 1}
                            {foreach from=$specs item=sp}
                                <input type='hidden' class='spec-hashs' value='{$sp.spec_det_id1}-{$sp.spec_det_id2}' data-instock='{$sp.instock}' data-price='{($sp.sale_price * ($productInfo.product_prom_discount / 100))|string_format:"%.2f"}' data-market-price="{$sp.market_price}" data-id="{$sp.id}" />
                            {/foreach}
                        {else}
                            {foreach from=$specs item=sp}
                                <input type='hidden' class='spec-hashs' value='{$sp.spec_det_id1}-{$sp.spec_det_id2}' data-instock='{$sp.instock}' data-price='{($sp.sale_price * $discount)|string_format:"%.2f"}' data-market-price="{$sp.market_price}" data-id="{$sp.id}" />
                            {/foreach}
                        {/if}
                    </div>
                    <dl class="pd-dsc clearfix" id="pd-dsc1" style='margin-top:8px;'>
                        <dt class="left">{$specs[0].spd1name}???</dt>
                        <dt>
                        <div class='pd-spec-dets clearfix'>
                            {foreach from=$specsDistinct.a.sps item=sp name=sploop}
                                <div class='pd-spec-sx enable' href='javascript:;' data-det-id='{$sp.spec_det_id1}'>{$sp.det_name1}</div>
                            {/foreach}
                        </div>
                        </dt>
                    </dl>
                {/if}
                {if $specsDistinct.b.spd2name neq ''}
                    <dl class="pd-dsc clearfix" id="pd-dsc2">
                        <dt class="left">{$specs[0].spd2name}???</dt>
                        <dt>
                        <div class='pd-spec-dets clearfix'>
                            {foreach from=$specsDistinct.b.sps item=sp name=sploop}
                                <div class='pd-spec-sx enable' href='javascript:;' data-det-id='{$sp.spec_det_id2}'>{$sp.det_name2}</div>
                            {/foreach}
                        </div>
                        </dt>
                    </dl>
                {/if}
                <!-- ??????????????? -->
                <dl class="pd-dsc clearfix">
                    <dt>????????????</dt>
                    <dt id="pd-market-instock">{$specs[0].instock}</dt>
                </dl>
            {else}
                {*??????????????????*}
                <div id='productOffline'>??????????????????????????????</div>
            {/if}
        </div>

        {if $productInfo.product_online eq 1}
            <header class="Thead" id="vpd-detail-header">????????????</header>
            <div id="vpd-content" class="notload">????????????????????????</div>
        {/if}

        <!-- ???????????? 
        {if $slist}
            <header class="Thead">????????????</header>
            <div id="pd-recoment">
                <div class='pd-box clearfix'>
                    {foreach from=$slist item=sl}
                        <a class="slist-item" href="{$docroot}?/vProduct/view/id={$sl.product_id}&showwxpaytitle=1">
                            <div class='pd-box-inner'>
                                <img src="{if $config.usecdn}{$config.imagesPrefix}product_hpic/{$sl.catimg}_x120{else}static/Thumbnail/?w=120&h=120&p={$config.productPicLink}{$sl.catimg}{/if}" alt='{$sl.product_name}' />
                                <p class='Elipsis'>{$sl.product_name}</p>
                                {*                                {if $sl.sale_prices ne '0.00'}
                                <span class="prices">&yen;{$sl.sale_prices * $entDiscount}</span>
                                {/if}*}
                            </div>
                        </a>
                    {/foreach}
                </div>
            </div>
        {/if}
        -->

        <!-- ??????????????? -->
        <div id="appCartWrap" class="clearfix">
            {if $productInfo.product_online eq 1}
                {if $productInfo.product_prom eq 0}<a class="button" id="addcart-button" data-prom="{$productInfo.product_prom}" data-add="1">???????????????</a>{/if}
                <a class="button" id="buy-button" data-prom="{$productInfo.product_prom}" data-add="0" {if $productInfo.product_prom eq 1}style="width: 99%;"{/if}>????????????</a>
            {else}
                <a class="button disable">?????????</a>
            {/if}
          	 <a id="toCart" href="?/Cart/cart/"><i>0</i></a>
        </div>

        {include file="../global/copyright.tpl"}
        <script src="static/script/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
        {strip}
            <script type="text/javascript">
                var productId = {$productid};
                var comId = {if $comid}{$comid}{else}0{/if};
                    wx.config({
                        debug: false,
                        appId: '{$signPackage.appId}',
                        timestamp: {if $signPackage.timestamp}{$signPackage.timestamp}{else}0{/if},
                                nonceStr: '{$signPackage.nonceStr}',
                                signature: '{$signPackage.signature}',
                                jsApiList: ['previewImage']
                            });
                            wx.onMenuShareTimeline({
                                title: '{$productInfo.product_name}',
                                link: '',
                                imgUrl: '{$config.imagesPrefix}product_hpic /{$productInfo.catimg}',
                                        success: function () {
                                            pvShareCallback(productId, comId);
                                        }
                                    });
                                    wx.onMenuShareAppMessage({
                                        title: '{$productInfo.product_name}',
                                        desc: '{$productInfo.product_name}',
                                        link: "http://" + window.location.host + "/?/vProduct/view/id=" + productId + "&showwxpaytitle=1" + comId,
                                        imgUrl: '{$config.imagesPrefix}product_hpic /{$productInfo.catimg}',
                                                success: function () {
                                                    pvShareCallback(productId, comId);
                                                }
                                            });
                                            function pvShareCallback(productId, comId) {
                                                if (comId > 0) {
                                                    $.post("http://" + window.location.host + "/?/Company/addComSpread/", {
                                                        productId: productId,
                                                        comId: comId
                                                    }, function (res) {
                                                    });
                                                }
                                                $.post("http://" + window.location.host + "/?/vProduct/ajaxUpProductShare/id=" + productId);
                                            }
            </script>
        {/strip}
        <script data-main="{$docroot}static/script/Wshop/shop_vproduct.js" src="{$docroot}static/script/require.min.js"></script>
    </body>
</html>
