
/* global shoproot, DataTableConfig */

var dT;
var loadingLock = false;
var target_page = 0;
requirejs(['jquery', 'util', 'fancyBox', 'Spinner', 'jUploader', 'jPrintArea'], function ($, util, fancyBox, Spinner, jUploader, jPrint) {
    
    $(function () {
        window.util = util;

        // 加载产品库存列表
        ajaxLoadProductsStocklist(util, listLoadCallback);
        $('#month-select').on('change', function () {
            ajaxLoadProductsStocklist(util, listLoadCallback);
        });
    });

    function listLoadCallback() {
        update_pagination();
        fnFancyBox('.various', function () {
            $('#save_stock_btn').click(updateStock);
        });
    }
          
    function updateStock()
    {
        if (!loadingLock) {
        var postData = $("#stock_data").serializeArray();
          
        // data validation
        if (util.isEmpty( $('input[name="prd_stockid"]').val())){
          util.Alert("无效库存标识", true);
          return ;
        }
        if (util.isEmpty($('input[name="produce"]').val())) {
          util.Alert("请设置当日生产数量", true);
          return ;
        }
        if (util.isEmpty( $('input[name="loss"]').val())){
          util.Alert("请设置当日损耗数量", true);
          return ;
        }
          
        // data post
        $.post(shoproot + '?/WdminAjax/update_stock', postData, function (r) {
            result = JSON.parse(r);
            if (result.err == 0) {
               util.Alert("更新成功", false, function () {
                // close editbox
                $.fancybox.close();
                // reload list
                ajaxLoadProductsStocklist(util, listLoadCallback);
                });
            } else {
                util.Alert('更新失败:'+result.msg);
            }
        });
        }
    }
          
        function click_page()
        {
            target_page = $(this).attr('data-dt-idx');
            ajaxLoadProductsStocklist(util, listLoadCallback);
        }
          
        function update_pagination()
        {
            $.get('?/WdminAjax/getTotalProductsStock', function (result) {
                  if (result.err == 0) {
                    var pagination = '';
                    total = Math.ceil(parseInt(result.total)/10);
                    startPage = target_page - 3; endPage = target_page + 3;
                    if (startPage < 0) startPage = 0;
                    if (endPage >= total) endPage = total-1;
                
                    for (var i=startPage; i<=endPage; i++){
                        if (i != target_page) {
                            pagination += '<a class="paginate_button" data-dt-idx="'+i+'" >'+(i+1)+'</a>';
                        } else {
                            pagination += '<a class="paginate_button current" data-dt-idx="'+i+'" >'+(i+1)+'</a>';
                        }
                    }
                    $('.dataTables_paginate').html(pagination);
                    if (pagination.length > 0) {
                        $('.paginate_button').unbind().bind("click", click_page);
                    }
                } else {
                
                }
            }, 'json');
        }
});

function ajaxLoadProductsStocklist(util, callback)
{
    $('.dTable tbody').load('?/WdminAjax/ajaxLoadProductsStocklist/page='+target_page, function (r) {
        if (r === '0') {
            util.listEmptyTip();
        } else {
            if (callback !== undefined) {
                callback();
            }
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