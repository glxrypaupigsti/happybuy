{include file='../__header.tpl'}
<link href="{$docroot}static/script/DataTables/media/css/bootstrap.min.css" type="text/css" rel="Stylesheet" />
<link href="{$docroot}static/less/jquery.datetimepicker.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
<link href="{$docroot}static/script/layui/layer/skin/layer.css/?v={$cssversion}" type="text/css" rel="Stylesheet" />
<i id="scriptTag">{$docroot}static/script/Wdmin/distribute/distribute_list.js</i>
<div id="iframe_loading" style="top:0;display:none;"></div>
<div style="text-align: right;margin-top:5px;margin-bottom:5px;">
	<span style="font-size: 18px;float:left;"><strong>配货单总数：{$total}</strong></span>
    <button class="wd-btn date_btn {if $day == -1}primary{/if}" href='javascript:;' id="yesterday" data-diff="-1" data-status="{$status}">前一天</button>
    <button class="wd-btn date_btn {if $day == 0}primary{/if}" href='javascript:;' id="today" data-diff="0" data-status="{$status}">今天</a>
    <button class="wd-btn date_btn {if $day == 1}primary{/if}" href='javascript:;' id="tomorrow" data-diff="1" data-status="{$status}">后一天</a>
</div>
<ul class="nav nav-tabs">
	<li style="width: 10%" class="active">
		<a data-status='not_delievery'>待配货列表</a>
	</li>
	<li style="width: 10%" >
		<a data-status='delievering'>配送中列表</a>
	</li>
	<li style="width: 10%" >
		<a data-status='delievered'>已发货列表</a>
	</li>
	<li style="width: 10%" >
		<a data-status='reached'>已送达列表</a>
	</li>
	<li style="width: 10%" >
		<a data-status='not_reached'>未送达列表</a>
	</li>
	<li style="width: 10%">
		<a data-status='cancel'>已取消列表</a>
	</li>
	<li style="width: 10%">
		<a data-status='all'>全部</a>
	</li>
</ul>

<input type="hidden" value="{$day}" id="day">
<input type="hidden" value="{$status}" id="status">
<table class="dTable" style="margin-top: 0px;">
    <thead>
        <tr>
            <th>配送单id</th>
            <th>订单序列号</th>
            <th>商品详情</th>
            <th>收货信息</th>
            <th>配送日期信息</th>
            <th>配送方式</th>
            <th>配送人员</th>
            {if $status eq 'not_delievery' or $status eq 'delievering' or $status eq 'delievered'}
            <th>操作</th>
            {/if}
        </tr>
    </thead>
    
    <tfoot>
         <tr>
            <th>配送单id</th>
            <th>订单序列号</th>
            <th>商品详情</th>
            <th>收货信息</th>
            <th>配送日期信息</th>
            <th>配送方式</th>
            <th>配送人员</th>
            {if $status eq 'not_delievery' or $status eq 'delievering' or $status eq 'delievered'}
            <th>操作</th>
            {/if}
        </tr>
    </tfoot>
    
    <tbody>
        {section name=oi loop=$distribute_list}
            <tr id='order-exp-{$distribute_list[oi].id}'>
                <td>{$distribute_list[oi].id}</td>
                <td>{$distribute_list[oi].order_serial_no}</td>
                <td>
	                {foreach $distribute_list[oi].products as $key => $val}
	                	<p>{$val.product_name}<span style='color:red'>x{$val.product_count}</span></p>
	                {/foreach}
                </td>
                <td>
                	<p>收货人：{$distribute_list[oi].address.user_name}</p>
                	<p>电话：{$distribute_list[oi].address.phone}</p>
                	<p>地址：{$distribute_list[oi].address.province}{$distribute_list[oi].address.city}{$distribute_list[oi].address.address}</p>
                	{if $distribute_list[oi].order_data.notes}
                        <p style="color:red;">备注：{$distribute_list[oi].order_data.notes}</p>
                    {/if}
                </td>
                <td>{$distribute_list[oi].exp_time}</td>
                <td>{$distribute_list[oi].express_code}</td>
                <td>{$distribute_list[oi].courier}</td>
                {if $status eq 'not_delievery' or $status eq 'delievering' or $status eq 'delievered'}
                <td class="gray font12">
                	{if $status eq 'not_delievery'}
                		<a data-id="{$distribute_list[oi].id}" class="begin_to_make fancybox.ajax" data-fancybox-type="ajax" href="{$docroot}?/FancyPage/fancyHint/id={$distribute_list[oi].id}&title=确认开始制作吗">开始制作</a>
                	
					{/if}
                	{if $status eq 'delievering'}
                    	<a data-id="{$distribute_list[oi].id}" class="delievering_distribute fancybox.ajax" data-fancybox-type="ajax" href="{$docroot}?/Distribute/ajax_delievery_order/id={$distribute_list[oi].id}&status={$status}">发货</a>
					{/if}
					
					{if $status eq 'delievering' or $status eq 'not_delievery'}
						<a data-id="{$distribute_list[oi].id}" class="cancel_distribute fancybox.ajax" data-fancybox-type="ajax" href="{$docroot}?/Distribute/reset_express_time/id={$distribute_list[oi].id}&status={$status}">取消</a>
					{/if}
					
					
					{if $status eq 'delievered'}
                    	<a data-id="{$distribute_list[oi].id}" class="reached fancybox.ajax" data-fancybox-type="ajax" href="{$docroot}?/FancyPage/fancyHint/id={$distribute_list[oi].id}&title=确认已经送达了吗">外送完成</a>
                    	<a data-id="{$distribute_list[oi].id}" class="not_reach fancybox.ajax" data-fancybox-type="ajax" href="{$docroot}?/Distribute/reset_express_time/id={$distribute_list[oi].id}&status={$status}">未送达</a>
					{/if}
                </td>
                {/if}
            </tr>
        {/section}
    </tbody>
</table>
{include file='../__footer.tpl'} 
