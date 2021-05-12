
DataTableConfig.order = [[0, 'desc']];
DataTableConfig.searching =false;

requirejs(['jquery', 'util', 'fancyBox', 'datatables', 'Spinner'], function($, util, fancyBox, dataTables, Spinner) {
	var dt ;
	 
    $(function() {
        dt = $('.dTable').dataTable(DataTableConfig).api();
//        $('#DataTables_Table_0_filter').hide();
//        $('.chargeLogDel').click(function() {
//            var node = $(this);
//            if (confirm('要删除该充值记录吗？')) {
//                $.post('?/ChargeManage/delete_charge_log/', {id: $(this).attr('data-id')}, function(r) {
//                    if (r > 0) {
//                        dt.row(node.parents('tr')).remove().draw();
//                        util.Alert('删除成功！');
//                    } else {
//                        util.Alert('删除失败！', true);
//                    }
//                });
//            }
//        });
        
        //制卡函数
	    fnFancyBox('.chargeLogDel',function(){
	     	$('#ok').on('click', function(){
	     		var id = $(this).attr('data-id');
	     		$.post('?/ChargeManage/delete_charge_log/', {id: id}, function(r) {
	     			$.fancybox.close();
	     			if (r > 0) {
                        util.Alert('删除成功,2s后将自动刷新！',false,null,1000);
                    } else {
                        util.Alert('删除失败！', true);
                    }
	     			
	     			util.delay_refresh('?/WdminPage/user_charge_log_list/');
                });
	     	});
	     	
	     	$('#close').on('click', function () {
	     		$.fancybox.close();
	     	})
	     	
	     });
        
        
    });
});