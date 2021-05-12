<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
<link rel="stylesheet" href="{$docroot}static/layer/layer.css" />
<link rel="stylesheet" href="{$docroot}static/font/fonticon.css" />
<link rel="stylesheet" href="{$docroot}static/css/commodity.css" />
<link rel="stylesheet" href="{$docroot}static/css/mobile.css" />
<link rel="stylesheet" href="{$docroot}static/css/bootstrap.css" />


<script data-main="{$docroot}static/script/Wshop/detail.js" src="{$docroot}static/script/require.min.js"></script>
<script src="{$docroot}static/layer/layer.js"></script>
<title>Cheerslife暖心下午茶</title>
</head>

<body>
        <input type="hidden" value="{$productid}" id="iproductId" />

<div class="detail-header comm-detail-header"> <span class="backicon"  onclick="window.history.back()"><i class="proicon icon-back"></i></span> <span class="title-text">商品详情</span> </div>
<div class="empty-header" style="height:51px;"></div>
<div class="comm-detail">
<div class="commodity-img">
<img src="{if $config.usecdn}{$config.imagesPrefix}product_hpic/{$images[0].image_path}_x500{else}{$config.productPicLink}{$images[0].image_path}{/if}">
</div>
<div class="commodity-body" data-instock="{$stock_info.stock}"  data-p="{$productInfo.product_id}" data-sp="{$specs[0].id}"  data-hash = "p{$productInfo.product_id}m{$specs[0].id}">
<div class="price-body">
<div class="comm-title"> {$productInfo.product_name}</div>
<div class="comm-price">
<span class="price-num">
{if $specs[0].sale_price != $specs[0].market_price}
<s style="color: #9a9a9a;font-size: 16px;"><b>&yen;</b>{$specs[0].market_price|string_format:"%.2f"}</s>&nbsp;&nbsp;
<b>&yen;</b>{$specs[0].sale_price|string_format:"%.2f"}
{else}
<b>&yen;</b>{$specs[0].sale_price|string_format:"%.2f"}
{/if}
</span>
<div class="price-detail">


{if $specs[0].instock > 0}
{if $count == '0'}
<div class="minus-hidden" style="display:none;">
<i class="proicon icon-minus"></i><span class="num">0</span></div><i class="proicon icon-plus" ></i>
</div>
{else}
<div class="minus-hidden">
		<i class="proicon icon-minus"></i><span class="num">{$count}</span></div><i class="proicon icon-plus" ></i>
{/if}
{/if}

<div class="clear"></div>
</div>
<div class="clear"></div>
</div>
<div class="commodity-infor" id="vpd-content">

</div>
</div>


<div class="body-footer comm-footer"> <div class="shopping-cart"><span class="cart-icon"><i class="proicon icon-cart"></i></span><span class="number">{$totalCount}</span><span class="cart-price">&yen;{$total|string_format:"%.2f"}</span><span class="send-price"></span></div>
  <div class="settlement" id="buy">结算</div></div>
  
  <div class="cart-list" style="display:none;">
<img src="{$docroot}static/img/mouse-ground.png" class="cartlist-bg"/>
 <div class="list-header" ><span class="header-left">商品列表</span><span class="header-right"><i class="proicon icon-empty"></i>清空购物车</span></div>
  <ul id="cart_list">
  </ul>
  </div>
</div>

</body>
</html>

