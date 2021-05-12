
DataTableConfig.order = [[0, 'desc']];
DataTableConfig.searching =true;
DataTableSelect = true;
DataTableMuli = true;
DataTableConfig.deferRender=true
requirejs(['jquery', 'util', 'fancyBox', 'datatables', 'Spinner'], function($, util, fancyBox, dataTables, Spinner) {

	var dt ;
	 
    $(function() {
    	
        $('.dTable tfoot th').each( function (i) {
        	if(i!=0 && i!=1 && i!=2 && i!=3 && i!=11 ){
        		 var title = $('.dTable tfoot th').eq( $(this).index() ).text();
                 $(this).html( '<input type="text" style="width:80px;" placeholder="'+title+'" />' );
        	}else{
        		$(this).html('');
        	}
        } );
        
        dt = $('.dTable').dataTable(DataTableConfig).api();
        
        dt.columns().eq(0).each(function (colIdx) {
//            $('input', dt.column(colIdx).footer()).on( 'keyup change', function () {
            $('input', dt.column(colIdx).footer()).on( 'change', function () {
                dt.column(colIdx).search(this.value).draw();
            } );
        } );
        
        $('#DataTables_Table_0_filter').hide();
        
        var refresh_url = '?/WdminPage/charge_card_list';
        
        //制卡函数
	    fnFancyBox('.chargeCardDel',function(){
	     	$('#ok').on('click', function(){
	     		var id = $(this).attr('data-id');
	     		$.post('?/ChargeManage/delete_charge_card/', {id: id}, function(r) {
	     			$.fancybox.close();
	     			if (r > 0) {
//                        dt.row(node.parents('tr')).remove().draw();
                        util.Alert('删除成功,2s后将自动刷新！',false,null,1000);
                    } else {
                        util.Alert('删除失败！', true);
                    }
	     			
	     			util.delay_refresh(refresh_url);
                });
	     	});
	     	
	     	$('#close').on('click', function () {
	     		$.fancybox.close();
	     	})
	     	
	     });
        
       //制卡函数
       fnFancyBox('.delievercard',function(){
        	$('#ok').on('click', function(){
        		var id = $(this).attr('data-id');
        		$.post('?/ChargeManage/deliever_card/', {id: id}, function(r) {
        			$.fancybox.close();
        			if (r > 0) {
        				util.Alert('制卡成功,2s后将自动刷新！',false,null,1000);
        			} else {
        				util.Alert('制卡失败！', true);
        			}
        		});
        		
        		util.delay_refresh(refresh_url);
        		
        	});
        	$('#close').on('click', function () {
        		$.fancybox.close();
        	})
        	
        });
       
       //激活函数
       fnFancyBox('.activatedcard',function(){
    	   $('#ok').on('click', function(){
    		   var id = $(this).attr('data-id');
    		   $.post('?/ChargeManage/activated_card/', {id: id}, function(r) {
			   $.fancybox.close();  
       		   if (r > 0) {
       				util.Alert('激活成功,2s后将自动刷新！',false,null,1000);
       			} else {
       				util.Alert('激活失败！', true);
       			}
       		    util.delay_refresh(refresh_url);
       		 });
    	  });
    	   $('#close').on('click', function () {
    		   $.fancybox.close();
    	   })
    	   
       });
        
        
     
        
        $('#mkcard').click(function(){
        	$('#mkcard_confirm').show();
        	$('.checkbox').removeClass('hidden');
        });
        
        $('#mkcard_confirm').click(function(){
        	var ids = [];
        	$('.pd-exp-checks').each(function(){
        		ids.push($(this).attr('data-id'));
        	});
        });
        
        //选中所有的
        $('.checkAll').click(function(){
        	if($(this).prop("checked") == true){ 
        		$('.pd-exp-checks').each(function(){
        			$(this).attr('checked',true);
        		})
        	}else{
        		$('.pd-exp-checks').each(function(){
        			$(this).attr('checked',false);
        		})
        	}
        });
        
    });
});