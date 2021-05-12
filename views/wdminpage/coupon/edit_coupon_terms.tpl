{include file='../__header.tpl'}
<link href="{$docroot}static/less/jquery.datetimepicker.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
<i id="scriptTag">{$docroot}static/script/Wdmin/coupon/edit_coupon_terms.js</i>
<form style="padding:15px 20px;padding-bottom: 70px;" id="settingFrom">

    <div class="fv2Field clearfix">
        <div class="fv2Left">
            <span>优惠名称</span>
        </div>
        <div class="fv2Right">
            <input type="text" class="gs-input" id="term_name" name="term_name" value="{$coupon_terms.term_name}" placeholder="请输入优惠条件名称" autofocus/>
        </div>
    </div>

    <div class="fv2Field clearfix">
        <div class="fv2Left">
            <span>表名</span>
        </div>
        <div class="fv2Right">
        	<select name="term_table"  id="table">
        	</select>
        </div>
    </div>
    <div class="fv2Field clearfix">
        <div class="fv2Left">
            <span>列名</span>
        </div>
        <div class="fv2Right">
        	<select name="term_column" id="column">
        	</select>
        </div>
    </div>
	<div class="fv2Field clearfix">
        <div class="fv2Left">
            <span>操作符</span>
        </div>
        <div class="fv2Right">
        	<select name="term_operate" id="operate">
        		<option value=">" {if $coupon_terms.term_operate=='>'}selected{/if}>大于</option>
        		<option value=">=" {if $coupon_terms.term_operate=='>='}selected{/if}>大于等于</option>
        		<option value="<" {if $coupon_terms.term_operate=='<'}selected{/if}>小于</option>
        		<option value="<=" {if $coupon_terms.term_operate=='<='}selected{/if}>小于等于</option>
        	</select>
        </div>
    </div>

    <div class="fv2Field clearfix">
        <div class="fv2Left">
            <span>详细描述</span>
        </div>
        <div class="fv2Right">
        	<input type="text" class="gs-input" id="term_name" name="term_detail" value="{$coupon_terms.term_detail}"  autofocus/>
        </div>
    </div>

</form>
<div class="fix_bottom" style="position: fixed">
    <a class="wd-btn primary" id='saveBtn' data-id='{$coupon_terms.id}' href="javascript:;">{if $coupon_terms.id > 0}保存{else}添加{/if}</a>
    <a onclick="history.go(-1)" class="wd-btn default">返回</a>
</div>

{include file='../__footer.tpl'} 