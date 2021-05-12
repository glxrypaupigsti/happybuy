<?php

/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http=>//www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http=>//www.iwshop.cn
 */
class Test extends Controller {

    public function put(){
        Curl::put($url, $postData);
    }
    
    public function orderRecile() {
        $expBegin = date('Y-m-d', strtotime('-29 DAY'));
        $orderIds = $this->Dao->select("GROUP_CONCAT(order_id)")->from(TABLE_ORDERS)->where("order_time < '$expBegin'")->getOne();
        if ($orderIds != '') {
            // 删除订单
            $this->Dao->delete()->from(TABLE_ORDERS)->where("order_id IN ($orderIds)")->exec();
            $this->Dao->delete()->from(TABLE_ORDERS_DETAILS)->where("order_id IN ($orderIds)")->exec();
            $this->Dao->delete()->from(TABLE_ORDER_ADDRESS)->where("order_id IN ($orderIds)")->exec();
        }
    }

    public function testRefund() {
        $url = 'http://hmws.ramalp.com/?/Order/orderRefund/';
        $postdata = array('id' => 25, 'amount' => 0.01);
        $ret = Curl::post($url, $postdata);
        echo $ret;
    }

    public function buildCatSearch() {
        $this->loadModel('Product');
        $pds = $this->Dao->select('product_id,product_cat')->from(TABLE_PRODUCTS)->exec();
        foreach ($pds as $pd) {
            echo $this->Product->buildCatSearch($pd['product_id'], $pd['product_cat']);
        }
    }

    /**
     * 反射方法
     */
    public function index() {
        $class = new ReflectionClass('Test');
        $instance = $class->newInstanceArgs();
        $methods = $class->getMethods();
        foreach ($methods as $m) {
            echo '<a href="?/Test/' . $m->getName() . '">' . $m->getName() . '</m>' . "<br />";
        }
    }

    public function notify() {
        $this->loadModel('mOrder');
        var_dump($this->mOrder->comNewOrderNotify(1));
        var_dump($this->mOrder->userNewOrderNotify(1, 'o_JvCuFQoYqbwIWOSPnrDkRP6Wrg'));
    }

    public function usercredit() {
        $this->loadModel('mOrder');
        $this->mOrder->creditFinalEstimate(851);
    }

    /**
     * 生成订单数据
     */
    public function genOrderData() {

        $this->Db->query('TRUNCATE TABLE orders;');

        $this->Db->query('TRUNCATE TABLE orders_detail;');

        $this->Db->query('TRUNCATE TABLE orders_address;');

        ini_set("max_execution_time", 7200);

        $this->loadModel('mOrder');

        $expFee = rand(6, 15);

        $openids = array('oau7MtyyFJq8Gp0t0_-zSBWUGHrA',
            'oau7Mt699Y3vWp_iQ5WMXTNCh4bs',
            'oau7Mt_ODbN8dpIuQzl0e3aPrSSg',
            'oau7MtyPfsEi35ETYSW7z_z3MKh8',
            'oau7Mt6wK0o75gvFFcvgwSClP0f4',
            'oau7Mt2EOgm3gVnemfdVlt4UbNX0',
            'oau7Mt92UnZUjn7eaRC5SW1A7pRg',
            'oau7Mt11b9WCLKX5X4XXh9AaLBN4',
            'oau7Mt6aeDyj7idm5EdvMzi5fdW4',
            'oau7MtxRlyW7d-n0fZkvaHkZWKaE',
            'oau7Mt27-PQlvJldvsDSRbo2eu_c',
            'oau7Mt0HIVhy-zVy2BpfOAti5zUY');

        $addrs = array(
            array(
                'proviceFirstStageName' => '新疆维吾尔自治区',
                'addressCitySecondStageName' => '广州市',
                'addressCountiesThirdStageName' => '天河区',
                'addressDetailInfo' => '新燕花园三期1201 新燕花园三期1201 新燕花园三期1201 新燕花园三期1201',
                'addressPostalCode' => 510006,
                'telNumber' => 18565518404,
                'userName' => '陈永才'
            ),
            array(
                'proviceFirstStageName' => '新疆维吾尔自治区',
                'addressCitySecondStageName' => '广州市',
                'addressCountiesThirdStageName' => '天河区',
                'addressDetailInfo' => '新燕花园三期1201 新燕花园三期1201 新燕花园三期1201 新燕花园三期1201',
                'addressPostalCode' => 510006,
                'telNumber' => 18565518404,
                'userName' => '陈永才2'
            )
        );
        $this->runStart();
        for ($i = 0; $i < 10000; $i++) {

            $pid = $this->Dao->select('product_id')->from(TABLE_PRODUCTS)->where('product_cat < 50')->orderby('RAND()')->limit(1)->getOne(false);

            $this->mOrder->create($openids[ceil(rand(0, count($openids) - 1))], 'asdasdasd', array("p{$pid}m0" => rand(1, 5)), $addrs[ceil(rand(0, count($addrs) - 1))], false, $expFee, '');
        }
        $this->runEnd();
    }
    
