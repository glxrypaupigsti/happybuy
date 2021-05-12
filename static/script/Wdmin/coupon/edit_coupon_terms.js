/* global shoproot */

/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */

requirejs(['jquery', 'util', 'datatables', 'Spinner', ], function ($, util,  dataTables, Spinner) {

	var table_column_code = '[{"table_name":"用户","table_value":"client","columns":[{"name":"积分","value":"client_credit"},{"name":"用户等级","value":"client_level"}]},{"table_name":"商品","table_value":"product_info","columns":[{"name":"满减","value":"selected_amount"},{"name":"每满减","value":"selected_mod_amount"},{"name":"加X换B","value":"selected_quantity"}]},{"table_name":"订单","table_value":"order","columns":[{"name":"总额","value":"order_amount"},{"name":"满减","value":"selected_amount"},{"name":"每满减","value":"selected_mod_amount"}]}]';
	var arr = JSON.parse(table_column_code);
	var options = getColumnsOption('client');
	var tableOptions = getTablesOption();
	
	$('#table').append(tableOptions);
	
	
	$('#table').change(function(){
		var columnOption = getColumnsOption($(this).val());
		$('#column').empty();
		$('#column').append(columnOption);
	});
	
	$('#table').change();
	

    $('#saveBtn').click(function () {
    	var term_name = $('input[name="term_name"]').val();
    	var term_table = $('#table').val();
    	var term_column = $('#column').val();
    	var term_operate = $('#operate').val();
    	var term_detail = $('input[name="term_detail"]').val();
    	
    	if(util.isEmpty(term_name)){
    		util.Alert("名称不能为空",true);
    		return ;
    	}
    	
    	
        var id = $(this).attr('data-id');
        $.post('?/Coupon/save_coupon_terms/', {
        	term_name: term_name,
        	term_table: term_table,
        	term_column: term_column,
            id: id,
            term_operate: term_operate,
            term_detail: term_detail
        }, function (r) {
            if (r.ret_code > 0) {
                if (id > 0) {
                    $('#saveBtn').attr('data-id', r);
                    util.Alert('保存成功');
                } else {
                    util.Alert('添加成功');
                }
                
                window.setTimeout(function () {
                    location.href = '?/WdminPage/coupon_terms';
                }, 2000);
                
            } else {
                util.Alert(r.ret_msg, true);
            }
        });
    });
    
    

	function getTablesOption(){
		var len = arr.length;
		var options,table;
		for(i=0;i<len;i++){
			table = arr[i];
			options += '<option value="'+table.table_value+'">'+table.table_name+'</option>'
		}
		return options;
	}
	
	
	function getColumnsOption(table_name){
		var len = arr.length;
		var colLen ;
		var table ;
		var i,j,column;
		var options = '';
		for(i=0;i<len;i++){
			var columns;
			table = arr[i];
			if(table.table_value == table_name){
				columns = table.columns;
				colLen = columns.length;
				for(j=0;j<colLen;j++){
					column = columns[j];
					options += '<option value="'+column.value+'">'+column.name+'</option>'
				}
				break;
			}
		}
		return options;
	}

});