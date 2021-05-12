
DataTableConfig.order = [[0, 'desc']];
DataTableConfig.searching = true;
requirejs(['jquery', 'util', 'fancyBox', 'datatables'], function($, util, fancyBox, dataTables) {
    $(function() {
        util.loadOrderStatNums();
        ajaxLoadOrderlist(util);
    });
});

function ajaxLoadOrderlist(util) {
    $('#orderlist').load('?/Wdmin/ajaxLoadOrderlist/page=0&status=refunded', function(r) {
        if (r === '0') {
            util.listEmptyTip();
        } else {
            $('.wshop-empty-tip').remove();
            $('.dTable').dataTable(DataTableConfig).api();
            //必须要加上这句，否则会在列表中第一行出现空格
            util.remove_margin_top();
            // 查看快递信息
            fnFancyBox('.od-list-pdinfo');
        }
    });
}