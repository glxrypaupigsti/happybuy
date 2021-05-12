
function update_dist_status(e)
{
    var status = $(e).closest('.take-order-now').attr('data-status');
    var id = $(e).closest('.take-order-now').attr('data-id');

    $.get("?/WdminAjax/update_dist_status/id="+id+"&status="+status, function (r) {
          location.reload();
    });
}