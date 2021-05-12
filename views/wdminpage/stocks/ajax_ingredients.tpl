<input type="hidden" name="total" value="{$total}" >
{strip}{if $olistcount == 0}0{else}
    {section name=oi loop=$stockList}
        <tr id='stock-exp-{$stockList[oi].id}'>
            <td class="hidden">{$stockList[oi].id}</td>
            <td class="od-exp-check"><input class='pd-exp-checks' type="checkbox" data-id='{$stockList[oi].id}' /></td>
            <td>{$stockList[oi].ingd_name}</td>
            <td>{$stockList[oi].instock}{$stockList[oi].unit_str}</td>
            <td>{$stockList[oi].ingd_threshold}{$stockList[oi].unit_str}</td>
            <td>
                <a class="various" href="{$docroot}?/WdminPage/ingredient_changelog/id={$stockList[oi].id}">查看</a>&nbsp;&nbsp;
            </td>
            <td>
                <a class="button" href="{$docroot}?/WdminPage/checkin_ingredient/id={$stockList[oi].id}">入库</a>&nbsp;&nbsp;
                <a class="button" style="color:#f37d2a;" href="{$docroot}?/WdminPage/checkout_ingredient/id={$stockList[oi].id}">出库</a>&nbsp;&nbsp;
                <a class="button" style="color:#e14747;" href="{$docroot}?/WdminPage/writedown_ingredient/id={$stockList[oi].id}">减计</a>
            </td>
        </tr>
    {/section}
{/if}{/strip}