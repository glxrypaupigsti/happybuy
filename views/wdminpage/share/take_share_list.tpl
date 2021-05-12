{include file='../__header.tpl'}

<div id="products-stock-list" style="margin-bottom: 54px;">

    <table class="dTable">
        <thead>
            <tr>
                <th class="hidden"></th>
                <th class="od-exp-check"><input class="checkAll" type="checkbox" /></th>
                <th>领取人</th>
                <th>领取时间</th>
                <th>领取金额</th>
            
                <th>分享描述</th>
            </tr>
        </thead>
        <tbody>
        {section name=oi loop=$list}
        <tr id='stock-exp-{$share_list[oi].id}'>
            <td class="hidden">{$share_list[oi].id}</td>
            <td class="od-exp-check"><input class='pd-exp-checks' type="checkbox" data-id='{$list[oi].id}' /></td>
            <td>{$list[oi]['uinfo'].client_name}</td>
  			<td>{$list[oi].add_time|date_format:"%Y-%m-%d"}</td>
  			<td>{$list[oi].coupon_money/100} 元</td>
  		    <td>{$list[oi].des}</td>
        </tr>
    {/section}
        </tbody>
    </table>
</div>

<input type="hidden" id="month-select" value="" />


{include file='../__footer.tpl'} 