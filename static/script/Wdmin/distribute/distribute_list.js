
DataTableConfig.order = [[0, 'desc']];
DataTableConfig.searching = true;
requirejs(['jquery', 'util', 'fancyBox', 'datatables', 'Spinner','WdatePicker','layer'], function($, util, fancyBox, dataTables, Spinner,WdatePicker,layer) {
	layer.ready(function(){ 
//	$(function(){ 
    	
    	$('.dTable tfoot th').each( function (i) {
    		if(i!=7){
				 var title = $('.dTable tfoot th').eq($(this).index()).text();
	             $(this).html( '<input type="text" style="width:80px;" placeholder="'+title+'" />' );
    		}else{
    			$(this).html('');
    		}
        } );
        
    	var oTable = $('.dTable').dataTable(DataTableConfig).api();
        
    	oTable.columns().eq(0).each(function (colIdx) {
            $('input', oTable.column(colIdx).footer()).on( 'change', function () {
            	oTable.column(colIdx).search(this.value).draw();
            } );
        } );
    	
    	$('#DataTables_Table_0_filter').hide();
    	
    	var url = '?/WdminPage/distribute_list/status=__STATUS&day=__DAY';
    	
    	
    	$('.date_btn').click(function(){
    		if(!$(this).hasClass('primary')){
    			$(this).addClass('primary')
    			$(this).siblings().removeClass('primary');
    			$('#day').val($(this).attr('data-diff'));
    			ajax_load_data($(this).attr('data-status'));
    		}
    	});
    	
    	$(".nav-tabs>li>a").click(function() {
			if(!$(this).hasClass("active")) {
				var status = $(this).attr('data-status');
				var day = $("#day").val();
				$(this).parent().siblings().removeClass("active");
				$(this).parent().addClass('active');
				ajax_load_data(status);
			}
		});
    	
    	
    	
    	function ajax_load_data(status){
    		var day = $("#day").val();
    		url =url.replace('__STATUS',status).replace('__DAY',day); 
    		window.location.href  = url;
    	}
    	
    	
    	/**
    	 * 取消配送
    	 */
    	fnFancyBox('.cancel_distribute', function () {
            $('#ok').on('click', function () {
            	var distribute_id = $('#distribute_id').val();
            	var exp_time1 = $('#exp_time1').val();
            	var exp_time2 = $('#exp_time2').val();
            	var status = $('#status').val();
            	if(util.isEmpty(exp_time1)){
            		util.Alert('请选择日期');
            		return;
            	}
            	if(util.isEmpty(exp_time2)){
            		util.Alert('请选择时间段');
            		return;
            	}
            	
            	var nowStr = util.getTodayTimeStr('y-M-d')+" 00:00:00";
            	var input_time = exp_time1 +" 00:00:00";
            	if(util.timeCompare(nowStr,input_time)>0){
            		util.Alert('请选择大于等于今天的日期',true);
            		return;
            	}
            	
            	var exp_time = exp_time1 + ' ' +exp_time2;
            	Spinner.spin($('#iframe_loading').get(0));
            	$.post('?/Distribute/delievery_reset/', {
            			id: distribute_id,
            			exp_time:exp_time,
            			status:'cancel'
            	}, function(data) {
            		Spinner.stop();
            		$.fancybox.close();
            		if (data.ret_code > 0) {
            			util.Alert('取消成功，已经重新分配了一个新的配送单！');
                    } else {
                    	util.Alert(data.ret_msg, true);
                    }
            		window.location.href = '?/WdminPage/distribute_list/status='+status;
                });
            })
            
            $('#close').on('click', function () {
            	$.fancybox.close();
            })
        });
    	
    	/**
    	 * 未送达
    	 */
    	fnFancyBox('.not_reach', function () {
    		$('#ok').on('click', function () {
            	var distribute_id = $('#distribute_id').val();
            	var exp_time1 = $('#exp_time1').val();
            	var exp_time2 = $('#exp_time2').val();
            	var status = $('#status').val();
            	if(util.isEmpty(exp_time1)){
            		util.Alert('请选择日期');
            		return;
            	}
            	if(util.isEmpty(exp_time2)){
            		util.Alert('请选择时间段');
            		return;
            	}
            	var nowStr = util.getTodayTimeStr('y-M-d')+" 00:00:00";
            	var input_time = exp_time1 +" 00:00:00";
            	if(util.timeCompare(nowStr,input_time)>0){
            		util.Alert('请选择大于等于今天的日期',true);
            		return;
            	}
            	
            	var exp_time = exp_time1 + ' ' +exp_time2;
            	Spinner.spin($('#iframe_loading').get(0));
            	$.post('?/Distribute/delievery_reset/', {
            			id: distribute_id,
            			exp_time:exp_time,
            			status:'not_reached'
            	}, function(data) {
            		Spinner.stop();
            		$.fancybox.close();
            		if (data.ret_code > 0) {
            			util.Alert('设置成功，已经重新分配了一个新的配送单！',false,null,1000);
                    } else {
                    	util.Alert(data.ret_msg, true);
                    }
            		window.location.href = '?/WdminPage/distribute_list/status='+status;
                });
            })
            
            $('#close').on('click', function () {
            	$.fancybox.close();
            })
    	});
    	
    	/**
    	 * 发货
    	 */
    	fnFancyBox('.delievering_distribute', function () {
    		$('#ok').on('click', function () {
//    			var expressCompany = $('#expressCompany').val();
    			var expressCompany =  $("#expressCompany").find("option:selected").text();
//    			var courier = $('#couriers').text();
    			var courier = $('#couriers').find("option:selected").text();
    			var status = $('#status').val();
    			var id = $('#distribute_id').val();
    			
    			if(util.isEmpty(expressCompany)){
    				util.Alert('请先在微点设置中设置配送方式');
    				return;
    			}
    			
    			if(util.isEmpty(courier)){
    				util.Alert('请先在微店设置中设置配送人员');
    				return;
    			}
    			Spinner.spin($('#iframe_loading').get(0));
    			$.post('?/Distribute/order_delievery/', {
    				id: id,
    				distribute_code:expressCompany,
    				courier:courier
    			},function(data) {
    				Spinner.stop();
    				$.fancybox.close();
    				if (data.ret_code > 0) {
    					util.Alert('发货成功！');
    				} else {
    					util.Alert(data.ret_msg, true);
    				}
    				window.location.href = '?/WdminPage/distribute_list/status='+status;
    			});
    		})
    		
    		$('#close').on('click', function () {
    			$.fancybox.close();
    		})
    	});
    	
    	
    	
        /**
         * 开始制作
         **/
        fnFancyBox('.begin_to_make', function () {
            $('#ok').on('click', function () {
//            	Spinner.spin($('#iframe_loading')).get(0);
            	$.get('?/Distribute/begin_to_make/', {id: $(this).attr('data-id')}, function(r) {
//            		Spinner.stop();
            		$.fancybox.close();
            		if (r > 0) {
                        util.Alert('已经开始制作！');
                    } else {
                        util.Alert('失败！', true);
                    }
            		window.location.href = '?/WdminPage/distribute_list/status=not_delievery';
                });
            })
            
            $('#close').on('click', function () {
            	$.fancybox.close();
            })
        });
        
        
        /**
         * 已送达
         */
        fnFancyBox('.reached', function () {
            $('#ok').on('click', function () {
            	//$(this).attr('data-id') 为弹出的内容框中ok按钮的data-id属性
            	$.get('?/Distribute/delievery_reached/', {id: $(this).attr('data-id')}, function(data) {
            		$.fancybox.close();
            		if (data.ret_code > 0) {
    					util.Alert('成功送达！');
    				} else {
    					util.Alert(data.ret_msg, true);
    				}
            		window.location.href = '?/WdminPage/distribute_list/status=delievered';
        		});
            })
            
            $('#close').on('click', function () {
            	$.fancybox.close();
            })
        });
        
    	var index = layer.load(0, {
    	    shade: [0.1,'#fff'] //0.1透明度的白色背景
    	});
    	
//    	layer.tips('上', '.search-w-box', {
//    		tips: [1, '#0FA6D8'] //还可配置颜色
//    	});
//    	layer.tips('左', '.search-w-box', {
//    		tips: [3, '#0FD6D8'] //还可配置颜色
//    	});
//    	layer.tips('右', '.search-w-box', {
//    		tips: [4, '#0FC6D8'] //还可配置颜色
//    	});
//    	layer.tips('左', '.search-w-box', {
//    		tips: [2, '#0FB6D8'] //还可配置颜色
//    	});
//    	
//    	layer.tips('我是另外一个tips，只不过我长得跟之前那位稍有些不一样。', '.search-w-box', {
//    	    tips: [4, '#3595CC'],
//    	    time: 4000
//    	});
    	
//    	layer.confirm('您是如何看待前端开发？', {
//    	    btn: ['重要','奇葩'] //按钮
//    	}, function(){
//    	    layer.msg('的确很重要', {icon: 1});
//    	}, function(){
//    	    layer.msg('也可以这样', {
//    	        time: 20000, //20s后自动关闭
//    	        btn: ['明白了', '知道了']
//    	    });
//    	});
    	
    	//正上方
//    	layer.msg('灵活运用offset', {
//    	    offset: 0,
//    	    shift: 6
//    	});
    	
    	//此处演示关闭
		setTimeout(function(){
		    layer.closeAll('loading');
		}, 2000);
		
    	
//    	//弹出即全屏
//    	var index = layer.open({
//    	    type: 2,
//    	    content: 'http://www.layui.com',
//    	    area: ['300px', '195px'],
//    	    maxmin: true
//    	});
//    	layer.full(index);
    	
//    	layer.alert('墨绿风格，点击确认看深蓝', {
//    	    skin: 'layui-layer-molv' //样式类名
//    	    ,closeBtn: 0
//    	}, function(){
//    	    layer.alert('偶吧深蓝style', {
//    	        skin: 'layui-layer-lan'
//    	        ,closeBtn: 1
//    	        ,shift: 4 //动画类型
//    	    });
//    	});
		
   	    //官网欢迎页
//	    layer.open({
//	        type: 2,
//	        //skin: 'layui-layer-lan',
//	        title: 'layer弹层组件',
//	        fix: false,
//	        shadeClose: true,
//	        maxmin: true,
//	        area: ['1000px', '500px'],
//	        content: 'http://layer.layui.com/?form=local',
//	        end: function(){
//	            layer.tips('试试相册模块？', '#photosDemo', {tips: 1})
//	        }
//	    });
//	    
//
//		//iframe层-多媒体
//		layer.open({
//		    type: 2,
//		    title: false,
//		    area: ['630px', '360px'],
//		    shade: 0.8,
//		    closeBtn: 0,
//		    shadeClose: true,
//		    content: 'http://player.youku.com/embed/XMjY3MzgzODg0'
//		});
//    	
//    	console.log(layer);
    });
});