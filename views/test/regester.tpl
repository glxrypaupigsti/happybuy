<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
<link rel="stylesheet" href="../../static/font/fonticon.css" />
<link rel="stylesheet" href="../../static/css/mobile.css" />
<script language="javascript" src="../../static/script/jquery-2.1.1.min.js"></script>
<title>个人中心</title>
</head>

<body style=" background-color:#ffffff;">
<div class="regester-body">
<img class="chreeslife-img" src="../../static/img/chreeslife.png"/>
<div class="regester-phone">
<div class="phone-fill">
<input type="text" name="mobile"  placeholder="请填写手机号"/><div  onclick="send_code();" class="code-btn"><a class="code-gain">发送验证码</a></div>
<div class="phone-code">
<input type="text"  placeholder="输入验证码"/>
</div>
</div>
<div class="take-order-now"><a class="order-sumbit"> 立即点单</a></div>
</div>
</div>
</body>
</html>
<script type="application/javascript">
var wait = 60;
function count_down()
{
    if(wait <= 0){
		$(".code-btn").removeClass("active")
		$(".code-btn").attr("onclick", "send_code()");
        $(".code-gain").html("发送验证码");
        wait = 60;
    } else {
		$(".code-btn").addClass("active")
		$(".code-btn").attr("onclick", "");
        $(".code-gain").html( "剩余" + wait + "秒");
        wait--;
        setTimeout("count_down()", 1000);
	
    }
}

function send_code()
{
    var tel = $("input[name='mobile']").val();
    var reg = /^1[3|4|5|7|8][0-9]\d{8}$/;
    if(!reg.test(tel)) {
        alert("请输入正确的手机号！");
        return false;
    }
	count_down();
    
}
$(document).ready(function(){
	})
</script>