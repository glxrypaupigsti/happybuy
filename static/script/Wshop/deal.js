
require([ 'config' ], function(config) {

	require([ 'util', 'jquery', 'Spinner', 'Cart' ], function(util, $, Spinner,
			Cart) {
		 layer.open({type: 2});
	  
		 setInterval(checkOrder,1000);//1000为1秒钟
		 function checkOrder()
	     {
	          var id = $('#orderId').val();
	          var url = "?/CashPay/checkPayOrder"
	          console.error("test");
	          $.post(url,{id:id},function(data){
	        	  
	        	  if(data.ret_code > 0){
	        	     history.replaceState(null, "CheersLife 下午茶", "?/CashPay/welcome_view");
	        		 window.location.href="?/CashPay/pay_success/id="+id; 
	        	  }
	        	  
	          });
	     }

	});

});

