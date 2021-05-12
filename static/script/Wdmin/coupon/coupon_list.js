
DataTableConfig.order = [[0, 'desc']];
DataTableConfig.searching = true;
requirejs(['jquery', 'util', 'fancyBox', 'datatables', 'Spinner','ztree', 'ztree_loader'], function($, util, fancyBox, dataTables, Spinner , ztree, treeLoader) {
    $(function() {
    	
    	//查看详情的弹出框
        fnFancyBox('.od-coupon-pdinfo');
    	
    	$('.dTable tfoot th').each( function (i) {
    		if(i!=12){
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
    	
    	var refresh_url = '?/WdminPage/coupon_list';
        
        fnFancyBox('.coupon_delete', function () {
        	var node = $(this);
            $('#ok').on('click', function () {
            	$.get('?/Coupon/delete_coupon/', {id: $(this).attr('data-id')}, function(r) {
            		$.fancybox.close();
            		if (r > 0) {
                        util.Alert('删除成功,2s后将自动刷新',false ,null,1000);
                    } else {
                        util.Alert('删除失败！', true);
                    }
            		util.delay_refresh(refresh_url);
                });
            })
            
            $('#close').on('click', function () {
            	$.fancybox.close();
            })
        });
        
        
        
        fnFancyBox('.activate_coupon', function () {
            $('#ok').on('click', function () {
            	//$(this).attr('data-id') 为弹出的内容框中ok按钮的data-id属性
            	$.get('?/Coupon/activate_coupon/', {id: $(this).attr('data-id')}, function(r) {
            		$.fancybox.close();
            		if (r > 0) {
        				util.Alert('激活成功,2s后将自动刷新',false ,null,1000);
        			} else {
        				util.Alert('激活失败！', true);
        			}
            		util.delay_refresh(refresh_url);
        		});
            })
            
            $('#close').on('click', function () {
            	$.fancybox.close();
            })
        });
        
        var coupon_id = 0;
        /**发放优惠券*/
        fnFancyBox('.give_coupon', function () {
            fnFancyLis();
        });
        
        /**
         * 
         * @param {type} type
         * @returns {undefined}
         */
        function fnFancyLis() {
            $('.ztree li').eq(0).click();
            $('.fancybox-skin').css('background', '#fff');
            // 目录树点击回调函数
            treeLoader.setting.callback.onClick = function (event, treeId, treeNode) {
            	ajax_load_user_data(treeNode);
            };
            // 初始化目录树
            treeLoader.init('#pds-catLeft', '?/Uc/getAllGroup/r=' + (new Date()).getTime(), function () {
                $('.ztree li').eq(0).click();
            });
            
            $('#pdSelectSearch').on('change', function () {
            	ajax_load_user_data(null,$(this).val());
            } );
        }
        
        
        
        
        function ajax_load_user_data(treeNode,keyword){
        	$('#pds-pdright #inlists').html('');
        	Spinner.spin($('#pds-pdright #inlists').get(0));
        	
        	var url = '?/WdminAjax/ajax_customer_select_in/';
        	if(treeNode){
        		 url = url + 'id=' + treeNode.dataId
        	}
        	if(keyword){
        		url = url + 'keyword='+keyword;
        	}
            $.get(url, function (html) {
            	$('#pds-pdright #inlists').html(html);
            	$('.pdBlock').unbind('click').bind('click', pdBlockLis);
                $('#okSProduct').unbind('click').bind('click', confirmCurtomer);
            });
        }
        
        /**
         * 选择之后的确认
         */
        function confirmCurtomer() {
            var user_ids = [];
            $('.pdBlock.selected').each(function () {
            	user_ids.push($(this).attr('data-id'));
            });
            if(user_ids.length<=0){
            	util.Alert('请选择要发放优惠券的用户',true,null,1000);
            	return ;
            }
            var coupon_id = $('#okSProduct').attr('data-coupon');
            var uids = user_ids.join(',');
            var url = '?/Coupon/give_coupon';
            $.post(url,{
            	coupon_id:coupon_id,
            	uids : uids
            },function(data){
            	if(data.ret_code>0){
            		$.fancybox.close();
            		util.Alert('发放成功，2s后将自动刷新',false,null,1000);
            		util.delay_refresh('?/WdminPage/coupon_list');
            	}else{
            		util.Alert(data.ret_msg,true,null,1000);
            	}
            });
            
        }

        /**
         * 商品块 点击监听
         * @returns {undefined}
         */
        function pdBlockLis() {
            $(this).toggleClass('selected');
            $(this).find('.sel').toggleClass('hov');
        }
        
        
    });
});