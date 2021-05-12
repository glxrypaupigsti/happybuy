
DataTableConfig.order = [[0, 'desc']];

requirejs(['jquery', 'util', 'fancyBox', 'datatables'], function($, util, fancyBox, dataTables) {
    $(function() {
        window.util = util;
        var dt;
        $('#orderlist').load('?/Wdmin/ajaxLoadOrderlist/page=0&status=all&cid=' + $('#cid').val(), function(r) {
            if (r === '0') {
                util.listEmptyTip();
            } else {
                dt = $('.dTable').dataTable(DataTableConfig).api();
                //必须加上这一行代码，否则在列表页中会出现空行
                util.remove_margin_top();
                $('.pd-list-viewExp,.od-list-pdinfo').fancybox();
            }
        });
    });
});