<input type="hidden" name="total" value="{$total}" >
{strip}{if $olistcount == 0}0{else}
    {section name=oi loop=$stockList}
        <tr id='stock-exp-{$stockList[oi].id}'>
            <td class="hidden">{$stockList[oi].id}</td>
            <td class="od-exp-check"><input class='pd-exp-checks' type="checkbox" data-id='{$stockList[oi].id}' /></td>
            <td>{$stockList[oi].change_time|date_format:"%Y-%m-%d %H:%M:%S"}</td>
            <td>{$stockList[oi].initial_stock}{$stockList[oi].unit_str}</td>
            <td>{$stockList[oi].instock}{$stockList[oi].unit_str}</td>
            <td>{$stockList[oi].change_type_str}</td>
            <td>{$stockList[oi].change_val}{$stockList[oi].unit_str}</td>
            <td>￥{$stockList[oi].change_price/100.0}</td>
            <td>{$stockList[oi].vendor}</td>
            <td>{$stockList[oi].change_user}</td>
        </tr>
    {/section}
{/if}{/strip}