
/* global shoproot, DataTableConfig */

var loadingLock = false;

requirejs(['jquery', 'util', 'fancyBox', 'Spinner'], function ($, util, fancyBox, Spinner) {
    $(function () {
        window.util = util;
            
        $("#add_prd_stock_btn").click(add_product);
        $('#pd-select').change(function(){
            sku_name = $(this).children('option:selected').html();
            $("input[name='sku_name']").val(sku_name);
        });
    });
          
    function add_product()
    {
        var postData = $("#stock-form").serializeArray();
        $.post(shoproot + '?/WdminAjax/add_product_stock', postData, function (r) {
            result = JSON.parse(r);
            if (result.err == 0) {
                util.Alert("添加成功", false, function () {
                        window.history.back();
                    });
                 } else {
                    util.Alert('添加失败:'+result.msg);
                 }
        });
    }
});