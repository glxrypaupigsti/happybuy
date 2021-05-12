{include file="../__header.tpl"}

<i id="scriptTag">page_writedown_ingredient</i>

<div class="fix_top fixed">
    <div class='button-set'>
        <a onclick="window.history.back();" class="button gray">返回</a>
        <a class='button' id="save_btn"  href="javascript:;">保存</a>
    </div>
</div>

<form id="ingredient-form" class='pt58'>
    <input type="hidden" name="change_type" value="3" />
    <input type="hidden" name="ingd_id" value="{$ingredient.id}" >
    <div style="padding: 22px;" class="clearfix">
        <div>
            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>食材名称</span>
                </div>
                <div class="fv2Right">
                    {$ingredient.ingd_name}
                </div>
            </div>
            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>减计时间</span>
                </div>
                <div class="fv2Right">
                    <input type="text" class="gs-input" name="change_time" value="{$smarty.now|date_format:"%Y-%m-%d %H:%M"}" autofocus/>
                </div>
            </div>
            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>减计数量</span>
                </div>
                <div class="fv2Right">
                    <input type="text" class="gs-input" style="width:60%;margin-right:10px;" name="change_val" value="0" autofocus/>{$ingredient.unit_str}
                </div>
            </div>
            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>减计金额</span>
                </div>
                <div class="fv2Right">
                    <input type="text" class="gs-input" style="width:60%;margin-right:10px;" name="change_price" placeholder="￥0.00" autofocus/>元
                </div>
            </div>
            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>物料规格</span>
                </div>
                <div class="fv2Right">
                    <input type="text" class="gs-input" name="spec" value="" autofocus/>
                </div>
            </div>
            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>物料条码</span>
                </div>
                <div class="fv2Right">
                    <input type="text" class="gs-input" name="barcode" value="0" autofocus/>
                </div>
            </div>

            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>供应商名称</span>
                </div>
                <div class="fv2Right">
                    <input type="text" class="gs-input" name="vendor" value="" autofocus/>
                </div>
            </div>

            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>减计人</span>
                </div>
                <div class="fv2Right">
                    <input type="text" class="gs-input" name="change_user" value="" autofocus/>
                </div>
            </div>
            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>减计原因</span>
                </div>
                <div class="fv2Right">
                    <textarea class="js_desc frm_textarea" style="width: 100%;height: 120px" name="change_note" ></textarea>
                </div>
            </div>
        </div>
    </div>
</form>

{include file="../__footer.tpl"}