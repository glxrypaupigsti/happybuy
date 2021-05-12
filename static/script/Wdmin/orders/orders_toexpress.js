
/* global shoproot, DataTableConfig */

var dT;

DataTableConfig.order = [[0, 'desc']];
DataTableConfig.searching = true;
requirejs(['jquery', 'util', 'fancyBox', 'datatables', 'Spinner', 'jUploader', 'jpaginate','jPrintArea'], function ($, util, fancyBox, dataTables, Spinner, jUploader, jPrint) {
    $(function () {
        window.util = util;
        util.loadOrderStatNums();

        // 加载订单列表
        ajaxLoadOrderlist(util, listLoadCallback);
        $('#month-select').on('change', function () {
            ajaxLoadOrderlist(util, listLoadCallback);
        });

        $('#data-exp').click(dataExp);
        $('#data-exp-confirm').click(dataExpConfirm);
        $('#data-exp-cancel').click(dataExpCancel);

        // 订单xlsx数据导出按钮
        fnFancyBox('#data-exp-confirm-hide', function () {
            $('.fancybox-skin').css('background', '#fff');
            $('.fancybox-skin').css('padding', '0');
            Spinner.spin($('#od-exp-frame').get(0));
            $.get(shoproot + '?/WdminAjax/ajaxOrderByIdsExporting/ids=' + checks_orderIds, function (html) {
                Spinner.stop();
                $('#od-exp-frame').html(html);
                $.fancybox.update();
                var data = [];
                $('#data-exp-do').click(function () {
                    if (data.length === 0) {
                        $('.dTableX tr').each(function () {
                            var tr = $(this);
                            var _data = {};
                            tr.find('input').each(function () {
                                eval("_data." + $(this).attr('name') + " = '" + $(this).val().replace("'", "") + "';");
                            });
                            data.push(_data);
                        });
                    }
                    $('.fancybox-inner').eq(0).append('<div class="divblock"></div>');
                    Spinner.spin($('.divblock').get(0));
                    $.post(shoproot + '?/XlsxExport/exportOrderList/', {
                        data: data,
                        expType: $('#exp-type').val()
                    }, function (r) {
                        if (r !== '0') {
                            util.Alert('数据生成成功');
                            $('#data-exp-do').hide();
                            $('.divblock').remove();
                            $('#data-exp-dl').attr('href', r).removeClass('hidden');
                            $('#data-exp-dl').get(0).click();
                            Spinner.stop();
                        }
                    });
                });
                $('.pricSig').on('keyup', function () {
                    if ($(this).val() === '') {
                        $(this).val(0);
                    }
                    $('#pricTotal' + $(this).attr('rel')).val($(this).val() * $(this).attr('data-count'));
                });
            });
        });

        // 订单数据转换按钮
        fnFancyBox('#data-exp-trans', function () {
            $('.fancybox-skin').css('background', '#fff');
            $('.fancybox-skin').css('padding', '0');
            $.jUploader({
                button: $('#data-exp-upload').get(0),
                action: shoproot + '?/XlsxExport/ajaxXlsTransform/',
                onUpload: function (fileName) {
                    Spinner.spin($('#od-exp-frame').get(0));
                },
                onComplete: function (fileName, response, html) {
                    Spinner.stop();
                    $('#od-exp-frame').html(html);
                    $.fancybox.update();
                }
            });
        });
    });


    function listLoadCallback() {
        fnFancyBox('.various', function () {
            // 发货按钮点击
            $('#despatchBtn').unbind('click').bind('click', function () {
                var orderId = parseInt($(this).attr('data-orderid'));
                var despatchExpressCode = $('#despatchExpressCode').val();
                var expressCompany = $('#expressCompany').val();
                if (despatchExpressCode === "") {
                    // 必须填入单号
                    $('#despatchExpressCode').addClass('shake').css('border-color', '#900');
                    setTimeout(function () {
                        $('#despatchExpressCode').removeClass('shake');
                    }, 500);
                } else {
                    // 发货走起
                    $('.fancybox-skin').eq(0).append('<div id="iframe_loading" style="top:0;background:rgba(255,255,255,0.7);"></div>');
                    Spinner.spin($('#iframe_loading').get(0));
                    // loading
                    $.post('?/Order/ExpressReady/', {
                        'orderId': orderId,
                        'ExpressCode': despatchExpressCode,
                        'expressCompany': expressCompany,
                        'expressStaff': $('#expressStaff').val()
                    }, function (res) {
                        Spinner.stop();
                        $('#iframe_loading').remove();
                        // loading stop
                        if (res === "1") {
                            util.Alert('发货成功');
                            dT.row($('#order-exp-' + orderId)).remove().draw();
                            $.fancybox.close();
                        } else {
                            util.Alert('发货失败，系统错误！');
                        }
                    });
                }
            });
        });

        //添加备注
        fnFancyBox('.notes', function () {
            $('#save_btn').unbind('click').bind('click',function(){
                $order_id = $('#order_id').val();
                $notes = $('#notes').val();

                if($notes.length > 100){
                    util.Alert('备注长度不能大于100',true);
                    return;
                }

                $url = '?/WdminAjax/updateNotes';
                $.post($url,{
                    'order_id' : $order_id,
                    'notes' : $notes
                },function(data){
                    if(data.ret_code >= 0){
                        util.Alert('修改成功');
                        $.fancybox.close();
                    }else{
                        util.Alert(data.ret_msg,true);
                    }
                });


            });
        });
    }
});

function ajaxLoadOrderlist(util, callback) {
    $('#orderlist').load('?/Wdmin/ajaxLoadOrderlist/page=0&month=' + $('#month-select').val(), function (r) {
        if (r === '0') {
            util.listEmptyTip();
        } else {
            if (callback !== undefined) {
                callback();
            }
            $('.wshop-empty-tip').remove();
            dT = $('.dTable').dataTable(DataTableConfig);
            //必须要加上这句，否则会在列表中第一行出现空格
            util.remove_margin_top();
        }
    });
}

function dataExp() {
    DataTableSelect = true;
    DataTableMuli = true;
    $('.od-exp-check').show();
    $('.wd-btn.hidden').removeClass('hidden');
    $('#data-exp-trans').hide();
    $(this).hide();
    dataTableLis();
}

function dataExpConfirm() {
    window.checks_orderIds = [];
    window.checks = $('.pd-exp-checks:checked');
    if (checks.length > 0) {
        checks.each(function () {
            checks_orderIds.push($(this).attr('data-id'));
        });
        checks_orderIds = checks_orderIds.join(',');
        location.href = shoproot + '?/XlsxExport/exportTransform/islocal=1&odlist=' + checks_orderIds;
    } else {
        util.Alert('请先选择要导出的订单', true);
    }
}

function dataExpCancel() {
    DataTableSelect = false;
    DataTableMuli = false;
    $('.od-exp-check').hide();
    $('#data-exp').show();
    $('#data-exp-confirm').addClass('hidden');
    $('#data-exp-trans').show();
    $(this).addClass('hidden');
    location.reload();
}