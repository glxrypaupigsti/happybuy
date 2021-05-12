{include file='../__header.tpl'}
<link href="{$docroot}static/less/jquery.datetimepicker.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
<i id="scriptTag">{$docroot}static/script/Wdmin/coupon/coupon_list.js</i>
<form style="padding:15px 20px;" id="settingFrom">
    {*
    <div class="fv2Field-query clearfix">
        <div class="fv2Left">
            <input type="text" class="gs-input-query" id="term_name" name="term_name" data-column="0"  placeholder="优惠券名称" autofocus/>
        </div>
        <div class="fv2Left">
            <input type="text" class="gs-input-query" id="effective_start" name="effect_start_time" data-column="11"  placeholder="有效期开始时间" autofocus/>
        </div>
        <div class="fv2Right">
        	<input type="text" class="gs-input-query" id="effective_end" name="term_name" data-column="11" placeholder="有效期结束时间" autofocus/>
           
        </div>
    </div>
    
    <div class="fv2Field-query clearfix">
        <div class="fv2Left">
        	 <select class="select-query" name="coupon_type" id="coupon_type" data-column="12">
            	<option value="-1">请选择优惠券类型</option>
            	<option value="0">商品类</option>
            	<option value="1">订单类</option>
            </select>
           
        </div>
        <div class="fv2Left">
            <select class="select-query" id="is_activated" name="is_activated" data-column="13">
            	<option value="-1">是否激活</option>
            	<option value="0">是</option>
            	<option value="1">否</option>
            </select>
        </div>
        <div class="fv2Left">
            <select class="select-query" id="discount_type" data-column="14">
            	<option value="-1">请选择折扣类型</option>
            	<option value="0">固定金额</option>
            	<option value="1">折扣比例</option>
            </select>
        </div>
    </div>
    *}
    {*
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
	*}
</form>
<a class="wd-btn primary" style="width:150px" href="{$docroot}?/Coupon/edit_coupon/">添加优惠券</a>


<table class="dTable">
    <thead>
        <tr>
            <th>优惠券名称</th>
            <th>优惠券类型</th>
            <th>是否过期</th>
            <th>折扣类型</th>
            <th>折扣值</th>
            <th>是否激活</th>
            {*<th>优惠券总量</th>*}
            <th>剩余数量</th>
            {*<th>有效期开始时间</th>*}
            {*<th>有效期结束时间</th>*}
            
            {*要加入查询的列表，直接隐藏*}
            <th class="hidden">effective_start</th>
            <th class="hidden">effective_end</th>
            <th class="hidden">coupon_type</th>
            <th class="hidden">is_activated</th>
            <th class="hidden">discount_type</th>
            
            
            {*<th>添加时间</th>*}
            <th>操作</th>
        </tr>
    </thead>
    
    <tfoot>
        <tr>
            <th>优惠券名称</th>
            <th>优惠券类型</th>
            <th>是否过期</th>
            <th>折扣类型</th>
            <th>折扣值</th>
            <th>是否激活</th>
            <th>剩余数量</th>
            {*<th>有效期开始时间</th>*}
            {*<th>有效期结束时间</th>*}
            <th class="hidden">effective_start</th>
            <th class="hidden">effective_end</th>
            <th class="hidden">coupon_type</th>
            <th class="hidden">is_activated</th>
            <th class="hidden">discount_type</th>
            <th>操作</th>
        </tr>
    </tfoot>
    
    <tbody>
        {section name=oi loop=$coupon_list}
            <tr id='order-exp-{$coupon_list[oi].id}'>
                <td>{$coupon_list[oi].coupon_name}</td>
                <td>{$coupon_list[oi].coupon_type_desc}</td>
                <td>{$coupon_list[oi].is_expired}</td>
                <td>{$coupon_list[oi].discount_type_desc}</td>
                <td>{$coupon_list[oi].discount_val}</td>
                <td>{if $coupon_list[oi].is_activated == 0}否{else}<span style="color:red;">是</span>{/if}</td>
                {*<td>{if $coupon_list[oi].coupon_stock < 0}不限量{else}{$coupon_list[oi].coupon_stock}{/if}</td>*}
                <td style="color:red;">{if $coupon_list[oi].coupon_stock_left < 0}不限量{else}{$coupon_list[oi].coupon_stock_left}{/if}</td>
                {*<td>{$coupon_list[oi].effective_start_format}</td>*}
                {*<td>{$coupon_list[oi].effective_end_format}</td>*}
                {*<td>{$coupon_list[oi].add_time_format}</td>*}
                
                {*要加入查询的列表，直接隐藏*}
                <td class="hidden">{$coupon_list[oi].effective_start}</td>
                <td class="hidden">{$coupon_list[oi].effective_end}</td>
                <td class="hidden">$coupon_list[oi].coupon_type</td>
                <td class="hidden">$coupon_list[oi].is_activated</td>
                <td class="hidden">$coupon_list[oi].discount_type</td>
                
                <td class="gray font12">
                
                	<a class="od-coupon-pdinfo fancybox.ajax" 
                           data-fancybox-type="ajax" 
                           href="{$docroot}?/Coupon/loadCouponDetail/id={$coupon_list[oi].id}">查看详情</a>
                	
                	{*<a class="lsBtn" href="?/Coupon/edit_coupon/id={$coupon_list[oi].id}">复制券</a>*}
                	{if $coupon_list[oi].is_activated == 0}
                    	<a class="lsBtn" href="?/Coupon/edit_coupon/id={$coupon_list[oi].id}">编辑</a>
                    	<a data-id="{$coupon_list[oi].id}" class="activate_coupon fancybox.ajax" data-fancybox-type="ajax" href="{$docroot}?/FancyPage/fancyHint/id={$coupon_list[oi].id}&title=激活后将不可编辑,确认激活吗">激活</a>
					{/if}
					
					{*只有用户券才有发放的功能*}
					{if $coupon_list[oi].coupon_type == 2 and $coupon_list[oi].coupon_stock_left != 0 and $coupon_list[oi].expired_state == 0}
                    	<a data-id="{$coupon_list[oi].id}" class="give_coupon fancybox.ajax"  href="{$docroot}?/WdminAjax/ajax_customer_select/coupon_id={$coupon_list[oi].id}" data-fancybox-type="ajax">发放</a>
					{/if}               	
                    <a data-id="{$coupon_list[oi].id}" class="coupon_delete del fancybox.ajax" data-fancybox-type="ajax" href="{$docroot}?/FancyPage/fancyHint/id={$coupon_list[oi].id}&title=确认删除吗">删除</a>
                    
                </td>
            </tr>
        {/section}
        
        <div style="display:none">
		     <div id="inline2" style="width:400px; height:120px">
		        <p>
		       		 确定删除吗？
		        </p>
		        <p style="text-align:center"><a href="javascript:;" onclick="$.fancybox.close();">关闭</a></p>
		     </div>
		</div>
    </tbody>
</table>
{include file='../__footer.tpl'} 
