 {foreach from=$product_list item=pd}

  <li class="remove_li" data-stock={$pd['pinfo'].instock} ><div class="prolist">
  <span class="title-pro">{$pd.product_name}</span>
  
  <div class="pro-buy-num"  data-p="{$pd.product_id}" data-sp="{$pd['pinfo'].id}" data-hash = "p{$pd.product_id}m{$pd['pinfo'].id}">
  <span class="pro-price-num">&yen; {$pd['pinfo'].sale_price|string_format:"%.2f"} </span>
  <i class="proicon icon-minus" id="cart_minus" style="font-size:24px;"></i><span class="num">{$pd.product_quantity}</span><i class="proicon icon-plus" id="cart_plus" style="font-size:24px;"></i></div></div>
    
  </li>
  {/foreach} 