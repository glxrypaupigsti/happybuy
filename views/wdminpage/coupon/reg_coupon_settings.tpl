{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/coupon/award_settings.js</i>
<form id="pd-baseinfo" class='pt58'>
    <p class="Thead">用户注册奖励</p>
    <div style="padding: 22px;" class="clearfix">
        <input id="pd-catimg" name="catimg" type="hidden" value="{$coupon.coupon_cover}" />
        <div class="clearfix">
            <div id="alterProductLeft">
                <!-- 优惠券类型 -->
                <div class="fv2Field clearfix">
                    <div class="fv2Left">
                        <span>奖励类型</span>
                    </div>
                    <div class="fv2Right margin-top">
                        <input type="radio" name="reg_award_type" value="0" {if $regAward.type == 0 }checked{/if}/> 不使用
                        <input type="radio" name="reg_award_type" value="1" {if $regAward.type == 1 }checked{/if}/> 优惠券
                        <input type="radio" name="reg_award_type" value="2" {if $regAward.type == 2 }checked{/if}/> 账户余额
                    </div>
                </div>      
                
                <!-- 奖励余额数-->
                <div class="fv2Field clearfix" style="max-width:100%;" id="reg_award_money">
                    <div class="fv2Left">
                        <span>奖励余额数</span>
                    </div>
                    <div class="fv2Right">
                        <input type="text" class="gs-input-query"  name="reg_award_money" value="{$regAward.value}" autofocus/> <div class='fv2Tip'>请填写数字，单位为分</div>
                    </div>
                </div>
                
                <!-- 应用于商品分类 -->
                <div class="fv2Field clearfix" style="max-width:100%;" id="reg_award_coupon">
                    <div class="fv2Left">
                        <span>奖励优惠券</span>
                    </div>
                    <div class="fv2Right margin-top">
                     {foreach from=$coupons item=coupon}
	                    <input name="reg_award_coupon" type="radio" value="{$coupon.id}"  {if $regAward.value == $coupon.id }checked{/if}/>{$coupon.coupon_name}
	                 {/foreach}
                    </div>
                </div>
			    
            </div>  

        </div>
    </div>

    <p class="Thead">用户订单奖励</p>
    <div style="padding: 22px;" class="clearfix">
        <input id="pd-catimg" name="catimg" type="hidden" value="{$coupon.coupon_cover}" />
        <div class="clearfix">
            <div id="alterProductLeft">
                <!-- 优惠券类型 -->
                <div class="fv2Field clearfix">
                    <div class="fv2Left">
                        <span>奖励类型</span>
                    </div>
                    <div class="fv2Right margin-top">
                        <input type="radio" name="paid_award_type" value="0" {if $paidAward.type == 0 }checked{/if}/> 不使用
                        <input type="radio" name="paid_award_type" value="1" {if $paidAward.type == 1 }checked{/if}/> 优惠券
                        <input type="radio" name="paid_award_type" value="2" {if $paidAward.type == 2 }checked{/if}/> 账户余额
                    </div>
                </div>

                <!-- 奖励余额数-->
                <div class="fv2Field clearfix" style="max-width:100%;" id="paid_award_money">
                    <div class="fv2Left">
                        <span>奖励余额数</span>
                    </div>
                    <div class="fv2Right">
                        <input type="text" class="gs-input-query"  name="paid_award_money" value="{$paidAward.value}" autofocus/>
                        <div class='fv2Tip'>请填写数字，单位为分</div>
                    </div>
                </div>

                <!-- 应用于商品分类 -->
                <div class="fv2Field clearfix" style="max-width:100%;" id="paid_award_coupon">
                    <div class="fv2Left">
                        <span>奖励优惠券</span>
                    </div>
                    <div class="fv2Right margin-top">
                        {foreach from=$coupons item=coupon}
                        <input name="paid_award_coupon" type="radio" value="{$coupon.id}"  {if $paidAward.value == $coupon.id }checked{/if}/>{$coupon.coupon_name}
                        {/foreach}
                    </div>
                </div>

            </div>

        </div>
        
        
        <p class="Thead">是否开启用户券叠加</p>
        <div class="clearfix">
            <div class="fv2Field clearfix">
                <div class="fv2Left margin-top">
                    <input type="radio" name="user_coupon_switch" value="1" {if $userCouponSwitch == 1 }checked{/if}/> 开启
                    <input type="radio" name="user_coupon_switch" value="0" {if $userCouponSwitch == 0 }checked{/if}/> 关闭
                </div>
            </div>

        </div>
    </div>
    
    

</form>
<div class="fix_top fixed">
    <div class='button-set'>
        <a onclick="history.go(-1);" class="button gray">返回</a>
        <a class='button' id="saveBtn"  href="javascript:;">保存</a>
    </div>
</div>
{include file='../__footer.tpl'}