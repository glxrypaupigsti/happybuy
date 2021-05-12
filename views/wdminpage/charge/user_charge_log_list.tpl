{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/charge/user_charge_log_list.js</i>
<form style="padding:15px 20px;" id="settingFrom">
    {*
    <div class="fv2Field-query clearfix">
        <div class="fv2Left">
            <select class="select-query" name="is_used" id="is_used" data-column="1">
            	<option value="-1">请选择是否使用</option>
            	<option value="1">是</option>
            	<option value="0">否</option>
            </select>
        </div>
        <div class="fv2Left">
            <select class="select-query" name="is_delivered" id="is_delivered" data-column="0">
            	<option value="-1">是否以发出制卡</option>
            	<option value="1">是</option>
            	<option value="0">否</option>
            </select>
        </div>
        <div class="fv2Left">
        	<input type="text" class="gs-input-query" id="amount" name="amount" placeholder="充值卡面值(>=)" autofocus data-column="4"/>
        </div>
    </div>
    *}
</form>

<table class="dTable">
    <thead>
        <tr>
            <th>用户名</th>
            <th>支付方式</th>
            <th>充值金额(元)</th>
            <th>实际支付金额(元)</th>
            <th>充值时间</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        {section name=oi loop=$user_charge_log_list}
            <tr id='order-exp-{$user_charge_log_list[oi].id}'>
                <td>{$user_charge_log_list[oi].user_name}</td>
                <td>{$user_charge_log_list[oi].charge_type_desc}</td>
                <td>{$user_charge_log_list[oi].amount/100}</td>
                <td>{$user_charge_log_list[oi].pay_amount/100}</td>
                <td>{$user_charge_log_list[oi].charge_time_format}</td>
                <td class="gray font12">
                	<a id="delievercard_{$user_charge_log_list[oi].id}" class="lsBtn del chargeLogDel fancybox.ajax" data-id="{$user_charge_log_list[oi].id}" data-fancybox-type="ajax" href="{$docroot}?/FancyPage/fancyHint/id={$user_charge_log_list[oi].id}&title=要删除该充值记录吗？">删除</a>
                </td>
            </tr>
        {/section}
    </tbody>
</table>
{include file='../__footer.tpl'} 
