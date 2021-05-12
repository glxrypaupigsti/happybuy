/* global shoproot */

/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
var couponCover = false;
var pdImages = ['', '', '', '', ''];
var pdId = false;
var pdImageWidth = false;
var entDiscount = [];
var entDisLastOpt;

requirejs(['jquery','util', 'fancyBox', 'ueditor', 'jUploader', 'Spinner','ztree', 'ztree_loader','WdatePicker','datetimepicker'], function ($,util, fancyBox, ueditor, jUploader,Spinner,ztree, treeLoader,WdatePicker) {
	//coupon_applied结构模版
	//应用与商品类中的商品数据结构模版
	var coupon_applied_template = '{"applied_type":"__APPLIED_TYPE","subtype":"__SUBTYPE","categorys":__CATEGORYS,"products":__PRODUCTS}';
	
	var coupon_applied_data_template_to_product = "{'applied_type':0,'subtype':'product','categorys':[],'products':[{'id':1,'name':'饮料','img':'*.jpg'}]}";
	//应用与商品类中的商品分类数据结构模版
	var coupon_applied_data_template_to_category = "{'applied_type':0,'subtype':'category','categorys':[{'id':1,'name':'饮料'],'products':[]}";
	//应用与订单类数据结构模版
	var coupon_applied_data_template_to_order = "{'applied_type':1,'subtype':'','categorys':[],'products':[]}";
	//coupon_terms条件数据模版
	var coupon_terms_template = "[{'name':'用户积分条件','table':'user','column':'group','operate':'>=','value':50}]";
	var coupon_terms_data_template = "[{'name':'用户积分条件','table':'user','column':'group','operate':'>=','value':50}]";
	//coupon_buddle条件数据模版
	var coupon_buddle_data_template = "[{'id':1,'name':'饮料','img':'*.jpg'}]";
	//coupon_buddle条件数据模版，目前这个字段属于保留字段，limit部分统一都设置为1
	var coupon_limit_data_template = "[{'limit':1,'coupons':''}]"; 
	
	
	
	
    loadingLock = false;
    init_hidden();
    
    fnFancyBox('#catimgPv');
    
    fnFancyBox('.pd-image-view');
    
    //设置日期可以进行时间选择
    init_time_picker();
    //radio的change事件
    init_radio_change();
    //选择产品的弹出框
    init_select_product('#sProduct',okSelectProduct);
    //兑换的产品的弹出框
    init_select_product('#scProdcut',okExchangeProduct);
    
    // 产品首图
    couponCover = $('#pd-catimg').val() === '' ? false : $('#pd-catimg').val();

    if ($('#mod').val() === 'edit') {
        $('#pd-catimg').val(couponCover);
        $('#catimgPv').attr('href', shoproot + 'uploads/coupon/' + response.imgn);
        fnPdimageDelete();
        pdId = parseInt($('#pid').val());
        fnFancyBox('.pd-image-view');
    } else {
        pdId = false;
    }
    
    pdBlockAdjust();
    // 商品条件添加按钮
    $('#btn_coupon_terms_add').click(function () {
        var tr = $('.coupon_terms_select').eq(0).clone(false);
        tr.find('select option:selected').each(function () {
            this.selected = false;
        });
        tr.find('input').val('');
        tr.attr('data-id', 0);
        tr.removeClass('hidden');
        $('#pd-spec-frame tbody').append(tr);
        fnCouponTermsListen();
    });
    
    
    /**
     * 优惠条件事件监听
     * @returns {undefined}
     */
    function fnCouponTermsListen() {
        $('.btn-delete-spectr').unbind('click').bind('click', function () {
            var nParent = $(this).parents('tr');
            if (nParent.attr('data-id') > 0) {
                // 赋值负数表示删除
                nParent.attr('data-id', nParent.attr('data-id') * -1);
                nParent.addClass('hidden');
            } else {
                // 否则直接删除节点, 不然会有无效数据
                nParent.remove();
            }
        });
        $('.coupon_terms').unbind('change').bind('change', couponTersmChange);
        $('.coupon_terms').change();
    }

    /**
     * 规格选择监听
     * @returns {undefined}
     */
    function couponTersmChange() {
        // 避免重复选择规格
    	var selectOption = $(this).find('option:selected');
        var termId = selectOption.attr('data-term-id');
        $(this).find('option').each(function () {
    		if ($(this).attr('data-term-id') === termId) {
//                selectTermsIds.push(termId);
    			this.disabled = true;
            }else{
            	this.disabled = false;
            }
    		
        });
        
        var parentsTr = $(this).parents('tr');
        parentsTr.find('input.coupon-terms-id').eq(0).val(selectOption.attr('data-term-id'));
        parentsTr.find('input.coupon-terms-table').eq(0).val(selectOption.attr('data-table'));
        parentsTr.find('input.coupon-terms-column').eq(0).val(selectOption.attr('data-column'));
        parentsTr.find('input.coupon-terms-operate').eq(0).val(selectOption.attr('data-operate'));
//        parentsTr.find('input.coupon-terms-value').eq(0).val(0);
        
    }


    var pdImageHeight = false;

    // 产品图片
    $('.pd-image-sec').each(function () {
        var btn = this;
        if (!pdImageHeight) {
            pdImageHeight = $(this).width();
        }
        $(this).height(pdImageHeight);
        $.jUploader({
            button: $(btn),
            action: shoproot + '?/Coupon/ImageUpload',
            onUpload: function (fileName) {
                util.Alert('图片上传中');
            },
            onComplete: function (fileName, response) {
                var Btn = $(this.button[0]);
                if (response.s > 0) {
                    var iid = parseInt(Btn.attr('data-id'));
                    var src = decodeURIComponent(response.link);
                    Btn.addClass('ove').removeClass('hover');
                    util.Alert('图片上传成功');
                    if (Btn.find('img').length > 0) {
                        Btn.find('img').attr('src', src);
                        Btn.find('.pd-image-view').attr('href', src);
                    } else {
                        Btn.append('<img src="' + src + '" /><a href="' + src + '" class="pd-image-view"> </a><i data-id=' + (iid - 1) + ' class="pd-image-delete"> </i>');
                    }
                    // 商品首图
                    if (!couponCover || iid === 0) {
//                        couponCover = 'product_hpic2__' + response.imgn;
                        couponCover = response.imgn;
                        $('#pd-catimg').val(couponCover);
                        $('#catimgPv').attr('href', shoproot + 'uploads/coupon/' + response.imgn);
                    }
                    if (iid !== 0) {
                        pdImages[iid - 1] = 'uploads/coupon/' + response.imgn;
                    }
                    fnFancyBox('.pd-image-view');
                    fnPdimageDelete();
                } else {
                    util.Alert('上传图片失败，请确保/uploads/product_hpic_tmp/具有写权限');
                }
            }
        });
        $(this).hover(function () {
            if (!$(this).hasClass('ove')) {
                $(this).addClass('hover');
            }
        }, function () {
            if (!$(this).hasClass('ove')) {
                $(this).removeClass('hover');
            }
        });
    });

    // 优惠券图片
    if (couponCover) {
        $('.pd-image-sec').eq(0).addClass('ove').append('<img src="' + shoproot + 'uploads/coupon/' + couponCover + '" />');
        $('#catimgPv').attr('href', shoproot + 'uploads/coupon/' + couponCover);
    }

    $('body').css('overflow-x', 'hidden');

    // 图片已经上传过了。
    $('#save_coupon_btn').unbind('click').click(__CouponAlterFinish);

    // 删除图片--
    function fnPdimageDelete() {
        $('.pd-image-delete').unbind('click').on('click', function () {
            var nP = $(this).parent();
            // 删除图集数据
            pdImages[parseInt($(this).attr('data-id'))] = '';
            // 删除标记
            nP.removeClass('ove').find('i,img,.pd-image-view').remove();
            nP = null;
        });
    }

    /**
     * 商品编辑结束
     * @returns {undefined}
     */
    function __CouponAlterFinish() {
        if (!loadingLock) {
            var postData = $('#pd-baseinfo').serializeArray();
            
//            var applied = getCouponAppliedData();
//            alert(applied);
//            return;
            
            if(util.isEmpty( $('input[name="coupon_name"]').val())){
            	util.Alert("优惠券名称不能为空",true);
            	return ;
            }
            if(util.isEmpty( $('input[name="coupon_type"]:checked').val())){
            	util.Alert("优惠券类型不能为空",true);
            	return ;
            }
            //只有选择了优惠券类型为商品类的时候才出现应用类型验证
            if($('input[name="coupon_type"]:checked').val() == 0){
            	if(util.isEmpty( $('input[name="coupon_applied_type"]:checked').val())){
            		util.Alert("优惠券应用类型不能为空",true);
                	return ;
            	}
            }
            
            //优惠券发放时间
            if(util.isEmpty( $('input[name="available_start"]').val())){
            	util.Alert("发放开始时间不能为空",true);
            	return ;
            }
            
            if(util.isEmpty( $('input[name="available_end"]').val())){
            	util.Alert("发放结束时间不能为空",true);
            	return ;
            }
            
            var compare_available_time_value = util.timeCompare($('input[name="available_start"]').val(),$('input[name="available_end"]').val());
            if(compare_available_time_value == 1){
            	util.Alert("发放开始时间不能大于结束时间",true);
            	return ;
            }else if(compare_available_time_value == 2){
            	util.Alert("发放结束时间不能小于当前时间",true);
            	return ;
            }
            
            //优惠券有效时间
            if(util.isEmpty( $('input[name="effective_start"]').val())){
            	util.Alert("优惠券有效开始时间不能为空",true);
            	return ;
            }
            
            if(util.isEmpty( $('input[name="effective_end"]').val())){
            	util.Alert("优惠券有效结束时间不能为空",true);
            	return ;
            }
            
            var compare_effective_time_value = util.timeCompare($('input[name="effective_start"]').val(),$('input[name="effective_end"]').val());
            if(compare_effective_time_value == 1){
            	util.Alert("优惠券有效开始时间不能大于结束时间",true);
            	return ;
            }else if(compare_effective_time_value == 2){
            	util.Alert("优惠券有效结束时间不能小于当前时间",true);
            	return ;
            }
            /*
            if(!couponCover){
            	util.Alert("请上传优惠券主图");
            	return ;
            }
            */
            if(util.isEmpty( $('input[name="coupon_stock"]').val()) || isNaN($('input[name="coupon_stock"]').val()) ){
            	util.Alert("商品数量不能为空且必须为数字",true);
            	return ;
            }
            if(util.isEmpty( $('input[name="discount_type"]:checked').val()) ){
            	util.Alert("折扣类型不能为空",true);
            	return ;
            }
            if(util.isEmpty( $('input[name="discount_val"]').val()) || !util.isIntNumber($('input[name="discount_val"]').val()) ){
            	util.Alert("折扣值必须大于0",true);
            	return ;
            }
            
        	var node = $(this);
            //loadingLock = true;
            node.html('数据处理中');
            
            //获取应用于商品还有分类的数据
            var applied = getCouponAppliedData();
            //获取绑定的商品数据
            var buddled = getCouponBuddledData();
            //获取绑定的条件数据
            var couponTerms = getCouponTermsData();
            var coupon_limit = getCouponLimitData();
            var id = $('input[name="id"]').val();
            // [HttpPost]
            $.post(shoproot + '?/Coupon/save_coupon', {
            	id:id,
            	coupon_name: $('input[name="coupon_name"]').val(),
            	coupon_type: $('input[name="coupon_type"]:checked').val(),
            	available_start: $('input[name="available_start"]').val(),
    			available_end: $('input[name="available_end"]').val(),
    			effective_start: $('input[name="effective_start"]').val(),
    			effective_end: $('input[name="effective_end"]').val(),
    			coupon_cover: couponCover,
    			coupon_stock: $('input[name="coupon_stock"]').val(),
    			discount_type: $('input[name="discount_type"]:checked').val(),
    			discount_val: $('input[name="discount_val"]').val(),
    			applied: applied,
    			bundled: buddled,
    			coupon_terms:couponTerms,
    			coupon_limit:coupon_limit
            }, function (r) {
                if (r > 0) {
                	var tips = '添加成功';
                	if(id>0){
                		tips = '保存成功';
                	}
                    util.Alert(tips, false, function () {
                        // 返回列表
                        history.go(-1);
                    });
                } else {
                    util.Alert('保存失败');
                }
                node.html('保存');
            });
        } else {

        }
    }

    /**
     * 商品删除监听
     */
    util.pdDeleteListen();

    /**
     * window resize 监听
     */
    util.onresize(function () {
        $('.pd-image-sec').each(function () {
            pdImageWidth = $(this).width();
            $(this).height(pdImageWidth);
        });
    });

    
    
    /**
     *	初始化隐藏的区域
     **/
    function init_hidden(){
    	$("#apply_to_type").hide();
    	$("#applied_to_cat").hide();
    	$("#apply_to_product").hide();
    	$("#exchange_product").hide();
    	$("#unit_fen").hide();
    	$("#unit_percentage").hide();
    }
    
    /**
     *	初始化时间选择控件
     **/
    function init_time_picker(){
    	$.datetimepicker.setLocale('zh');
        $('#effective_start').datetimepicker({
            format: 'Y-m-d H:i:s'
        });
        $('#effective_end').datetimepicker({
        	format: 'Y-m-d H:i:s'
        });
        $('#available_start').datetimepicker({
        	format: 'Y-m-d H:i:s'
        });
        $('#available_end').datetimepicker({
        	format: 'Y-m-d H:i:s'
        });
    }
    
    /**
     *	初始化单选按钮的选择事件
     **/
    function init_radio_change(){
    	//初始化选中的事件
    	var coupon_type_value = $('input[name="coupon_type"]:checked').val();
    	if( coupon_type_value == 0){
    		coupon_type_to_product();
    	}else if(coupon_type_value == 1){
    		coupon_type_to_order();
    	}else{
    		coupon_type_to_user();
    	}
    	
    	
    	$('input[name="coupon_type"]').change(function(){
        	var value = $(this).val();
        	if(value == 0){ //应用类型选择商品的时候
        		coupon_type_to_product();
        	}else if(value == 1){//应用类型选择订单的时候
        		coupon_type_to_order();
        	}else{
        		coupon_type_to_user();
        	}
        });
        
        $('input[name="coupon_applied_type"]').change(function(){
        	var value = $(this).val();
        	if(value == 0){ //应用于商品的时候
        		coupon_applied_to_product();
        	}else{ //应用于商品分类的时候
        		coupon_applied_to_cat();
        	}
        });
        $('input[name="discount_type"]').change(function(){
        	var value = $(this).val();
        	if(value == 1){ //只有折扣类型按比例算时候才显示单位%，否则全部显示单位分
        		$('#unit_percentage').show();
        		$('#unit_fen').hide();
        		$('#exchange_product').hide();
        	}else if(value == 4){ 
        		$('#unit_percentage').hide();
        		$('#unit_fen').show();
        		$('#exchange_product').show();
	        }else{ 
	        	$('#unit_percentage').hide();
	        	$('#unit_fen').show();
	        	$('#exchange_product').hide();
	        }
        });
//        $('input[name="coupon_type"]').change();
//        $('input[name="coupon_applied_type"]').change();
//        $('input[name="discount_type"]').change();
    }
    
    /**
     * 应用于商品的显示效果
     */
    function coupon_type_to_product(){
		if($('input[name="coupon_applied_type"]:checked').val()==0){
			coupon_applied_to_product();
		}else{
			coupon_applied_to_cat();
		}
		
		//应用条件的显示与隐藏
		$("#product_coupon_terms").show();
		$("#order_coupon_terms").hide();
		$("#user_coupon_terms").hide();
		
		//清除优惠条件中的所有的值
//		$(".coupon_terms_select").each(function(){
//			$(this).remove();
//		});
    }
    
    /**
     * 应用于商品分类的显示效果
     **/
    function coupon_applied_to_cat(){
    	$('#applied_to_cat').show();
		$('#apply_to_product').hide();
		//折扣类型的隐藏显示字段
		$('input[name="discount_type"][value="0"]').hide();
		$('input[name="discount_type"][value="1"]').hide();
		$('input[name="discount_type"][value="2"]').hide();
		$('input[name="discount_type"][value="3"]').hide();
		$('input[name="discount_type"][value="4"]').show();
		$('input[name="discount_type"][value="5"]').show();
		
		$('.stable_money').hide();
		$('.proportion').hide();
		$('.full_reduction').hide();
		$('.mod_full_reduction').hide();
		$('.exchange').show();
		$('.give').show();
    }
    /**
     * 应用于商品的显示效果
     **/
    function coupon_applied_to_product(){
    	$('#apply_to_type').show();
		$('#apply_to_product').show();
		$('#applied_to_cat').hide();
		
    	$('input[name="discount_type"][value="0"]').show();
		$('input[name="discount_type"][value="1"]').show();
		$('input[name="discount_type"][value="2"]').show();
		$('input[name="discount_type"][value="3"]').show();
		$('input[name="discount_type"][value="4"]').hide();
		$('input[name="discount_type"][value="5"]').hide();
		
		
		$('.stable_money').show();//直减
		$('.proportion').show();//比例折扣
		$('.full_reduction').show(); //满减
		$('.mod_full_reduction').show();//每满减
		$('.exchange').hide(); //换购
		$('.give').hide();//满X赠Y
    }
    
    
    /**
     * 应用于订单全的显示效果
     */
    function coupon_type_to_order(){
    	$('#apply_to_type').hide();
		$('#apply_to_product').hide();
		$('#applied_to_cat').hide();
		
		$('input[name="discount_type"][value="0"]').show();
		$('input[name="discount_type"][value="1"]').show();
		$('input[name="discount_type"][value="2"]').show();
		$('input[name="discount_type"][value="3"]').show();
		$('input[name="discount_type"][value="4"]').hide();
		$('input[name="discount_type"][value="5"]').hide();
		//文字
		$('.stable_money').show(); //直减
		$('.proportion').show(); //比例折扣
		$('.full_reduction').show(); //满减
		$('.mod_full_reduction').show();//每满减
		$('.exchange').hide(); //换购
		$('.give').hide(); //满X赠Y
		
		//应用条件的显示与隐藏
		$("#product_coupon_terms").hide();
		$("#order_coupon_terms").show();
		$("#user_coupon_terms").hide();
		
		//清除优惠条件中的所有的值
//		$(".coupon_terms_select").each(function(){
//			$(this).remove();
//		});
    }
    
    /**
     * 应用于用户券的显示效果
     */
    function coupon_type_to_user(){
    	$('#apply_to_type').hide();
		$('#apply_to_product').hide();
		$('#applied_to_cat').hide();
		
		//折扣类型的显示和隐藏
		$('input[name="discount_type"][value="0"]').show();
		$('input[name="discount_type"][value="1"]').show();
		$('input[name="discount_type"][value="2"]').hide();
		$('input[name="discount_type"][value="3"]').hide();
		$('input[name="discount_type"][value="4"]').hide();
		$('input[name="discount_type"][value="5"]').hide();
		//折扣类型的文字
		$('.stable_money').show(); //直减
		$('.proportion').show(); //比例折扣
		$('.full_reduction').hide(); //满减
		$('.mod_full_reduction').hide();//每满减
		$('.exchange').hide();
		$('.give').hide();
		
		//应用条件的显示与隐藏
		$("#product_coupon_terms").hide();
		$("#order_coupon_terms").hide();
		$("#user_coupon_terms").show();
		
		//清除优惠条件中的所有的值
//		$(".coupon_terms_select").each(function(){
//			$(this).remove();
//		});
    }
    
    /**
     * 初始化选择商品的弹出框 
     */
    function init_select_product(productSelector,fnOkProduct){
    	fnFancyBox(productSelector, function () {
            $('.fancybox-skin').css('background', '#fff');
            var inlist = $('#pds-pdright #inlists');
            // 目录树点击回调函数
            treeLoader.setting.callback.onClick = function (event, treeId, treeNode) {
                inlist.html('');
                Spinner.spin(inlist.get(0));
                $.get('?/FancyPage/ajaxPdBlocks/id=' + treeNode.dataId, function (html) {
                    inlist.html(html);
                    $('.pdBlock').bind('click', pdBlockLis);
                    $('#okSProduct').bind('click', fnOkProduct);
                });
            };

            // 初始化目录树
            treeLoader.init('#pds-catLeft', '?/vProduct/ajaxGetCategroys/r=' + (new Date()).getTime());

            $('#pdSelectSearch').bind('keydown', function (e) {
                var key = e.which;
                if (key === 13) {
                    if ($(this).val() === '') {
                        return false;
                    }
                    // [HttpGet]
                    $.get('?/FancyPage/ajaxPdBlocks/key=' + $(this).val(), function (html) {
                        inlist.html(html);
                        $('.pdBlock').bind('click', pdBlockLis);
                        $('#okSProduct').bind('click', okSProduct);
                    });
                }
            });
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

    /**
     * 商品选择Fancybox回调
     * @returns {undefined}
     */
    function okSelectProduct() {
        var blocks = $('.pdBlock.selected').clone();
        blocks.removeClass('selected').find('.sel').remove();
        blocks.addClass("selectProduct"); //只是为了区分
        $('#ProductItem').prepend(blocks);
        pdBlockAdjust();
        $.fancybox.close();
    }
    
    /**
     * 商品选择Fancybox回调
     * @returns {undefined}
     */
    function okExchangeProduct() {
        var blocks = $('.pdBlock.selected').clone();
        blocks.removeClass('selected').find('.sel').remove();
        blocks.addClass("selectBuddleProduct"); //只是为了区分选择的商品和绑定赠送的商品
        $('#scProductItem').prepend(blocks);
        var pdCountSelector = '#';
        pdBlockAdjust();
        $.fancybox.close();
    }
    
    
    
    /**
     * 商品选择自适应调整
     * @returns {undefined}
     */
    function pdBlockAdjust() {
        // 删除监听
        var allBlocks = $('#ProductItem .pdBlock');
        var Relid = [];
        allBlocks.hover(function () {
            var i = $('<i class="pd-image-delete"> </i>');
            i.bind('click', function () {
                $(this).parent().fadeOut(function () {
                    $(this).remove();
                    pdBlockAdjust();
                });
            });
            $(this).append(i);
        }, function () {
            $(this).find('.pd-image-delete').remove();
        });
        // 计算relId
        allBlocks.each(function (i, node) {
            $(this).find('.sel').remove();
            Relid.push($(this).attr('data-id'));
        });
        // 赋值
        relId = Relid.join(',');
        // 选择计数
        $('#selectPdCount').removeClass('hidden').html('已选择' + $('#ProductItem .pdBlock').length + '个产品');
        // 隐藏提示
        $('#spdTip').hide();
    }
    
    
    /**
     * 获取applied字段值
     */
    function getCouponAppliedData(){
    	var applied = coupon_applied_template;
        var coupon_type = $('input[name="coupon_type"]:checked').val();
        applied = applied.replace('__APPLIED_TYPE',coupon_type);
        if(coupon_type == 0){ //商品类
        	applied = applied.replace('__SUBTYPE',$('input[name="coupon_applied_type"]:checked').val());
        }else{   //订单类
        	applied = applied.replace('__SUBTYPE','""');
        	applied = applied.replace('__PRODUCTS','""');
        	applied = applied.replace('__CATEGORYS','""');
        }
        
        var products = [];
//        :[{'id':1,'name':'饮料','img':'*.jpg'}]}
        $('.selectProduct').each(function(i){
        	var id = $(this).attr('data-id');
        	var name =$(this).find('p').eq(0).text();
        	var imgSrc =$(this).find('img').eq(0).attr('src');
        	var index  = imgSrc.lastIndexOf("/");
        	imgSrc = imgSrc.substring(index+1);
        	var obj = {
        		'id':id,
        		'name':name,
        		'img':imgSrc
        	};
        	products.push(obj);
        })
        
        var categorys = [];
        $('input[name="coupon_product_cat"]:checked').each(function(i){
        	var id = $(this).val();
//        	var cat_name = $(this).attr('data-name');
//        	var cat_parent = $(this).attr('data-parent');
//        	
//        	var cat = {
//        		'id':id,
//        		'cat_name':cat_name,
//        		'cat_parent':cat_parent
//        	};
        	categorys.push(id);
        });
        
        if(products.length>0 && $('input[name="coupon_applied_type"]:checked').val() == 0){ //选择了商品
        	 applied = applied.replace('__PRODUCTS',JSON.stringify(products));
        }else{
        	applied = applied.replace('__PRODUCTS','""');
        }
        
        if(categorys.length>0 && $('input[name="coupon_applied_type"]:checked').val() == 1){  //选择了商品分类
        	applied = applied.replace('__CATEGORYS',JSON.stringify(categorys));
        }else{
        	applied = applied.replace('__CATEGORYS','""');
        }
       
        
        return applied;
    }
    
    /**
     * 获取使用条件coupon_terms字段值
     */
    function getCouponTermsData(){
//    	var coupon_terms_data_template = "[{'id':0,'name':'用户积分条件','table':'user','column':'group','operate':'>=','value':50}]";
    	var terms = [];
    	$('.coupon_terms_select').each(function(i){
    		if(i>0){ //去掉第一行
    			len = terms.length;
        		var name = $(this).find('.coupon_terms').eq(0).find('option:selected').attr('data-name');
        		var id = $(this).find('.coupon-terms-id').eq(0).val();
        		var table = $(this).find('.coupon-terms-table').eq(0).val();
        		var column = $(this).find('.coupon-terms-column').eq(0).val();
        		var operate = $(this).find('.coupon-terms-operate').eq(0).val();
        		var value = $(this).find('.coupon-terms-value').eq(0).val();
        		var obj ={
        			'id':id,
        			'name':name,
        			'table':table,
        			'column':column,
        			'operate':operate,
        			'value':value
        		}
        		
        		terms.push(obj);
    		}
    		
    	});
    	
    	//做去重处理
    	var coupon_terms ='';
    	if(terms.length>0){
    		var newTerms = deleteRepeatTerms(terms);
        	coupon_terms = JSON.stringify(newTerms);
    	}
    	return coupon_terms;
    }
    
    /**
     * 获取使用条件buddle字段值
     */
    function getCouponBuddledData(){
    	var products = [];
//      :[{'id':1,'name':'饮料','img':'*.jpg'}]}
      $('.selectBuddleProduct').each(function(i){
      	var id = $(this).attr('data-id');
      	var name =$(this).find('p').eq(0).text();
      	var imgSrc =$(this).find('img').eq(0).attr('src');
      	var index  = imgSrc.lastIndexOf("/");
      	imgSrc = imgSrc.substring(index+1);
      	var obj = {
      		'id':id,
      		'name':name,
      		'img':imgSrc
      	};
      	products.push(obj);
      })
      var buddled = '';
      if(products.length>0){
    	  buddled = JSON.stringify(products);
      }
      return buddled;
    }
    
    /**
     * 获取coupon_limit数据
     */
    function getCouponLimitData(){
    	var obj = {
    			'limit':$('input[name="coupon_limit"]:checked').val(),
    			'coupons':''
    	};
    	var data = JSON.stringify(obj);
    	return data;
    }
    
    /**
     * 数字去重
     **/
   function deleteRepeatTerms(arr){
	    var newArray=[];
	    var len=arr.length;

	    for (var i=0;i<len ;i++){
	        for(var j=i+1;j<len;j++){
	            if(arr[i].id===arr[j].id){
	                j=++i;
	            }
	        }
	        newArray.push(arr[i]);
	    }
	    return newArray;
    }

});