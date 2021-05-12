<div style="width:500px;">
<div class="orderwpa-top" >
        <span class="orderwpa-serial">
            日期：<span class="orderstatus">{$stock.stock_date|date_format:"%Y-%m-%d"}</span>
            <br />
            品名：<span class="orderstatus">{$stock.sku_name}</span>
            <br />
        </span>
    </div>

    <form id="stock_data">
        <input type="hidden" name="prd_stockid" value="{$stock.id}" />
        <div class="orderwpa-address clearfix" style="line-height:32px;">
            <p>
                物料可生产：<input type="text" style="width:50px;text-indent:10px;" maxlength="4" name="avaliable" value="{$stock.avaliable}" />&nbsp;&nbsp;&nbsp;&nbsp;
                当日生产数：<input type="text" style="width:50px;text-indent:10px;" maxlength="4" name="produce" value="{$stock.produce}" />&nbsp;&nbsp;&nbsp;&nbsp;
                当日损耗数：{strip}{if $loss_editable == true}<input type="text" style="width:50px;text-indent:10px;" maxlength="4" name="loss" value="{$stock.loss}" />{else}<input type="hidden" name="loss" value="{$stock.loss}" />{$stock.loss}{/if}{/strip}
            </p>
            <p>期初库存数：{$stock.instock} &nbsp;&nbsp;&nbsp;&nbsp;当日销售数：{$stock.sold}</p>
            <p>备注：<textarea class="js_desc frm_textarea" style="width: 94%;height: 120px;margin-left: 3%;" name="user_note" >{$stock.user_note}</textarea></p>
        </div>
    </form>

    <div style="text-align: center;margin-top: 10px;">
        <a class="wd-btn primary" name="confirm" href='javascript:;' id="save_stock_btn" >确认</a>
    </div>
</div>