    /**
     * 用户获取优惠券 
     */
    public function testInsertUserCoupon($Q){
    	$this->loadModel('UserCoupon');
    	$uid = 4;
   		$ret = $this->UserCoupon->insertUserCoupon($Q->coupon_id,$uid);
   		if($ret == -2){
   			$msg = "coupon doesnot exists";
   		}else if($ret == -1){
   			$msg = "you cannot get the coupon again";
   		}else{
   			$msg = "success";
   		}
   		$this->echoMsg($ret,$msg);
    }

    /**
     * 用户获取自己使用和未使用的优惠券
     */
    public function testGetUserCouponListByState($Q){
    	//已使用
//     	http://localhost/yummy/?/Test/testGetUserCouponListByState/is_used=1
		//未使用
//     	http://localhost/yummy/?/Test/testGetUserCouponListByState/is_used=0
    	$this->loadModel('UserCoupon');
    	$uid = 4;
    	$is_used = $Q->is_used;
    	$ret = $this->UserCoupon->getUserCouponListByState($uid,$is_used);
    	echo $this->toJson($ret);
    }
    
    /**
     * 用户获取优惠券
     */
    public function testGetUserExpiredCouponList($Q){
//     	http://localhost/yummy/?/Test/testGetUserExpiredCouponList
    	$this->loadModel('UserCoupon');
    	$uid = 4;
    	$ret = $this->UserCoupon->getUserExpiredCouponList($uid);
    	echo $this->toJson($ret);
    }
    
    public function test_use_coupon($Q){
    	$this->loadModel('Coupons');
    	$coupon_id = $Q->coupon_id;
    	$uid = 116;
    	$time = time();
    	$coupon_info = $this->Coupons->get_coupon_info($coupon_id);
    	echo $this->Coupons->use_selected_coupon($coupon_info,$uid,$time);
    }
    
    
    public function test_batch_reduce_amount($Q){
    	$coupon_ids = $Q->coupon_ids;
    	
    	$uid = 116;
    	$this->loadModel('mOrder');
    	$order_amount = 10;
    	$total_money = 0;
    	if(strpos($coupon_ids, ',') !== -1){
    		echo 'contains dou hao'.$val;
    		$coupon_arr = explode(',',$coupon_ids);
    		foreach($coupon_arr as $key => $val){
    			echo 'id=========>'.$val;
    			$reduce_money = $this->mOrder->cal_reduce_amount_by_coupon_id($order_amount,$val,$uid = null,$is_user_coupon = false);
    			echo 'reduce amount====>'.$reduce_money;
    			echo '   ';
    			$total_money  = $total_money + $reduce_money;
    		}
    	}else{
    		echo 'id=========>'.$val;
    		$reduce_money = $this->mOrder->cal_reduce_amount_by_coupon_id($order_amount,$coupon_id,$uid = null,$is_user_coupon = false);
    		echo 'reduce amount====>'.$reduce_money;
    		echo '   ';
    		$total_money = $reduce_money;
    	}
    	
    	echo 'total_money====>'.$total_money;
    }
    
    
    public function get_not_use_coupon(){
    	$this->loadModel('Coupons');
    	$this->loadModel('UserCoupon');
    	$uid = 116;
    	$time = time();
    	$user_coupons = $this->Coupons->get_avaliable_coupons_for_order($time, $uid,1);
    	foreach ($user_coupons as $key => $val){
    		$coupon_info = $this->Coupons->get_coupon_info($val['coupon_id']);
    		echo json_encode($coupon_info);
    		echo '\n';
    	}
    } 
    
    /**
     * 用户使用优惠券
     */
    public function testUseCoupon($Q){
    	
//     	http://localhost/yummy/?/Test/testUseCoupon/coupon_id=10
    	$this->loadModel('UserCoupon');
    	$uid = 4;
    	$coupon_id = $Q->coupon_id;
    	$ret = $this->UserCoupon->useCoupon($uid,$coupon_id);
    	if($ret == -2){
    		$msg = "coupon doesnot exists";
    	}else if($ret == -1){
    		$msg = "coupon is expired";
    	}else if($ret == -3){
    		$msg = "you donot have this coupon";
    	}else if($ret == -4){
    		$msg = "you have used this coupon";
    	}else{
    		$msg = "success";
    	}
    	
    	$this->echoMsg($ret,$msg);
    }
    
