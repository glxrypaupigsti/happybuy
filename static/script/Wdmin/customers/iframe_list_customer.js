DataTableConfig.searching = true;
requirejs(['jquery', 'util', 'fancyBox', 'datatables','Spinner'], function ($, util, fancyBox, dataTables, Spinner ) {
    if (parent.frameOnload !== undefined) {
        parent.frameOnload();
    }
    var oTable = $('.dTable').dataTable(DataTableConfig).api();
    util.remove_margin_top();
   
    //添加的按钮
    var button_html = '<div class="button-set">'
    				 +'<a class="wd-btn primary"  href="?/WdminPage/iframe_alter_customer/id=0">添加会员</a>'
    				 +'<a class="button blue send fancybox.ajax" data-fancybox-type="ajax" id="batch_send_msg">消息群推</a>'
    				 +'</div>';
    $('#DataTables_Table_0_filter').append(button_html);
    
    //初始化批量选择的效果
	util.init_batch_select_effect();
    		
    $('#batch_send_msg').click(function(){
    	var open_ids = [];
    	$('input[name="check_list"]:checked').each(function(){
    		open_ids.push($(this).attr('data-openid'));
    	});
    	
    	if(open_ids.length <= 0){
    		util.Alert('请选择要群发的用户',true ,null ,1000);
    		return;
    	}
    	
    	var open_id_str = open_ids.join(',');
    	var url = '?/FancyPage/batch_send_wechat_msg/open_ids='+open_id_str;
    	$(this).attr('href',url);
    	fnFancyBox('.blue',function(){
    		
    		$('#send_btn').click(function(){
    			var open_ids = $('#open_ids').val();
    			var msg_type = $('input[name="msg_type"]:checked').val();
    			var msg_content = $('#msg_content').val();
    			var msg_url = encodeURI($('#msg_url').val());
    			
    			if(util.isEmpty(msg_content)){
    				util.Alert('请输入....',true,null,1000);
    				return;
    			}
    			
    			//只有在选择为连接通知时候才校验url
    			if(msg_type == 1){ 
    				if(util.isEmpty(msg_url)){
        				util.Alert('请输入链接地址',true,null,1000);
        				return;
        			}
    			}
    			Spinner.spin($('#loading').get(0));
    			var url = '?/Uc/ajax_batch_send_wechat_msg';
    			$(this).html('发送中,请稍候...');
    			$(this).attr('disabled','disabled');
    			$.post(url,{
    				open_ids : open_ids,
    				msg_type : msg_type,
    				msg_content : msg_content,
    				msg_url : msg_url
    			},function(data){
					Spinner.stop();
    				$(this).html('发送中');
					$(this).removeAttr('disabled');
    				if(data.ret_code > 0){
    					$.fancybox.close();
    					util.Alert(data.ret_msg,false,null,1000);
    				}else{
    					util.Alert(data.ret_msg,true,null,1000);
    				}
    			});
    		})
    	});
    });
    
    
    
    
});