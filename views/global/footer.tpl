{include file="./copyright.tpl"}
<div class="bottom_nav">  
    <a class="nav_index {if $controller eq 'Index' or $controller eq 'vProduct'}hover{/if}" href="{$docroot}"><i></i>购物</a>
    <a class="nav_search {if $controller eq 'Search'}hover{/if}" href="{$docroot}?/vProduct/view_category/"><i></i>搜索</a>
    <a class="nav_shopcart {if $controller eq 'Order'}hover{/if}" href="{$docroot}?/Cart/cart"><i></i>购物车</a>
    <a class="nav_me {if $controller eq 'Uc' or $controller eq 'Company'}hover{/if}" href="{$docroot}?/Uc/home"><i></i>个人中心</a>
</div>