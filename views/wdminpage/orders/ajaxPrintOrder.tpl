<div id="order_print" style="width:270px;">
    <img src="{$docroot}static//images/login/logo-print.png" style="width: 65%;margin-left: 17%;margin-bottom: 10px;" />
    <div class="orderwpa-top">
        <span class="orderwpa-serial">
            订单编号：{$data.serial_number}
            <br />
            下单时间：{$data.order_time}
            <br />
        </span>
    </div>
    <div class="clearfix" style="margin:10px 0;">
        {section name=od loop=$data.products}
        <div class='orderwpa-pdlist' style="float:none;width:100%;height:26px;">
            <div style="margin-left: 10px;line-height: 20px;">
                <div class="Elipsis pro_fontsize" style="float:left;">{$data.products[od].product_name}</div>
                <div class="price-right" style="float:right;">
                    <i class="opprice">&yen;{$data.products[od].product_discount_price}</i> &times;
                    <i id="order{$data.order_id}count" class="opcount">{$data.products[od].product_count}</i>
                </div>
            </div>
        </div>
        {/section}
    </div>
<div style="border-top:1px dashed #8a8e8f;">
        <div class='orderwpa-pdlist' style="margin-top:5px;float:none;width:100%;height:26px;">
            总金额：<div class="price-right" style="float:right;">&yen;{$data.order_amount}</div>
        </div>
        <div class='orderwpa-pdlist' style="margin-top:5px;float:none;width:100%;height:26px;">
            优惠：<div class="price-right" style="float:right;">-&yen;{($data.order_amount-$data.pay_amount)|string_format:"%.2f"}</div>
        </div>
        <div class='orderwpa-pdlist' style="margin-top:5px;float:none;width:100%;height:26px;">
            实付：<div class="price-right" style="float:right;">&yen;{$data.pay_amount}</div>
        </div>
    </div>
<div class="orderwpa-address clearfix" style="font-size:16px;">
        <p>顾客姓名：{$data.address.user_name}</p>
        <p>联系电话：{$data.address.phone}</p>
        <p>地址：{$data.address.province}{$data.address.city}{$data.address.address}</p>
        <p>送达时间：{$data.exptime}</p>
        <!--<p>备注：{$data.leword}</p>-->
        <!--<p>邮编：{$data.address.postal_code}</p>-->
        {*if $data.reci_head neq ''}
            <p>发票抬头：{$data.reci_head}</p>
            <p>发票内容：{$data.reci_cont}</p>
            <p>发票税额：&yen;{$data.reci_tex}</p>
        {else}
        	<p>发票信息：不开发票</p>
        {/if*}
    </div>
    <img src="{$docroot}?/WdminPage/genOrderTrackQRCode/id={$data.order_id}" style=" width:50%;margin-left: 25%;margin-bottom: 10px;" />
</div>
<script>$("div#order_print").printArea();</script>