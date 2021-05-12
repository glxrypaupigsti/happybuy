<?php

// 支付授权目录 112.124.44.172/wshop/
// 支付请求示例 index.php
// 支付回调URL http://112.124.44.172/wshop/?/Order/payment_callback
// 维权通知URL http://112.124.44.172/wshop/?/Service/safeguarding
// 告警通知URL http://112.124.44.172/wshop/?/Service/warning

/**
 * Webservice接口
 */
class API extends Controller {

    const MAX_PAGE_SIZE = 100;
    
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        $this->loadModel('mWebservice');
    }

    /**
     * 通过授权获取access_token接口
     * http://localhost/yummy/?/API/authorization/app_id=qz94476cb6c5e6af75&app_secret=09ddf9125d5d9dff29f2cbb05d4f72df1f62fcb
     */
    public function authorization($Q){
        $app_id = $Q->app_id;
        $app_secret = $Q->app_secret;

        if(empty($app_id)){
            $this->echoApiMsg(APP_ID_ERROR,'APP_ID错误');
            die(0);
        }

        if(empty($app_secret)){
            $this->echoApiMsg(APP_SECRET_ERROR,'APP_SECRET错误');
            die(0);
        }
        $merchant_info = $this->mWebservice->get_merchant_info_by_appid($app_id);
        if(!$merchant_info){
            $this->echoApiMsg(MERCHANT_NOT_EXIST,'商户不存在');
            die(0);
        }

        if($merchant_info['app_secret'] != $app_secret){
            $this->echoApiMsg(APP_SECRET_NOT_MATCH,'APP_SECRET不匹配');
            die(0);
        }

        $access_token_info =  $this->mWebservice->get_access_token($merchant_info);
        $expired_in = $this->mWebservice->get_expired_in();
        $data = array(
            'access_token' => $access_token_info['access_token'],
            'expired_in' => $expired_in
        );
        $this->echoApiMsg(SUCCESS,'SUCCESS',$data);
    }

    /**
     * 菜品分类列表
     * http://localhost/yummy/?/API/product_cats/access_token=r8Ea4+oY6ZUzP7RyzzSUxQbNRzDrmmM-RghXf02tNBg-
     */
    public function product_cats($Q){

        $access_token = $Q->access_token;
        $valid_result = $this->valid_access_token($access_token);
        if($valid_result['error_code'] != SUCCESS){
            $this->echoApiMsg($valid_result['error_code'],$valid_result['msg']);
            die(0);
        }
        $where[] = 'is_open=1';
        $list = $this->mWebservice->product_cats($where);
        $this->echoApiMsg(SUCCESS,'SUCCESS',$list);
    }

    /**
     * 菜品列表
     * http://localhost/yummy/?/API/product_list/access_token=r8Ea4+oY6ZUzP7RyzzSUxQbNRzDrmmM-RghXf02tNBg-&cat_id=100
     */
    public function product_list($Q){

        $access_token = $Q->access_token;
        $valid_result = $this->valid_access_token($access_token);
        if($valid_result['error_code'] != SUCCESS){
            $this->echoApiMsg($valid_result['error_code'],$valid_result['msg']);
            die(0);
        }

        $cat_id = $Q->cat_id;
        if(empty($cat_id)){
            $this->echoApiMsg(CAT_ID_INVALID,'商品分类编号不合法');
            die(0);
        }

        $page = $Q->page;
        $per_page = $Q->per_page;

        if(!$page){
            $page = 1;
        }
        if(!$per_page){
            $per_page = 10;
        }

        if($per_page and $per_page > self::MAX_PAGE_SIZE){
            $per_page = self::MAX_PAGE_SIZE;
        }

        $data = $this->mWebservice->get_product_list($cat_id,$page,$per_page,$Q->date);
        $this->echoApiMsg(SUCCESS,'SUCCESS',$data);

    }



    /**
     * 菜品详情
     * http://localhost/yummy/?/API/product_detail/access_token=r8Ea4+oY6ZUzP7RyzzSUxQbNRzDrmmM-RghXf02tNBg-&product_id=1
     */
    public function product_detail($Q){
        $access_token = $Q->access_token;
        $valid_result = $this->valid_access_token($access_token);
        if($valid_result['error_code'] != SUCCESS){
            $this->echoApiMsg($valid_result['error_code'],$valid_result['msg']);
            die(0);
        }

        $sku_id = $Q->product_id;

        $sku_data = $this->mWebservice->decomposit_sku_id($sku_id);
        $product_id = $sku_data['product_id'];
        error_log('product detail product_id====>'.$product_id);

        if(empty($product_id)){
            $this->echoApiMsg(PRODUCT_ID_INVALID,'商品编号不合法');
            die(0);
        }
        $product_info = $this->mWebservice->get_product_info_by_product_id($product_id);
        if(!$product_info){
            $this->echoApiMsg(PRODUCT_ID_INVALID,'商品编号不合法');
            die(0);
        }
        $data = $this->mWebservice->get_product_detail($product_info,$sku_id,$sku_data['spec_id']);
        $this->echoApiMsg(SUCCESS,'SUCCESS',$data);

    }


    /**
     * 提交订单
     */
    public function submit_order(){
        error_log('post parames==================================>'.json_encode($_POST));
        $this->loadModel('APIUtil');
        $access_token = $this->pPost('access_token');
        $valid_result = $this->valid_access_token($access_token);
        if($valid_result['error_code'] != SUCCESS){
            $this->echoApiMsg($valid_result['error_code'],$valid_result['msg']);
            die(0);
        }
        $nonce_str = $this->pPost('nonce_str');
        if(empty($nonce_str)){
            $this->echoApiMsg(NONCE_STR_INVALID,'nonce_str参数不合法');
            die(0);
        }
        $signature = $this->pPost('signature');
        if(empty($signature)){
            $this->echoApiMsg(SINGATURE_INVALID,'signature参数不合法');
            die(0);
        }

        $timestamp = $this->pPost('stamp');
        if(empty($timestamp)){
            $this->echoApiMsg(TIMESTAMP_INVALID,'stamp参数不合法');
            die(0);
        }


        error_log('shipment--------------------->'.$this->pPost('shipment'));
        error_log('order_data--------------------->'.$this->pPost('order_data'));

        $shippment_str = $this->pPost('shipment');
        $shippment = json_decode($shippment_str,true);
        error_log('shipment address--------------------->'.$shippment['user_name']);
        if(empty($shippment_str)){
            $this->echoApiMsg(SHIPPMENT_INVALID,'shippment参数不合法');
            die(0);
        }

        $remark = $this->pPost('remark');
        if(!$remark){
            $this->echoApiMsg(REMARK_INVALID,'remark参数不合法');
            die(0);
        }
        $order_data_str = $this->pPost('order_data');
        if(empty($order_data_str)){
            $this->echoApiMsg(ORDER_DATA_INVALID,'order_data参数不合法');
            die(0);
        }


        //验证shipment参数体
        $shipment_keys = array('user_name','telphone','address','date');
        if($this->valid_body($shippment,$shipment_keys)==0){
            $this->echoApiMsg(SHIPMENT_BODY_PARAMS_NOT_MATHC,'shipment参数json格式不正确');
            die(0);
        }

        //验证shipment参数体
        $order_data = json_decode($order_data_str,true);
        $order_data_keys = array('id','product_num');
        $order_data_valid = true;
        foreach($order_data as $key => $val){
            error_log('one data =====>'.json_encode($val));
            $result =  $this->valid_body($val,$order_data_keys);
            if($result==0){
                $order_data_valid = false;
                break;
            }
        }
        if(!$order_data_valid){
            $this->echoApiMsg(ORDER_DATA_BODY_PARAMS_NOT_MATHC,'order_data参数json格式不正确');
            die(0);
        }

        //验证签名
        $client_signature = $this->pPost('signature');
        //需要串联的key
        $params_key_arr = array('access_token','remark','timestamp','nonce_str','order_data','shipment');
        //需要将值进行特殊处理的key集合
        $special_keys = array('remark','order_data','shipment');
        $special_key_values = array(
            'remark' => 'string',
            'order_data' => 'body',
            'shipment' => 'addr',
        );
        $server_signature =$this->APIUtil->gen_signature($_POST,$params_key_arr,$special_keys,$special_key_values);
        error_log('client_signature====>'.$client_signature);
        error_log('server_signature====>'.$server_signature);
        if($client_signature != $server_signature){
            $this->echoApiMsg(SINGATURE_NOT_MATCH,'签名错误');
            die(0);
        }
        $data = $this->mWebservice->submit_order($_POST);
        $this->echoApiMsg(SUCCESS,'SUCCESS',$data);


    }


    /**
     * 订单详情
     * http://localhost/yummy/?/API/order_detail/access_token=r8Ea4+oY6ZUzP7RyzzSUxQbNRzDrmmM-RghXf02tNBg-&order_no=20151125093913000021
     */
    public function order_detail($Q){
        $access_token = $Q->access_token;
        $valid_result = $this->valid_access_token($access_token);
        if($valid_result['error_code'] != SUCCESS){
            $this->echoApiMsg($valid_result['error_code'],$valid_result['msg']);
            die(0);
        }

        $order_no = $Q->order_no;
        if(empty($order_no)){
            $this->echoApiMsg(ORDER_NO_INVALID,'订单号不合法');
            die(0);
        }

        $order_info = $this->mWebservice->get_order_info_by_order_no($order_no);
        if(!$order_info){
            $this->echoApiMsg(ORDER_NO_INVALID,'订单号不合法');
            die(0);
        }

        $data = $this->mWebservice->order_detail($order_info);
        $this->echoApiMsg(SUCCESS,'SUCCESS',$data);

    }


    /**
     * 创建商户
     * http://localhost/yummy/?/API/create_merchant/merchant_name=test
     */
    public function create_merchant($Q){
        $merchant_name = $Q->merchant_name;
        if(empty($merchant_name)){
            $this->echoApiMsg(-1,'请输入商户名称');
            die(0);
        }
        $data = array(
            'merchant_name' => $merchant_name
        );
        $app_info = $this->mWebservice->create_merchant_mock_data($data);
        $this->echoApiMsg(SUCCESS,'SUCCESS',$app_info);

    }



    /**
     * 校验access_token
     */
    public function valid_access_token($access_token,$current_time){

        $err_code = SUCCESS;
        $msg = 'SUCCESS';

        if(!$current_time){
            $current_time = time();
        }

        if(empty($access_token)){
            $err_code = ACCESS_TOKEN_ERROR;
            $msg = 'ACCESS_TOKEN无效';
        }else{
            $access_token_info = $this->mWebservice->get_access_token_info_by_token($access_token);
            if($access_token_info){
                $expired_time = $access_token_info['expired_time'];
                if($expired_time < $current_time){
                    $err_code = ACCESS_TOKEN_EXPIRED;
                    $msg = 'ACCESS_TOKEN已过期';
                }
            }else{
                $err_code = ACCESS_TOKEN_ERROR;
                $msg = 'ACCESS_TOKEN无效';
            }
        }

        $result = array(
            'error_code' => $err_code,
            'msg' => $msg
        );

        return $result;

    }


    public function valid_body($input_values_arr,$valid_keys){
        error_log('input_values_str ==========================>'.$shipment_str);
        $mathch_key_count = 0;

        error_log('valid_keys====>'.json_encode($valid_keys));
        foreach($input_values_arr as $key => $val){
            error_log('current key==========================>'.$key);
            if(in_array($key,$valid_keys)){
                $mathch_key_count++;
            }
        }
        error_log('mathch_key_count ==========================>'.$mathch_key_count);
        error_log('$input_values_arr ==========================>'.count($input_values_arr));
        $result = 1;
        if($mathch_key_count != count($valid_keys)){
            $result = 0;
        }
        error_log('result ==========================>'.$result);
        return $result;
    }
}
