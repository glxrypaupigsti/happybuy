<ul>
    {foreach from=$products item=pd}
    <li cat-data="cat-03" data-stock={$pd['pinfo'].instock} >
        <div class="pro-img" onclick="location = '{$docroot}?/vProduct/view/id={$pd.product_id}&showwxpaytitle=1';">
            <img src="/uploads/product_hpic/{$pd.catimg}" />
        </div>
        <div class="pro-detail">
            <div class="pro-title">
                <div class="titleleft">
                    <span class="title-text" id="product_name" >{$pd.product_name}</span>
                    {if $pd['pinfo'].instock <= 10 && $pd['pinfo'].instock != 0}
                    <span class="pro-num">{$prefix}仅剩{$pd['pinfo'].instock}份</span>
                    {else if $pd['pinfo'].instock >10}
                    <span class="pro-limit">{$prefix}限量供应</span>
                    {else if $pd['pinfo'].instock < 1}
                    <span class="sold-out">{$prefix}已售罄</span>
                    {/if}
                </div>
            </div>
            <div class="pro-intru" data-p="{$pd.product_id}" data-sp="{$pd['pinfo'].id}"  data-hash = "p{$pd.product_id}m{$pd['pinfo'].id}" >{$pd.product_subname}</div>
        </div>
        <div class="pro-price">
            <span class="price-num" >
                {if $pd['pinfo'].sale_price != $pd['pinfo'].market_price}
                <s style="color: #9a9a9a;font-size: 16px;"><b>&yen;</b>{$pd['pinfo'].market_price|string_format:"%.2f"}</s>&nbsp;&nbsp;
                <b>&yen;</b>{$pd['pinfo'].sale_price|string_format:"%.2f"}
                {else}
                <b>&yen;</b>{$pd['pinfo'].sale_price|string_format:"%.2f"}
                {/if}
            </span>
            <div class="buy-num">
            <!-- 未点单隐藏style-->
            {if  $pd['pinfo'].instock > 0}
                {if $pd.product_quantity == '0'}
                <div class="hidden" style="display:none;"><i class="proicon icon-minus"></i><span class="num">{$pd.product_quantity}</span></div><i class="proicon icon-plus" ></i>
                {else}
                <div class="hidden" ><i class="proicon icon-minus"></i><span class="num">{$pd.product_quantity}</span></div><i class="proicon icon-plus" ></i>
                {/if}
            {/if}
            </div>
            <div class="clear"></div>
        </div>
    </li>
    {/foreach}
</ul>
  