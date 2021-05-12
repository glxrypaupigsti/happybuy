<?php

// 支付授权目录 112.124.44.172/wshop/
// 支付请求示例 index.php
// 支付回调URL http://112.124.44.172/wshop/?/Order/payment_callback
// 维权通知URL http://112.124.44.172/wshop/?/Service/safeguarding
// 告警通知URL http://112.124.44.172/wshop/?/Service/warning

/**
 * 订单类
 */
class Order extends Controller {

    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
    }

    // 支付回调页面 代付
    public function payment_notify_req() {
        // postStr
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $reqId = str_replace('req-', '', $postObj->out_trade_no);
        // 微信交易单号
        $transaction_id = $postObj->transaction_id;
        // 更新订单状态
        $this->Db->query(sprintf("UPDATE `order_reqpay` SET `wepay_serial` = '%s',`dt` = NOW() WHERE `id` = %s AND `openid` = '%s';", $transaction_id, $reqId, $postObj->openid));
        // 邮件通知
        if ($reqId > 0) {
            // order_reqpay
            $this->loadModel('mOrder');
            $this->loadModel('WechatSdk');
            $orderId = $this->Dao->select('order_id')->from('order_reqpay')->where("id = $reqId")->getOne();
            // 检查募集成功
            $orderInfo = $this->mOrder->getOrderInfo($orderId);
            $reqEd = $this->mOrder->getOrderReqAmount($orderId);
            if ($reqEd == $orderInfo['order_amount']) {
                // 成功
                $this->Dao->update(TABLE_ORDERS)->set(array('status' => 'payed'))->where("order_id = $orderId")->exec();
                Messager::sendText(WechatSdk::getServiceAccessToken(), $orderInfo['wepay_openid'], "您有一笔代付进度已经到达100%！请进入个人中心查看");
            } else {
                Messager::sendText(WechatSdk::getServiceAccessToken(), $orderInfo['wepay_openid'], "您有一笔订单成功获得代付！请进入个人中心查看");
            }
        }
        // 返回success
        echo "<xml><return_code><![CDATA[SUCCESS]]></return_code></xml>";
    }

    // 支付回调页面
    public function payment_notify() {
        global $config;
         $this->loadModel('User');
         $this->loadModel('mOrder');
         $this->loadModel('mCashPay');
        // postStr
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        // orderid 
        $out_trade_no = $postObj->out_trade_no;
        $isMode = strpos($out_trade_no,'dq') === 0;
        $mode = 1;//0线下餐厅支付  1商品购买支付
		if($isMode){
    	   $mode = 0; 
    	   $orderId = str_replace('dq', '', $postObj->out_trade_no);
    	   $len = strlen($orderId);
           $orderNum = substr($orderId,$len-6,$len);
           $orderId =  preg_replace('/^0*/', '',$orderNum);
         	
         	$pay = $this->mCashPay->getCrashPayInfo($orderId);
         
          if($pay && !$pay['is_send']){
          
            $code = $this->mCashPay->getCode();          
            $openid = "oalpuuDbXtJL4oIBzDBNW8n9ZdoY";//点沁
            $this->mCashPay->paySuccessNotify($orderId,$openid);
            $this->mCashPay->payUserNotify($orderId,$postObj->openid); 

             $statusArray =  array(
                             'status' => 'payed',
                             'code'=>$code,
                             'is_send'=>1
                          );
            $this->mCashPay->updateOrder($orderId,$statusArray);
          }   
		}else{
    	   $mode = 1; 
    	   $orderId = str_replace($config->out_trade_no_prefix, '', $postObj->out_trade_no);
    	   
		
        
        $len = strlen($orderId);
        $orderNum = substr($orderId,$len-6,$len);
        $orderId =  preg_replace('/^0*/', '',$orderNum);
 
        // 微信交易单号
        $transaction_id = $postObj->transaction_id;
        // 更新订单状态
        $wepay_serial = $this->Dao->select('wepay_serial')->from(TABLE_ORDERS)->where("order_id = $orderId")->getOne(false);

        if ($wepay_serial == '') {
            $UpdateSQL = sprintf("UPDATE `orders` SET `wepay_serial` = '%s',`status` = 'payed',`wepay_openid` = '%s' WHERE `order_id` = %s AND `status` <> 'payed';", $transaction_id, $postObj->openid, $orderId);
            
            $r1 = $this->Db->query($UpdateSQL);

            // 邮件通知
            if ($r1 !== false && $orderId > 0) {

                $orderInfo = $this->mOrder->GetSimpleOrderInfo($orderId);
               // error_log("=============payment_notify=============".json_encode($orderInfo));
                if($orderInfo){
                    
                    $uinfo = $this->User->getUserInfoRaw($postObj->openid);
                    $uid = $uinfo['client_id'];

                    // 支付成功奖励？
                    $awardSettings = $this->Dao->select("value")->from('wshop_settings')->where("`key` = 'award_settings'")->getOne();
                    if (!empty($awardSettings)) {
                        // 得到支付奖励类型
                        $awardSettingsArr = json_decode($awardSettings, true);
                        if ($awardSettingsArr['paid_award'] AND ($awardSettingsArr['paid_award']['type'] > 0)) {
                            $awardType = $awardSettingsArr['paid_award']['type'];
                            $value = $awardSettingsArr['paid_award']['value'];
                            $this->loadModel('WechatSdk');
                            
                            if ($awardType == 1) {
                                // 返券
                                $this->loadModel('Coupons');
                                $this->loadModel('UserCoupon');
                                // add coupon to user
                                $rtnCode = $this->UserCoupon->insertUserCoupon($value, $uid, true,'order');
                                $this_coupon = $this->Coupons->get_coupon_info(($value));
                                $discount_val = intval($this_coupon['discount_val']/100);
                                $content = "偷偷送你一张（".$discount_val."元）优惠券~不要告诉别人呦\n适用范围：CheersLife全部商品\n使用规则：在下单时选中优惠券抵用即可\n快来享用健康美食吧~";
                                $url = $config->domain.'?/Coupon/user_coupon/';
                            } else if ($awardType == 2) {
                                // 返余额
                                $user_info = $this->User->getUserInfoRaw($postObj->openid);
                                $deposit = $user_info['client_money'];
                                $award = $value/100;
                                //面额,以分单位
                                $amount = $deposit + $award;
                                $this->User->updateUserMoneyByOpenId($postObj->openid, $amount);
                                //error_log('reg award is money ================>'.$money);
                                $content = "偷偷送你（".$award."元）~不要告诉别人呦\n适用范围：CheersLife全部商品\n使用规则：在下单时选中优惠券抵用即可\n快来享用健康美食吧~";
                                $url = $config->domain.'?/Uc/home/';
                            }
                            // send weixin msg
                            Messager::sendNotification(WechatSdk::getServiceAccessToken(), $postObj->openid, $content, $url);
                            
                        }
                    }
                    

                    if($orderInfo[0]['pay_type'] !=  0){
                      $balance_amount =round($orderInfo[0]['pay_amount'] - $orderInfo[0]['online_amount'],2);
                      $id = $this->moneyPay($uid,$balance_amount);
                    }


                }
                
                

                $this->loadModel('User');
                $this->loadModel('mOrder');
                $this->loadModel('mOrderDistribute');
                //添加配送单
                //error_log("======== begin to go to create_distribute_list_by_order_id wechat callback ===================". $pay_amount);
                $this->mOrderDistribute->create_distribute_list_by_order_id($orderId);
                
                $this->mOrder->cutInstock($orderId);
                // 商户订单通知
                @$this->mOrder->comNewOrderNotify($orderId);
                // 用户订单通知 模板消息
                @$this->mOrder->userNewOrderNotify($orderId, $postObj->openid);
                // 导入订单数据到个人信息
                @$this->User->importFromOrderAddress($orderId);
                // 积分结算
                @$this->mOrder->creditFinalEstimate($orderId);
                // 减库存
                
                
            }

            // 返回success
            echo "<xml><return_code><![CDATA[SUCCESS]]></return_code></xml>";
        }
       }
    }


     /**
    * 生成订单
   * // openid , 产品 list ,地址, 付款类型,发票抬头，发票内容 ，总额，需支付金额，在线支付金额，余额支付，运费
    */
    public function  createOrder()
    {
        $this->loadModel('mOrder');
        $this->loadModel('User');
        $this->loadModel('Coupons');
        $this->loadModel('mProductSpec');
        $this->loadModel('Product');
        $this->loadModel('Stock');
        
        $openid = $this->getOpenId();
        $this->loadModel('Carts');
        $uinfo = $this->User->getUserInfo($openid);
        $uid = $uinfo['uid'];
        $addrData = $_POST['addrData'];
        $reciHead = $_POST['reciHead'];
        $reciCont = $_POST['reciTex'];
        // format time
        $time = $_POST['time'];
        $time = str_replace('月', '-', $time);
        $time = str_replace('日', '', $time);
        $coupon = $_POST['coupon'];
        $userBalance = $uinfo['balance'];
        $isbalance = $_POST['isbalance'];
        
        $pay_type = 0;//默认 0  在线支付  1 余额支付  2 在线+余额度
        $yun = 0;
        
        
        $online_amount = 0;
        $balance_amount = 0;
        
        
        if(!Controller::inWechat()){
            $this->show('./index/error.tpl');
            die(0);
        }
   
    	//验证库存
        $cartList = $this->mOrder->orderListRepack(json_decode($_POST['cartData'], true));
        //$ret[] = array('pid' => intval($matchs[1]), 'spid' => intval($matchs[2]), 'count' => intval($count));
        $product_stock_not_enouph_msg_arr = array();
        $stock_not_enouph = '库存不足';
        $hint = '';
        // split $time to get deliver date
        $check_time = strtotime(substr($time, 0, 10)); //'2015-01-01'
        foreach ($cartList as $key =>$cart){
        	//$product_spec_info = $this->mProductSpec->get_spec_sale_price($cart['spid']);
        	$product_info = $this->Product->get_simple_product_info($cart['pid']);
            $stock_info = $this->Stock->get_product_instock_by_sku_and_date($cart['spid'], $check_time);
        	
        	if($stock_info['stock']<$cart['count']){ //库存
        		$hint = $product_info['product_name'].$stock_not_enouph;
        		$product_stock_not_enouph_msg_arr[] = $hint;
        	}
        }
        //库存不足直接退出当前
        if(!empty($product_stock_not_enouph_msg_arr)){
        	$this->echoMsg(-1,implode(',',$product_stock_not_enouph_msg_arr));
        	die(0);
        }
        
        $amount = $this->mOrder->sumOrderAmount($cartList);
        $pay_amount= $amount;
        

      if($coupon != ""){
      
          $arr = explode(",",$coupon);
          $redAmount = 0;
          foreach($arr as $u){
           
              $couponInfo = $this->Coupons->get_coupon_info($u); 
              if($couponInfo['coupon_type'] == 1 || $couponInfo['coupon_type'] == 2){
                  
                  $couponAmount = $this->mOrder->cal_reduce_amount_by_coupon_id($amount,$u,$uid,false);
                  $redAmount = $redAmount + $couponAmount;
              }
         }

          $pay_amount = round($pay_amount - $redAmount,2);
          if($pay_amount <= 0){
            $pay_amount = 0;
          }
          
      }
        $orderId = $this->mOrder->createOrder($openid,json_decode($_POST['cartData'], true),$addrData,$pay_type,$reciHead,$reciCont,$amount,$pay_amount,$online_amount,$balance_amount,$yun,$coupon,$isbalance,$time);
		
		
		 if($coupon != ""){
      
            $arr = explode(",",$coupon);
            foreach($arr as $u){
                 $couponInfo = $this->Coupons->get_coupon_info($u);
                 $this->Coupons->use_selected_coupon($couponInfo,$uid,time());
            }
         }
        //清空购物车
        $this->Carts->del_cart($uid);
        $this->echoMsg(1,$orderId);
        
    }
    
    
    
    public function ajaxOrderPay(){
        $this->loadModel('User');
        $this->loadModel('mOrder');
        $this->loadModel('Coupons');
        $this->loadModel('mProductSpec');
        $this->loadModel('Product');
        $this->loadModel('mOrderDistribute');
        $this->loadModel('Stock');
        $orderId = $_POST['orderId'];
        $openid = $this->getOpenId();
        
        $uinfo = $this->User->getUserInfo($openid);
        $uid = $uinfo['uid'];
        $userBalance = $uinfo['client_money'];
        $pay_type = 0;
        $online_amount = 0;
        $balance_amount = 0;
       // error_log("===========================orderId======".$orderId);

        if($orderId <= 0){
           $this->echoMsg(-1,"无效订单");
        }else{
           $checkResult = $this->mOrder->isValidOrder($orderId);
           $orderInfo = $this->mOrder->GetSimpleOrderInfo($orderId);
           if($checkResult == ""){
            // error_log("=============checkResult===================订单已支付或者订单不存在".$checkResult);

               $this->echoMsg(-1,"订单已支付或者订单不存在");
           }else{
	           	//只检查未支付订单中的库存
	           	if($orderInfo[0]['status'] == 'unpay'){
	           		$order_product_list = $this->mOrder->GetOrderDetails(intval($orderInfo[0]['order_id']));
	           	
	           		//$ret[] = array('pid' => intval($matchs[1]), 'spid' => intval($matchs[2]), 'count' => intval($count));
	           		$product_stock_not_enouph_msg_arr = array();
	           		$stock_not_enouph = '库存不足';
	           		$hint = '';
	           		//$check_time = time();
                    $deliver_time = $orderInfo[0]['exptime'];
                    $check_time = strtotime(substr($deliver_time, 0, 10)); //'2015-01-01'
	           		foreach($order_product_list as $key => $order_product){
	           			//$product_spec_info = $this->mProductSpec->get_spec_sale_price($order_product['product_price_hash_id']);
	           			 
	           			$stock_info = $this->Stock->get_product_instock_by_sku_and_date($order_product['product_price_hash_id'], $check_time);
	           			
	           			$product_info = $this->Product->get_simple_product_info($order_product['product_id']);
	           		
	           			if($stock_info['stock']<$order_product['product_count']){ //库存
	           				$hint = $product_info['product_name'].$stock_not_enouph;
	           				$product_stock_not_enouph_msg_arr[] = $hint;
	           			}
	           		}
	           	
	           		//error_log("============product_stock_not_enouph_msg_arr==================".json_encode($product_stock_not_enouph_msg_arr));
	           		//库存不足直接弹出
	           		if(!empty($product_stock_not_enouph_msg_arr)){
	           			$this->mOrder->updateOrderStatus($orderInfo[0]['order_id'],"closed",false);
	           			$this->echoMsg(-1,implode(",",$product_stock_not_enouph_msg_arr));
	           			die(0);
	           		}
	           	
	           	}
            
             $pay_amount = $orderInfo[0]['pay_amount'];

             //error_log("======== pay_amount===================". $pay_amount);
             if($pay_amount == 0){
                   $balance_amount = $pay_amount;
                   $online_amount = 0;
                   $pay_type = 1;
                   $id = $this->moneyPay($uid,$balance_amount);
              
                         $stateArray =  array(
                             'status' => 'payed',
                             'pay_type' => $pay_type,
                             'balance_amount' =>$balance_amount
                          );
                      $this->mOrder->updateOrder($orderId,$stateArray);
                      $this->mOrder->cutInstock($orderId);
                      $this->mOrder->comNewOrderNotify($orderId);
                      //加入配送单
                      //error_log("======== begin to go to create_distribute_list_by_order_id 1 ===================". $pay_amount);
                      $this->mOrderDistribute->create_distribute_list_by_order_id($orderId);
                    $this->echoMsg(1,'成功');
             }else{
       
             if($orderInfo[0]['isbalance'] == '0'){
             
                $online_amount = $pay_amount;
                $balance_amount = 0;
                $pay_type = 0;
                $payTypeArray =  array(
                   'pay_type' => $pay_type,
                    'online_amount' => $online_amount

                );
                $this->mOrder->updateOrder($orderId,$payTypeArray);
                $result = $this->wechatPay($online_amount,$orderInfo[0]['serial_number']);
                
                $this->echoMsg(2,$result);
             }else{
                  //start
              if($userBalance == 0){

                 $orderNum = $orderInfo[0]['serial_number'];
                if($pay_amount !=  $orderInfo[0]['online_amount']){
                  $orderNum = $this->mOrder->generateOrderNum($orderId);
                  $serArray = array('serial_number'=>$orderNum);
                  $this->mOrder->updateOrder($orderId,$serArray);
                }
                  //在线支付
                $online_amount = $pay_amount;
                $balance_amount = 0;
                $pay_type = 0;

                $payTypeArray =  array(
                   'pay_type' => $pay_type,
                   'online_amount' => $online_amount,
                   'balance_amount' => 0
                );



                //error_log("======$userBalance=======checkResult===================oneline".$online_amount);

                $this->mOrder->updateOrder($orderId,$payTypeArray);
                $result = $this->wechatPay($online_amount,$orderNum);

                $this->echoMsg(2,$result);
              }else {
               if($pay_amount  > $userBalance){
               
               	   
                   $online_amount = $pay_amount - $userBalance;
                   $orderNum =$orderInfo[0]['serial_number'];
        		   if($online_amount !=  $orderInfo[0]['online_amount']){
        		   
						//更改订单序列号
						$orderNum = $this->mOrder->generateOrderNum($orderId);
						$serArray = array('serial_number'=>$orderNum);
						$this->mOrder->updateOrder($orderId,$serArray);
        		   }	
                   
                   $online_amount = round($online_amount,2);
                   $balance_amount = $userBalance;
                   //在线+ 余额支付 
                   //error_log("=============checkResult===================oneline+ pay_amount".$pay_amount."===userBalance====".$userBalance."====online===".$online_amount);
                   $pay_type = 2;
                   $result = $this->wechatPay($online_amount,$orderNum);
                   $stateArray =  array(
                             'online_amount' => $online_amount,
                             'pay_type' => $pay_type,
                             'balance_amount' =>$balance_amount
                          );
                   $this->mOrder->updateOrder($orderId,$stateArray);

                   $this->echoMsg(2,$result);
                }else{
                  //error_log("=============checkResult===================balance".$checkResult);

                   //余额支付
                   $balance_amount = $pay_amount;
                   $online_amount = 0;
                   $pay_type = 1;
                   $id = $this->moneyPay($uid,$balance_amount);
                   if($id){
                         $stateArray =  array(
                             'status' => 'payed',
                             'pay_type' => $pay_type,
                             'online_amount'=>$online_amount,
                             'balance_amount' =>$balance_amount
                          );
                      $this->mOrder->updateOrder($orderId,$stateArray);
                      $this->mOrder->cutInstock($orderId);
                      $this->mOrder->comNewOrderNotify($orderId);
                      //加入配送单
                      //error_log("======== begin to go to create_distribute_list_by_order_id 2 ===================". $pay_amount);
                      $this->mOrderDistribute->create_distribute_list_by_order_id($orderId);
                       
                       // 支付成功奖励？
                       $awardSettings = $this->Dao->select("value")->from('wshop_settings')->where("`key` = 'award_settings'")->getOne();
                       if (!empty($awardSettings)) {
                           // 得到支付奖励类型
                           $awardSettingsArr = json_decode($awardSettings, true);
                           if ($awardSettingsArr['paid_award'] AND ($awardSettingsArr['paid_award']['type'] > 0)) {
                               global $config;
                               $awardType = $awardSettingsArr['paid_award']['type'];
                               $value = $awardSettingsArr['paid_award']['value'];
                               $this->loadModel('WechatSdk');
                               
                               if ($awardType == 1) {
                                   // 返券
                                   $this->loadModel('Coupons');
                                   $this->loadModel('UserCoupon');
                                   // add coupon to user
                                   $rtnCode = $this->UserCoupon->insertUserCoupon($value, $uid, true,'order');
                                   $this_coupon = $this->Coupons->get_coupon_info(($value));
                                   $discount_val = intval($this_coupon['discount_val']/100);
                                   $content = "偷偷送你一张（".$discount_val."元）优惠券~不要告诉别人呦\n适用范围：CheersLife全部商品\n使用规则：在下单时选中优惠券抵用即可\n快来享用健康美食吧~";
                                   $url = $config->domain.'?/Coupon/user_coupon/';
                               } else if ($awardType == 2) {
                                   $user_info = $this->User->getUserInfoRaw($openid);
                                   // 返余额
                                   $deposit = $user_info['client_money'];
                                   $award = $value/100;
                                   //面额,以分单位
                                   $amount = $deposit + $award;
                                   $this->User->updateUserMoneyByOpenId($openid, $amount);
                                   //error_log('reg award is money ================>'.$money);
                                   $content = "偷偷送你（".$award."元）~不要告诉别人呦\n适用范围：CheersLife全部商品\n使用规则：在下单时选中优惠券抵用即可\n快来享用健康美食吧~";
                                   $url = $config->domain.'?/Uc/home/';
                               }
                               // send weixin msg
                               Messager::sendNotification(WechatSdk::getServiceAccessToken(), $openid, $content, $url);
                           }
                       }
                      
                      $this->echoMsg(1,'成功');    
                   }else{
                      $this->echoMsg(-1,'失败');    
                   }
                 
                }
            }//end
            }

           }
         }
        
        }
    
    
    }
    
    
    
    /**
    * 余额支付
    */
    public function moneyPay($uid,$balance_amount){
    
  
       $this->loadModel('User');
       $id = $this->User->mantUserBalance($balance_amount,$uid, $type = User::MANT_BALANCE_DIS);
       return $id;
   }

  
    
    /**
     * Ajax获取订单请求数据包
     */
    public function wechatPay($amount,$id) {
        global $config;
        $this->loadModel('mOrder');
        
        $orderId = $id;


        $openid = $this->getOpenId();
        // 订单总额
        
        
        $totalFee = $amount * 100;

       
        $nonceStr = $this->Util->createNoncestr();

        $timeStamp = strval(time());

        $pack = array(
            'appid' => APPID,
            'body' => $config->shopName,
            'timeStamp' => $timeStamp,
            'mch_id' => PARTNER,
            'nonce_str' => $nonceStr,
            'notify_url' => $config->order_wxpay_notify,
            'out_trade_no' => $config->out_trade_no_prefix . $orderId,
            'spbill_create_ip' => $this->getIp(),
            'total_fee' => $totalFee,
            'trade_type' => 'JSAPI',
            'openid' => $openid
        );

        $pack['sign'] = $this->Util->paySign($pack);

        $xml = $this->Util->toXML($pack);
       //error_log("========ret=========".$xml);
        $ret = Curl::post('https://api.mch.weixin.qq.com/pay/unifiedorder', $xml);

        error_log("========ret=========".$ret);

        $postObj = json_decode(json_encode(simplexml_load_string($ret, 'SimpleXMLElement', LIBXML_NOCDATA)));

        //error_log("================postObj==========".$postObj->prepay_id);
        if (empty($postObj->prepay_id) || $postObj->return_code == "FAIL") {
            // 支付发起错误
            $this->log('wepay_error:' . $postObj->return_msg);
		
        }


        #var_dump($postObj);

        $packJs = array(
            'appId' => APPID,
            'timeStamp' => $timeStamp,
            'nonceStr' => $nonceStr,
            'package' => "prepay_id=" . $postObj->prepay_id,
            'signType' => 'MD5'
        );

        $JsSign = $this->Util->paySign($packJs);

        $packJs['timestamp'] = $timeStamp;

        $packJs['paySign'] = $JsSign;

        return $packJs;


    }
  
    /**
     * cookie
     * 计算订单总量
     * @return <float>
     */
    private function countOrderSum($orderid) {
        $sum = $this->Db->query("SELECT `order_amount` FROM `orders` WHERE `order_id` = $orderid;");
        return $sum[0]['order_amount'];
    }

    /**
     * expressDetail 查看物流情况
     */
    public function expressDetail($Query) {

        global $config;
        $this->loadModel('mUserAddress');
        $this->loadModel('User');
        $this->loadModel('mQuestion');
    
        if (!isset($Query->order_id) && $Query->order_id > 0) {
            exit(0);
        }
        $this->loadModel('JsSdk');

        if(!Controller::inWechat() && !$this->debug){

            $this->show('./index/error.tpl');
            die(0);
        }
        $openId = $this->getOpenId();
        $this->User->wechatAutoReg($openId);

        $this->cacheId = $openId . $Query->order_id;
        $uinfo = $this->User->getUserInfo($openId);

        $openid = $openId;//$this->getOpenId();
        if (!$this->isCached()) {

            $openIds = explode(',', $this->getSetting('order_notify_openid'));

            $Query->order_id = addslashes($Query->order_id);

            // 订单信息
            $orderData = $this->Db->getOneRow("SELECT * FROM `orders` WHERE `order_id` = $Query->order_id;");

            $openIds[] = $orderData['wepay_openid'];

            if (!in_array($openId, $openIds)) {
                echo 0;
            } else {
                $this->loadModel('Product');
                $orderProductsList = $this->Db->query("SELECT `catimg`,`pi`.product_name,`pi`.product_id,`sd`.product_count,`sd`.product_discount_price,`sd`.product_price_hash_id FROM `orders_detail` sd LEFT JOIN `products_info` pi on pi.product_id = sd.product_id WHERE `sd`.order_id = " . $Query->order_id);
                $expressCode = include dirname(__FILE__) . '/../config/express_code.php';

               $address = $this->mUserAddress->get_user_address_by_id($orderData['address_id']);
               $this->Smarty->assign('address', $address);

                //$orderData['address'] = $address['city'].$address['address'];
                $orderData['express_com1'] = $expressCode[$orderData['express_com']];
                $orderData['statusX'] = $config->orderStatus[$orderData['status']];
                foreach ($orderProductsList as &$pds) {
                    $d = $this->Product->getProductInfoWithSpec($pds['product_id'], $pds['product_price_hash_id']);
                    $pds['spec1'] = $d['det_name1'];
                    $pds['spec2'] = $d['det_name2'];
                }
                $this->Smarty->assign('orderdetail', $orderData);
                $this->Smarty->assign('productlist', $orderProductsList);
                $this->Smarty->assign('title', '订单详情');
            }
        }
        $isSend = $this->mQuestion->isSendCoupon($uinfo['uid'],$Query->order_id);
        $isComment = 0;
        if($isSend){
             $isComment = 1;
        }
        
        $signPackage = $this->JsSdk->GetSignPackage();
        $this->assign('signPackage', $signPackage);
        $this->assign('isComment', $isComment);
        $this->show();
    }

    /**
     * 订单取消
     * @todo a lot
     */
    public function cancelOrder() {
        $orderId = $_POST['orderId'];
        
        $this->loadModel('mOrder');
        $ordersArr = $this->mOrder->GetSimpleOrderInfo($orderId);
        $order = $ordersArr[0];
        if($order['status'] != 'unpay'){
          $cancelSql = "UPDATE " . TABLE_ORDERS . " SET `status` = 'canceled' WHERE `order_id` = $orderId;";
        }else{
          $cancelSql = "UPDATE " . TABLE_ORDERS . " SET `status` = 'closed' WHERE `order_id` = $orderId;";
        }
        $rst = $this->Db->query($cancelSql);
        # echo $cancelSql;
        echo $rst > 0 ? "1" : "0";
    }

    /**
     * ajax确认收货 | 过期自动确认订单
     * @param type $Q
     * @return boolean
     */
    public function confirmExpress($Q) {
        // orders >> received
        $this->loadModel('mOrder');
        $this->loadModel('WechatSdk');
        $orderIds = array();
        $recycle = isset($Q->rec);
        if ($recycle) {
            $expDay = $this->getSetting('order_confirm_day');
            $expDate = date('Y-m-d', strtotime('-' . $expDay . ' DAY'));
            $idStr = $this->Dao->select("GROUP_CONCAT(order_id)")->from(TABLE_ORDERS)->where("`send_time` <= '$expDate' AND `status` = 'delivering'")->getOne();
            if ($orderIds == '') {
                return false;
            } else {
                $orderIds = explode(',', $idStr);
            }
        } else {
            $orderIds[] = intval($this->pPost('orderId'));
        }
        foreach ($orderIds as $orderId) {
            if ($orderId > 0) {
                $updateSql = "UPDATE `orders` SET status = 'received',`receive_time` = NOW() WHERE `order_id` = $orderId;";
                // 推广结算
                $orderData = $this->mOrder->GetOrderDetail($orderId);
                $companyCom = $orderData['company_com'];
                if ($companyCom != '0' && $companyCom > 0) {
                    // 代理商结算
                    $clientId = $orderData['client_id'];
                    $orderCount = $orderData['product_count'];
                    // todo model
                    foreach ($orderData['products'] as $productId => $count) {
                        $_rst = $this->Db->query("UPDATE `" . COMPANY_SPREAD . "` SET `turned` = `turned` + 1 WHERE `com_id` = '$companyCom' AND `product_id` = $productId;");
                        if (!$_rst) {
                            $this->Db->query("INSERT INTO `" . COMPANY_SPREAD . "` (`product_id`,`com_id`,`turned`) VALUES ($productId,'$companyCom',1);");
                        }
                    }
                    $companyInfo = $this->Dao->select()->from('companys')->where("id=$companyCom")->getOneRow();
                    // 代理回报比例
                    $percent = floatval($companyInfo['return_percent']);
                    // 代理Openid
                    $openid = $companyInfo['openid'];
                    // 代理UID
                    $comUid = $companyInfo['uid'];
                    // 代理所获得收益
                    $comAmount = floatval($orderData['order_amount'] * $percent);
                    // 查询二级分销
                    // 上级代理ID
                    $comcom = $this->Dao->select('client_comid')->from('clients')->where("client_id=$comUid")->getOne();
                    if ($comcom !== false) {
                        $comcomIncome = $comAmount * floatval($this->settings['com_sale_pcent']);
                        $comAmount = $comAmount - $comcomIncome;
                        // 二级回报
                        $this->Db->query("INSERT INTO `company_income_record` (`amount`,`date`,`client_id`,`order_id`,`com_id`,`pcount`) VALUE ($comcomIncome, NOW(), $clientId, $orderId, '$comcom',$orderCount);");
                    }
                    // 第一级回报
                    $this->Db->query("INSERT INTO `company_income_record` (`amount`,`date`,`client_id`,`order_id`,`com_id`,`pcount`) VALUE ($comAmount, NOW(), $clientId, $orderId, '$companyCom',$orderCount);");
                    Messager::sendText(WechatSdk::getServiceAccessToken(), $openid, date('Y-m-d') . " 您名下的会员总额为" . $orderData['order_amount'] . "的订单已完成，您获得 $comAmount 元收益！");
                }
                $ret = $this->Db->query($updateSql);
                if ($recycle) {
                    return $ret;
                } else {
                    echo $ret;
                }
            } else {
                if ($recycle) {
                    return false;
                } else {
                    echo 0;
                }
            }
        }
    }

    /**
     * 订单发货
     */
    public function ExpressReady() {
        $this->Smarty->caching = false;
        $this->loadModel('mOrder');
        $this->loadModel('WechatSdk');

        $orderId = intval($_POST['orderId']);
        $expressCode = $_POST['ExpressCode'];
        $expressCompany = $_POST['expressCompany'];
        $expressStaff = $this->post('expressStaff');
        $tmpId = 'Mb_Sy1m-1onfxMsI9FNGdBgKtnrAHE8D1P5p8DMdJMs';
        $notifyOpenid = array();
        if (!empty($expressStaff)) {
            $notifyOpenid[] = $expressStaff;
        }

        $expressList = include dirname(__FILE__) . '/../config/express_code.php';

        if ($this->mOrder->despacthGood($orderId, $expressCode, $expressCompany)) {
            global $config;
            $orderData = $this->Db->getOneRow("SELECT `oa`.`order_id`,`oa`.`tel_number`,`od`.wepay_openid,`od`.client_id,`od`.serial_number,`od`.order_time,`oa`.user_name,`od`.order_amount FROM `orders` `od` LEFT JOIN `orders_address` `oa` ON `oa`.order_id = `od`.order_id WHERE `oa`.order_id = $orderId;");
            $notifyOpenid[] = $orderData["wepay_openid"];
            // wechat notify
            foreach ($notifyOpenid as $openid) {
                Messager::sendTemplateMessage($tmpId, $openid, array(
                    'first' => '您有一笔订单已发货',
                    'keyword1' => "#" . $orderData['serial_number'],
                    'keyword2' => $expressList[$expressCompany],
                    'keyword3' => $expressCode,
                    'remark' => '点击详情 随时查看订单状态'), $config->domain . "?/Order/expressDetail/order_id=$orderData[order_id]");
            }
            // wechat notify test
            // assign
            echo 1;
        } else {
            echo 0;
        }
    }

    /*
     * @HttpPost only
     * 获取快递跟踪情况
     * @return <html>
     */

    public function ajaxGetExpressDetails() {
        $typeCom = $_POST["com"]; //快递公司
        $typeNu = $_POST["nu"];  //快递单号
        $url = "http://api.ickd.cn/?id=105049&secret=c246f9fa42e4b2c1783ef50699aa2c4d&com=$typeCom&nu=$typeNu&type=html&encode=utf8";
        //优先使用curl模式发送数据
        $res = Curl::get($url);
        echo $res;
    }

    /**
     * ajax 订单退款处理
     */
    public function orderRefund() {
    
        $this->loadModel('mOrder');
        $this->loadModel('User');
        $orderId = intval($this->pPost('id'));
         // 退款金额
        $amount = floatval($this->pPost('amount'));
        
        $orderInfo = $this->mOrder->GetOrderDetail($orderId);
         //error_log("====================pay_type===============".$orderInfo['pay_type']);
        $uinfo = $this->User->getUserInfo($orderInfo['wepay_openid']);

        if($orderInfo['pay_type'] == '1'){
          //余额退款
           $this->User->mantUserBalance($amount, $uinfo['uid'], $type = User::MANT_BALANCE_ADD);
           $this->mOrder->updateOrderStatus($orderId,"refunded",false);  
        }else if($orderInfo['pay_type'] == '2'){
        
           $balance = $orderInfo['balance_amount'];
           $online = $orderInfo['online_amount'];
          // error_log("================pay_time 2 ===================balance".$balance."===========online==========".$online);
           $this->User->mantUserBalance($balance, $uinfo['uid'], $type = User::MANT_BALANCE_ADD);
           $ret = $this->mOrder->orderRefund($orderId, $online);
           if($ret == 'SUCCESS') {
               $this->mOrder->updateOrderStatus($orderId,"refunded",false);  
             
           }

                
        }else{
       //微信退款
        // 退款结果
        $ret = $this->mOrder->orderRefund($orderId, $amount);
        // 可退款金额
        $rAmount = $this->mOrder->getUnRefunded($orderId);
        // 已退款金额
        $rAmounted = $this->mOrder->getRefunded($orderId);
   
             if($ret == 'SUCCESS') {
                // 申请已提交 进一步处理订单
                if ($rAmount == $amount || $rAmount < 0.01) {
                    // 已经全部退款
                    $this->mOrder->updateOrderStatus($orderId, 'refunded', $rAmounted + $rAmount);
    
                    
                } else {
                    // 部分退款
                    $this->mOrder->updateOrderStatus($orderId, 'canceled', $rAmounted + $amount);
                }
                echo 1;
            } else {
                echo 0;
            }
       }     
      
    }

    /**
     * 检查限购
     * @param type $key
     * @return boolean
     */
    private function checkPromLimit($key) {
        if ($key == '') {
            return false;
        } else {
            $matchs = array();
            preg_match("/p(\d+)m(\d+)/is", $key, $matchs);
            // product id
            $pid = intval($matchs[1]);
            $uid = $this->getUid();
            $limitDay = $this->Dao->select('product_prom_limitdays')->from(TABLE_PRODUCTS)->where("product_id = $pid")->getOne();
            $orderS = $this->Db->query("select order_time as `date` from orders_detail `dt`
left join orders `od` on `od`.order_id = `dt`.order_id
where `dt`.product_id = $pid and `od`.client_id = $uid
and 
(`status` = 'payed' or `status` = 'delivering' or `status` = 'received')");
            foreach ($orderS as $od) {
                if ($od['date'] > $limitDay) {
                    return false;
                }
            }
            return true;
        }
    }

    /**
     * 代付
     * @param type $Q
     */
    public function reqPay($Q) {
        if (isset($Q->id) && $Q->id > 0) {
            $orderId = intval($Q->id);

            $this->cacheId = $orderId;

            if (!$this->isCached()) {

                $this->loadModel('User');
                $this->loadModel('mOrder');
                $this->loadModel('JsSdk');

                $orderInfo = $this->mOrder->getOrderInfo($orderId);

                $orderDetail = $this->mOrder->GetOrderDetailList($orderId);

                $userInfo = $this->User->getUserInfoRaw($orderInfo['client_id']);

                $reqEd = $this->mOrder->getOrderReqAmount($orderId);

                $reqCount = $this->mOrder->getOrderReqCount($orderId);

                // 参与朋友
                $reqList = $this->mOrder->getOrderReqList($orderId);

                $signPackage = $this->JsSdk->GetSignPackage();

                $this->assign('signPackage', $signPackage);
                $this->assign('userInfo', $userInfo);
                $this->assign('orderInfo', $orderInfo);
                $this->assign('orderDetail', $orderDetail);
                $this->assign('reqed', $reqEd);
                $this->assign('reqcount', $reqCount);
                $this->assign('reqlist', $reqList);
                $this->assign('isfinish', $reqEd == $orderInfo['order_amount']);
            }

            $this->show();
        }
    }

    /**
     * ajax检查购物车
     */
    public function checkCart() {
        if (empty($_POST['data'])) {
            $this->echoJson(array());
        } else {
            $this->loadModel('Product');
            $this->Smarty->caching = false;
            $data = json_decode($_POST['data'], true);
            $pdList = array();
            $matchs = array();
            foreach ($data as $key => $count) {
                preg_match("/p(\d+)m(\d+)/is", $key, $matchs);
                $pid = intval($matchs[1]);
                if (count($this->Product->checkExt($pid)) === 0) {
                    $pdList[] = $key;
                }
            }
            $this->echoJson($pdList);
        }
    }

    /**
     * 下单成功页面
     * 提示分享，返回首页，返回个人中心选项
     */
    public function order_success($Query) {
        $orderAddress = $this->Db->getOneRow("SELECT * FROM `orders_address` WHERE `order_id` = $Query->orderid;");
        $this->assign('orderAddress', $orderAddress);
        $this->assign('title', '下单成功');
        $this->show();
    }

    /**
     * 订单评价
     * @param type $Query
     */
    public function commentOrder($Query) {
        $orderId = intval($Query->order_id);
        if ($orderId > 0) {
            $this->Load->model('mOrder');
            $orderData = $this->mOrder->GetOrderDetail($orderId);
            $this->assign('order', $orderData);
            $this->assign('title', '订单评价');
            $this->show();
        }
    }

    /**
     * 订单评价
     */
    public function addComment() {
        $content = intval($this->pPost('commentText'));
        $stars = intval($this->pPost('stars'));
        $orderId = intval($this->pPost('orderId'));
        $openId = $this->getOpenId();
        if ($orderId > 0 && !empty($openId)) {
            $this->loadModel('mOrder');
            if ($this->mOrder->checkOrderBelong($openId, $orderId)) {
                // 检查订单归属
                if ($this->mOrder->addComment($openId, $orderId, $content, $stars)) {
                    $this->echoMsg(0);
                } else {
                    $this->echoMsg(-1);
                }
            } else {
                $this->echoMsg(-1);
            }
        } else {
            $this->echoMsg(-1);
        }
    }
    
    /**
    * 未支付订单 状态重置为无效 时间为60分钟
    */
    public function resetOrderByTime(){
      $this->loadModel('mOrder');
        $time =date('Y-m-d H:i:s',time());
        $status = 'unpay';
        $where = " WHERE status = '".$status."'";
        $orderList = $this->mOrder->queryOrderList($where);      
        foreach ($orderList as $index => $order) {
           
            $orderTime =  $orderList[$index]['order_time'];
            $min=floor((strtotime($time)-strtotime($orderTime))%86400/60);
            $date=floor((strtotime($time)-strtotime($orderTime))/86400);
            
            if($min >=60 || $date >=1){
                $id = $orderList[$index]['order_id'];
                $this->mOrder->updateOrderStatus($orderList[$index]['order_id'],"closed",false);
            } 
        }
    }
    
   /**
   * 已发货订单 2个小时未确认收货自动默认为 收货
   */ 
   public function resetRecevedByTime(){
   
        $this->loadModel('mOrder');
        $time =date('Y-m-d H:i:s',time());
        $status = 'delivering';
        $where = " WHERE status = '".$status."'";
        $orderList = $this->mOrder->queryOrderList($where);      
        
        foreach ($orderList as $index => $order) {
           
            $orderTime =  $orderList[$index]['send_time'];
            $min=floor((strtotime($time)-strtotime($orderTime))%86400/60);
            $date=floor((strtotime($time)-strtotime($orderTime))/86400);
            
            if($min >=120 || $date >=1){
                $id = $orderList[$index]['order_id'];
                $data = array(
                  'status' => 'received',
                   'receive_time' => time()
                 );
                $this->mOrder->updateOrder($orderList[$index]['order_id'],$data);
                
            } 
        }
   }

}
