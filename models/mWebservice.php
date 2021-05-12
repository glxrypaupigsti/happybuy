<?php

include_once 'Curl.php';
include_once 'DigCrypt.php';

/**
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <liao@qiezilife.com>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.icheerslife.com
 */
class mWebservice extends Model {

    /**
     * access_token有效时间
     */
    const ACCESS_TOKEN_EXPIRED_IN = 7200;

    /**
     * refresh_token有效时间，3600*24*30*3
     */
    const REFRESH_TOKEN_EXPIRED_IN = 7776000;

    /**
     * 获取access_token
     */
    public function get_access_token($merchant_info){
        $app_id = $merchant_info['app_id'];
        $access_token = $this->get_access_token_by_uid($merchant_info['uid']);
        error_log('access_token====>'.json_encode($access_token));
        if(!$access_token){
            //商户信息不存在，则直接生成一条数据
            $token_info = $this->insert_access_token($merchant_info);
        }else{
            $token_info = $this->get_access_token_by_uid($merchant_info['uid']);
            $time = time();
            //token已经过期
            if($token_info['expired_time'] < $time){
                $expired_time = $time + self::ACCESS_TOKEN_EXPIRED_IN;
                $uid = $merchant_info['uid'];
                $access_token = $this->gen_access_token($merchant_info);
                $this->update_access_token($uid,$access_token,$expired_time);
                $token_info['access_token']  = $access_token;
                $token_info['expired_time']  = $expired_time;
            }

        }
        return $token_info;
    }


    /**
     * 创建商户
     */
    public function create_merchant_mock_data($datas){
        $this->loadModel('APIUtil');
        $app_id = $this->APIUtil->gen_app_id();
        while(true){
            $merchant_info = $this->get_merchant_info_by_appid($app_id);
            if($merchant_info){ //存在则继续生成
                $app_id = $this->APIUtil->gen_app_id();
            }else{
                break;
            }
        }

        $merchant_no = $this->APIUtil->gen_merchant_no();
        while(true){
            $merchant_info = $this->get_merchant_info_by_merchant_no($merchant_no);
            if($merchant_info){
                $merchant_no = $this->APIUtil->gen_merchant_no();
            }else{
                break;
            }
        }


        $salt = $this->APIUtil->create_random_str(6);
        $app_secret = $this->APIUtil->gen_app_secret($app_id,$salt);

        $merchant_name = $datas['merchant_name'];
        $column_str = 'client_nickname,client_name,client_sex,client_joindate';
        $join_date = date('Y-m-d', time());
        $values=array($merchant_name,$merchant_name,'m',$join_date);
        $uid = $this->insert_data(TABLE_USER,$column_str,$values);
        error_log('uid====>'.$uid);




        $merchant_account = $this->APIUtil->gen_merchant_account($merchant_no);
        $merchant_password = $this->APIUtil->gen_merchant_password();

        $time = time();
        $merchant_insert_column_str = 'app_id,app_secret,salt,uid,merchant_no,merchant_account,merchant_password,add_time,last_update_time';
        $merchant_values = array($app_id,$app_secret,$salt,$uid,$merchant_no,$merchant_account,$merchant_password,$time,$time);
        $this->insert_data(TABLE_SHOP_MERCHANT_INFO,$merchant_insert_column_str,$merchant_values);
        $data = array(
            'app_id' => $app_id,
            'app_secret' => $app_secret
        );
        return $data;
    }

    /**
     * 获取分类列表
     */
    public function product_cats($where=array()){
        $condition = '';
        if(count($where)>0){
            $condition = implode(" and ",$where);
            $condition = " where " .$condition;
        }
        $sql = "select cat_id id ,cat_name from " .TABLE_PRODUCT_CATEGORY . $condition;
        error_log('product_cats sql ====>' .$sql);

        $cat_list = $this->Db->query($sql,false);
        return $cat_list;
    }

