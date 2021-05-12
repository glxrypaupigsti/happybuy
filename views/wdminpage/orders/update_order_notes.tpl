<div style="width:200px">

    <div class="gs-label">订单编号</div>
    <div class="gs-text">
        <input type="hidden" value="{$order_id}" id="order_id" autofocus/>
        <input type="text" value="{$order.serial_number}" id="order_serial_no" readonly autofocus/>
    </div>

    <div class="gs-label">备注信息</div>
    <div class="gs-text">
        <textarea id="notes" rows="10" cols="22">{$order.notes}</textarea>
    </div>
    <div class="center" style="margin-top: 15px">
        <a id="save_btn" href="javascript:;" class="wd-btn primary">提交</a>
    </div>
</div>