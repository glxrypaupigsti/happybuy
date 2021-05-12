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

requirejs(['jquery', 'util', 'datatables', 'Spinner'], function ($, util,  dataTables, Spinner) {

	

    $('#saveBtn').click(function () {
    	var amount = $('input[name="amount"]').val();
    	var sale_price = $('input[name="sale_price"]').val();
    	var num = $('input[name="num"]').val();
    	var charge_code = $('input[name="charge_code"]').val();
    	
    	var id = parseInt($(this).attr('data-id'));
    	
    	if(!util.isFloatNumber(amount)){
    		util.Alert("面额必须为大于0的数字",true);
    		return ;
    	}
    	
    	if(!util.isFloatNumber(sale_price)){
    		util.Alert("售价必须为大于0的数字",true);
    		return ;
    	}
    	
    	if(id <= 0 || isNaN(id)){
    		if(!util.isIntNumber(num)){
        		util.Alert("数量不能为空且必须为整数",true);
        		return ;
        	}
    	}
    	Spinner.spin($('#loading').get(0));
    	$('#saveBtn').attr("disabled",true);
        $.post('?/ChargeManage/save_charge_card/', {
        	sale_price: sale_price,
        	num: num,
        	charge_code: charge_code,
            id: id,
            amount: amount
        }, function (r) {
        	Spinner.stop();
        	$('#saveBtn').removeAttr('disabled');
            if (r > 0) {
//            	Spinner.stop();
                if (id > 0) {
                    $('#saveBtn').attr('data-id', r);
                    util.Alert('保存成功');
                } else {
                    util.Alert('添加成功');
                }
                util.delay_refresh('?/WdminPage/charge_card_list');
            } else {
                util.Alert('操作失败', true);
            }
        });
    });
});