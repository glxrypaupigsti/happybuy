{include file='../__header.tpl'}
{if $ed}
    <input type="hidden" value="{$coupon.product_id}" id="pid" /> 
{/if}
<link href="{$docroot}static/less/jquery.datetimepicker.css?v={$cssversion}" type="text/css" rel="Stylesheet" />
<input type="hidden" value="{$smarty.server.HTTP_REFERER}" id="http_referer" /> 
<i id="scriptTag">{$docroot}static/script/Wdmin/coupon/edit_coupon.js</i>
<form id="pd-baseinfo" class='pt58'>
    <div style="padding: 22px;" class="clearfix">
		<input type="hidden" value="{$mod}" id="mod" /> 
		<input type="hidden" value="{$coupon.id}"  name="id"/> 
        <input id="pd-catimg" name="catimg" type="hidden" value="{$coupon.coupon_cover}" />

        <div class="clearfix">

            <div id="alterProductLeft">

                <!-- 优惠券名称 -->
                <div class="fv2Field clearfix">
                    <div class="fv2Left">
                        <span>优惠券名称</span>
                    </div>
                    <div class="fv2Right">
                        <input type="text" class="gs-input" name="coupon_name" value="{$coupon.coupon_name}" id="pd-form-title" autofocus/>
                    </div>
                </div>
                
                <!-- 优惠券简介 -->
                <div class="fv2Field clearfix">
                    <div class="fv2Left">
                        <span>优惠券简介</span>
                    </div>
                    <div class="fv2Right">
                        <span class="frm_textarea_box" style="width: 375px;"><textarea class="js_desc frm_textarea" id="pd-form-desc" name="coupon_detail">{$coupon.coupon_detail}</textarea></span>
                    </div>
                </div>

                <!-- 优惠券类型 -->
                <div class="fv2Field clearfix">
                    <div class="fv2Left">
                        <span>优惠券类型</span>
                    </div>
                    <div class="fv2Right margin-top">
                        <input type="radio" name="coupon_type" value="0" {if $coupon.coupon_type == 0 }checked{/if}/> 商品券
                        <input type="radio" name="coupon_type" value="1" {if $coupon.coupon_type == 1 }checked{/if}/> 订单券
                        <input type="radio" name="coupon_type" value="2" {if $coupon.coupon_type == 2 }checked{/if}/> 用户券
                    </div>
                </div>      

                <!-- 优惠券应用类别 -->
                <div class="fv2Field clearfix" id="apply_to_type">
                    <div class="fv2Left">
                        <span>应用类型</span>
                    </div>
                    <div class="fv2Right margin-top">
                        <input type="radio" name="coupon_applied_type" value="0" {if $coupon.select_applied_subtype == 0 }checked{/if}/> 商品
                        <input type="radio" name="coupon_applied_type" value="1" {if $coupon.select_applied_subtype == 1 }checked{/if}/> 商品分类
                    </div>
                </div>   

                <!-- 优惠券发放开始时间-->
                <div class="fv2Field clearfix">
                    <div class="fv2Left">
                        <span>发放开始时间</span>
                    </div>
                    <div class="fv2Right">
                        <input type="text" class="gs-input" id="available_start" name="available_start" value="{$coupon.available_start}" placeholder="发放开始时间" autofocus/> 
                    </div>
                </div>    
                
                <!-- 优惠券发放结束时间-->
                <div class="fv2Field clearfix">
                    <div class="fv2Left">
                        <span>发放结束时间</span>
                    </div>
                    <div class="fv2Right">
                        <input type="text" class="gs-input" id="available_end" name="available_end" value="{$coupon.available_start}" placeholder="发放开始时间" autofocus/> 
                    </div>
                </div>
                <!-- 优惠券有效期开始时间-->
                <div class="fv2Field clearfix">
                    <div class="fv2Left">
                        <span>有效期开始时间</span>
                    </div>
                    <div class="fv2Right">
                        <input type="text" class="gs-input" id="effective_start" name="effective_start" value="{$coupon.effective_start}" placeholder="有效期开始时间" autofocus/> 
                    </div>
                </div>    
                
                <!-- 优惠券有效期结束时间-->
                <div class="fv2Field clearfix">
                    <div class="fv2Left">
                        <span>有效期结束时间</span>
                    </div>
                    <div class="fv2Right">
                        <input type="text" class="gs-input" id="effective_end" name="effective_end" value="{$coupon.effective_end}" placeholder="有效期结束时间" autofocus/> 
                    </div>
                </div>
                
                <!-- 优惠券数量-->
                <div class="fv2Field clearfix">
                    <div class="fv2Left">
                        <span>优惠券数量</span>
                    </div>
                    <div class="fv2Right">
                        <input type="text" class="gs-input-query"  name="coupon_stock" value="{$coupon.coupon_stock}" autofocus/> <div class='fv2Tip'>如果不限制数量请填写-1000</div>
                    </div>
                </div>
                
                <!-- 折扣类型 -->
                <div class="fv2Field clearfix" style="max-width:100%;">
                    <div class="fv2Left">
                        <span>折扣类型</span>
                    </div>
                    <div class="fv2Right margin-top">
                        <input type="radio"  name="discount_type" value="0" {if $coupon.discount_type == 0 }checked{/if}/> <span class='stable_money'>固定金额</span>
                        <input type="radio" class='proportion' name="discount_type" value="1" {if $coupon.discount_type == 1 }checked{/if}/><span class='proportion'> 折扣比例</span>
                        <input type="radio" class='full_reduction' name="discount_type" value="2" {if $coupon.discount_type == 2 }checked{/if}/> <span class='full_reduction'>满x减y</span>
                        <input type="radio" class='mod_full_reduction' name="discount_type" value="3" {if $coupon.discount_type == 3 }checked{/if}/> <span class='mod_full_reduction'>每满x减y</span>
                        <input type="radio" class='exchange' name="discount_type" value="4" {if $coupon.discount_type == 4 }checked{/if}/> <span class='exchange'>加x换购B</span>
                        <input type="radio" class='give' name="discount_type" value="5" {if $coupon.discount_type == 5 }checked{/if}/> <span class='give'>买M件送N件</span>
                    </div>
                </div> 
                <!-- 折扣值 -->
                <div class="fv2Field clearfix">
                    <div class="fv2Left">
                        <span>折扣值</span>
                    </div>
                    <div class="fv2Right">
                        <input type="text" class="gs-input-query"  name="discount_val" value="{$coupon.discount_val}"  autofocus/><span id="unit_fen">分</span><span id="unit_percentage">%</span>
                    </div>
                </div> 
                <!-- 应用于商品分类 -->
                <div class="fv2Field clearfix" style="max-width:100%;" id="applied_to_cat">
                    <div class="fv2Left">
                        <span>应用于分类</span>
                    </div>
                    
                    <div class="fv2Right margin-top">
                    
                     {foreach from=$product_cats item=cat}
                        <input name="coupon_product_cat" type="checkbox" value="{$cat.cat_id}" data-name='{$cat.cat_name}' data-parent='{$cat.cat_parent}'/> {$cat.name}
                     {/foreach}
                    </div>
                </div> 
                
                
                <!-- 商品对应 -->        
			    <div class="fv2Field typeHash clearfix" id="apply_to_product" style="width:100%;">
			        <div class="fv2Left">
			            <span>选择商品</span>
			        </div>
			        
			        <div class="fv2Right">
			            <a id="sProduct" href="?/FancyPage/ajaxSelectProduct/" class="wd-btn primary fancybox.ajax" data-fancybox-type="ajax" style="margin:0;width:389px;" data-id="">选择产品</a>
			            <div class='fv2Tip hidden' id="selectPdCount">已选择100个产品</div>
			            <div id="ProductItem" class="clearfix">
			                {if $select_applied_products}
			                    {include file='../fancy/ajaxCouponSelectPdBlocks.tpl'}
			                {/if}
			            </div>
			        </div>
			    </div>
			    
			    
			    <!-- 换购商品对应 -->        
			    <div class="fv2Field typeHash clearfix" id="exchange_product" style="width:100%;">
			        <div class="fv2Left">
			            <span>换购商品</span>
			        </div>
			        <div class="fv2Right">
			            <a id="scProdcut" href="?/FancyPage/ajaxSelectProduct/" class="wd-btn primary fancybox.ajax" data-fancybox-type="ajax" style="margin:0;width:389px;" data-id="">选择产品</a>
			            <div class='fv2Tip hidden' id="exchangePdCount">已选择100个产品</div>
			            <div id="scProductItem" class="clearfix">
			                {if $select_bundled}
			                    {include file='../fancy/ajaxCouponBundlePdBlocks.tpl'}
			                {/if}
			            </div>
			        </div>
			    </div>
			    
			    <div class="fv2Field clearfix" style="max-width:100%;">
		            <div class="fv2Left">
		                <span>优惠券使用条件</span>
		            </div>
		            <div class="fv2Right">
		                <div class="button-set l pt0">
		                    <!-- 优惠券使用条件加按钮 -->
		                    <a class="button" id="btn_coupon_terms_add" href="javascript:;">添加</a>
		                </div>
		                <table id='pd-spec-frame' class="{if $pd.specs|count eq 0}hidden1{/if}">
		                    <thead>
		                        <tr><th style="width:180px;">条件名称</th><th style="width:180px;">表名</th><th style="width:180px;">列名</th><th>操作符</th><th>操作值</th><th style="width: 40px;">操作</th></tr>
		                    </thead>
		                    <tbody>
		                        <tr class="coupon_terms_select hidden" data-id="#">
		                            <td>
		                                <select class="coupon_terms" id="order_coupon_terms">
		                                    {foreach from=$order_coupon_terms item=terms}
	                                            <option value="{$terms.id}" data-term-id="{$terms.id}" data-name="{$terms.term_name}" data-table="{$terms.term_table}" data-column="{$terms.term_column}" data-operate="{$terms.term_operate}">{$terms.term_name}</option>
		                                    {/foreach}
		                                </select>
		                                <select class="coupon_terms" id="user_coupon_terms">
		                                    {foreach from=$user_coupon_terms item=terms}
	                                            <option value="{$terms.id}" data-term-id="{$terms.id}" data-name="{$terms.term_name}" data-table="{$terms.term_table}" data-column="{$terms.term_column}" data-operate="{$terms.term_operate}">{$terms.term_name}</option>
		                                    {/foreach}
		                                </select>
		                                <select class="coupon_terms" id="product_coupon_terms">
		                                    {foreach from=$product_coupon_terms item=terms}
	                                            <option value="{$terms.id}" data-term-id="{$terms.id}" data-name="{$terms.term_name}" data-table="{$terms.term_table}" data-column="{$terms.term_column}" data-operate="{$terms.term_operate}">{$terms.term_name}</option>
		                                    {/foreach}
		                                </select>
		                            </td>
		                            <td>
		                            	<input type="hidden"  class="coupon-terms-id" value="{$terms.id}" readonly>
		                                <input type="text"  class="coupon-terms-table" value="{$terms.terms_table}" readonly>
		                            </td>
		                            <td><input type="text" class="coupon-terms-column" value="{$terms.terms_column}" readonly></td>
		                            <td><input type="text" class="coupon-terms-operate" value="{$terms.terms_operate}" readonly></td>
		                            <td><input type="text" class="coupon-terms-value" value="{$terms.value}"></td>
		                            <td><a class="btn-delete-spectr" href="javascript:;">删除</a></td>
		                        </tr>
		                        
		                        
		                        {*$select_coupon_terms_str*}
		                        {foreach from=$select_coupon_terms item=sterm}
		                            <tr class="coupon_terms_select" data-id="{$sterm.id}">
		                                <td>
			                                <select class="coupon_terms">
	                                            <option value="{$sterm.id}" data-term-id="{$sterm.id}" data-name="{$sterm.name}" data-table="{$sterm.table}" data-column="{$term.column}" data-operate="{$term.operate}">{$sterm.name}</option>
			                                </select>
			                            </td>
		                                <td>
			                                <input type="hidden"  class="coupon-terms-id" value="{$sterm.id}" readonly>
			                                <input type="text"  class="coupon-terms-table" value="{$sterm.table}" readonly>
			                            </td>
			                            <td><input type="text" class="coupon-terms-column" value="{$sterm.column}" readonly></td>
			                            <td><input type="text" class="coupon-terms-operate" value="{$sterm.operate}" readonly></td>
			                            <td><input type="text" class="coupon-terms-value" value="{$sterm.value}"></td>
			                            <td><a class="btn-delete-spectr" href="javascript:;">删除</a></td>
		                            </tr>
		                        {/foreach}
		                    </tbody>
		                </table>
		                <div class='fv2Tip'>请点击添加一个使用条件</div>
		            </div>
		        </div>
			    
			    <!-- 是否可与其他优惠券复用-->
                <div class="fv2Field clearfix" style="max-width:100%;">
                    <div class="fv2Left">
                        <span>是否可复用</span>
                    </div>
                    <div class="fv2Right margin-top">
                        <input type="radio" name="coupon_limit" value="1" checked/> 否
                        <input type="radio" name="coupon_limit" value="0" /> 是
                    </div>
                </div> 
			    
			    
            </div>  
			{*
            <div id="alterProductRight">
                <div class="t1">优惠券图片</div>
                <!-- 优惠券大图 -->
                <a class="pd-image-sec" data-id="0" href="javascript:;"></a>
                <div class="t2">建议使用500&#215;500尺寸图片 <a id="catimgPv" href="/uploads/product_hpic/1433819046557657a6967c4.jpeg">预览</a></div>
            </div>
			*}
        </div>
    </div>

</form>
<div class="fix_top fixed">
    <div class='button-set'>
        <a onclick="history.go(-1);" class="button gray">返回</a>
        <a class='button' id="save_coupon_btn"  href="javascript:;">{if $coupon.id}保存{else}添加{/if}</a>
    </div>
</div>
{include file='../__footer.tpl'} 