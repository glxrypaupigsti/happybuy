<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
<link rel="stylesheet" type="text/css" href="../../static/city_select/mobile-select-area.css">
<link rel="stylesheet" type="text/css" href="../../static/city_select/dialog.css">
        <script type="text/javascript" src="../../static/city_select/zepto.min.js"></script>
        <script type="text/javascript" src="../../static/city_select/dialog.js"></script>
        <script type="text/javascript" src="../../static/city_select/mobile-select-area.js"></script>
<link rel="stylesheet" href="../../static/font/fonticon.css" />
<link rel="stylesheet" href="../../static/css/mobile.css" />
<script language="javascript" src="../../static/script/jquery-2.1.1.min.js"></script>
<title>Cheerslife暖心下午茶</title>
</head>

<body style="background-color:#eeeeee;">
<div class="fill-address-body">
<div class="detail-header"> <span class="backicon"><i class="proicon icon-back"></i></span> <span class="title-text">添加新地址</span> </div>
<div class="fill-add-detail">
<div class="fill-name"><input placeholder="收货人姓名：" type="text" class="nametext" /></div>
<div class="fill-send-address"><span class="address-left">地区</span>
 <input type="hidden" name="province" value="" />
<input type="hidden" name="city" value="" />
<input id="txt_area" type="text" class="addtext reg-city" />
<input type="hidden" id="hd_area" value="12,124,3269"/>
</div>
<div class="send-add-detail"><input placeholder="详细地址：（目前只对陆家嘴地区开放）"  type="text" class="detailtext" /></div>
<div class="fill-phone"><input placeholder="手机号码："  type="text" class="phone-text" /></div>
</div>
<div class="add-address">确认添加</div>
</div>
</body>
</html>

        <script>
        var selectArea = new MobileSelectArea();
        selectArea.init({trigger:'#txt_area',value:$('#hd_area').val(),data:'data.json'});
        </script>