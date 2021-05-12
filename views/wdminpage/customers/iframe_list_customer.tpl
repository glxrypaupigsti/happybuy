{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/customers/iframe_list_customer.js</i>
<table cellpadding=0 cellspacing=0 class="dTable" style="margin-top:45px;">
    <thead >
        <tr >
            <th class='hidden'> </th>
            <th> 
            	<input type="checkbox" id="check_all"/>
            </th>
            <th>头像</th>
            <th>姓名</th>
            <th>性别</th>
            <th>省市</th>
            <th>积分</th>
            <th>订单数量</th>
            <!--
            <th>代理</th>
            -->
            <th>等级</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody style="margin-top:-45px;">
        {section name=ls loop=$list}
            <tr>
                <td class="hidden">{$list[ls].cid}</td>
                <td>
                	 <input type="checkbox" name="check_list" data-id="{$list[ls].cid}" data-openid="{$list[ls].client_wechat_openid}"/>
               	</td>
                <td><img class='ccl-head' src='{if $list[ls].client_head eq ''}{$docroot}static/images/login/profle_1.png{else}{$list[ls].client_head}/64{/if}' /></td>
                <td>{$list[ls].client_name}</td>
                <td>{$list[ls].client_sex}</td>
                <td>{$list[ls].client_province} {$list[ls].client_city}</td>
                <td>{$list[ls].client_credit}</td>
                <td>{if $list[ls].order_count eq 0}{$list[ls].order_count}{else}<a href="{$docroot}?/WdminPage/customer_profile/id={$list[ls].cid}">{$list[ls].order_count}</a>{/if}</td>
               	<!-- 
                <td>{$list[ls].company_name}</td>
                -->
                <td>{$list[ls].levelname}</td>
                <td>
                    <a class="us-edit" href="{$docroot}?/WdminPage/iframe_alter_customer/id={$list[ls].cid}">编辑</a> 
                    <a class="us-view" href="{$docroot}?/WdminPage/customer_profile/id={$list[ls].cid}">查看</a>
                    {*{if !$iscom} / <a class='us-del del' data-id='{$list[ls].cid}' href='javascript:;'>删除</a>{/if}*}
                </td>
            </tr>
        {/section}
    </tbody>
</table>
{include file='../__footer.tpl'} 