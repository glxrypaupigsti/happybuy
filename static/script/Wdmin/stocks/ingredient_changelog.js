
/* global shoproot, DataTableConfig */

var loadingLock = false;
var target_page = 0;
requirejs(['jquery', 'util', 'fancyBox', 'Spinner', 'jUploader', 'jPrintArea'], function ($, util, fancyBox, Spinner, jUploader, jPrint) {
          
        $(function () {
            window.util = util;
            ajaxLoadIngredientChangelog(util, listLoadCallback);
          
            $('#data-exp').click(dataExp);
            $('#data-exp-confirm').click(dataExpConfirm);
            $('#data-exp-cancel').click(dataExpCancel);
        });
          
          function listLoadCallback()
          {
            update_pagination();
          }
          
          function click_page()
          {
            target_page = $(this).attr('data-dt-idx');
            ajaxLoadIngredientChangelog(util, listLoadCallback);
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

function ajaxLoadIngredientChangelog(util, callback)
{
    ingd_id = $("input[name=id]").val();

    $.get('?/WdminAjax/loadIngredientChangelog/page='+target_page+'&id='+ingd_id, function (r) {
          if (r === '0') {
                util.listEmptyTip();
          } else {
                $('.dTable tbody').html(r);
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
    $('.dataTables_paginate').hide();
    $(this).hide();
    dataTableLis();
}

function dataExpConfirm() {
    window.selected_Ids = [];
    window.checks = $('.pd-exp-checks:checked');
    if (checks.length > 0) {
        checks.each(function () {
            selected_Ids.push($(this).attr('data-id'));
        });
        exportIds = selected_Ids.join(',');
        location.href = shoproot + '?/XlsxExport/exportIngredientChangeLog/ids=' + exportIds;
    } else {
        util.Alert('请先选择要导出的项目', true);
    }
}

function dataExpCancel() {
    DataTableSelect = false;
    DataTableMuli = false;
    $('.od-exp-check').hide();
    $('#data-exp').show();
    $('#data-exp-confirm').addClass('hidden');
    $('#data-exp-trans').show();
    $('.dataTables_paginate').show();
    $(this).addClass('hidden');
    //location.reload();
}