    /**
     * 获取商品可用优惠券
     */
    public function testGetProductAvailableCoupons($Q){
//     	http://localhost/yummy/?/Test/testGetProductAvailableCoupons/product_id=79
    	$this->loadModel('Coupons');
    	$product_id = $Q->product_id;
    	$time = time();
    	$uid = 4;
    	$ret = $this->Coupons->get_avaliable_coupons_for_product($product_id, $time, $uid);
    	echo json_encode($ret);
    }
    
    /**
     * 获取订单可用优惠券
     */
    public function testGetOrderAvailableCoupons($Q){
//     	http://localhost/yummy/?/Test/testGetOrderAvailableCoupons/order_id=79
    	$this->loadModel('Coupons');
    	//order_id = 77;
//     	$order_id = $Q->order_id;
    	$time = time();
    	$uid = 4;
    	$ret = $this->Coupons->get_avaliable_coupons_for_order($order_id, $time, $uid);
    	echo json_encode($ret);
    }
    
    /**
     * 计算订单优惠券和用户优惠券的折扣值
     */
    public function testCalOrderAmountWithCoupon($Q){
    	$this->loadModel('mOrder');
    	$order_id = $Q->order_id;
    	$coupon_ids = "28";
    	$amount = $this->mOrder->calc_amount($order_id,$coupon_ids);
    	echo $amount;
    } 
    
    
    public function testCalReduceAmount($Q){
    	error_log('enter method');
    	$this->loadModel('mOrder');
    	$order_id = $Q->order_id;
    	$coupon_id = 27;
    	$uid = 4;
    	//是否为用户优惠券的标志，当值为false时候表示为订单优惠券
    	$is_user_coupon = true; 
    	$ret = $this->mOrder->cal_reduce_amount_by_coupon_id($order_id,$coupon_id,$uid,$is_user_coupon);
    	error_log("order_id====>".$order_id);
    	if($ret == -2){   //优惠券不存在
    		$msg = "coupon doesnot exists";
    	}else if($ret == -1){ //优惠券已经过期
    		$msg = "coupon is expired";
    	}else if($ret == -3){ //用户没有该优惠券
    		$msg = "you donot have this coupon";
    	}else if($ret == -4){ //用户该优惠券已经使用
    		$msg = "you have used this coupon";
    	}else if($ret == -5){ //订单不存在
    		$msg = "order donot exists";
    	}else{
    		$msg = "success";
    	}
    	$this->echoMsg($ret,$msg);
    }
    
    
    public function testCalCartProductAmount($Q){
    	$this->loadModel('Carts');
    	$uid = $Q->uid;
    	$amount = $this->Carts->calc_cart_amount($uid);
    	echo $amount;
    }
    
    
    public function get_avaliable_coupons_for_order($Q){
    	$this->loadModel('Coupons');
    	$time = time();
    	$uid = $Q->uid;
    	$ret = $this->Coupons->get_avaliable_coupons_for_order($time, $uid,0);
    	echo json_encode($ret);
    }
    
    
    public function test_get_charge_code($Q){
//     	http://localhost/yummy/?/Test/test_get_charge_code/num=1000000
    	$this->loadModel('mChargeCard');
    	$num = $Q->num;
    	if(!$num || $num <= 0){
    		$num = 1;
    	}
    	
    	for($i=0;$i<$num;$i++){
    		$code = $this->mChargeCard->create_voucher_code();
    		$code = $code.'('.strlen($code).')';
    		echo $code;
    		echo '  ';
    	}
    }
    
     public function testGeo(){

  		$address = '浦东新区盛夏路560号';
  		$ret = Curl::get('http://api.map.baidu.com/geocoder/v2/?address=浦东新区盛夏路560号&output=json&ak=0NnLgeO4V61jARaU0PMOT0OB&callback=showLocation');
  		
		echo $ret;
    }
    
    
    public function testAjaxCreateDistributeList($Q){
    	$order_id = $Q->order_id;
    	$this->loadModel('mOrderDistribute');
    	$state = $this->mOrderDistribute->create_distribute_list_by_order_id($order_id);
    	echo $state;
    }
    
