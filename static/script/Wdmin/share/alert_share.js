
/* global shoproot, DataTableConfig */

var loadingLock = false;

requirejs(['jquery', 'util', 'fancyBox', 'Spinner'], function ($, util, fancyBox, Spinner) {
    $(function () {
        window.util = util;
            
        $("#add_share").click(add_product);
        
    });
          
    function add_product()
    {
        var postData = $("#share-form").serializeArray();
        $.post(shoproot + '?/WdminAjax/alert_share_setting', postData, function (r) {
   
          util.Alert("修改成功", false, function () {
                        window.location.reload();
          });
                
        });
    }
});