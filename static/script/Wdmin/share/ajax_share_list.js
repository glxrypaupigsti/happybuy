
/* global shoproot, DataTableConfig */

var dT;
var loadingLock = false;

requirejs(['jquery', 'util', 'fancyBox', 'Spinner', 'jUploader', 'jpaginate','jPrintArea'], function ($, util, fancyBox, Spinner, jUploader, jPrint) {
    
    $(function () {
        window.util = util;

        // 加载产品库存列表
        ajaxShareList(util, callback);
        $('#month-select').on('change', function () {
        	ajaxShareList(util, listLoadCallback);
        });
    });

});

function ajaxShareList(util, callback) {
    $('.dTable tbody').load('?/WdminAjax/ajaxShareList/page=0&month=' + $('#month-select').val(), function (r) {
        if (r === '0') {
            util.listEmptyTip();
        } else {
            if (callback !== undefined) {
                callback();
            }
        }
    });
}

function callback(){
	
	
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