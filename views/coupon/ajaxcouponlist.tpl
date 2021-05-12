{section name=oi loop=$coupons}
    <div class="uc-orderitem" id="orderitem{$orders[oi].order_id}">
        <div class="uc-seral clearfix">
         
        </div>
    
        <div class="uc-summary clearfix" style='padding:8px 7px;text-align:right;'>
            
            <p class="order_serial">{$coupons[oi].coupon_name}</p>

            <a class="olbtn wepay" href="?/Cart/index_order/couponId={$coupons[oi].id}">使用</a>
          
        </div>
    </div>
{/section}