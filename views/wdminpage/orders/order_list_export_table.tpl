<table class="dTable">
    <thead>
        <tr>
            <th class="hidden"></th>
            <th>收货人</th>
            <th>身份证(备注中提取)</th>
            <th>收货地址</th>
            <th>收货电话</th>
            <th>商品名称</th>
            <th>商品编号</th>
            <th>单价</th>
            <th>数量</th>
            <th>总价</th>
            <th>运费</th>
            <th>下单时间</th>
            <th style="padding-right: 20px;">目标仓库</th>
        </tr>
    </thead>
    <tbody>
        {section name=oi loop=$orderlist}
            <tr>
                <td class="hidden"><input type='hidden' name='orderid' value='{$orderlist[oi].serial_number}' /></td>
                <td><input name='name' type="text" style='width: 45px;text-align: center;' value="{$orderlist[oi].user_name}" /></td>
                <td><input name='pids' type="text" style='width: 140px' value="{$orderlist[oi].ids}" /></td>
                <td><input name='addr' type="text" {if !$fullwidth}style='width: 140px'{else}style="width:88%"{/if} value="{$orderlist[oi].addr}" /></td>
                <td><input name='tels' type="text" style='width: 90px;text-align: center;' value="{$orderlist[oi].tel_number}" /></td>
                <td><input name='pdname' type="text" style='width: 80px' value="{$orderlist[oi].pdname}" /></td>
                <td><input name='pdcode' type="text" style='width: 80px' value="{$orderlist[oi].product_code}" /></td>
                <td class="prices font12"><input name='pric_sig' class='pricSig' rel='{$smarty.section.oi.index}' data-count='{$orderlist[oi].product_count}' type="text" onclick='this.select()' style='width: 60px;text-align: center;' value="{$orderlist[oi].product_discount_price}" /></td>
                <td><input type='hidden' name='pcount' value='{$orderlist[oi].product_count}' />{$orderlist[oi].product_count}</td>
                <td class="prices font12"><input name='pric_tot' type="text" onclick='this.select()' style='width: 60px;text-align: center;' value="{$orderlist[oi].total}" id='pricTotal{$smarty.section.oi.index}' /></td>
                <td><input name='yunfei' type="text" style='width: 35px;text-align: center;' onclick='this.select()' value="{if $orderlist[oi].order_yunfei > 0}{$orderlist[oi].order_yunfei}{else}0{/if}" /></td>
                <td style="padding-right: 20px;"><input type='hidden' name='date' value='{$orderlist[oi].order_time}' />{$orderlist[oi].order_time|date_format:"%Y-%m-%d"}</td>
                <td>
                    <input type='hidden' name='poscode' value='{$orderlist[oi].postal_code}' />
                    <select style='width: 70px;' name='exp-type' class="exp-type">
                        {if $islocal}
                            <option value='2' selected>保税仓</option>
                            <option value='3'>广州仓</option>
                        {else}
                            <option value='0' selected>保税仓</option>
                            <option value='1'>广州仓</option>
                        {/if}
                    </select>
                </td>
            </tr>
        {/section}
    </tbody>
</table>