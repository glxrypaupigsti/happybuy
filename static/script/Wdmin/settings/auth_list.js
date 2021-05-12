/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */

requirejs(['jquery', 'util', 'fancyBox', 'datatables'], function ($, util, fancyBox, dataTables) {
    $(function () {

        var dt = $('.dTable').dataTable(DataTableConfig).api();
        var authList = $('td:hidden');//管理员权限td节点
        var enAuth = [];//权限集合(英文)
       
        var adminAccount = [];//已有管理员账号集合;
        var isDiff = false;//账号是否重复,true:无重复 || false:有重复

        //权限英文替换为中文
        $.each(authList, function (i, node) {
        	var zhAuth = [];//权限集合(中文)
            enAuth = $(node).text().split(',');

            $.each(enAuth, function (index, item) {
                if (item == 'stat')
                    zhAuth.push('报表');
                else if (item == 'orde')
                    zhAuth.push('订单');
                else if (item == 'prod')
                    zhAuth.push('商品');
                else if (item == 'gmes')
                    zhAuth.push('消息');
                else if (item == 'user')
                    zhAuth.push('会员');
                else if (item == 'comp')
                    zhAuth.push('代理');
                else if (item == 'sett')
                    zhAuth.push('微店');
                else if (item == 'coupon')
                	zhAuth.push('优惠券');
                else if (item == 'charge')
                	zhAuth.push('充值管理');
                else if (item == 'distribute')
                	zhAuth.push('配送管理');
                else if (item == 'stock')
                   zhAuth.push('库存管理');
            });

            $(node).after('<td>' + zhAuth.join(',') + '</td>');
            
        });

        //已有管理员账号集合
        $('.sorting_1').each(function (i, node) {
            adminAccount.push($(node).text());
        });

        //添加权限 || 编辑权限 (模态框)
        fnFancyBox('#add-level,.add-level', function () {

            $('.expprovince label').unbind('click').click(function () {
                $(this).parent().find('input').click();
            });

            //账号输入框失去焦点事件
            $('#acc').on('blur', function () {

                var newAccount = $(this).val();//新账号
                $.each(adminAccount, function (i, val) {
                    newAccount == val ? isDiff = false : isDiff = true;
                });

            });

            //保存按钮点击
            $('#al-com-save').unbind('click').click(function () {

                var auth = [];

                $('#authList input').each(function () {
                    if ($(this).get(0).checked) {
                        auth.push($(this).val());
                    }
                });
                var cid = parseInt($(this).attr('data-id'));

                //检测新账号是否和已有账号重复
                var pwd = $('#pwd').val();
                var account = $('#acc').val();
                if(null == account || '' == account){
            		util.Alert("帐号不能为空", true);
            		return ;
            	}
                
                if(cid > 0){ //编辑帐号
                	if(null != pwd && ''!=pwd){ //填了密码在进行验证
            			if(pwd.length < 6){
            				util.Alert("密码必须不少于6位", true);
                    		return ;
            			}
            		}
                }else{ //添加帐号
                	if(isDiff){
                		if(null == pwd || '' == pwd || pwd.length < 6){
                    		util.Alert("密码必须不少于6位", true);
                    		return ;
                    	}
                	}else{
                		util.Alert('操作失败,账号名重复或为空!', true);
                		return;
                	}
                }
                	
                $.post('?/wSettings/addAuth/', {
                    id: cid > 0 ? cid : '',
                    acc: $('#acc').val(),
                    pwd: $('#pwd').val(),
                    auth: auth.join(',')
                }, function (res) {
                    if (res > 0) {
                        $.fancybox.close();
                        location.reload();
                        //刷新帐号校验的数组，先重置，然后再遍历
                        adminAccount = [];
                        $('.sorting_1').each(function (i, node) {
                            adminAccount.push($(node).text());
                        });
                        
                        util.Alert('操作成功');
                    } else {
                        util.Alert('操作失败!', true);
                    }
                });

            });
        });

        $('.envs_del').click(function () {
            if (confirm('你确认要删除么')) {
                var node = $(this);
                $.post('?/wSettings/deleteAuth/', {
                    id: $(this).attr('data-id')
                }, function (res) {
                    if (res > 0) {
                        util.Alert('删除成功');
                        dt.row(node.parents('tr')).remove().draw();
                    } else {
                        util.Alert('操作失败!', true);
                    }
                });
            }
        });

    });
});