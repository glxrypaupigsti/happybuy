{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/coupon/user_coupon_list.js</i>

<div class="button-set">
    <a class="button del" href="javascript:;" id="batch_del">批量删除</a>
</div>

<table class="dTable">
    <thead>
        <tr>
        	<th>
        		<input type="checkbox" id="check_all">
        	</th>
            <th>用户名</th>
            <th>优惠券编号</th>
            <th>优惠券名称</th>
            <th>是否使用</th>
            <th>是否过期</th>
            <th>获取时间</th>
            <th>获取来源</th>
            <th>操作</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
        	<th><input type="checkbox" id="check_all">/th>
            <th>用户名</th>
            <th>优惠券编号</th>
            <th>优惠券名称</th>
            <th>是否使用</th>
            <th>是否过期</th>
            <th>获取时间</th>
            <th>获取来源</th>
            <th>操作</th>
        </tr>
    </tfoot>
    
    <tbody>
        {section name=oi loop=$user_coupons}
            <tr id='order-exp-{$user_coupons[oi].id}'>
                <td><input type="checkbox" name="check_list" data-id="{$user_coupons[oi].id}"></td>
                <td>{$user_coupons[oi].user_info.client_nickname}</td>
                <td>{$user_coupons[oi].coupon_id}</td>
                <td>{$user_coupons[oi].coupon_name}</td>
                <td>{$user_coupons[oi].is_used_desc}</td>
                <td>{$user_coupons[oi].expire_desc}</td>
                <td>{date("Y-m-d",{$user_coupons[oi].add_time})}</td>
                <td>{$user_coupons[oi].come_from_desc}</td>
                <td class="gray font12">
                	<a  class="lsBtn del userCouponDel fancybox.ajax" data-id="{$user_coupons[oi].id}" data-fancybox-type="ajax" href="{$docroot}?/FancyPage/fancyHint/id={$user_coupons[oi].id}&title=要删除该条记录吗？">删除</a>
                </td>
                
            </tr>
        {/section}
    </tbody>
</table>
<div id="loading" style="display:none" />
{include file='../__footer.tpl'} 
