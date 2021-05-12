<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
<meta name="format-detection" content="telephone=no" />
<link rel="stylesheet" href="{$docroot}static/layer/layer.css" />
<link rel="stylesheet" href="{$docroot}static/font/fonticon.css" />
<link rel="stylesheet" href="{$docroot}static/css/commodity.css" />
<link rel="stylesheet" href="{$docroot}static/css/bootstrap.css" />
<script data-main="{$docroot}static/script/Wshop/index.js?v={$cssversion}" src="{$docroot}static/script/require.min.js"></script>
<script src="{$docroot}static/layer/layer.js"></script>
<script src="{$docroot}static/iscroll/iscroll.js"></script>
 <script>
                var myscroll;
				var obj=new Object();
				obj.vScrollbar=false;	
                function loaded(){
                           myscroll=new iScroll("wrapper",obj);
                 }
               window.addEventListener("DOMContentLoaded",loaded,false);
         </script>
<title>Cheerslife暖心下午茶</title>
</head>

<body>
    <input type="hidden" value="{$catId}" id="cat" />
    <input type="hidden" value="{$show_tip}" name="show_tip" />
    <div class="product-body">
        <div class="header-time">
            <img src="{$docroot}static/img/time_re.png"  style="display:none;" class="top-mask">
            <section>
                <div class="sec-btn">
                    <div class="sec-send" id="selected_day">
                        <li>
                        <div class="text-left">
                            <span class="time-icon header-time-icon"><i class="usericon icon-cale"></i></span>
                            <span class="time-day">{$selected_day}</span><span class="time-weak">{$selected_weekday}</span>
                            </div>
                        </li>
                    </div>
                    <span class="select-icon"><i class="usericon icon-spread"></i></span>
                </div>
                <div class="select-time" style="display:none;"></div>
            </section>
        </div>
        <div class="header-empty" style="background-color:#ffffff;"></div>
        <div class="mid-mask" style="display:none;">
            <img src="{$docroot}static/img/reserve1.png" class="mask-1" >
            <img src="{$docroot}static/img/reserve2.png" class="mask-2"  >
        </div>
<img src="{$docroot}static/img/bottom-mask.png" class="bot-mask" style="display:none;">
  <div class="body-header">

    <div class="left-cat" id="wrapper">
      <ul>
        {foreach $topcat as $key => $val}
        {if {$key} == 0}
        <li class="cat-a active" cat-data="cat-01"><span class="cat-num">{$val['count']}</span><a class="cat-btn" data-catid="{$val.cat_id}">{$val.cat_name}</a></li>
        {else}
        <li  class="cat-c" cat-data="cat-03"><span class="cat-num">{$val['count']}</span><a class="cat-btn" data-catid="{$val.cat_id}">{$val.cat_name}</a></li>
        {/if}
        {/foreach}
        
      </ul>
    </div>
    <div  id="rightContainer"  class="right-pro" >
      <div id="list-loading" style="display: block; padding-top:50px;"><img src="{$docroot}static/images/icon/loading.gif" width="30"></div>
    </div>
  </div>
  <div class="body-footer">
    <div class="shopping-cart"><span class="cart-icon"><i class="proicon icon-cart"></i></span><span class="number">{$count}</span><span class="cart-price">&yen;{$total|string_format:"%.2f"}</span><span class="send-price"></span></div>
    <div class="settlement" id="buy">结算</div>
  </div>
  <div class="cart-list" style="display:none;"> <img src="{$docroot}static/img/mouse-ground.png" class="cartlist-bg"/>
    <div class="list-header" ><span class="header-left">商品列表</span><span class="header-right"><i class="proicon icon-empty"></i>清空购物车</span></div>
    <ul id="cart_list">
    </ul>
  </div>
</div>
</body>
</html>
