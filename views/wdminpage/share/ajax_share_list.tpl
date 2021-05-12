{strip}
    {section name=oi loop=$share_list}
        <tr id='stock-exp-{$share_list[oi].id}'>
            <td class="hidden">{$share_list[oi].id}</td>
            <td class="od-exp-check"><input class='pd-exp-checks' type="checkbox" data-id='{$share_list[oi].id}' /></td>
              <td>{$share_list[oi]['uinfo'].client_name}</td>
            <td>{$share_list[oi].add_time|date_format:"%Y-%m-%d"}</td>
            <td>{$share_list[oi].share_count}</td>
            <td>{if $share_list[oi].is_valid == '1'}
              <span style="color:red">过期</span>
            {else}
              有效
            {/if}
            </td>
            <td>
            {if $share_list[oi].type == '0'}
              用户中心
            {else}
              订单类型
            {/if}
            </td>
            <td>{$share_list[oi].share_money}</td>
            <td>{$share_list[oi].coupon_value}</td>

            <td>
  <a class="various fancybox.ajax" 
                 data-fancybox-type="ajax" 
                 href="{$docroot}?/WdminAjax/detailShare/id={$share_list[oi].id}">详情</a>&nbsp;&nbsp;</td>
        </tr>
    {/section}
{/strip}