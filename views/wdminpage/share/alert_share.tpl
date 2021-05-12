{include file="../__header.tpl"}
<link href="/static/css/base_pagination.css" type="text/css" rel="Stylesheet" />
<i id="scriptTag">page_alert_share</i>

<div class="fix_top fixed">
    <div class='button-set'>
        <a onclick="window.history.back();" class="button gray">返回</a>
        <a class='button' id="add_share"  href="javascript:;">保存</a>
    </div>
</div>

<form id="share-form" class='pt58'>
    <input type="hidden" name="sku_name" value="{$default_sku_name}" >
    <div style="padding: 22px;" class="clearfix">
        <div id="alterProductLeft">
         

            <div class="fv2Field clearfix">
                     <div class="fv2Left">
                            <span>分享优惠设置</span>
                        </div>
                        <div class="fv2Right margin-top">
                            {foreach from=$coupons item=coupon}
                            <input name="paid_award_coupon" type="radio" value="{$coupon.id}"  {if $couponid == $coupon.id }checked{/if}/>{$coupon.coupon_name}
                            {/foreach}
                        </div>
            </div>

            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>分享次数</span>
                </div>
                <div class="fv2Right">
                    <input type="text" class="gs-input" placeholder="超过此次数 分享就无效" name="share_count" value="{$share_count}" autofocus/>
                </div>
            </div>

            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>分享金额百分比</span>
                </div>
                <div class="fv2Right">
                    <input type="text" class="gs-input" placeholder="范围0~1" name="percents" value="{$percents}" id="pd-form-title" autofocus/>
                </div>
            </div>

        </div>
    </div>
</form>

{include file="../__footer.tpl"}