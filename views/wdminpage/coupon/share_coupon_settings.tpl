{include file='../__header.tpl'}

<i id="scriptTag">{$docroot}static/script/Wdmin/coupon/share_coupon_settings.js</i>

<input type="hidden" value="{$smarty.server.HTTP_REFERER}" id="http_referer" /> 
<div style="margin-bottom: 60px;padding: 10px 20px;">

	<input type="hidden" id="expcompany" value="{$settings.user_share_coupons}" />
    <p class="Thead">设置分享的优惠券(仅限于已激活的用户券)</p>

    <div class="fv2Field clearfix" style="max-width: 100%;">
        <div class="fv2Right" style="margin-left: 0;">
            <div class="clearfix">
                {foreach from=$coupons item=coupon}
                    <a class="expitem" data-k="{$coupon.id}" href="javascript:;">{$coupon.coupon_name}</a>
                {/foreach}
            </div>
        </div>
    </div>


</div>

<div class="fix_bottom fixed">
    <a class="wd-btn primary" id='saveBtn' style="width:150px" href="javascript:;">保存设置</a>
</div>

{include file='../__footer.tpl'}