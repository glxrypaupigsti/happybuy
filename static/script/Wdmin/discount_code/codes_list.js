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

        $('.delete-codes').click(function () {
            if (confirm('你确定要删除吗')) {
                var id = $(this).attr('data-id');
                $.post('?/wDiscountCode/alterCode/', {
                    id: -1 * id
                }, function (r) {
                    if (r.ret_code === 0) {
                        util.Alert('操作成功', false, function () {
                            location.reload();
                        });
                    } else {
                        util.Alert('操作失败', true);
                    }
                });
            }
        });

        fnFancyBox('#add_codes, .alter-codes', function () {
            $('#save_btn').click(function () {
                var codes = $('#gcodes').val();
                if (codes !== '') {
                    var id = $(this).attr('data-id');
                    $.post('?/wDiscountCode/alterCode/', {
                        id: id,
                        codes: codes,
                        qid: $('#qid').val()
                    }, function (r) {
                        $.fancybox.close();
                        if (r.ret_code === 0) {
                            $('#codes-' + id).html(codes);
                            util.Alert('操作成功', false, function () {
                                location.reload();
                            });
                        } else {
                            util.Alert('操作失败', true);
                        }
                    });
                } else {
                    util.Alert('请输入关键字', true);
                }
            });
        });

        // 批量上传
        $.jUploader({
            button: 'code_upload',
            action: '?/wDiscountCode/upload/id=' + $('#qid').val(),
            accept: 'txt',
            onComplete: function (fileName, response) {
                if (response.ret_code === 0) {
                    util.Alert('操作成功', false, function () {
                        location.reload();
                    });
                } else {
                    util.Alert('操作失败!', true);
                }
            }
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