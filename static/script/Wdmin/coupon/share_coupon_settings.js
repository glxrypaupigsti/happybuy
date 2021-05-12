/* global hov */

/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */

requirejs(['util', 'fancyBox', 'Spinner', 'jUploader', 'ztree', 'ztree_loader', 'baiduTemplate'], function (util, fancyBox, Spinner, jUploader, ztree, treeLoader, baiduTemplate) {

    var hov = $('#expcompany').val();
    
    var hov_arr = hov.split(',');
    if (hov !== '') {
        $('.expitem').each(function () {
        	var key = $(this).attr('data-k');
        	for(var i=0,len=hov_arr.length;i<len;i++){
        		if(hov_arr[i] == key){
        			$(this).addClass('hov');
        			break;
        		}
        	}
        	
        });
    }

    $('.expitem').click(function () {
        $(this).toggleClass('hov');
    });


    $('#saveBtn').click(function () {
        hov = [];
        $('.expitem.hov').each(function () {
            hov.push($(this).attr('data-k'));
        });
        
        // [HttpPost]
        $.post('?/wSettings/updateSettings/', {
            data: [
                {
                    name: 'user_share_coupons',
                    value: hov.join(',')
                }
            ]
        }, function (r) {
            if (r > 0) {
                util.Alert('保存成功');
            } else {
                util.Alert('保存失败', true);
            }
        });
    });
   

});