{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/charge/edit_charge_card.js</i>
<form style="padding:15px 20px;padding-bottom: 70px;" id="settingFrom">
	<input type="hidden" class="gs-input" name="charge_code" value="{$charge_card.charge_code}" />
    <div class="fv2Field clearfix">
        <div class="fv2Left">
            <span>面额</span>
        </div>
        <div class="fv2Right">
            <input type="text" class="gs-input-query" name="amount" value="{$charge_card.amount}" placeholder="请输入充值卡面额" autofocus {if $charge_card.is_activated==1}readonly{/if}/>&nbsp;分
        </div>
    </div>

    <div class="fv2Field clearfix">
        <div class="fv2Left">
            <span>售价</span>
        </div>
        <div class="fv2Right">
        	<input type="text" class="gs-input-query" name="sale_price" value="{$charge_card.sale_price}" placeholder="请输入充值卡售价" autofocus/>&nbsp;分
        </div>
    </div>
    
    {if $charge_card.id <= 0}
	    <div class="fv2Field clearfix">
	        <div class="fv2Left">
	            <span>数量</span>
	        </div>
	        <div class="fv2Right">
	        	<input type="text" class="gs-input-query" name="num"  placeholder="请输入充值卡数量" autofocus/>
	        </div>
	    </div>
	{/if}

</form>



<div class="fix_bottom" style="position: fixed">
    <a class="wd-btn primary" id='saveBtn' data-id='{$charge_card.id}' href="javascript:;">{if $charge_card.id > 0}保存{else}添加{/if}</a>
    <a onclick="history.go(-1)" class="wd-btn default">返回</a>
</div>

{include file='../__loading.tpl'} 

{include file='../__footer.tpl'} 