    /**
     * 获取菜品列表
     */
    public function get_product_list($cat_id,$page,$per_page,$date = null){

        $where[] = "`delete`=0 and product_cat=".$cat_id;
        $total = $this->get_count(TABLE_PRODUCTS,$where);
        $list = $this->get_paged_product_list($where,$page,$per_page,$date);
        $data = array(
            'total' => $total,
            'list' => $list
        );
        return $data;
    }

    /**
     * 获取菜品详情
     */
    public function get_product_detail($product_info,$sku_id,$spec_id){
        $product_id = $product_info['id'];
        $product_info['sub_img_urls'] = $this->get_product_sub_img($product_id);
        $product_info['sales'] = $this->get_product_sales($product_id,$spec_id);
        //最后更改sku
        $product_info['id'] = $sku_id;
        return $product_info;
    }

    /**
     * 提交订单
     */
    public function submit_order($post_params){
        $order_no = $this->create_order($post_params);
        $array = array(
            'order_no' => $order_no
        );
        return $array;
    }

    /**
     * 订单详情
     */
    public function order_detail($order_info){
        $address_id =  $order_info['address_id'];
        $shipment = $this->get_order_address_by_id($address_id);
        $order_id = $order_info['order_id'];
        $products = $this->get_order_products_by_order_id($order_id);
        $data = array(
            'order_no' => $order_info['order_no'],
            'notes' => $order_info['notes'],
            'shipment' => $shipment,
            'products' => $products
        );
        return $data;

    }



    /**
     * 分页获取商品列表
     */
    public function get_paged_product_list($where=array(),$page,$per_page,$date = null){
        $column = "product_id id ,product_name,catimg product_main_img_url,product_subname product_brief";
        $page_params=array(
            'page' => $page,
            'per_page' => $per_page
        );
        $order_by = 'id desc';
        $list = $this->get_list(TABLE_PRODUCTS,$column,$where,$page_params,$order_by);

        foreach($list as $key => &$val){
            $product_id = $val['id'];
            //组装skui
            $spec_info = $this->get_product_spec($product_id);
            $spec_id = $spec_info['id'];
            $sku_id = $this->assembly_sku_id($product_id,$spec_id);
            $val['id'] = $sku_id;

            $val['product_main_img_url'] = $this->get_complete_url($val['product_main_img_url']);
            $spec = $this->get_product_spec($product_id);
            $val['product_stock'] = $this->get_product_stock($spec['id'],$date);
            $val['sales'] = $this->get_product_sales($product_id,$spec_id);
            $val['product_price'] = $spec_info['sale_price'];
        }

        return $list;

    }


    /**
     * 创建订单
     */
    public function create_order($data){
        error_log('request parameters============================>'.json_encode($data));
        //获取用户id
        $token = $data['access_token'];
        $access_token_info = $this->get_access_token_info_by_token($token);
        $uid = $access_token_info['uid'];
        //生成订单号
        $order_id = $this->get_next_order_id();
        $serial_number = $this->gen_order_no($order_id);

        //下订单时间
        $time = time();
        $order_time = date("Y-m-d H:i:s",$time);
        //总价
//        $order_amount = $data['total_amount'];
        $order_amount = 0;
        $order_datas = json_decode($data['order_data'],true);
        error_log('request order_datas============================>'.$order_datas);
        $product_count = 0;
        foreach($order_datas as $key => &$val){
            $product_count = $product_count + intval($val['product_num']);
            $sku_data = $this->decomposit_sku_id($val['id']);
            //spec_id
            $val['spec_id'] = $sku_data['spec_id'];
            $single_price = $this->get_product_price($sku_data['spec_id']);
            $val['product_discount_price'] = $single_price;
            $val['product_id'] = $sku_data['product_id'];
            $order_amount = $order_amount + $single_price*intval($val['product_num']);
            //这里是sku,应该转化为product_id和spec_id
//            $val['product_spec_id'] = $this->get_product_spec($val['id']);
        }
        //支付状态为已支付
        $status = 'payed';
        $shipment = json_decode($data['shipment'],true);
        error_log('request shipment============================>'.$order_datas);
        //配货时间
        $exptime = $shipment['date'].' 14:00-15:00';

        //订单来源
        $user_info = $this->get_user_info_by_uid($uid);
//        $come_from = 'vita';
        $come_from = $user_info['nickname'];
        $pay_type = 0; //微信支付
        $address_id = 0;

        $remark = $data['remark'];

        $address_info = $this->validate_address($uid,$shipment['user_name'],$shipment['address'],$shipment['telphone']);
        error_log('address_info=================>'.json_encode($address_info));
        if($address_info){
            $address_id = $address_info['id'];
        }else{
            $address_id = $this->create_user_address($uid,$shipment['user_name'],$shipment['address'],$shipment['telphone']);
        }

        //插入订单数据
        $order_insert_column = 'order_id,client_id,order_time,order_amount,product_count,serial_number,status,exptime,pay_type,address_id,pay_amount,online_amount,come_from,notes';
        $order_values = array($order_id,$uid,$order_time,$order_amount,$product_count,$serial_number,$status,$exptime,$pay_type,$address_id,$order_amount,$order_amount,$come_from,$remark);
        $this->insert_data(TABLE_ORDERS,$order_insert_column,$order_values);

        //创建商品详情
        $this->create_order_detail($order_datas,$order_id);

        //创建配货单
        $this->create_order_distribute($serial_number,$address_id,'not_delievery',$exptime);

        return $serial_number;
    }


