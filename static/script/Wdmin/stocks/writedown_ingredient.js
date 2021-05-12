
/* global shoproot, DataTableConfig */

var loadingLock = false;

requirejs(['jquery', 'util', 'fancyBox', 'Spinner'], function ($, util, fancyBox, Spinner) {
          $(function () {
            window.util = util;
            
            $("#save_btn").click(writedown_ingredient);
            });
          
          function writedown_ingredient()
          {
          var postData = $("#ingredient-form").serializeArray();
          $.post(shoproot + '?/WdminAjax/writedown_ingredient', postData, function (r) {
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