    public function GetOrderDetailBySerialNo($Q){
    	$this->loadModel('mOrder');
    	$serial_no = $Q->serial_no;
    	echo $serial_no;
    	$list = $this->mOrder->GetOrderDetailBySerialNo($serial_no);
    	echo $this->toJson($list);
    }
    
    
    public function get_shared_coupon_list(){
    	$this->loadModel('Coupons');
    	$list = $this->Coupons->get_shared_coupon_list();
    	echo $this->toJson($list);
    }


	public function generate_base_app_info(){
		$this->loadModel('APIUtil');
		$app_id = $this->APIUtil->gen_app_id();
		echo 'app_id====>'.$app_id;
		echo("\n");
		$salt = $this->APIUtil->create_random_str(6);
		echo 'salt====>'.$salt;
		echo("\n");
		$app_secret = $this->APIUtil->gen_app_secret($app_id,$salt);
		echo 'app_secret====>'.$app_secret;
		echo("\n");
		echo("\n");
		$access_token = $this->APIUtil->gen_access_token($app_id,$app_secret,$salt);
		echo 'access_token====>'.$access_token;
		echo("\n");
		echo("\n");
		$source_str = $this->APIUtil->des_decrypt($access_token);
		echo 'source_str====>'.$source_str;
		echo("\n");
		echo("\n");
		echo("\n");

		$nonce_str = $this->APIUtil->create_random_str(13);

		$time = time();

		$post_params = array(
			'stamp' =>$time,
			'access_token' => $access_token,
			'remark' => 'string',
			'nonce_str' => $nonce_str,
			'order_data' => 'body',
			'shipment' => 'address',
		);
		$special_keys = array('remark','order_data','shipment');
		$special_key_values = array(
			'remark' => 'string',
			'order_data' => 'body',
			'shipment' => 'addr',
		);
		$key_arr = array('access_token','remark','timestamp','nonce_str','order_data','shippment');
		$signature = $this->APIUtil->gen_signature($post_params,$key_arr,$special_keys,$special_key_values);
		echo("\n");
		echo("\n");
		echo("\n");
		echo 'signature==========>'.$signature;

		echo("\n");
		echo("\n");
		echo("\n");
		$data = array(
			array(
				'order_no' =>'20160104183922001'
			),
			array(
				'order_no' =>'20160104183922002'
			),
			array(
				'order_no' =>'20160104183922003'
			)

		);
		echo $this->echoApiMsg(200,'success',$data);
	}


	public function submit_order($Q){
		$this->loadModel('APIUtil');
		$this->loadModel('mWebservice');

		$environment = 0;

		global $config;
		$domian = $config->domain;
		$shoproot = $config->shoproot;

		if(strpos($domian,'test.icheerslife.com')!== false){
			$sku_id1 = '00141091';
			$sku_id2 = '00142092';
		}else{
			$sku_id1 = '00001001';
			$sku_id2 = '00002002';
		}
		$domian = substr($domian,0,strlen($domian)-1);

		$url=$domian.$shoproot.'?/API/submit_order/';
		$time = time();
		$nonce_str = $this->APIUtil->create_random_str(13);

		$date = date('Y-m-d');

		$shipment = array(
			'user_name' => 'wangli',
			'telphone' => '13812217711',
			'address' => '盛夏路580号',
			'date' => $date
 		);

		$order_data = array(
			array(
				'id' => $sku_id1,
				'product_num' => 2
			),
			array(
				'id' => $sku_id2,
				'product_num' => 2
			)
		);
		$token = $Q->access_token;


		$ship = json_encode($shipment);
		$order_datas = json_encode($order_data);
		$post_params=array(
			'access_token'=>$token,
			'stamp'=>$time,
			'nonce_str'=>$nonce_str,
			'remark'=>'ss',
			'shipment'=>$ship,
			'order_data' =>$order_datas
		);
		$params_key_arr = array('access_token','remark','timestamp','nonce_str','order_data','shipment');
		//需要将值进行特殊处理的key集合
		$special_keys = array('remark','order_data','shipment');
		$special_key_values = array(
			'remark' => 'string',
			'order_data' => 'body',
			'shipment' => 'addr',
		);
		$server_signature =$this->APIUtil->gen_signature($post_params,$params_key_arr,$special_keys,$special_key_values);


		$fields=array(
			'access_token'=>$token,
			'signature'=>$server_signature,
			'stamp'=>$time,
			'nonce_str'=>$nonce_str,
			'remark'=>'ss',
			'shipment'=>$ship,
			'order_data' =>$order_datas
		);

		echo Curl::post($url,$fields);


	}

}
