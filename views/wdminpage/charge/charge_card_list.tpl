{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/charge/charge_card_list.js</i>
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
<a class="wd-btn primary" style="width:150px" href="{$docroot}?/ChargeManage/edit_charge_card/">批量生成充值卡</a>
{*
<a class="wd-btn primary" style="width:150px" id="mkcard">批量制卡</a>
<a class="wd-btn primary" style="width:150px" id="mkcard_confirm" style="display:none;">确认</a>
*}
<table class="dTable">
	 <thead>
        <tr>
        	<th class="checkbox hidden"><input class="checkAll" type="checkbox" /></th>
        	<th style="display:none;">制卡状态值</th>
            <th style="display:none;">使用状态值</th>
            <th>充值卡id</th>
            <th>充值卡编号</th>
            <th>充值卡密码</th>
            <th>面值</th>
            <th>售价</th>
            <th>已制卡</th>
            <th>已激活</th>
            <th>已使用</th>
            <th>操作</th>
        </tr>
    </thead>
    
    <tfoot>
        <tr>
        	<th class="checkbox hidden"><input class="checkAll" type="checkbox" /></th>
        	<th style="display:none;">制卡状态值</th>
            <th style="display:none;">使用状态值</th>
            <th>充值卡id</th>
            <th>充值卡编号</th>
            <th>充值卡密码</th>
            <th>面值</th>
            <th>售价</th>
            <th>已制卡</th>
            <th>已激活</th>
            <th>已使用</th>
            <th>操作</th>
        </tr>
    </tfoot>
    	
    <tbody>
    	
        {section name=oi loop=$charge_cards_list}
            <tr id='order-exp-{$charge_cards_list[oi].id}'>
            	<td class="checkbox hidden"><input class='pd-exp-checks' type="checkbox" data-id='{$charge_cards_list[oi].id}' /></td>
            	<td style="display:none;">{$charge_cards_list[oi].is_delivered}</td>
                <td style="display:none;">{$charge_cards_list[oi].is_used}</td>
                
                <td>{$charge_cards_list[oi].id}</td>
                <td>{$charge_cards_list[oi].serial_no}</td>
                <td>{$charge_cards_list[oi].charge_code}</td>
                <td>{$charge_cards_list[oi].amount}</td>
                <td>{$charge_cards_list[oi].sale_price}</td>
                <td>{if $charge_cards_list[oi].is_delivered == 1}<span style="color:red;">已制卡</span>{else}未制卡{/if}</td>
                <td>{if $charge_cards_list[oi].is_activated == 1}<span style="color:red;">已激活</span>{else}未激活{/if}</td>
                <td>{if $charge_cards_list[oi].is_used == 1}<span style="color:red;">已使用</span>{else}未使用{/if}</td>
                <td class="gray font12">
                	{if $charge_cards_list[oi].is_used == 0}
                    	<a class="lsBtn" href="?/ChargeManage/edit_charge_card/id={$charge_cards_list[oi].id}">编辑</a>
                	{/if}
                	
                    {if $charge_cards_list[oi].is_delivered == 0}
                    	<a id="delievercard_{$charge_cards_list[oi].id}" class="lsBtn delievercard fancybox.ajax" data-id="{$charge_cards_list[oi].id}" data-fancybox-type="ajax" href="{$docroot}?/FancyPage/fancyHint/id={$charge_cards_list[oi].id}&title=确定制卡吗">制卡</a>
                	{/if}
                	
                	{if $charge_cards_list[oi].is_activated == 0}
                		<a id="activatedcard_{$charge_cards_list[oi].id}" class="lsBtn activatedcard fancybox.ajax" data-id="{$charge_cards_list[oi].id}" data-fancybox-type="ajax" href="{$docroot}?/FancyPage/fancyHint/id={$charge_cards_list[oi].id}&title=激活后将不可编辑，确定激活吗">激活</a>
                	{/if}
                	<a id="chargeCardDel_{$charge_cards_list[oi].id}" class="lsBtn del chargeCardDel fancybox.ajax" data-id="{$charge_cards_list[oi].id}" data-fancybox-type="ajax" href="{$docroot}?/FancyPage/fancyHint/id={$charge_cards_list[oi].id}&title=确定删除吗">删除</a>
                </td>
            </tr>
        {/section}
    </tbody>
</table>
{include file='../__footer.tpl'} 
