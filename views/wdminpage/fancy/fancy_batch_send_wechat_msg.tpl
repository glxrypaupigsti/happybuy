<div id="inline2" style="width:400px; height:400px">
	<div id="loading" style="display:none" />
	<input type="hidden" value="{$open_ids}" id="open_ids">
    <div class="gs-label">
    	消息类型:
    </div>
    <div class="gs-label">
        <input type="radio" name="msg_type" value="0" checked/>文本消息
        <input type="radio" name="msg_type" value="1" />带链接的推送通知
    </div>
    
    <div class="gs-label" id="label_msg_url"></div>
    <div class="gs-text" id="div_msg_url">
        <input type="text" id="msg_url" placeholder="请填写相对地址"/>
    </div>
    
    <div class="gs-label" style="color:red;" id='url_hint'>
    	(优惠券链接请填写：?/Coupon/user_coupon)
    </div>
    
    <div class="gs-label">消息内容</div>
    <div class="gs-text">
        <textarea id="msg_content" rows="10" cols="100"></textarea>
    </div>
    
    <div class="center" style="margin-top: 15px">
        <a id="send_btn" href="javascript:;" class="wd-btn primary">发送</a>
    </div>
</div>
<script>

	if($('input[name="msg_type"]').val() == 0){
		choose_text_msg();
	}else{
		choose_link_msg();
	}


	$('input[name="msg_type"]').click(function(){
		if($(this).val() == 1){
			choose_link_msg();
		}else{
			choose_text_msg();
		}
	});
	
	//发送连接消息的地址
	function choose_link_msg(){
		$('#msg_content').val('亲，终于等到你~ 这张X元优惠券给你预留好久了\n适用范围：CheersLife全部商品\n使用规则：在下单时选中优惠券抵用即可\n快来享用健康美食吧~');
		$('#div_msg_url').show();
		$('#label_msg_url').show();
		$('#url_hint').show();
	}
	//发送连接消息的地址
	function choose_text_msg(){
		$('#msg_content').val('');
		$('#div_msg_url').hide();
		$('#label_msg_url').hide();
		$('#url_hint').hide();
	}
</script>