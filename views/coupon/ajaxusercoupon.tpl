 {section name=oi loop=$couponList}
	{if $state == 0}
	  <li class="not-used-coupon">
	    <div class="coupon-amount"><span class="amount-money">{$couponList[oi].coupon_value}</span><span class="yuan">元</span></div>
	    <div class="coupon-detail"><span class="condition">{$couponList[oi].coupon_name}</span><span class="superposition">不可与其他优惠券共享</span><span class="expire-time">有效期至：{date("Y-m-d",{$couponList[oi].effective_end})}</span></div>
	  </li>
	{else if $state == 1}
	  <li class="has-use-coupon">
	        <div class="coupon-amount"><span class="amount-money">{$couponList[oi].coupon_value}</span><span class="yuan">元</span></div>
	        <div class="coupon-detail"><span class="condition">{$couponList[oi].coupon_name}</span><span class="superposition">不可与其他优惠券共享</span><span class="expire-time">有效期至：{date("Y-m-d",{$couponList[oi].effective_end})}</span></div>
	       <!-- 已使用图标-->  
	       <img src="../../static/img/hasuse.png" class="failure-img" />
	  </li>
	{else if $state == 2}
	  <li class="expired-coupon">
	        <div class="coupon-amount"><span class="amount-money">{$couponList[oi].coupon_value}</span><span class="yuan">元</span></div>
	        <div class="coupon-detail"><span class="condition">{$couponList[oi].coupon_name}</span><span class="superposition">不可与其他优惠券共享</span><span class="expire-time">有效期至：{date("Y-m-d",{$couponList[oi].effective_end})}</span></div>
	       <!-- 已失效图标-->  
	       <img src="../../static/img/failure.png" class="failure-img" />
	  </li>
	{/if}
{/section}
