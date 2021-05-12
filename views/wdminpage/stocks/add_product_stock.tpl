{include file="../__header.tpl"}
<link href="/static/css/base_pagination.css" type="text/css" rel="Stylesheet" />
<i id="scriptTag">page_add_product_stock</i>

<div class="fix_top fixed">
    <div class='button-set'>
        <a onclick="window.history.back();" class="button gray">返回</a>
        <a class='button' id="add_prd_stock_btn"  href="javascript:;">保存</a>
    </div>
</div>

<form id="stock-form" class='pt58'>
    <input type="hidden" name="sku_name" value="{$default_sku_name}" >
    <div style="padding: 22px;" class="clearfix">
        <div id="alterProductLeft">
            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>库存日期</span>
                </div>
                <div class="fv2Right">
                    <input type="text" class="gs-input" name="stock_date" value="{$smarty.now|date_format:"%Y-%m-%d"}" autofocus="">
                </div>
            </div>

            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>库存产品</span>
                </div>
                <div class="fv2Right">
                    {strip}
                        <select id="pd-select" style="color:#000" name="sku_id" >
                        {if $sku_list|count > 0}
                            {foreach from=$sku_list item=this_sku}
                            <option value="{$this_sku.id}" >{$this_sku.name}</option>
                            {/foreach}
                        {else}
                            <option value="0">未分类</option>
                        {/if}
                        </select>
                    {/strip}
                </div>
            </div>

            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>物料可生产数</span>
                </div>
                <div class="fv2Right">
                    <input type="text" class="gs-input" name="avaliable" value="0" autofocus/>
                </div>
            </div>

            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>当日生产数</span>
                </div>
                <div class="fv2Right">
                    <input type="text" class="gs-input" name="produce" value="0" id="pd-form-title" autofocus/>
                </div>
            </div>

        </div>
    </div>
</form>

{include file="../__footer.tpl"}