{section name=oi loop=$orders}
     <ul>
    <li>
      <div class="orderlist" onclick="location = '{$docroot}?/Order/expressDetail/order_id={$orders[oi].order_id}';">
        <div class="order-number"><span class="order-num">订单号：{$orders[oi].serial_number}</span><span class="order-state"><b>&bull;</b>{$orders[oi].statusX}</span></div>
        <div class="pro-list">
         {section name=di loop=$orders[oi]['data']}
          <p><span class="title-pro">{$orders[oi]['data'][di].product_name}</span><span class="pro-price">&yen;{$orders[oi]['data'][di].product_discount_price}</span><span class="pro-num">×{$orders[oi]['data'][di].product_count}</span></p>
          {/section}
        </div>
        <div class="order-pay"><span class="pay-left">总计</span><span class="pay-right">&yen;{$orders[oi].pay_amount}</span></div>
      </div>

    </li>
    
  </ul>
{/section}
