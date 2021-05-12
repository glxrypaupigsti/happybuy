<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
<link rel="stylesheet" href="../../static/font/fonticon.css" />
<link rel="stylesheet" href="../../static/css/mobile.css" />
<link rel="stylesheet" href="../../static/css/commodity.css" />
<script language="javascript" src="../../static/script/jquery-2.1.1.min.js"></script>
<title>Cheerslife暖心下午茶</title>
</head>

<body style="background-color:#eeeeee;">
<div class="order-detail-body">
  <div class="detail-header"> <span class="backicon"><i class="proicon icon-back"></i></span> <span class="title-text">订单详情</span> </div>
  <div class="order-detail">
    <div class="order-address">
      <div class="sendto">送到这：</div>
      <div class="add-detail"> <span class="order-name">王先生&nbsp;15282738273</span><span class="modify-icon"><i class="proicon icon-right"></i></span>  <span class="order-add">上海市浦东新区盛夏路560号402A</span> </div>
      </div>
    <div class="order-list">
      <ul>
        <li><span class="pro-name">坚果巧克力布朗宁</span>
          <div class="num-price"><span class="num-commodity">×1</span><span class="price-commodity">￥35</span></div>
        </li>
        <li><span class="pro-name">坚果巧克力布朗宁</span>
          <div class="num-price"><span class="num-commodity">×1</span><span class="price-commodity">￥35</span></div>
        </li>
        <li><span class="pro-name">坚果巧克力布朗宁</span>
          <div class="num-price"><span class="num-commodity">×1</span><span class="price-commodity">￥35</span></div>
        </li>
      </ul>
    </div>
    <div class="send-cost"><span class="send-cost-left">运送费</span><span class="send-cost-right">￥5</span></div>
  </div>
  <div class="order-detail1">
    <div class="detail-modify send-time"><span class="detail-modify-left">送达时间</span><span class="modify-icon"><i class="proicon icon-right"></i></span><span class="detail-modify-right">尽快送达</span></div>
    <div class="detail-modify discount"><span class="detail-modify-left">可用优惠券</span><span class="modify-icon"><i class="proicon icon-right"></i></span><span class="detail-modify-right">无</span></div>
    <div class="balance"><span class="detail-modify-left">扣减余额</span><span class="yes-no"><img src="../../static/img/yes.png" /></span></div>
  </div>
  <div class="detail-footer">
    <div class="price-all">共计：￥40</div>
    <div class="settle-btn">结算</div>
  </div>
</div>
</body>
</html>
<script type="application/javascript">
function balance_change(){
	$(".yes-no").click(function(){
		var yes=$(".yes-no img").attr("src").indexOf("yes");
		if(yes>0){
			var replace_yes=$(".yes-no img").attr("src").replace('yes','no');
			$(".yes-no img").attr("src",replace_yes);
			}
			else if(yes<=0){
				var replace_no=$(".yes-no img").attr("src").replace('no','yes');
				$(".yes-no img").attr("src",replace_no);}
		})
	}
$(document).ready(function(){
balance_change();
	})
</script>

