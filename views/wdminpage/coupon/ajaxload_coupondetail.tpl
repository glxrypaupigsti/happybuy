<div style="width:550px;">
    <div class="orderwpa-top">
    	<div>
        	<div>
        		<label>优惠券名称：</label>
        		<label>{$coupon.coupon_name}</label>
        	</div>
        	
        	<div>
        		<label>优惠券类型：</label>
        		<label style="color:red;">{$coupon.coupon_type_desc}</label>
        	</div>
        	
        </div>	
        <div >
        	<div>
        		<label>折扣类型：</label>
        		<label style="color:red;">{$coupon.discount_type_desc}</label>
        	</div>
        	
        	<div >
        		<label>是否激活：</label>
        		<label {if $coupon.is_activated==1}style="color:red;"{/if}>{$coupon.is_activated_desc}</label>
        	</div>
        	
        </div>	
        <div >
        	<div >
        		<label>发放开始时间：</label>
        		<label style="margin-top:-25px;">{$coupon.available_start}</label>
        	</div>
        	<div >
        		<label>发放结束时间：</label>
        		<label>{$coupon.available_end}</label>
        	</div>
        </div>
        <div >
        	<div>
        		<label>有效期开始：</label>
        		<label>{$coupon.effective_start}</label>
        	</div>
        	<div>
        		<label>有效期结束：</label>
        		<label style="color:red;">{$coupon.effective_end}</label>
        	</div>
        </div>
        <div >
        	<div>
        		<label>发放数量：</label>
        		<label>{$coupon.coupon_stock_desc}</label>
        	</div>
        	<div>
        		<label>剩余数量：</label>
        		<label style="color:red;">{$coupon.coupon_stock_left_desc}</label>
        	</div>
        </div>
        <div >
        	<div>
        		<label>是否过期：</label>
        		<label>{$coupon.is_expired}</label>
        	</div>
        	<div>
        		<label>折扣值：</label>
        		<label>{$coupon.discount_val}</label>
        	</div>
        </div>
    </div>
    {if $select_coupon_terms}
	    <div class="clearfix" style="border-bottom:1px solid #dedede;margin-bottom:10px;">
	    	<b>使用条件</b>
	        {section name=od loop=$select_coupon_terms}
	            <div class='coupon-terms-pdlist'>
	                <div style="height: 25px;line-height: 20px;color:red;">
	                    <div class="Elipsis">{$select_coupon_terms[od].term_desc}</div>
	                </div>
	            </div>
	        {/section}
	    </div>
	{/if}
	
	{if $select_applied_categorys}
	    <div class="clearfix" style="border-bottom:1px solid #dedede;margin-bottom:10px;">
	    	<b>适用于分类</b>
	        {section name=od loop=$select_applied_categorys}
	            <div class='coupon-terms-pdlist'>
	                <div style="height: 25px;line-height: 20px;color:red;">
	                    <div class="Elipsis">{$select_applied_categorys[od].cat_name}{$select_applied_categorys[od].id}</div>
	                </div>
	            </div>
	        {/section}
	    </div>
	{/if}
    
    {if $select_applied_products}
	    <div class="clearfix-new">
	    	<div><b>适用于商品</b></div>
	        {section name=od loop=$select_applied_products}
	            <div class='orderwpa-pdlist'>
	                <img width="60px" height="60px" src="{$docroot}static/Thumbnail/?w=100&h=100&p={$docroot}uploads/coupon/{$coupon.products[od].img}" />
	                <div style="margin-left: 70px;height: 60px;line-height: 20px;">
	                    <div class="Elipsis">{$select_applied_products[od].name}</div>
	                    <div style="margin-top:3px;">
	                        <i class="opprice">&yen;{$select_applied_products[od].name}</i>
	                    </div>
	                    {*
	                    <div style="margin-top:0;color:#666;font-size: 12px;">{if $coupon.products[od].det_name1 neq ''}[{$coupon.products[od].det_name1}{$coupon.products[od].det_name2}]{else}[默认规格]{/if}</div>
	                	*}
	                </div>
	            </div>
	        {/section}
	    </div>
	{/if}
  
</div>
