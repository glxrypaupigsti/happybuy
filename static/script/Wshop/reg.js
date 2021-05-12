require([ 'config' ], function(config) {

	require([ 'util', 'jquery', 'Spinner', 'Slider', 'Tiping' ], function(util,
			$, Spinner, Slider, Tiping) {

         var wait = 60;

		$('#bind_btn').click(function(){
			
		   var phone = $("input[name='mobile']").val();
		   var code = $("input[name='code']").val();

			$.post('?/Uc/ajaxVeriCode', {
				phone : phone,
				code:code
			}, function(data) {
			
				if (data != "") {

					if (data.ret_code == '1') {
						
						window.location.href="?/Uc/home";
					}else{
						 layer.open({
                 content: '绑定失败！',
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
			
		});
		
		function count_down() {
			if (wait <= 0) {
				$(".code-btn").removeClass("active")
				$(".code-btn").attr("onclick", "send_code()");
				$(".code-gain").html("发送验证码");
				wait = 60;
			} else {
				$(".code-btn").addClass("active")
				$(".code-btn").attr("onclick", "");
				$(".code-gain").html("剩余" + wait + "秒");
				wait--;
				setTimeout("count_down()", 1000);
			}
		}

		

		$('#code_btn').click(function(){
			
			var tel = $("input[name='mobile']").val();
			var reg = /^1\d{10}$/;
			 if(!reg.test(tel)) {
			 layer.open({
                 content: '请输入正确的手机号！',
                 time: 3 //3秒后自动关闭
            });
			return false;
			 }
			
			$.post('?/Uc/ajaxSendCode', {
				phone : tel
			}, function(data) {

				if (data != "") {
					if (data.ret_code == '1') {
						count_down();
					}
					 layer.open({
                 content: data.ret_msg,
                 time: 3 //3秒后自动关闭
            });

				} else {
					layer.open({
                 content:'发送失败',
                 time: 3 //3秒后自动关闭
            });
				}
			});	
			
			
		});

	});



});
