
/* global shoproot, DataTableConfig */

var dT;
var loadingLock = false;
var target_page = 0;
requirejs(['jquery', 'util', 'fancyBox', 'Spinner', 'jUploader', 'jPrintArea'], function ($, util, fancyBox, Spinner, jUploader, jPrint) {
    
    $(function () {
        window.util = util;

        // 加载食材库存列表
        ajaxLoadIngredientsStocklist(util, listLoadCallback);
        $('#month-select').on('change', function () {
            ajaxLoadIngredientsStocklist(util, listLoadCallback);
        });
    });

    function listLoadCallback() {
        update_pagination();
    }
          
        function click_page()
        {
            target_page = $(this).attr('data-dt-idx');
            ajaxLoadIngredientsStocklist(util, listLoadCallback);
        }
          
        function update_pagination()
        {
          var pagination = '';
          var per_page = 10;
          total = $('input[name=total]').val();
          total = Math.ceil(total/per_page);
          
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
        }
});

function ajaxLoadIngredientsStocklist(util, callback)
{
    $('.dTable tbody').load('?/WdminAjax/loadIngredientsStock/page='+target_page, function (r) {
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