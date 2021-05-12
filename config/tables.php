<?php

/*
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
// 用户表
define('TABLE_USER', 'clients');

// 用户-代理 映射表
define('TABLE_COMPANY_USERS', 'company_users');

// 商品表
define('TABLE_PRODUCTS', 'products_info');

// 商品价格表
define('TABLE_PRODUCT_ONSALE', 'product_onsale');

// 商品系列表
define('TABLE_PRODUCT_SERIALS', 'product_serials');

// 商品规格表
define('TABLE_PRODUCT_SPEC', 'product_spec');

// 商品分类表
define('TABLE_PRODUCT_CATEGORY', 'product_category');

// 商品库存表
define('TABLE_PRODUCT_INSTOCK', 'product_instock');

// 商品品牌表
define('TABLE_BRAND', 'product_brand');

// 订单表
define('TABLE_ORDERS', 'orders');

// 订单明细表
define('TABLE_ORDERS_DETAILS', 'orders_detail');

// 订单地址表
define('TABLE_ORDER_ADDRESS', 'orders_address');

// 订单评论表
define('TABLE_ORDERS_COMMENT', 'orders_comment');

// banner表
define('TABLE_BANNERS', 'wshop_banners');

// 搜索记录表
define('TABLE_SEARCH_RECORD', 'wshop_search_record');

// 积分记录表
define('TABLE_CREDIT_RECORD', 'client_credit_record');

// 会员等级表
define('TABLE_USER_LEVEL', 'client_level');

// 会员红包表
define('TABLE_USER_ENVL', 'client_envelopes');

// 会员红包类型表
define('TABLE_USER_ENVL_TYPE', 'client_envelopes_type');

// 权限表
define('TABLE_AUTH', 'admin');

// 首页板块
define('TABLE_HOME_SECTION', 'wshop_settings_section');

// 代付订单表
define('TABLE_ORDER_REQS', 'order_reqpay');

// 抢红包活动
define('TABLE_ENVS_ROBLIST', 'envs_robblist');

// 团购表
define('TABLE_GROUP_BUYING', 'group_buying');

// 团购参团表
define('TABLE_GROUP_BUYING_FRIENDS', 'group_buying_friends');

// 素材表
define('TABLE_GMESS', 'gmess_page');

// 优惠券表
define('TABLE_SHOP_COUPONS', 'shop_coupons');

// 购物车表
define('TABLE_SHOP_CART', 'shop_cart');

// 优惠券条件表
define('TABLE_SHOP_COUPONS_TERMS', 'shop_coupons_terms');


// 用户优惠券表
define('TABLE_USER_COUPON', 'user_coupon');


//充值卡表
define('TABLE_SHOP_CHARGE_CARD', 'shop_charge_card');

//充值记录表
define('TABLE_USER_CHARGE_LOG', 'user_charge_log');


// 用户充值卡表
define('TABLE_USER_CHARGE_CARD', 'user_charge_card');

// 订单配送表
define('TABLE_ORDER_DISTRIBUTE', 'order_distribute');

define('TABLE_SPEC', 'wshop_spec_det');
define('TABLE_SKU', 'products_spec');
// 产品库存表
define('TABLE_PRODUCT_STOCK', 'product_instock');
// 原料库存表
define('TABLE_INGREDIENTS_STOCK', 'ingredients_instock');
define('TABLE_INGREDIENTS_STOCK_HISTORY', 'ingredients_stock_change');
// 包装配件库存表
define('TABLE_PACKS_STOCK', 'packs_instock');
    
// 线下店面支付记录表
define('TABLE_SHOP_PAY', 'crash_pay');

// 用户地址表
define('TABLE_USER_ADDRESS', 'user_address');

// access_token表
define('TABLE_SHOP_ACCESS_TOKEN', 'shop_access_token');

// 商户信息表
define('TABLE_SHOP_MERCHANT_INFO', 'shop_merchant_info');

// 接口调用统计表
define('TABLE_API_INVOKE_COUNT', 'API_INVOKE_COUNT');