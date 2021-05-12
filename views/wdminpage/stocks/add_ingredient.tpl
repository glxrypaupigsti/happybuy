{include file="../__header.tpl"}
<link href="/static/css/base_pagination.css" type="text/css" rel="Stylesheet" />
<i id="scriptTag">page_add_ingredient</i>

<div class="fix_top fixed">
    <div class='button-set'>
        <a onclick="window.history.back();" class="button gray">返回</a>
        <a class='button' id="add_ingredient_btn"  href="javascript:;">保存</a>
    </div>
</div>

<form id="ingredient-form" class='pt58'>
    <div style="padding: 22px;" class="clearfix">
        <div id="alterProductLeft">
            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>食材名称</span>
                </div>
                <div class="fv2Right">
                    <input type="text" class="gs-input" name="ingd_name" value="" autofocus="">
                </div>
            </div>

            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>计量单位</span>
                </div>
                <div class="fv2Right">
                    <select style="color:#000" name="ingd_unit" >
                        <option value="0" >克</option>
                        <option value="1" >公斤</option>
                        <option value="2" >毫升</option>
                        <option value="3" >升</option>
                        <option value="4" >个</option>
                    </select>
                </div>
            </div>

            <div class="fv2Field clearfix">
                <div class="fv2Left">
                    <span>警戒库存</span>
                </div>
                <div class="fv2Right">
                    <input type="text" class="gs-input" name="ingd_threshold" value="0" autofocus/>
                </div>
            </div>

        </div>
    </div>
</form>

{include file="../__footer.tpl"}