    /**
     * 批量添加订单详情
     */
    public function create_order_detail($products,$order_id){
        $sql = 'insert into '.TABLE_ORDERS_DETAILS.'(order_id,product_id,product_count,product_discount_price,product_price_hash_id) values ';
        foreach($products as $key => $val){
            $values[] = '('.$order_id.','.$val['product_id'].','.$val['product_num'].','.$val['product_discount_price'].','.$val['spec_id'].')';
        }
        $values_str = implode(',',$values);
        $sql = $sql . $values_str;
        error_log('batch insert order detail sql ====>'.$sql);
        $this->Db->query($sql,false);
    }

    /**
     * 创建配货单
     */
    public function create_order_distribute($order_serial_no,$address_id,$status,$exp_time){
        $time = time();
        //插入订单数据
        $order_insert_column = 'order_serial_no,address_id,status,exp_time,add_time,update_time';
        $order_values = array($order_serial_no,$address_id,$status,$exp_time,$time,$time);
        return $this->insert_data(TABLE_ORDER_DISTRIBUTE,$order_insert_column,$order_values);
    }

    /**
     * 生成订单号
     */
    public function get_next_order_id(){
        $column = "MAX(order_id)+1 id";
        $order_info = $this->get_detail(TABLE_ORDERS,$column);
        return intval($order_info['id']);
    }

    /**
     * 生成订单序列号
     */
    public function gen_order_no($order_id){
        $this->loadModel('mOrder');
        return $this->mOrder->generateOrderNum($order_id);
    }

    /**
     * 订单对应的商品列表
     */
    public function get_order_products_by_order_id($order_id){
        $sql = "select pro.product_name,pro.product_id id,pro.catimg product_main_img_url,pro.product_subname product_brief, detail.product_count product_num from " . TABLE_ORDERS_DETAILS." detail ".
               "left join ".TABLE_PRODUCTS." pro " .
               "on detail.product_id = pro.product_id ".
               "where detail.order_id=".$order_id;

        error_log('get_order_products_by_order_id  ====>' .$sql);
        $products = $this->Db->query($sql,false);

        foreach($products as $key => &$val){
            $val['product_main_img_url'] = $this->get_complete_url($val['product_main_img_url']);
        }
        return $products;

    }


    /**
     * 订单详情
     */
    public function get_order_info_by_order_no($order_no){
        $where[] = "serial_number = '".$order_no."'" ;
        $column = "order_id,serial_number order_no,address_id,notes";
        $order_info = $this->get_detail(TABLE_ORDERS,$column,$where);
        return $order_info;
    }


       /**
     * 获取订单地址
     */
    public function get_order_address_by_id($id){
        $where[] = "id=".$id;
        $column = "user_name ,phone telphone,address";
        $address_info = $this->get_detail('user_address',$column,$where);
        return $address_info;
    }


