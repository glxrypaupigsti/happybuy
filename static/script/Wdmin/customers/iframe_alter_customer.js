
var loading = false;

requirejs(['jquery', 'util', 'fancyBox', 'datatables', 'provinceCity', 'Spinner'], function($, util, fancyBox, dataTables, provinceCity, Spinner) {
    provinceCity.bind('#client_province', '#client_city');

    // ajaxAlterCustomer
    $('#save_btn').click(function() {
        if (!loading) {
            loading = true;
            $('#iframe_loading').show();
            Spinner.spin($('#iframe_loading').get(0));
            var id = parseInt($(this).attr('data-id'));
            $.post(shoproot + '?/WdminAjax/ajaxAlterCustomer/', {
                id: id,
                data: $('#form_alter_customer').serializeArray()
            }, function(r) {
                loading = false;
                Spinner.stop();
                $('#iframe_loading').hide();
                if (r > 0) {
                    util.Alert(id === 0 ? '添加成功' : '保存成功');
                } else {
                    util.Alert(id === 0 ? '添加失败，请检查你的输入!' : '保存失败', true);
                }
            });
        }
    });

    $('#delete_btn').click(function() {
        if (confirm('你确认要删除这个会员么，该操作无法恢复')) {
            var node = $(this);
            $.post(shoproot + '?/WdminAjax/ajaxDeleteCustomer/', {
                id: node.attr('data-id')
            }, function(res) {
                if (res.ret_code > 0) {
                    util.Alert('删除成功');
                    location.href = $('#http_referer').val();
                } else {
                    util.Alert(res.ret_msg, true);
                }
            });
        }
    });
});