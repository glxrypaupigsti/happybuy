{include file="../__header.tpl"}
<link href="{$docroot}/static/css/base_pagination.css" type="text/css" rel="Stylesheet" />
<i id="scriptTag">page_iframe_list_products</i>
<input type="hidden" id="cat" value="{$cat}" />
<input type="hidden" id="listype" value="0" />
<input type="hidden" id="totalCount" value="{$totalCount}" />
<div id="list"{if !$iscom} style="margin-bottom: 50px;"{/if}>
    <div id="DataTables_Table_0_filter" class="dataTables_filter clearfix">
        <div class="search-w-box"><input type="text" class="searchbox" placeholder="输入搜索内容" /></div>
        <div class="button-set">
            <a class="button gray" href="javascript:;" onclick='location.reload()'>刷新</a>
            {*            <a class="button blue" href="javascript:;" onclick="$('#__stockmanage', parent.parent.document).get(0).click();">库存管理</a>*}
            <a class="button blue" href="javascript:;" id='refresh_static'>刷新缓存</a>
            <a class="button" href="?/WdminPage/iframe_alter_product/mod=add&catid={$cat}">添加商品</a>
        </div>
    </div>
    <table class="dTable">
        <thead>
            <tr>
                <th class="hidden"> </th>
                <th> </th>
                <th style="width:320px">产品名称</th>
                {if !$iscom}<th>编号</th>{/if}
                <th>价格</th>
                <th>浏览</th>
                <th style='width:200px;'>操作</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

<!-- 模板1开始，可以使用script（type设置为text/html）来存放模板片段，并且用id标示 -->
<script id="t:pd_list" type="text/html">
    {literal}
        <%for(var i=0;i<list.length;i++){%>
            <tr class="defTr font12">
                <td class="hidden"><%=list[i].product_id%></td>
                <td>
                    <img class="pdlist-image" height="50" width="50" src="<%=list[i].catimg%>" />
                </td>
                <td><%=list[i].product_name.substring(0,30)%></td>
                <td><%=list[i].product_code%></td>
                <td class="prices font12">&yen;<%=list[i].sale_prices%></td>
                <td><%=list[i].product_readi%></td>
                <th>
                	<!-- 
                    <a class="pd-qrcodebtn fancybox.ajax" data-fancybox-type="ajax" href="?/WdminPage/product_share_qrcode/id=<%=list[i].product_id%>" data-product-id="<%=list[i].product_id%>">二维码</a>&nbsp;
                    -->
                    <a class="pd-altbtn" href="?/WdminPage/iframe_alter_product/mod=edit&id=<%=list[i].product_id%>" data-product-id="<%=list[i].product_id%>">编辑</a>&nbsp;
                    <a href="javascript:;" onclick="parent.parent.window.open('?/vProduct/view/id=<%=list[i].product_id%>');">预览</a>
                    <a class="pd-altbtn pd-switchonline <%if(list[i].product_online == 1){%>tip<%}%>" href="javascript:;" data-product-id="<%=list[i].product_id%>" data-product-online="<%=list[i].product_online%>"><%if(list[i].product_online == 1){%>下架<%}else{%>上架<%}%></a>&nbsp;
                    <a class="pd-altbtn pd-del-btn del" href="javascript:;" data-product-id="<%=list[i].product_id%>">删除</a>
                </th>
            </tr>
            <%}%>
        {/literal}
    </script>
    <!-- 模板1结束 -->
	<div id="Pagination" class="quotes" style="margin-top:-10px;">aaaaa</div> 
    
    {include file="../__footer.tpl"} 