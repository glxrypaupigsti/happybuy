
DataTableConfig.order = [[0, 'desc']];
DataTableConfig.searching =false;

requirejs(['jquery', 'util', 'fancyBox', 'datatables', 'Spinner'], function($, util, fancyBox, dataTables, Spinner) {

    $(function() {
        var dt = $('.dTable').dataTable(DataTableConfig).api();
        var refresh_url = '?/WdminPage/coupon_terms';
        fnFancyBox('.couponTermDel', function () {
            $('#ok').on('click', function () {
            	$.post('?/Coupon/delete_coupon_terms/', {id: $(this).attr('data-id')}, function(r) {
            		$.fancybox.close();
            		if (r > 0) {
                        util.Alert('删除成功,2s后将自动刷新',false ,null,1000);
                    } else {
                        util.Alert('删除失败！', true);
                    }
            		util.delay_refresh(refresh_url);
                });
            })
            
            $('#close').on('click', function () {
            	$.fancybox.close();
            })
        });
        
    });

});