/* global DataTableConfig */

/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */

requirejs(['jquery', 'util', 'datatables', 'jUploader', 'fancyBox'], function ($, util, dataTables, jUploader) {
    $(function () {

        var dt = $('.dTable').dataTable(DataTableConfig).api();

        $('.delete-discount').click(function () {
            if (confirm('你确定要删除吗')) {
                var id = $(this).attr('data-id');
                var node = $(this);
                $.post('?/wDiscountCode/alterCodes/', {
                    id: -1 * id
                }, function (r) {
                    if (r.ret_code === 0) {
                        util.Alert('操作成功', false, function () {
                            node.parents('tr').slideUp();
                        });
                    } else {
                        util.Alert('操作失败', true);
                    }
                });
            }
        });

        fnFancyBox('#add_discounts, .alter-discount', function () {
            $('#save_btn').click(function () {
                var keywords = $('#gkeyword').val();
                var discount = parseFloat($('#gdiscount').val());
                var template = $('#mpdcont').val();
                if (discount === '') {
                    return util.Alert('请输入优惠价格', true);
                }
                if (keywords === '') {
                    return util.Alert('请输入关键字', true);
                }
                var id = $(this).attr('data-id');
                $.post('?/wDiscountCode/alterCodes/', {
                    id: id,
                    keywords: keywords,
                    discount: discount,
                    template: template
                }, function (r) {
                    $.fancybox.close();
                    if (r.ret_code === 0) {
                        $('#keywords-' + id).html(keywords);
                        util.Alert('操作成功');
                    } else {
                        util.Alert('操作失败', true);
                    }
                });

            });
        });

        $('.envs_del').click(function () {
            if (confirm('你确认要删除么')) {
                var node = $(this);
                $.post('?/wSettings/delteEnvs/', {
                    id: $(this).attr('data-id')
                }, function (res) {
                    if (res > 0) {
                        util.Alert('删除成功');
                        dt.row(node.parents('tr')).remove().draw();
                    } else {
                        util.Alert('操作失败!', true);
                    }
                });
            }
        });

    });
});