    /**
     * 分解sku
     */
    public function decomposit_sku_id($sku_id){
//        $sku_id = base64_decode($sku_id);
        $split_index = 5;
        $product_str = substr($sku_id,0,$split_index);
        $product_id = intval($product_str);

        $spec_str = substr($sku_id,$split_index);
        $spec_id = intval($spec_str);
        $data = array(
            'product_id' => $product_id,
            'spec_id' => $spec_id
        );

        return $data;

    }

    /**
     * 组装sku
     */
    public function assembly_sku_id($product_id,$spec_id){
        error_log('product_id====>'.$product_id.',spec_id====>'.$spec_id);
        $prefix_total_len = 5;
        $suffix_total_len = 3;
        //商品编号部分
        $product_str = strval($product_id);
        $product_part_len = strlen($product_str);
        $product_part_zero_len = $prefix_total_len - $product_part_len;
        $product_part_zero_str = '';
        for($i=0;$i<$product_part_zero_len;$i++){
            $product_part_zero_str = $product_part_zero_str.'0';
        }
        $product_part = $product_part_zero_str.$product_str;

        //商品规格部分
        $spec_part_zero_str = '';
        $spec_str =strval($spec_id);
        $spec_str_len = strlen($spec_str);
        $spec_part_zero_len = $suffix_total_len - $spec_str_len;
        for($i=0;$i<$spec_part_zero_len;$i++){
            $spec_part_zero_str = $spec_part_zero_str.'0';
        }
        $spec_part = $spec_part_zero_str.$spec_str;
        //使用base_64加密
//        $sku_id = base64_encode($product_part.$spec_part);
        $sku_id = $product_part.$spec_part;
        error_log('product_id====>'.$product_id.',spec_id====>'.$spec_id);
        return $sku_id;

    }

    /**
     * 获取商品的详情
     */
    public function get_product_info_by_product_id($product_id){
        $where[] = "product_id=".$product_id;
        $column = "product_id id ,product_name,catimg product_main_img_url,product_subname product_brief,product_desc product_description ";
        $product_info = $this->get_detail(TABLE_PRODUCTS,$column,$where);
        return $product_info;

    }

    /**
     * 获取商品的图片
     */
    public function get_product_sub_img($product_id){
        $where[] = "product_id=".$product_id;
        $column = "image_path";
        $img_list = $this->get_list('product_images',$column,$where);
        $sub_imgs = array();
        foreach($img_list as $key => $val){
            $sub_imgs[] = $this->get_complete_url($val['image_path']);
        }
        return $sub_imgs;
    }


    /**
     * 获取商品的图片完整的url
     */
    public function get_complete_url($origin_path){
        global $config;
        $base_path = $config->domain.'uploads/product_hpic/';
        return $base_path .$origin_path;
    }


    /**
     * 获取商品价格
     */
    public function get_product_price($spec_id){
        $where[] = "id=".$spec_id;
        $spec = $this->get_detail(TABLE_PRODUCT_SPEC,'sale_price',$where);
        return $spec['sale_price'];
    }

    /**
     *  商品库存
     */
    public function get_product_stock($spec_id,$target_time = ''){
        $this->loadModel('Stock');
        if($target_time){
            $target_time = strtotime($target_time);
        }else{
            $target_time = time();
        }
        $stock_info = $this->Stock->get_product_instock_by_sku_and_date($spec_id, $target_time);
        $instock = $stock_info['stock'];
        return $instock;
    }


    /**
     *  商品销量
     */
    public function get_product_sales($product_id,$spec_id){
        $sql = 'select sum(od.product_count) count from orders_detail od LEFT JOIN orders so on so.order_id = od.order_id '.
               'where so.`status` in("payed","received") '.
               'and od.product_id = '.$product_id.' and od.product_price_hash_id = '.$spec_id;
        error_log('get_product_sales sql====>'.$sql);
        $data = $this->Db->getOneRow($sql);
        error_log('get_product_sales data====>'.json_encode($data));
        return intval($data['count']);

    }

