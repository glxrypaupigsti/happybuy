<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
<link rel="stylesheet" href="../../static/font/fonticon.css" />
<link rel="stylesheet" href="../../static/css/mobile.css" />
<script language="javascript" src="../../static/script/jquery-2.1.1.min.js"></script>
<title>Cheerslife暖心下午茶</title>
</head>

<body style="background-color:#eeeeee;">
<div class="select-add-body">
<div class="detail-header"> <span class="backicon"><i class="proicon icon-back"></i></span> <span class="title-text">收货地址</span> <span class="modify-add">编辑</span></div>
<div class="address-list">
 <ul>
 <li>
 <div class="address-detail"><span class="full-name">乐维维&nbsp;&nbsp;15267328323</span><span class="full-address">上海市浦东新区盛夏路560号</span></div>
 <span class="select-btn active"><i class="proicon icon-select"></i></span>
 <span class="delete-btn" style="display:none;"><i class="proicon icon-empty"></i></span>
 </li>
 <li>
 <div class="address-detail"><span class="full-name">乐维维&nbsp;&nbsp;15267328323</span><span class="full-address">上海市浦东新区盛夏路560号</span></div>
 <span class="select-btn"><i class="proicon icon-select"></i></span>
<span class="delete-btn" style="display:none;"><i class="proicon icon-empty"></i></span>
 </li>
 <li>
 <div class="address-detail"><span class="full-name">乐维维&nbsp;&nbsp;15267328323</span><span class="full-address">上海市浦东新区盛夏路560号</span></div>
 <span class="select-btn"><i class="proicon icon-select"></i></span>
 <span class="delete-btn" style="display:none;"><i class="proicon icon-empty"></i></span>
 </li>
 
 </ul>
 </div>
 <div class="add-address"><i class="proicon icon-add"></i></div>
</div>
</body>
</html>
<script type="application/javascript">
function select_address(){
	$(".address-list ul li .select-btn").click(function(){
		if($(this).hasClass('active')){}
		else{
			$(".address-list ul li .select-btn").removeClass('active');
			$(this).addClass('active');
			}
		})
	}
function modify_address(){
	$(".modify-add").click(function(){
		if($(this).text()=="编辑"){
		$(".select-btn").hide();
		$(".delete-btn").show();
		$(this).html("完成")
		
		}
		else{
		$(".select-btn").show();
		$(".delete-btn").hide();
			$(this).html("编辑")
			$(".address-list ul li:first").find(".select-btn").addClass('active');
			}
		})
	}
function delete_address(){
	$(".delete-btn").click(function(){
		 var r = confirm("确认删除这张图片吗？");
		 if(r){
			 $(this).parents("li").remove();
			 
			 }
		})
	}
$(document).ready(function(){
	modify_address();
	select_address();
	delete_address();
	
	})
</script>
