
DataTableConfig.order = [[0, 'desc']];
DataTableConfig.searching = true;
requirejs(['jquery', 'util', 'datatables'], function($, util, dataTables) {
    $(function() {
        var dt = $('.dTable').dataTable(DataTableConfig).api();
        //必须要加上这句，否则会在列表中第一行出现空格
        util.remove_margin_top();
    });
});