    /**
     * 获取spec
     */
    public function get_product_spec($product_id){
        $this->loadModel('Product');
        $spec = $this->Product->getProductSpecs($product_id);
        return $spec[0];
    }

    /**
     *  创建用户地址
     */
    public function create_user_address($uid,$user_name,$address,$phone){
        $time = time();
        //插入订单数据
        $user_address = 'uid,user_name,address,phone';
        $user_address_values = array($uid,$user_name,$address,$phone);
        $data =  $this->insert_data(TABLE_USER_ADDRESS,$user_address,$user_address_values);
        error_log('insert address id====================>'.$data) ;
        return $data;
    }

    /**
     *  检验用户地址
     */
    public function validate_address($uid,$user_name,$address,$phone){
        $where[] = "uid='".$uid."'";
        $where[] = "user_name='".$user_name."'";
        $where[] = "address='".$address."'";
        $where[] = "phone='".$phone."'";
        $address_info = $this->get_detail(TABLE_USER_ADDRESS,'*',$where);
        return $address_info;
    }

    /**
     * 获取用户信息
     */
    public function get_user_info_by_uid($uid){
        $this->loadModel('User');
        return $this->User->getUserInfo($uid);
    }


    /**
     * 插入access_token
     */
    public function insert_access_token($merchant_info){
        $time = time();
        $uid = $merchant_info['uid'];

        $access_token = $this->gen_access_token($merchant_info);
        $expired_time = $time + self::ACCESS_TOKEN_EXPIRED_IN;

        $refresh_token = $this->gen_access_token($merchant_info,true);
        $refresh_expired_time = $time + self::REFRESH_TOKEN_EXPIRED_IN;

        //控制char插入的token唯一
        while(true){
            $access_token_info = $this->get_access_token_info_by_token($access_token);
            if($access_token_info){
                $access_token = $this->gen_access_token($merchant_info);
            }else{
                break;
            }
        }

        while(true){
            $refresh_token_info = $this->get_access_token_info_by_fresh_token($refresh_token);
            if($refresh_token_info){
                $refresh_token = $this->gen_access_token($merchant_info,true);
            }else{
                break;
            }
        }

        $keys_arr = 'uid,access_token,expired_time,add_time,last_update_time,refresh_token,refresh_expired_time';
        $values = array($uid,$access_token,$expired_time,$time,$time,$refresh_token,$refresh_expired_time);
        $this->insert_data(TABLE_SHOP_ACCESS_TOKEN,$keys_arr,$values);

        $token_data = array(
            'access_token' => $access_token,
            'expired_time' => $expired_time,
            'refresh_expired_time' => $refresh_expired_time,
            'refresh_expired_time' => $refresh_expired_time,
        );
        return $token_data;
    }

    /**
     * 根据token获取access_token
     */
    public function get_access_token_by_uid($uid){
        $where[] = "uid = ".$uid ;
        $token_info = $this->get_detail(TABLE_SHOP_ACCESS_TOKEN,"*",$where);
        return $token_info;
    }


    /**
     * 根据token获取access_token
     */
    public function get_access_token_info_by_token($token){
        $where[] = "access_token = '".$token."'";
        $token_info = $this->get_detail(TABLE_SHOP_ACCESS_TOKEN,"*",$where);
        return $token_info;
    }
    /**
     * 根据token获取access_token
     */
    public function get_access_token_info_by_fresh_token($fresh_token){
        $where[] = "fresh_token = '".$fresh_token."'";
        $token_info = $this->get_detail(TABLE_SHOP_ACCESS_TOKEN,"*",$where);
        return $token_info;
    }

    public function update_access_token($uid,$access_token,$expired_time){
        $where[] = "uid=".$uid;
        $time = time();
        $data = array(
            'access_token' => $access_token,
            'expired_time' => $time + self::ACCESS_TOKEN_EXPIRED_IN
        );
        return $this->update_data(TABLE_SHOP_ACCESS_TOKEN,$data,$where);
    }


    /**
     * 根据appid获取商户详情
     */
    public function get_merchant_info_by_appid($app_id){
        $where[] = "app_id = '".$app_id."'";
        $merchant_info = $this->get_detail(TABLE_SHOP_MERCHANT_INFO,'*',$where);
        return $merchant_info;
    }

