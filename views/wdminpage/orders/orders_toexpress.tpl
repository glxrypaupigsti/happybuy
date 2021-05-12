{include file='../__header.tpl'}
<i id="scriptTag">page_orders_toexpress</i>
<div id="orderlist" style="margin-bottom: 54px;"></div>
<input type="hidden" id="month-select" value="" />
<div class="fix_bottom" style="position: fixed">
    <a class="wd-btn primary" id='data-exp'>数据导出</a>
{*    <a class="wd-btn primary" href="{$docroot}?/XlsxExport/exportTransform/" id='data-exp-trans'>格式转换</a>*}
    <a class="wd-btn primary hidden" id='data-exp-confirm'>确认</a>
    <a class="wd-btn primary fancybox.ajax" style="display: none;" data-fancy-type="ajax" href="{$docroot}?/WdminPage/orderListExport/" id='data-exp-confirm-hide'>确认导出</a>
    <a class="wd-btn default hidden" id='data-exp-cancel'>返回</a>
</div>
{include file='../__footer.tpl'} 