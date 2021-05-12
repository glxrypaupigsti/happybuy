{include file='../__header.tpl'}
<i id="scriptTag">page_stocks_ingredients</i>

<div id="ingredients-stock-list" class="dataTables_wrapper no-footer" style="margin-bottom: 54px;">
    <div class="dataTables_filter clearfix">
        <div class="search-w-box"><input type="text" class="searchbox" placeholder="输入搜索内容" /></div>
        <div class="button-set">
            <a class="button gray" href="javascript:;" onclick='location.reload()'>刷新</a>
            <a class="button" href="?/WdminPage/add_ingredient">新增种类</a>
        </div>
    </div>
    <table class="dTable">
        <thead>
            <tr>
                <th class="hidden"></th>
                <th class="od-exp-check"><input class="checkAll" type="checkbox" /></th>
                <th>品名</th>
                <th>库存数</th>
                <th>警戒库存</th>
                <th>变更历史</th>
                <th>操作</th>
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