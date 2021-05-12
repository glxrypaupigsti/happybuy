
DataTableConfig.order = [[3, 'desc']];

requirejs(['jquery', 'util', 'fancyBox', 'datatables'], function($, util, fancyBox, dataTables) {
    $(function() {
        var dt = $('.dTable').dataTable(DataTableConfig).api();
        // 结算统计
        var incomesum = 0;
        $('.bill_amounts').each(function() {
            incomesum += parseFloat($(this).attr('data-amount'));
        });
        $('#com-income-count').html('&yen;' + incomesum);
    });
});