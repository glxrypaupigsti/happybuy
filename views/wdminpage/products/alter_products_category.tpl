{include file='../__header.tpl'}
<i id="scriptTag">page_alter_products_categroy</i>
<div class="clearfix">
    <div id="categroys">
        <div id="_ztree" class="ztree"></div>
        <div class="fix_bottom fixed" style="display: none;width:201px;">
            <a id="add_category_btn" onclick="javascript:;" class="wd-btn primary fancybox.ajax" data-fancybox-type="ajax" 
               href="{$docroot}?/WdminPage/ajax_add_category/">添加分类</a>
        </div>
    </div>
    <div id="cate_settings">
        <div id="iframe_loading" style="top:0;"></div>
        <iframe id="iframe_altercat" src="" width="100%" frameborder="0"></iframe>
    </div>
</div>
{include file='../__footer.tpl'} 