{include file='../__header.tpl'}
<link href="{$docroot}static/script/DataTables/media/css/bootstrap.min.css" type="text/css" rel="Stylesheet" />
<link href="{$docroot}static/script/layui/layer/skin/layer.css" type="text/css" rel="Stylesheet" />
<i id="scriptTag">{$docroot}static/script/Wdmin/distribute/distribute_list_test.js</i>

<div style="text-align: right;margin-top:15px;margin-bottom:5px;">
	<span style="font-size: 18px;float:left;margin-left:10px;">配货单总数：<b id="total"></b></span>
    <button class="wd-btn date_btn {if $day == -1}primary{/if}" href='javascript:;' id="yesterday" data-diff="-1" data-status="{$status}">前一天</button>
    <button class="wd-btn date_btn {if $day == 0}primary{/if}" href='javascript:;' id="today" data-diff="0" data-status="{$status}">今天</a>
    <button class="wd-btn date_btn {if $day == 1}primary{/if}" href='javascript:;' id="tomorrow" data-diff="1" data-status="{$status}">后一天</a>
    <button class="wd-btn date_btn {if $day == 7}primary{/if}" href='javascript:;' id="yesterday" data-diff="7" data-status="{$status}">本周</button>
    <button class="wd-btn date_btn {if $day == 30}primary{/if}" href='javascript:;' id="yesterday" data-diff="30" data-status="{$status}">本月</button>
    <button class="wd-btn date_btn {if $day == 366}primary{/if}" href='javascript:;' id="yesterday" data-diff="366" data-status="{$status}">所有</button>
</div>


<ul class="nav nav-tabs">
	<li style="width: 10%" class="active">
		<a data-status='not_delievery'>待配货列表</a>
	</li>
	<li style="width: 10%" >
		<a data-status='delievering'>待配送列表</a>
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
            <th>订单详情</th>
            <th>商品详情</th>
            <th>收货信息</th>
            <th>配送日期信息</th>
            <th>配送方式</th>
            <th>配送人员</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<div id="page" style="margin-top:10px;float:right;"></div>

{*laytpl模版展示数据区域*}
<script id="data_render" type="text/html">
	{literal}
		{{# for(var i = 0, len = d.list.length; i < len; i++){ }}
		    <tr id='order-exp-{{ d.list[i].id }}'>
		        <td>{{ d.list[i].id }}</td>
		        <td>
					{{# if(d.list[i].order_data){ }}
						<p>订单编号：{{ d.list[i].order_serial_no }}</p>
						<p>订单总额：￥{{ d.list[i].order_data.order_amount }}</p>
						{{# if(d.list[i].order_data.discount_amount > 0){ }}
							<p style="color:red; ">优惠金额：￥{{ d.list[i].order_data.discount_amount}}</p>
						{{#  } }}
						{{# if(d.list[i].order_data.online_amount > 0){ }}
							<p>微信支付金额：￥{{ d.list[i].order_data.online_amount }}</p>
						{{#  } }}
						{{# if(d.list[i].order_data.balance_amount > 0){ }}
							<p>余额支付金额：￥{{ d.list[i].order_data.balance_amount }}</p>
						{{#  } }}
						<p style="color: red;">备注：{{ d.list[i].order_data.notes }}</p>
					{{# }else{ }}
						<p style="color:red; ">订单数据不存在</p>
					{{#  } }}

				</td>
		        
		        <td>
		        	{{# for(var j = 0, len2 = d.list[i].products.length; j < len2; j++){ }}
	                	<p>{{ d.list[i].products[j].product_name }}<span style='color:red'>x{{ d.list[i].products[j].product_count }}</span></p>
	                {{# } }}
                </td>
                <td>
					{{# if(d.list[i].address){ }}
						<p>收货人：{{ d.list[i].address.user_name }}</p>
						<p>电话：{{ d.list[i].address.phone }}</p>
						<p>地址：{{ d.list[i].address.province }}{{ d.list[i].address.city }}{{ d.list[i].address.address }}</p>
					{{#  } else{ }}
						<p　style="color:red; ">地址信息不存在</p>
					{{#  } }}
				</td>
                <td>{{ d.list[i].exp_time }}</td>
                <td>{{ d.list[i].express_code }}</td>
                <td>{{ d.list[i].courier }}</td>
                <td class="gray font12">
					{{# if(d.status == 'not_delievery'){ }}                	
            			<a data-id="{{ d.list[i].id }}" class="begin_to_make fancybox.ajax" data-fancybox-type="ajax" href="?/FancyPage/fancyHint/id={{ d.list[i].id }}&title=确认开始制作吗">开始制作</a>
                	{{# } }}
                	
                	{{# if(d.status == 'delievering'){ }} 
                		<a data-id="{{ d.list[i].id }}" class="delievering_distribute fancybox.ajax" data-fancybox-type="ajax" href="?/Distribute/ajax_delievery_order/id={{ d.list[i].id }}&status={{ 1 }}">发货</a>
					{{# } }}
					
					{{# if(d.status == 'delievering' || d.status == 'not_delievery'){ }} 
						<a data-id="{{ d.list[i].id }}" class="cancel_distribute fancybox.ajax" data-fancybox-type="ajax" href="?/Distribute/reset_express_time/id={{ d.list[i].id }}&status={{ 1 }}">取消</a>
                	{{# } }}
                	
                	{{# if(d.status == 'delievered'){ }} 
	                	<a data-id="{{ d.list[i].id }}" class="reached fancybox.ajax" data-fancybox-type="ajax" href="?/FancyPage/fancyHint/id={{ d.list[i].id }}&title=确认已经送达了吗">外送完成</a>
	                	<a data-id="{{ d.list[i].id }}" class="not_reach fancybox.ajax" data-fancybox-type="ajax" href="?/Distribute/reset_express_time/id={{ d.list[i].id }}&status={{ 1 }}">未送达</a>
                	{{# } }}
                </td>
		        
		    </tr>
		{{# } }}
	{/literal}
</script>



{include file='../__footer.tpl'} 
