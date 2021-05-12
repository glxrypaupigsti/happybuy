
DataTableConfig.order = [[0, 'desc']];
DataTableConfig.searching =true;

requirejs(['jquery', 'util', 'fancyBox', 'datatables', 'Spinner'], function($, util, fancyBox, dataTables, Spinner) {
	 
    $(function() {
    	$('.dTable tfoot th').each( function (i) {
    		if(i!=8 && i!=0){
				 var title = $('.dTable tfoot th').eq($(this).index()).text();
	             $(this).html( '<input type="text" style="width:80px;" placeholder="'+title+'" />' );
    		}else{
    			$(this).html('');
    		}
        } );
        
    	var oTable = $('.dTable').dataTable(DataTableConfig).api();
        
    	oTable.columns().eq(0).each(function (colIdx) {
            $('input', oTable.column(colIdx).footer()).on( 'change', function () {
            	oTable.column(colIdx).search(this.value).draw();
            } );
        } );
    	
    	$('#DataTables_Table_0_filter').hide();
    	//初始化批量选择的效果
    	util.init_batch_select_effect();
    	//
    	$('#batch_del').click(function(){
    		var ids_arr = [];
    		$('input[name="check_list"]:checked').each(function(){
    			ids_arr.push($(this).attr('data-id'));
    		});
    		
    		if(ids_arr.length <= 0){
    			util.Alert('请选择要删除的记录',true,null,1000);
    			return;
    		}
    		
    		var r = confirm('删除后将不可恢复，确定删除吗');
    		if(r){
    			Spinner.spin($('#loading').get(0));
        		var ids = ids_arr.join(",");
        		$.get('?/Coupon/batch_delete_user_coupon/ids='+ids,function(data){
        			Spinner.stop();
        			if(data.ret_code > 0){
        				util.Alert(data.ret_msg+',2s后将自动刷新',false,null,1000);
        			}else{
        				util.Alert(data.ret_msg,true,null,1000);
        			}
        			util.delay_refresh('?/WdminPage/user_coupon_list/');
        		});
    		}
    		
    	})
    	
    	
        //删除函数
	    fnFancyBox('.userCouponDel',function(){
	     	$('#ok').on('click', function(){
	     		var id = $(this).attr('data-id');
	     		$.get('?/Coupon/delete_user_coupon/', {id: id}, function(r) {
	     			$.fancybox.close();
	     			if (r > 0) {
                        util.Alert('删除成功,2s后将自动刷新！',false,null,1000);
                    } else {
                        util.Alert('删除失败！', true);
                    }
	     			
	     			util.delay_refresh('?/WdminPage/user_coupon_list/');
                });
	     	});
	     	
	     	$('#close').on('click', function () {
	     		$.fancybox.close();
	     	})
	     	
	     });
        
        
    });
});