
DataTableConfig.order = [[0, 'desc']];
DataTableConfig.searching =false;

requirejs(['jquery', 'util', 'fancyBox', 'datatables', 'Spinner'], function($, util, fancyBox, dataTables, Spinner) {

    $(function() {
        var dt = $('.dTable').dataTable(DataTableConfig).api();
        $('.couponTermDel').click(function() {
            var node = $(this);
            if (confirm('要删除这个分类吗？')) {
                $.post('?/Coupon/delete_coupon_terms/', {id: $(this).attr('data-id')}, function(r) {
                    if (r > 0) {
                        dt.row(node.parents('tr')).remove().draw();
                        util.Alert('删除成功！');
                    } else {
                        util.Alert('删除失败！', true);
                    }
                });
            }
        });
        
//        //单机单元格的参数 
//        $('.dTable tbody').on( 'click', 'td', function () {
//            var cell = dt.cell( this );
//            cell.data( cell.data() + 1 ).draw();
//            // note - call draw() to update the table's draw state with the new data
//        } );
    });

});