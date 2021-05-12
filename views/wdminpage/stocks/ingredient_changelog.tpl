{include file='../__header.tpl'}
<i id="scriptTag">page_ingredient_changelog</i>

<div id="ingredients-stock-list" class="dataTables_wrapper no-footer" style="margin-bottom: 54px;">
    <input type="hidden" name="id" value="{$ingd_id}" >
    <div class="dataTables_filter clearfix">
        <div class="search-w-box"><input type="text" class="searchbox" placeholder="输入搜索内容" /></div>
        <div class="button-set">
            <a onclick="window.history.back();" class="button gray">返回</a>
        </div>
    </div>
    <table class="dTable">
        <thead>
            <tr>
                <th class="hidden"></th>
                <th class="od-exp-check"><input class="checkAll" type="checkbox" /></th>
                <th>日期</th>
                <th>期初库存</th>
                <th>期末库存</th>
                <th>变动类型</th>
                <th>变动数量</th>
                <th>变动金额</th>
                <th>供应商情况</th>
                <th>操作人</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <div class="dataTables_paginate" >
    </div>
</div>

<input type="hidden" id="month-select" value="" />
<div class="fix_bottom" style="position: fixed">
    <a class="wd-btn primary" id='data-exp'>数据导出</a>
    <a class="wd-btn primary hidden" id='data-exp-confirm'>确认</a>
    <a class="wd-btn primary fancybox.ajax" style="display: none;" data-fancy-type="ajax" href="{$docroot}?/WdminPage/productsInstockExport/" id='data-exp-confirm-hide'>确认导出</a>
    <a class="wd-btn default hidden" id='data-exp-cancel'>返回</a>
</div>

{include file='../__footer.tpl'} 