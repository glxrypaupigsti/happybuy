{strip}{if $olistcount == 0}0{else}
    {section name=oi loop=$stockList}
        <tr id='stock-exp-{$stockList[oi].id}'>
            <td class="hidden">{$stockList[oi].id}</td>
            <td class="od-exp-check"><input class='pd-exp-checks' type="checkbox" data-id='{$stockList[oi].id}' /></td>
            <td>{$stockList[oi].stock_date|date_format:"%Y-%m-%d"}</td>
            <td>{$stockList[oi].sku_name}</td>
            <td>{$stockList[oi].avaliable}</td>
            <td>{$stockList[oi].instock}</td>
            <td>{$stockList[oi].produce}</td>
            <td>{$stockList[oi].sold}</td>
            <td>{$stockList[oi].loss}</td>
            <td>{$stockList[oi].produce+$stockList[oi].instock-$stockList[oi].sold-$stockList[oi].loss}</td>
            <td>
                <a class="various fancybox.ajax" data-orderid="{$stockList[oi].id}" data-fancybox-type="ajax" href="{$docroot}?/WdminAjax/editProductStockDetail/id={$stockList[oi].id}">编辑</a>&nbsp;&nbsp;
            </td>
        </tr>
    {/section}
{/if}{/strip}