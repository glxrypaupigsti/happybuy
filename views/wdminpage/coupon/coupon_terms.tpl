{include file='../__header.tpl'}
<i id="scriptTag">{$docroot}static/script/Wdmin/coupon/coupon_terms.js</i>
<table class="dTable">
    <thead>
        <tr>
            <th>名称</th>
            <th>表名</th>
            <th>表字段列</th>
            <th>操作符</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        {section name=i loop=$terms}
            <tr id='coupon-exp-{$terms[i].id}'>
                <td>{$terms[i].term_name}</td>
                <td>{$terms[i].term_table}</td>
                <td>{$terms[i].term_column}</td>
                <td>{$terms[i].term_operate}</td>
               <td class="gray font12">
                    <a class="lsBtn" href="?/Coupon/edit_coupon_terms/id={$terms[i].id}">编辑</a>
                    <a data-id="{$terms[i].id}" class="lsBtn couponTermDel del fancybox.ajax" data-fancybox-type="ajax" href="{$docroot}?/FancyPage/fancyHint/id={$terms[i].id}&title=确认删除吗">删除</a>
                </td>
            </tr>
        {/section}
    </tbody>
</table>
<div class="fix_bottom fixed">
    <a class="wd-btn primary" style="width:150px" href="{$docroot}?/Coupon/edit_coupon_terms/">添加条件类别</a>
</div>
{include file='../__footer.tpl'} 