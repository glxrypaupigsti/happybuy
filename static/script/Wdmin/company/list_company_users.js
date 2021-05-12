
DataTableConfig.order = [[0, 'desc']];
requirejs(['jquery', 'util', 'fancyBox', 'datatables'], function($, util, fancyBox, dataTables) {
    $(function() {
        var dt = $('.dTable').dataTable(DataTableConfig).api();
    });
});