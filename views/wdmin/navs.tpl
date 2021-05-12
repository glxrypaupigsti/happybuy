{if $admin_level eq 0}
    {if $Auth.stat}
        <a href="javascript:;" class="navItem" id="navitem2" rel='subnav2'>
            <b></b><i class='label'>报表中心</i>
        </a>
        <div class='subnavs clearfix' id='subnav2'>
            <a class='cap-nav-item' href='javascript:;' data-page="overview" data-nav="home">微店总览</a>
            <a class='cap-nav-item' href='javascript:;' data-page="sale_trend" data-nav="home">销售分析</a>
            <a class='cap-nav-item' href='javascript:;' data-page="user_ans" data-nav="home">用户分析</a>    
            {*        <a class='cap-nav-item' href='javascript:;' data-page="area_ans" data-nav="home">地区分析</a>*}
            {*  <a class='cap-nav-item' href='javascript:;' data-page="com_sale" data-nav="home">代理分析</a>*}
        </div>
    {/if}

    {if $Auth.orde}
        <a href="javascript:;" class="navItem" id="navitem4" rel='subnav4'>
            <b></b><i class='label'>订单管理</i>
        </a>
        <div class='subnavs clearfix' id='subnav4'>
            <a id='__odpaylist' class='cap-nav-item' href='javascript:;' data-page="orders_toexpress" data-nav="orders">未发货订单 <b class="icount">(0)</b></a>
            <a id='__oddevlist' class='cap-nav-item' href='javascript:;' data-page="orders_expressing" data-nav="orders">已发货订单 <b class="icount">(0)</b></a>
            <a class='cap-nav-item' href='javascript:;' data-page="orders_unpay" data-nav="orders">未支付订单 <b class="icount">(0)</b></a>
            <a class='cap-nav-item' href='javascript:;' data-page="orders_refunded" data-nav="orders">已退款订单 <b class="icount">(0)</b></a>
            <a class='cap-nav-item' href='javascript:;' data-page="orders_received" data-nav="orders">已完成订单 <b class="icount">(0)</b></a>
            <a id='__odcanlist' class='cap-nav-item' href='javascript:;' data-page="orders_toreturn" data-nav="orders">退款申请 <b class="icount">(0)</b></a>
            <a id='__odcanlist' class='cap-nav-item' href='javascript:;' data-page="orders_closed" data-nav="orders">已关闭订单 <b class="icount">(0)</b></a>
            <a id='__odalllist' class='cap-nav-item' href='javascript:;' data-page="orders_all" data-nav="orders">全部订单 <b class="icount">(0)</b></a>
            <a class='cap-nav-item' href='javascript:;' data-page="orders_history_address" data-nav="orders">收货地址</a>
        </div>
    {/if}
    
    {if $Auth.distribute}
        <a href="javascript:;" class="navItem" id="navitem27" rel='subnav27'>
            <b></b><i class='label'>配送管理</i>
        </a>
        <div class='subnavs clearfix' id='subnav27'>
            <a id='__distributelist' class='cap-nav-item' href='javascript:;' data-page="distribute_list" data-nav="coupon">配送列表 </a>
        </div>
    {/if}
    
    {if $Auth.coupon}
        <a href="javascript:;" class="navItem" id="navitem25" rel='subnav25'>
            <b></b><i class='label'>优惠券管理</i>
        </a>
        <div class='subnavs clearfix' id='subnav25'>
            <a id='__couponlist' class='cap-nav-item' href='javascript:;' data-page="coupon_list" data-nav="coupon">优惠券列表 </a>
            <a id='__coupontermlist' class='cap-nav-item' href='javascript:;' data-page="coupon_terms" data-nav="coupon">使用条件列表 </a>
            <a id='__sharecouponsettings' class='cap-nav-item' href='javascript:;' data-page="share_coupon_settings" data-nav="coupon">分享优惠券设置 </a>
            <a id='__awardsettings' class='cap-nav-item' href='javascript:;' data-page="award_settings" data-nav="coupon">奖励设置 </a>
            <a id='__usercoupons' class='cap-nav-item' href='javascript:;' data-page="user_coupon_list" data-nav="coupon">用户优惠券 </a>
        </div>
    {/if}
	
	{if $Auth.charge}
        <a href="javascript:;" class="navItem" id="navitem26" rel='subnav26'>
            <b></b><i class='label'>充值管理</i>
        </a>
        <div class='subnavs clearfix' id='subnav26'>
            <a id='__chargeList' class='cap-nav-item' href='javascript:;' data-page="charge_card_list" data-nav="charge">充值卡列表 </a>
            <a id='__chargeRecordList' class='cap-nav-item' href='javascript:;' data-page="user_charge_log_list" data-nav="charge">用户充值记录 </a>
        </div>
    {/if}

    {if $Auth.prod}
        <a href="javascript:;" class="navItem" id="navitem5" rel='subnav5'>
            <b></b><i class='label'>商品管理</i>
        </a>
        <div class='subnavs clearfix' id='subnav5'>     
            <a id='__pdlist' class='cap-nav-item' href='javascript:;' data-page="list_products" data-nav="products">商品管理 <b class="icount">(0)</b></a>
            {* <a id='__stockmanage' class='cap-nav-item' href='javascript:;' data-page="list_product_instock" data-nav="products">库存管理 <b class="icount">(0)</b></a> *}
            <a id='__catlist' class='cap-nav-item' href='javascript:;' data-page="alter_products_category" data-nav="products">分类管理 <b class="icount">(0)</b></a>
            <a id='__specmanage' class='cap-nav-item' href='javascript:;' data-page="alter_product_specs" data-nav="products">规格管理 <b class="icount">(0)</b></a>
            <a class='cap-nav-item' href='javascript:;' data-page="alter_product_serials" data-nav="products">系列管理 <b class="icount">(0)</b></a>
            <a class='cap-nav-item' href='javascript:;' data-page="alter_product_brand" data-nav="products">品牌管理 <b class="icount">(0)</b></a>
            <a class='cap-nav-item' href='javascript:;' data-page="deleted_products" data-nav="products">回收站 <b class="icount">(0)</b></a>
        </div>
    {/if}

    {if $Auth.stock}
        <a href="javascript:;" class="navItem" id="navitem16" rel='subnav16'>
            <b></b><i class='label'>库存管理</i>
        </a>
        <div class='subnavs clearfix' id='subnav16'>
            <a id='__pd-stock' class='cap-nav-item' href='javascript:;' data-page="products_stock" data-nav="stocks">成品管理 <b class="icount">(0)</b></a>
            <a id='__ing-stock' class='cap-nav-item' href='javascript:;' data-page="ingredients_stock" data-nav="stocks">食材管理 <b class="icount">(0)</b></a>
            <a id='__packs-stock' class='cap-nav-item' href='javascript:;' data-page="packs_stock" data-nav="stocks">包装配件 <b class="icount">(0)</b></a>
        </div>
    {/if}

	<!--
    {if $Auth.gmes}
        <a href="javascript:;" class="navItem" id="navitem15" rel='subnav15'>
            <b></b><i class='label'>营销中心</i>
        </a>
        <div class='subnavs clearfix' id='subnav15'>
            <a class='cap-nav-item' href='javascript:;' data-page="user_envsend" data-nav="customers">红包发放</a>
            <a class='cap-nav-item' href='javascript:;' data-page="settings_envs" data-nav="customers">红包设置</a>
            <a class='cap-nav-item' href='javascript:;' data-page="envsRobList" data-nav="customers">限抢红包</a>
            <a class='cap-nav-item' href='javascript:;' data-page="settings_sign" data-nav="settings">签到积分</a>
            <a class='cap-nav-item' href='javascript:;' data-page="discount_code_list" data-nav="settings">优惠码</a>
            {*            <a class='cap-nav-item' href='javascript:;' data-page="settings_group" data-nav="settings">团购促销</a>*}
        </div>
    {/if}
	-->
    {if $Auth.gmes}
        <a href="javascript:;" class="navItem" id="navitem6" rel='subnav6'>
            <b></b><i class='label'>消息群发</i>
        </a>
        <div class='subnavs clearfix' id='subnav6'>
            <!--<a id='__usmessage' class='cap-nav-item' href='javascript:;' data-page="customer_messages">会员消息</a>-->
            <a class='cap-nav-item' href='javascript:;' data-page="gmess_list" data-nav="gmess">素材列表</a>
            <!--<a class='cap-nav-item' href='javascript:;' data-page="gmess_category" data-nav="gmess">素材分类</a>-->
            <a class='cap-nav-item' href='javascript:;' data-page="gmess_send" data-nav="gmess">消息群发</a>
            <a class='cap-nav-item' href='javascript:;' data-page="gmess_sent" data-nav="gmess">群发历史</a>
        </div>
    {/if}

    {if $Auth.user}
        <a href="javascript:;" class="navItem" id="navitem9" rel='subnav9'>
            <b></b><i class='label'>会员管理</i>
        </a>
        <div class='subnavs clearfix' id='subnav9'>
            <a id='__uslist' class='cap-nav-item' href='javascript:;' data-page="list_customers" data-nav="customers">会员列表</a>
            <a class='cap-nav-item' href='javascript:;' data-page="user_level" data-nav="customers">会员等级</a>
            <!--        <a class='cap-nav-item' href='javascript:;' data-page="list_customers" data-nav="customers">分组管理</a>
                    <a class='cap-nav-item' href='javascript:;' data-page="list_customers" data-nav="customers">粉丝列表</a>-->
        </div>
    {/if}

 <!--
    {if $Auth.comp}
        <a href="javascript:;" class="navItem" id="navitem8" rel='subnav8'>
            <b></b><i class='label'>代理合作</i>
        </a>
        <div class='subnavs clearfix' id='subnav8'>
            <a id='__comlist' class='cap-nav-item' href='javascript:;' data-page="list_companys" data-nav="company">全部代理 <b class="icount">(0)</b></a>
            <a class='cap-nav-item' href='javascript:;' data-page="company_requests" data-nav="company">代理申请 <b class="icount">(0)</b></a>
            <a class='cap-nav-item' href='javascript:;' data-page="company_withdrawal" data-nav="company">代理结算 <b class="icount">(0)</b></a>
            <a class='cap-nav-item' href='javascript:;' data-page="company_bills" data-nav="company">结算历史</a>
            <a class='cap-nav-item' href='javascript:;' data-page="corporation" data-nav="company">合作伙伴</a>
        </div>
    {/if}
    
    -->
    
       <a href="javascript:;" class="navItem" id="navitem8" rel='subnav8'>
            <b></b><i class='label'>分享管理</i>
        </a>
         <div class='subnavs clearfix' id='subnav8'>
            <a id='__comlist' class='cap-nav-item' href='javascript:;' data-page="alert_share" data-nav="share">分享设置<b class="icount"></b></a>
            <a class='cap-nav-item' href='javascript:;' data-page="share_list" data-nav="share">分享记录<b class="icount"></b></a>
 
        </div>


    {if $Auth.sett}
        <a href="javascript:;" class="navItem" id="navitem7" rel='subnav7'>
            <b></b><i class='label'>微店设置</i>
        </a>
        <div class='subnavs clearfix' id='subnav7'>
            <a class='cap-nav-item' href='javascript:;' data-page="settings_base" data-nav="settings">基础设置</a>
            <a class='cap-nav-item' href='javascript:;' data-page="settings_autoresponse" data-nav="setttings">自动回复</a>
            <a class='cap-nav-item' href='javascript:;' data-page="settings_menu" data-nav="settings">自定菜单</a>
            <a class='cap-nav-item' href='javascript:;' data-page="settings_banners" data-nav="settings">广告设置</a>
            <a class='cap-nav-item' href='javascript:;' data-page="settings_section" data-nav="settings">首页板块</a>
            <a class='cap-nav-item' href='javascript:;' data-page="settings_expfee" data-nav="settings">运费模板</a>
            <a class='cap-nav-item' href='javascript:;' data-page="settings_expcompany" data-nav="settings">快递设置</a>
            <a class='cap-nav-item' href='javascript:;' data-page="settings_reci" data-nav="settings">发票设置</a>
            <a class='cap-nav-item' href='javascript:;' data-page="settings_auth" data-nav="settings">管理权限</a>
        </div>
    {/if}
{/if}

<br />
<br />
<br />