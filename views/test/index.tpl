<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
<link rel="stylesheet" href="{$docroot}static/font/fonticon.css" />
<link rel="stylesheet" href="{$docroot}static/css/commodity.css" />
 <script data-main="{$docroot}static/script/Wshop/index.js?v={$cssversion}" src="{$docroot}static/script/require.min.js"></script>

<title>Cheerslife暖心下午茶</title>
</head>

<body>
        <input type="hidden" value="{$catId}" id="cat" />

<div class="product-body">
  <div class="body-header">
    <div class="left-cat">
      <ul>
          {foreach $topcat as $key => $val}
                 {if {$key} == 0}
                        
                     <li class="cat-a active" cat-data="cat-01"><span class="cat-num">1</span><a class="cat-btn" data-catid="{$val.cat_id}">{$val.cat_name}</a></li>
                 {else}
                    <li  class="cat-c" cat-data="cat-03"><span class="cat-num">3</span><a class="cat-btn" data-catid="{$val.cat_id}">{$val.cat_name}</a></li>
                 
                 {/if}
          {/foreach}
      
      </ul>
      
    </div>
    <div  id="rightContainer"  class="right-pro" >
    <ul>
 
    </ul>
    </div>
  </div>
  <div class="body-footer"> <div class="shopping-cart"><span class="cart-icon"><i class="proicon icon-cart"></i></span><span class="number">{$count}</span><span class="cart-price">￥37</span><span class="send-price">&nbsp;|&nbsp;满68起送</span></div>
  <div class="settlement" id="buy">结算</div></div>
  
  <div class="cart-list" style="display:none;">
  <img src="{$docroot}static/img/mouse-ground.png" class="cartlist-bg"/>
<div style="background-color:#ffffff;">  <div class="list-header" ><span class="header-left">商品列表</span><span class="header-right"><i class="proicon icon-empty"></i>清空购物车</span></div>
  <ul id="cart_list">

  </ul>
  </div>
  </div>
  
</div>
</body>
</html>

