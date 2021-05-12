{include file='../__header.tpl'}
<link href="{$docroot}static/less/jquery.datetimepicker.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
<i id="scriptTag">{$docroot}static/script/Wdmin/coupon/coupon_list.js</i>
<form style="padding:15px 20px;" id="settingFrom">
    <div class="fv2Field-query clearfix">
        <div class="fv2Left">
            <input type="text" class="gs-input-query" id="term_name" name="term_name" value="{$coupon.term_name}" placeholder="优惠条件名称" autofocus/>
        </div>
        <div class="fv2Left">
            <input type="text" class="gs-input-query" id="effective_start" name="effect_start_time" value="{$coupon.term_name}" placeholder="有效期开始时间" autofocus/>
        </div>
        <div class="fv2Right">
        	<input type="text" class="gs-input-query" id="effective_end" name="term_name" value="{$coupon.term_name}" placeholder="有效期结束时间" autofocus/>
           
        </div>
    </div>
    
    <div class="fv2Field-query clearfix">
        <div class="fv2Left">
        	 <select class="select-query" name="coupon_type" id="coupon_type">
            	<option value="-1">请选择优惠券类型</option>
            	<option value="0">商品类</option>
            	<option value="1">订单类</option>
            </select>
           
        </div>
        <div class="fv2Left">
            <select class="select-query" id="is_activated" name="is_activated">
            	<option value="-1">是否激活</option>
            	<option value="0">是</option>
            	<option value="1">否</option>
            </select>
        </div>
        <div class="fv2Left">
            <select class="select-query">
            	<option value="-1">请选择折扣类型</option>
            	<option value="0">固定金额</option>
            	<option value="1">折扣比例</option>
            </select>
        </div>
    </div>
    
    <div class="fv2Field-query clearfix">
        <div class="fv2Left">
        	 <select class="select-query">
            	<option value="-1">是否支持换购</option>
            	<option value="0">是</option>
            	<option value="1">否</option>
            </select>
        </div>
        <div class="fv2Left">
           	<a class="wd-btn danger" id='saveBtn' data-id='{$coupon.id}' href="javascript:;">查询</a>
        </div>
    </div>
   

</form>
<a class="wd-btn primary" style="width:150px" href="{$docroot}?/Coupon/edit_coupon/">添加优惠券</a>


<table class="dTable">
    <thead>
        <tr>
            <th>优惠券名称</th>
            <th>优惠券类型</th>
            <th>折扣类型</th>
            <th>折扣值</th>
            <th>是否激活</th>
            <th>优惠券总量</th>
            <th>剩余数量</th>
            <th>有效期开始时间</th>
            <th>有效期结束时间</th>
            <th>有效期结束时间</th>
            <th>使用条件</th>
            <th>限制条件</th>
            <th>添加时间</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        {section name=oi loop=$orderlist}
            <tr id='order-exp-{$orderlist[oi].order_id}'>
                <td>{$orderlist[oi].serial_number}</td>
                <td>{$orderlist[oi].address.user_name}</td>
                <td>{$orderlist[oi].address.tel_number}</td>
                <td class="prices font12">&yen;{$orderlist[oi].order_amount}</td>
                <td>
                    <a class="various fancybox.ajax" 
                       data-orderid="{$orderlist[oi].order_id}"
                       data-fancybox-type="ajax" 
                       href="{$docroot}?/WdminAjax/loadOrderDetail/id={$orderlist[oi].order_id}">点击查看</a>
                </td>
                <td>{$orderlist[oi].product_count} 件</td>
                <td>{$orderlist[oi].order_time}</td>
                <td class="orderstatus {$orderlist[oi].status}">{$orderlist[oi].statusX}</td>
            </tr>
        {/section}
    </tbody>
</table>
{include file='../__footer.tpl'} 
