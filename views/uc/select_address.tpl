<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
    <meta name="format-detection" content="telephone=no" />  
    <meta name="format-detection" content="email=no" /> 
<link rel="stylesheet" href="{$docroot}static/font/fonticon.css" />
<link rel="stylesheet" href="{$docroot}static/css/mobile.css" />
<link rel="stylesheet" href="{$docroot}static/layer/layer.css" />
<script language="javascript" src="{$docroot}static/script/jquery-2.1.1.min.js"></script>
<script data-main="{$docroot}static/script/Wshop/address.js?v={$smarty.now}" src="{$docroot}static/script/require.min.js"></script> 
<script src="{$docroot}static/layer/layer.js"></script>
<title>Cheerslife暖心下午茶</title>
</head>

<body style="background-color:#eeeeee;">
<div class="select-add-body">
<input type="hidden" id="couponId" value="{$couponId}" />
<input type="hidden" id="time" value="{$time}" />
<input type="hidden" id="isbalance" value="{$isbalance}" />

<div class="detail-header"> <span class="backicon" id="select_back"><i class="proicon icon-back"></i></span> <span class="title-text">收货地址</span>

 {if {$address}}
 <span class="modify-add">编辑</span>
 {/if}
 </div>
 <div class="empty-header" style="height:51px;"></div> 
<div class="address-list">
 <ul>
 
  {foreach from=$address item=ad}
 <li>
 {if {$ad.enable} == '1'}
  <div class="address-detail" data-id={$ad.id}><span class="full-name">{$ad.user_name}&nbsp;&nbsp;{$ad.phone}</span><span class="full-address">{$ad.area}{$ad.address}</span></div>
 <span class="select-btn active"><i class="proicon icon-select"></i></span>
 <span class="delete-btn" style="display:none;"><i class="proicon icon-empty"></i></span>
 
 {else}
  <div class="address-detail" data-id={$ad.id}><span class="full-name">{$ad.user_name}&nbsp;&nbsp;{$ad.phone}</span><span class="full-address">{$ad.area}{$ad.address}</span></div>
 <span class="select-btn"><i class="proicon icon-select"></i></span>
 <span class="delete-btn" style="display:none;"><i class="proicon icon-empty"></i></span>
 
 {/if}

 </li>
  {/foreach} 
 
 </ul>
 </div>
 <div class="add-address" id="add" ><i class="proicon icon-add" ></i></div>
</div>
</body>
</html>
