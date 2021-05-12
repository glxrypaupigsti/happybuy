/* global hov */


requirejs(['util'], function (util) {

    var reg_obj, paid_obj,user_coupon_switch;
	init_radio_change();
	
    function validated_reg_award()
    {
          var award_type_value = $('input[name="reg_award_type"]:checked').val();
          var money = $('input[name="reg_award_money"]').val();
          var coupon = $('input[name="reg_award_coupon"]:checked').val();
          if(award_type_value == 1){
            if(util.isEmpty(coupon)){
                util.Alert('请选择优惠券',true);
                return false;
            }
            value = coupon;
          } else if(award_type_value == 2) {
            if(util.isEmpty(money) || isNaN(money)){
                util.Alert('金额不能为空且必须为数字',true);
                return false;
            }
            value = money;
          } else {
            value = 0;
          }
          reg_obj = {
            'type':award_type_value,
            'value':value
          }
          return true;
    }
        
    function validated_paid_award()
    {
          var award_type_value = $('input[name="paid_award_type"]:checked').val();
          var money = $('input[name="paid_award_money"]').val();
          var coupon = $('input[name="paid_award_coupon"]:checked').val();
          if(award_type_value == 1){
            if(util.isEmpty(coupon)){
                util.Alert('请选择优惠券',true);
                return false;
            }
            value = coupon;
          } else if(award_type_value == 2) {
            if(util.isEmpty(money) || isNaN(money)){
                util.Alert('金额不能为空且必须为数字',true);
                return false;
            }
            value = money;
          } else {
            value = 0;
          }
          paid_obj = {
            'type':award_type_value,
            'value':value
          }
          return true;
    }
    $('#saveBtn').click(function () {
        if (!validated_reg_award()) return false;
        if (!validated_paid_award()) return false;
    	
        
        var user_coupon_switch = $('input[name="user_coupon_switch"]:checked').val();
        var settings = {
	        'reg_award':reg_obj,
	        'paid_award':paid_obj,
	        'user_coupon_switch':user_coupon_switch
        };
    	
        $.post('?/wSettings/updateSettings/', {
            data: [
                {
                    name: 'award_settings',
                    value: JSON.stringify(settings)
                }
            ]
        }, function (r) {
            if (r > 0) {
                util.Alert('保存成功');
            } else {
                util.Alert('保存失败', true);
            }
        });
    });
    
    
    /**
     *	初始化单选按钮的选择事件
     **/
    function set_reg_award_to_none(){
        $('#reg_award_money').hide();
        $('#reg_award_coupon').hide();
    }
          function set_reg_award_to_coupon(){
        $('#reg_award_money').hide();
        $('#reg_award_coupon').show();
    }
          
    function set_reg_award_to_money(){
        $('#reg_award_money').show();
        $('#reg_award_coupon').hide();
    }

    function set_paid_award_to_none(){
        $('#paid_award_money').hide();
        $('#paid_award_coupon').hide();
    }
    function set_paid_award_to_coupon(){
        $('#paid_award_money').hide();
        $('#paid_award_coupon').show();
    }
          
    function set_paid_award_to_money(){
        $('#paid_award_money').show();
        $('#paid_award_coupon').hide();
    }
    function init_radio_change(){
        //初始化注册奖励选中的事件
    	var reg_award_type_value = $('input[name="reg_award_type"]:checked').val();
        switch (reg_award_type_value) {
            default:
            case '0':
                set_reg_award_to_none();
                break;
            case '1':
                set_reg_award_to_coupon();
                break;
            case '2':
                set_reg_award_to_money();
                break;
        }
    	
    	
    	$('input[name="reg_award_type"]').change(function(){
        	var value = $(this).val();
            switch (value) {
                default:
                case '0':
                    set_reg_award_to_none();
                    break;
                case '1':
                    set_reg_award_to_coupon();
                    break;
                case '2':
                    set_reg_award_to_money();
                    break;
            }
        });
        
          //初始化订单奖励选中的事件
          var paid_award_type_value = $('input[name="paid_award_type"]:checked').val();
          switch (paid_award_type_value) {
                default:
                case '0':
                    set_paid_award_to_none();
                    break;
                case '1':
                    set_paid_award_to_coupon();
                    break;
                case '2':
                    set_paid_award_to_money();
                    break;
          }
          
          
          $('input[name="paid_award_type"]').change(function(){
                var value = $(this).val();
                switch (value) {
                    default:
                    case '0':
                        set_paid_award_to_none();
                        break;
                    case '1':
                        set_paid_award_to_coupon();
                        break;
                    case '2':
                        set_paid_award_to_money();
                        break;
                }
           });
    }
    
    
    
    
    
   

});