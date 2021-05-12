require([ 'config' ], function(config) {
        
        require([ 'util', 'jquery' ], function(util,$) {
                var wait = 60;
                var code_state = "IDLE";
                function count_down()
                {
                if(wait <= 0){
                    // reset state
                    $(".code-btn").removeClass("active")
                    $(".code-btn").attr("onclick", "send_code()");
                    $(".code-gain").html("发送验证码");
                    wait = 60;
                    code_state = "IDLE";
                } else {
                    $(".code-btn").addClass("active")
                    $(".code-btn").attr("onclick", "");
                    $(".code-gain").html( "剩余" + wait + "秒");
                    wait--;
                    setTimeout(count_down, 1000);
                
                }
                }
                
                function bind_phone()
                {
                var phone = $.trim($("input[name='mobile']").val());
                var code = $.trim($("input[name='code']").val());
                var reg = /^1[3|4|5|7|8][0-9]\d{8}$/;
                
                if (phone.length == 0 || !reg.test(phone)) {
					layer.open({
                 content: '请填写正确的手机号码',
                 time: 3 //3秒后自动关闭
                  });
                   
                    return false;
                }
                if (code.length == 0) {
					layer.open({
                 content: '请填写收到的验证码',
                 time: 3 //3秒后自动关闭
                  });
                    return false;
                }
                
                $.post('?/Uc/ajaxVeriCode', {
                       phone : phone,
                       code:code
                       }, function(data) {
                       $("#code_btn").click(send_code);
                       if (data != "") {
                        if (data.ret_code == '1') {
                            window.location.href="?/Uc/home/";
                        }else{
							 layer.open({
                 content: data.ret_msg,
                 time: 3 //3秒后自动关闭
            });
                        }
                       } else {
                  layer.open({
                 content: '绑定失败！',
                 time: 3 //3秒后自动关闭
                  });
                       }
                       });
                }
                
                function send_code()
                {
                if (code_state != "IDLE") return false;
                var tel = $.trim($("input[name='mobile']").val());
                var reg = /^1[3|4|5|7|8][0-9]\d{8}$/;
                
                if(false == reg.test(tel)){
					layer.open({
                 content: '请输入正确的手机号！',
                 time: 3 //3秒后自动关闭
                  });
                    return false;
                }
                
                code_state = "SENDING";
                // start count down
                count_down();
                
                // request server to send code
                $.post('?/Uc/ajaxSendCode', {
                       phone : tel
                       }, function(data) {
                       
                       if (data != "") {
                       if (data.ret_code == '1') {
                       }
					    layer.open({
                 content: data.ret_msg,
                 time: 3 //3秒后自动关闭
                  });
                       
                       } else {
						   layer.open({
                 content: '发送失败！',
                 time: 3 //3秒后自动关闭
                  });
                       }
                       });
                
                }
                
                $("#code_btn").click(send_code);
                $("#bind_btn").click(bind_phone);
        });
});
