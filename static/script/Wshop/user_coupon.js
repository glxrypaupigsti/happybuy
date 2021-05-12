/* global wx, shoproot, parseFloat */

/**
 * Desc
 * 
 * @description Holp You Do Good But Not Evil
 * @copyright Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author Chenyong Cai <ycchen@iwshop.cn>
 * @package Wshop
 * @link http://www.iwshop.cn
 */

require([ 'config' ], function(config) {

	require([ 'util', 'jquery' ], function(util, $) {
		var state = 0;
		var currentOrderpage = 1;
		var load_not_used = true,load_used = true,load_expired = true;
		var totalheight;
		coupon_select();
		ajax_load_data(0,currentOrderpage);
		
		function coupon_select() {
			$(".coupon-cat ul li").click(function() {
				if(!$(this).hasClass("active")) {
					state = $(this).attr('data-state');
					show_data_element(state);
					$(this).siblings().removeClass("active");
					$(this).addClass('active');
					ajax_load_data(state,currentOrderpage);
				}
			});
			
		}
		
		function ajax_load_data(state,page) {
			page = parseInt(page);
			//显示loading标志
			if(state == 0){
				loading = load_not_used;
			}else if(state ==1 ){
				loading = load_used;
			}else{
				loading = load_expired;
			}
			if(loading){ //控制重复加载
				$('#list-loading').show();
				var url = '?/Coupon/ajaxUserCouponList/page=' + currentOrderpage + '&state='+state;
				$.get(url, function(data) {
					$('#list-loading').hide();
					if(data && data.length>0){
						var rst = JSON.parse(data);
						if(null != rst){
							var len =rst.length,i=0;
							var append_str = get_html_append_str(state);
							console.log(append_str);
							var html = '';
							var value = 0;
							for(i=0;i<len;i++){
								if(rst[i].discount_type == 1){
									value = parseInt(rst[i].coupon_value)/10;
								}else{
									value = parseInt(rst[i].coupon_value)/100;
								}

								console.log('value===>'+value);
								html = html + append_str.replace('__COUPON_VALUE',value).replace('__COUPON_NAME',rst[i].coupon_name).replace('__EFFECTIVE_END',convertStampToStr('Y-m-d H:i',rst[i].effective_end)).replace('__COUPON_UNIT_DESC',rst[i].coupon_unit_desc);
							}
							console.log('html============>'+html);
							//隐藏loading标志
							if(state == 0){
								load_not_used = false;
								$('#not-used-coupon-wrapper').append(html);
							}else if(state == 1){
								load_used = false;
								$('#used-coupon-wrapper').append(html);
							}else{
								load_expired = false;
								$('#expired-coupon-wrapper').append(html);
							}
						}
						
					}
					
				});
			}
			
		}
		
		
		function show_data_element(state){
			if(state == 0){
				$('#not-used-coupon-wrapper').show();
				$('#used-coupon-wrapper').hide();
				$('#expired-coupon-wrapper').hide();
			}else if(state ==1){
				$('#not-used-coupon-wrapper').hide();
				$('#used-coupon-wrapper').show();
				$('#expired-coupon-wrapper').hide();
			}else{
				$('#not-used-coupon-wrapper').hide();
				$('#used-coupon-wrapper').hide();
				$('#expired-coupon-wrapper').show();
			}
		}
		
		
		
		
		function get_html_append_str(state){
			var html  = '<li class="not-used-coupon">'+
							 '<div class="coupon-amount"><span class="amount-money">__COUPON_VALUE</span><span class="yuan">__COUPON_UNIT_DESC</span></div>'+
							 '<div class="coupon-detail"><span class="condition">__COUPON_NAME</span><span class="superposition">不可与同类优惠券共享</span><span class="expire-time">有效期至：__EFFECTIVE_END</span></div>'+
						'</li>'; 
			if(state == 0){
				html  = '<li class="not-used-coupon">'+
							 '<div class="coupon-amount"><span class="amount-money">__COUPON_VALUE</span><span class="yuan">__COUPON_UNIT_DESC</span></div>'+
							 '<div class="coupon-detail"><span class="condition">__COUPON_NAME</span><span class="superposition">不可与同类优惠券共享</span><span class="expire-time">有效期至：__EFFECTIVE_END</span></div>'+
						'</li>'; 
			}else if(state == 1){
				html  = '<li class="has-use-coupon">'+
							 '<div class="coupon-amount"><span class="amount-money">__COUPON_VALUE</span><span class="yuan">__COUPON_UNIT_DESC</span></div>'+
							 '<div class="coupon-detail"><span class="condition">__COUPON_NAME</span><span class="superposition">不可与同类优惠券共享</span><span class="expire-time">有效期至：__EFFECTIVE_END</span></div>'+
							 '<img src="../../static/img/hasuse.png" class="failure-img" />'+
						'</li>'; 
			}else if(state == 2){
				html  = '<li class="expired-coupon">'+
							 '<div class="coupon-amount"><span class="amount-money">__COUPON_VALUE</span><span class="yuan">元</span></div>'+
							 '<div class="coupon-detail"><span class="condition">__COUPON_NAME</span><span class="superposition">不可与同类优惠券共享</span><span class="expire-time">有效期至：__EFFECTIVE_END</span></div>'+
							 '<img src="../../static/img/failure.png" class="failure-img" />'+
						'</li>'; 
			}
			return html;
		}
		
		
		function convertStampToStr(format, timestamp){
		    var a, jsdate=((timestamp) ? new Date(timestamp*1000) : new Date());
		    var pad = function(n, c){
		        if((n = n + "").length < c){
		            return new Array(++c - n.length).join("0") + n;
		        } else {
		            return n;
		        }
		    };
		    var txt_weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
		    var txt_ordin = {1:"st", 2:"nd", 3:"rd", 21:"st", 22:"nd", 23:"rd", 31:"st"};
		    var txt_months = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]; 
		    var f = {
		        // Day
		        d: function(){return pad(f.j(), 2)},
		        D: function(){return f.l().substr(0,3)},
		        j: function(){return jsdate.getDate()},
		        l: function(){return txt_weekdays[f.w()]},
		        N: function(){return f.w() + 1},
		        S: function(){return txt_ordin[f.j()] ? txt_ordin[f.j()] : 'th'},
		        w: function(){return jsdate.getDay()},
		        z: function(){return (jsdate - new Date(jsdate.getFullYear() + "/1/1")) / 864e5 >> 0},
		       
		        // Week
		        W: function(){
		            var a = f.z(), b = 364 + f.L() - a;
		            var nd2, nd = (new Date(jsdate.getFullYear() + "/1/1").getDay() || 7) - 1;
		            if(b <= 2 && ((jsdate.getDay() || 7) - 1) <= 2 - b){
		                return 1;
		            } else{
		                if(a <= 2 && nd >= 4 && a >= (6 - nd)){
		                    nd2 = new Date(jsdate.getFullYear() - 1 + "/12/31");
		                    return date("W", Math.round(nd2.getTime()/1000));
		                } else{
		                    return (1 + (nd <= 3 ? ((a + nd) / 7) : (a - (7 - nd)) / 7) >> 0);
		                }
		            }
		        },
		       
		        // Month
		        F: function(){return txt_months[f.n()]},
		        m: function(){return pad(f.n(), 2)},
		        M: function(){return f.F().substr(0,3)},
		        n: function(){return jsdate.getMonth() + 1},
		        t: function(){
		            var n;
		            if( (n = jsdate.getMonth() + 1) == 2 ){
		                return 28 + f.L();
		            } else{
		                if( n & 1 && n < 8 || !(n & 1) && n > 7 ){
		                    return 31;
		                } else{
		                    return 30;
		                }
		            }
		        },
		       
		        // Year
		        L: function(){var y = f.Y();return (!(y & 3) && (y % 1e2 || !(y % 4e2))) ? 1 : 0},
		        //o not supported yet
		        Y: function(){return jsdate.getFullYear()},
		        y: function(){return (jsdate.getFullYear() + "").slice(2)},
		       
		        // Time
		        a: function(){return jsdate.getHours() > 11 ? "pm" : "am"},
		        A: function(){return f.a().toUpperCase()},
		        B: function(){
		            // peter paul koch:
		            var off = (jsdate.getTimezoneOffset() + 60)*60;
		            var theSeconds = (jsdate.getHours() * 3600) + (jsdate.getMinutes() * 60) + jsdate.getSeconds() + off;
		            var beat = Math.floor(theSeconds/86.4);
		            if (beat > 1000) beat -= 1000;
		            if (beat < 0) beat += 1000;
		            if ((String(beat)).length == 1) beat = "00"+beat;
		            if ((String(beat)).length == 2) beat = "0"+beat;
		            return beat;
		        },
		        g: function(){return jsdate.getHours() % 12 || 12},
		        G: function(){return jsdate.getHours()},
		        h: function(){return pad(f.g(), 2)},
		        H: function(){return pad(jsdate.getHours(), 2)},
		        i: function(){return pad(jsdate.getMinutes(), 2)},
		        s: function(){return pad(jsdate.getSeconds(), 2)},
		        //u not supported yet
		       
		        // Timezone
		        //e not supported yet
		        //I not supported yet
		        O: function(){
		            var t = pad(Math.abs(jsdate.getTimezoneOffset()/60*100), 4);
		            if (jsdate.getTimezoneOffset() > 0) t = "-" + t; else t = "+" + t;
		            return t;
		        },
		        P: function(){var O = f.O();return (O.substr(0, 3) + ":" + O.substr(3, 2))},
		        //T not supported yet
		        //Z not supported yet
		       
		        // Full Date/Time
		        c: function(){return f.Y() + "-" + f.m() + "-" + f.d() + "T" + f.h() + ":" + f.i() + ":" + f.s() + f.P()},
		        //r not supported yet
		        U: function(){return Math.round(jsdate.getTime()/1000)}
		    };
		       
		    return format.replace(/[\\]?([a-zA-Z])/g, function(t, s){
		        if( t!=s ){
		            // escaped
		            ret = s;
		        } else if( f[s] ){
		            // a date function exists
		            ret = f[s]();
		        } else{
		            // nothing special
		            ret = s;
		        }
		        return ret;
		    });
	    }

	});

});