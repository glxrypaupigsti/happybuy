/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */

requirejs(['jquery', 'util', 'fancyBox', 'datatables'], function ($, util, fancyBox, dataTables) {
    $(function () {

        util.dataTableLis();

        fnFancyBox('#add-level, .levedit', function () {
            $('#al-com-save').unbind('click').click(function () {
                var cid = parseInt($(this).attr('data-id'));
                
                if(null == $('#name').val() || '' == $('#name').val() ){
                	util.Alert('会员等级不能为空');
                	return;
                }
                
                if(null == $('#credit').val() || '' == $('#credit').val() || isNaN($('#credit').val())){
                	util.Alert('积分必须为数字');
                	return;
                }
                
                if(isNaN($('#credit').val())){
                	util.Alert('积分必须为数字');
                	return;
                }
                
                if(isNaN($('#discount').val())){
                	util.Alert('积分必须为数字');
                	return;
                }
                
                if(isNaN($('#feed').val())){
                	util.Alert('积分返比必须为数字');
                	return;
                }
                
                $.post('?/WdminAjax/modUserLevel/', {
                    id: cid,
                    name: $('#name').val(),
                    discount: $('#discount').val(),
                    credit: $('#credit').val(),
                    feed: $('#feed').val(),
                    upable: $('#upable').get(0).checked ? 1 : 0
                }, function (res) {
                    if (res > 0) {
                        $.fancybox.close();
                        location.reload();
                        util.Alert('修改成功');
                    } else {
                        util.Alert('修改失败', true);
                    }
                });
            });
        });

        $('.delevel').click(function () {
            if (confirm('你确认要删除么')) {
                var node = $(this);
                $.post('?/WdminAjax/deleteLevel/', {
                    id: $(this).attr('data-id')
                }, function (res) {
                    if (res > 0) {
                        util.Alert('删除成功');
                        node.parents('tr').remove();
                    } else {
                        util.Alert('操作失败!', true);
                    }
                });
            }
        });

    });
});