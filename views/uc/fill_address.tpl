<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
<link rel="stylesheet" type="text/css" href="{$docroot}static/city_select/mobile-select-area.css">
<link rel="stylesheet" type="text/css" href="{$docroot}static/city_select/dialog.css">
        <script type="text/javascript" src="{$docroot}static/city_select/zepto.min.js"></script>
        <script type="text/javascript" src="{$docroot}static/city_select/dialog.js"></script>
        <script type="text/javascript" src="{$docroot}static/city_select/mobile-select-area.js"></script>
 <link rel="stylesheet" href="{$docroot}static/layer/layer.css" />
<link rel="stylesheet" href=".{$docroot}static/font/fonticon.css" />
<link rel="stylesheet" href="{$docroot}static/css/mobile.css" />
<link rel="stylesheet" href="{$docroot}static/css/bootstrap.css" />
<script data-main="{$docroot}static/script/Wshop/address.js?v={$smarty.now}" src="{$docroot}static/script/require.min.js"></script> 
<script src="{$docroot}static/layer/layer.js"></script>
<title>U时光—小区快乐购商城</title>
</head>

<body style="background-color:#eeeeee;">
<script >
//var selectArea = new MobileSelectArea();
</script>
<input type="hidden" id="couponId" value="{$couponId}" />
<input type="hidden" id="time" value="{$time}" />
<input type="hidden" id="isbalance" value="{$isbalance}" />
<div class="fill-address-body">
<div class="detail-header"> <span class="backicon" id="back"><i class="proicon icon-back"></i></span> <span class="title-text">添加新地址</span> </div>
<div class="empty-header" style="height:51px;"></div>
<div class="fill-add-detail">
<div class="fill-name"><input placeholder="收货人姓名：" type="text" class="nametext" id="name" /></div>
<div class="fill-send-address"><span class="address-left">地区</span>
 <input type="hidden" name="province" value="" />
<input type="hidden" name="city" value="" />
<input id="txt_area" type="text" class="addtext reg-city" value="上海市-浦西" readonly="readonly" />
<input type="hidden" id="hd_area" value="12,124,3269"/>
</div>
<div class="send-add-detail"><input placeholder="详细地址：（目前只此小区开放）"  type="text" class="detailtext" id="address"/></div>
<div class="fill-phone"><input placeholder="手机号码："  value="{$phone}" type="text" class="phone-text" id="phone" /></div>
</div>
<div class="add-address" id="submit">确认添加</div>
</div>
</body>
</html>