    /**
     * 根据$merchant_no获取商户详情
     */
    public function get_merchant_info_by_merchant_no($merchant_no){
        $where[] = "merchant_no='".merchant_no."'";
        $merchant_info = $this->get_detail(TABLE_SHOP_MERCHANT_INFO,null,$where);
        return $merchant_info;
    }



    /**
     * 插入数据的公用方法
     * $keys_str格式为uid,access_token,expired_time,add_time
     * $values为单个值得集合数组array(1,2,3,5)
     */
    public function insert_data($table_name,$keys_str,$values){
//        $this->Dao->insert($this->table2, 'keywords, code_discount, `template`')->values(array($keywords, $discount, $template))->exec();
        return $this->Dao->insert($table_name, $keys_str)->values($values)->exec();
    }


    /**
     * 获取列表
     */
    public function get_list($table_name,$colunms = '*',$where_condition=array(),$page_params=array(),$order_by = ''){
        $where = '';
        if(count($where_condition)>0){
            $where = implode(' and ',$where_condition);
            $where = "where " . $where;
        }

        $sql = 'select __COLUNMS from __TABLE_NAME  __WHERE_CONDITION  ';
        $sql = str_replace('__COLUNMS',$colunms,$sql);
        $sql = str_replace('__TABLE_NAME',$table_name,$sql);
        $sql = str_replace('__WHERE_CONDITION',$where,$sql);
        if(!empty($order_by)){
            $sql = $sql . 'order by '.$order_by;
        }

        //是否分页
        if(count($page_params)>0){
            $page = $page_params['page'];
            $per_page = $page_params['per_page'];
            $offset = ($page-1) * $per_page;
            $sql = $sql . ' limit '. $offset .',' . $per_page;
        }

        error_log('object list sql ====>'.$sql);
        return $this->Db->query($sql,false);
    }

    public function get_count($table_name , $where_condition=array()){
        $where = '';
        if(count($where_condition)>0){
            $where = implode(' and ',$where_condition);
            $where = " where " . $where;
        }
        $sql = 'select count(1) count from '.$table_name .$where;
        error_log('object count sql ====>'.$sql);
        $count = $this->Db->getOneRow($sql,false);
        return intval($count['count']);
    }


    /**
     * 更新数据的公用方法
     */
    public function get_detail($table_name,$colunms = '*',$where_condition=array()){
        $where = '';
        if(count($where_condition)>0){
            $where = implode(' and ',$where_condition);
            $where = 'where '.$where;
        }
        $sql = 'select __COLUNMS from __TABLE_NAME  __WHERE_CONDITION ';
        $sql = str_replace('__COLUNMS',$colunms,$sql);
        $sql = str_replace('__TABLE_NAME',$table_name,$sql);
        $sql = str_replace('__WHERE_CONDITION',$where,$sql);
        error_log('object detail sql ====>'.$sql);
        return $this->Db->getOneRow($sql,false);
    }

    /**
     * 更新数据的公用方法
     * $data格式为array('ename' => $name, 'ephone' => $phone)
     * $where_arr 为数组，格式为key=value
     */
    public function update_data($table_name,$data,$where_arr=array()){
        if(count($where_arr)>0){
            $where = implode(' and ',$where_arr);
        }
        return $this->Dao->update($table_name)->set($data)->where($where)->exec();
    }


    /**
     * 公用的生成access_token方法
     */
    public function gen_access_token($merchant_info,$is_refresh_token = false){
        $this->loadModel('APIUtil');
        $app_id = $merchant_info['app_id'];
        $app_secret = $merchant_info['app_secret'];
        $salt = $merchant_info['salt'];
        $access_token = $this->APIUtil->gen_access_token($app_id,$app_secret,$salt,$is_refresh_token);
        return $access_token;
    }


    /**
     * 公用的生成access_token方法
     */
    public function get_expired_in(){
        return self::ACCESS_TOKEN_EXPIRED_IN;
    }


}
