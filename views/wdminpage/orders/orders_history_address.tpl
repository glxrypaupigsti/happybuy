{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/orders/orders_history_address.js</i>
<table class='dTable'  style="margin-top:45px;">
    <thead>
        <tr>
            <th>姓名</th>
            <th>电话</th>
            <th style='padding-right: 10px;'>地址</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$address item=addr}
            <tr>
                <td>{$addr.user_name}</td>
                <td>{$addr.phone}</td>
                <td style='padding-right: 10px;'>{$addr.province}{$addr.city}{$addr.address}</td>
            </tr>
        {/foreach}
    </tbody>
</table>
{include file='../__footer.tpl'} 