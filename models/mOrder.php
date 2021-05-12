<?php

/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class mOrder extends Model {

    // 商品售价数组
    private $productSalePrices;
    // 订单商品数量
    private $order_product_count = 0;
    // 发货通知接口
    private $deliver_notify_url = "https://api.weixin.qq.com/pay/delivernotify?access_token=";
    private $openid = false;
    private $uid;




 /**
     * Create New Order <wechat payment success>
     * @param string $wepaySerial
     * @param int $orderList
     * @return <boolean>
     * @todo 事务处理
     * // openid , 产品 list ,地址, 付款类型,发票抬头，发票内容 ，总额，需支付金额，在线支付金额，余额支付，运费
     */
    public function createOrder($openid,  $orderListUnpack = array(), $addrData, $pay_type, $reciHead = '', $reciCont = '',$amount,$pay_amount,$online_amount,$balance_mount,$yun,$coupons,$isbalance,$time) {

        global $config;

        $this->loadModel('User');

        // order infos
        $this->openid = $openid;
        $this->uid = $this->User->getUidByOpenId($this->openid);

        // 打包订单列表
        $orderList = $this->orderListRepack($orderListUnpack);


        $orderStatus = 'unpay';
        

        $serial_number = "";

        $orderId = $this->Dao->insert(TABLE_ORDERS, '`status`,`serial_number`,`client_id`,`product_count`,`order_amount`,`order_yunfei`,`order_time`,`wepay_openid`,`reci_head`,`reci_cont`,`exptime`,`pay_amount`,`online_amount`,`balance_amount`,`pay_type`,`user_coupon`,`address_id`,`isbalance`')
                        ->values(array($orderStatus,$serial_number,$this->uid, $this->order_product_count, $amount, $yun, 'NOW()', $openid, $reciHead, $reciCont, $time,$pay_amount,$online_amount,$balance_mount,$pay_type,$coupons,$addrData,$isbalance))->exec();
		
		
		$serial_number = $this->generateOrderNum($orderId);
		$serArray = array('serial_number'=>$serial_number);
		$this->updateOrder($orderId,$serArray);
		
        // orders_details
        $SQL_orderDetails = $this->genOrderDetailSQL($orderId, $orderList);
        // finalquery
        $this->Db->query($SQL_orderDetails);

        return $orderId;
    }

	public function generateOrderNum($orderId){
	
	   $len = strlen($orderId);
       $num = "000000";
       $orderNum =date("Ymdhis").substr($num,$len).$orderId;
       return $orderNum ;
	
	}

    
    public function updateOrderInfo($orderId,$time,$addrData,$balancePay,$reciHead,$reciTex,$coupon){
    
       
        return $this->Dao->update('orders')->set(array(
                'exptime' => $time,
                'pay_type' => $balancePay,
                'reci_head' => $reciHead,
                'reci_cont' => $reciTex,
                'user_coupon' => $coupon,
                'address_id' => $addrData
        ))->where("order_id=" . $orderId)->exec(); 
        
    }
    
    public function updateOrder($orderId,$data = array()){
    
        return $this->Dao->update('orders')->set($data)->where("order_id =".$orderId)->exec(); 
    }
    
    
    public function updateOrderExpTimeBySerialNo($serial_no,$exp_time){
    	return $this->Dao->update('orders')->set(array(
    			'exptime' => $exp_time
    	))->where("serial_number=" . $serial_no)->exec();
    }
    
    public function updateOrderStatusBySerialNo($serial_no,$status){
    	error_log('serial_no===>'.$serial_no.",status===>".$status);
    	return $this->Dao->update('orders')->set(array(
    			'status' => $status
    	))->where("serial_number=" . $serial_no)->exec();
    }
    

 
 


    /**
     * 
     * @param type $orderList
     * @return type
     */
    public function orderListRepack($orderList) {
        $matchs = array();
        $ret = array();
        foreach ($orderList as $key => $count) {
            preg_match("/p(\d+)m(\d+)/is", $key, $matchs);
            $ret[] = array('pid' => intval($matchs[1]), 'spid' => intval($matchs[2]), 'count' => intval($count));
        }
        return $ret;
    }

    /**
     * 计算订单总金额
     * @param type $orderList
     * @return <Int> orderAmount
     */
    public function sumOrderAmount($orderList) {
        $return = 0;
        $this->loadModel('User');
        foreach ($orderList as $ord) {
            $pid = $ord['pid'];
            // HACK:check current weekday to find discount should be applied
            /*
            $weekday = date('N', strtotime($_COOKIE['deliver_date']));
            if (4 == $weekday) {
                // Thursday is "HALF-day" sale
                $discount = 1;
            } else {
                $discount = 1;
            }
            */
            $discount =0.9; //$this->User->getDiscount($this->uid);
            $pinfo = $this->Dao->select()->from(TABLE_PRODUCTS)->where("product_id = $pid")->getOneRow();
            if ($pinfo['product_prom'] == 1 && time() < strtotime($pinfo['product_prom_limitdate'])) {
                $discount = $pinfo['product_prom_discount'] / 100;
            }
            if ($ord['spid'] > 0) {
                $salePrice = $this->Db->getOne("SELECT sale_price FROM `product_spec` WHERE `product_id` = $pid AND `id` = $ord[spid];");
            } else {
                $salePrice = $this->Db->getOne("SELECT sale_prices FROM `product_onsale` WHERE `product_id` = $pid;");
            }
            if ($salePrice > 0.01) {
                $this->productSalePrices[$pid] = $salePrice * $discount;
            } else {
                $this->productSalePrices[$pid] = $salePrice;
            }
            $this->order_product_count += $ord['count'];
            $return += $this->productSalePrices[$pid] * $ord['count'];
        }
        return $return;
    }

    /**
     * 订单积分结算
     * @param type $orderId
     * @return boolean
     */
    public function creditFinalEstimate($orderId) {
        $this->loadModel('UserCredit');
        // uid
        $uid = $this->Dao->select('client_id')->from(TABLE_ORDERS)->where("order_id = $orderId")->getOne();
        // amount
        $amount = $this->Dao->select('order_amount')->from(TABLE_ORDERS)->where("order_id = $orderId")->getOne();
        // 获取等级信息
        $lev = $this->UserLevel->getLevByUid($uid);
        // 积分赠送
        $creditTotal = $amount * $lev['level_credit_feed'] / 100;
        if ($creditTotal > 0) {
            $ret = $this->UserCredit->add($uid, $creditTotal);
            $this->UserLevel->checkUpdate($orderId);
            if ($ret) {
                $this->UserCredit->record($uid, $creditTotal, 0, $orderId);
            }
            return $ret;
        }
        return false;
    }

    /**
     * 
     * @param type $orderList
     * @return SQLstatment <string>
     */
    private function genOrderDetailSQL($orderId, $orderList) {
        // original sql statment
        $SQL = sprintf("INSERT INTO orders_detail 
            (`order_id`,`product_id`,`product_count`,`product_discount_price`,`is_returned`,`product_price_hash_id`)
            VALUES ");
        $_tmp = array();
        foreach ($orderList as $ord) {
            // pack params
            array_push($_tmp, sprintf("(%s, %s, %s, %s, 0, %s)", $orderId, $ord['pid'], $ord['count'], $this->productSalePrices[$ord['pid']], $ord['spid']));
        }
        return $SQL . implode(',', $_tmp) . ';';
    }

    /**
     * 写入订单地址
     * @todo 自动归集
     * @param type $orderid
     * @param type $addrData
     * @return string $hash
     */
    public function writeAddressData($orderid, $addrData) {
        $client_id = intval($this->pCookie('uid'));
        $hash = hash('md4', $this->toJson($addrData));
        $SQL = sprintf("INSERT IGNORE INTO `orders_address` (`order_id`,`client_id`,`user_name`,`tel_number`,`postal_code`,`address`,`province`,`city`,`hash`) "
                . "VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');", $orderid, $client_id, $addrData['userName'], $addrData['telNumber'], $addrData['addressPostalCode'], $addrData['Address'], $addrData['proviceFirstStageName'], $addrData['addressCitySecondStageName'], $hash);
        return $this->Db->query($SQL) ? $hash : false;
    }

    /**
     * despacthGood
     * 发货 - 通知
     */
    public function despacthGood($orderId, $expressCode, $expressCompany) {
        $SQL = sprintf("UPDATE `orders` SET `send_time` = NOW(),`status` = 'delivering',`express_code` = '%s',`express_com`='%s' WHERE order_id = $orderId;", $expressCode, $expressCompany);
        $AffectRow = $this->Db->query($SQL);
        if ($AffectRow != false) {
            $this->wechat_deliverNotify($orderId);
            return true;
        }
        return false;
    }

    /**
     * 微信后台发货通知接口
     */
    public function wechat_deliverNotify($orderId) {

        include_once(dirname(__FILE__) . "/../lib/Tools.php");
        include_once(dirname(__FILE__) . "/../lib/wepaySdk/WxPayHelper.php");
        include_once(dirname(__FILE__) . "/WechatSdk.php");

        $SignTool = new SignTool();
        $orderId = (string) $orderId;

        $dtime = (string) time();

        $data = $this->Db->query("SELECT `wepay_serial`,`wepay_openid` FROM `orders` WHERE `order_id` = $orderId;");
        $data = $data[0];

        if ($data['wepay_serial'] != '') {
            $Stoken = WechatSdk::getServiceAccessToken();

            // app_signature：appid、appkey、openid、transid、out_trade_no、deliver_timestamp、deliver_status、deliver_msg

            $SignTool->setParameter('appid', APPID);
            $SignTool->setParameter('appkey', APPSECRET);
            $SignTool->setParameter('deliver_timestamp', $dtime);
            $SignTool->setParameter('deliver_status', "1");
            $SignTool->setParameter('deliver_msg', "ok");
            $SignTool->setParameter('openid', $data['wepay_openid']);
            $SignTool->setParameter('out_trade_no', $orderId);
            $SignTool->setParameter('transid', $data['wepay_serial']);

            $WxPayHelper = new WxPayHelper();
            $app_signature = $WxPayHelper->get_biz_sign($SignTool->parameters);

            $postData = array(
                "appid" => APPID,
                "openid" => $data['wepay_openid'],
                "transid" => $data['wepay_serial'],
                "out_trade_no" => $orderId,
                "deliver_timestamp" => $dtime,
                "deliver_status" => "1",
                "deliver_msg" => "ok",
                "app_signature" => $app_signature,
                "sign_method" => SIGNTYPE
            );

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $this->deliver_notify_url . $Stoken);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            // exec
            curl_exec($curl);
            // close
            curl_close($curl);
            return true;
        } else {
            return false;
        }
    }
    
    

    /**
     * 获取单个订单的详情
     */
    public function GetSimpleOrderInfo($id) {
        if ($id > 0) {
            return $this->Dao->select()->from(TABLE_ORDERS)->alias('od')
            ->where("od.order_id = $id")->exec();
        } else {
            return false;
        }
    }
    
    /**
     * 获取单个订单的详情
     */
    public function get_order_info_by_id($id) {
    	return $this->Dao->select()->from(TABLE_ORDERS)->where("order_id = $id")->getOneRow();
    }
    
    /**
    * 判断订单是否有效
    * 
    */
    public function isValidOrder($id){
    
        if ($id > 0) {
           $order = $this->Dao->select()->from(TABLE_ORDERS)->alias('od')
            ->where("od.order_id = $id")->exec();
           if($order){
             if($order[0]['status'] == 'unpay')
                return true;
             return false;
           }else{
             return false;
           }
        }else{
         
         return false;
        }
    }
    

    /**
     * 获取订单详情
     * @param <int> $id 订单id
     */
    public function GetOrderDetail($id) {
        if ($id > 0) {
            $this->loadModel('Product');
            $orderData = $this->Db->getOneRow("SELECT * FROM `orders` WHERE `order_id` = $id");
//             $orderData['address'] = $this->Db->getOneRow("SELECT * FROM `orders_address` WHERE order_id = $id;");
            $address_id = $orderData['address_id'];
            $orderData['address'] = $this->Db->getOneRow("SELECT * FROM `user_address` WHERE id = $address_id;");
            $orderData['products'] = $this->Db->query("SELECT product_id, product_price_hash_id, product_count, product_discount_price FROM `orders_detail` where order_id = " . $orderData['order_id']);
            foreach ($orderData['products'] as &$pds) {
                $pinfo = $this->Product->getProductInfoWithSpec($pds['product_id'], $pds['product_price_hash_id']);
                $pinfo = array_merge(array('product_count' => $pds['product_count'], 'product_discount_price' => $pds['product_discount_price']), $pinfo);
                $pds = $pinfo;
            }
            return $orderData;
        } else {
            return false;
        }
    }

    /**
     * 获取订单列表
     * @param <int> $id 订单id
     */
    public function GetOrderDetailList($id) {
        if ($id > 0) {
            $orderData = $this->Dao->select()->from(TABLE_ORDERS_DETAILS)->alias('od')
                            ->leftJoin(TABLE_PRODUCTS)->alias('po')
                            ->on('po.product_id = od.product_id')
                            ->where("od.order_id = $id")->exec();
            return $orderData;
        } else {
            return false;
        }
    }
    

    /**
     * 获取订单Serial_num获取订单列表
     * @param <int> $id 订单id
     */
    public function GetOrderDetailBySerialNo($serial_no){
    	$sql = 'select pro.product_name ,det.product_count from orders_detail det
				LEFT JOIN orders ord
				on ord.order_id = det.order_id
				LEFT JOIN products_info pro
				on pro.product_id = det.product_id
    			where ord.serial_number ='.$serial_no;
    	return $this->Db->query($sql);
    }


    /**
     *  根据serial_no获取订单详情
     */
    public function  GetOrderInfoBySerialNo($serial_no){
        return $this->Dao->select()->from(TABLE_ORDERS)->where('serial_number='.$serial_no)->getOneRow();
    }
    

    /**
     * 获取订单列表，单表
     */
    public function GetOrderDetails($id) {
       return $this->Dao->select()->from(TABLE_ORDERS_DETAILS)->where('order_id='.$id)->exec(false);
    }

    /**
     * 訂單付款通知发货
     * @param type $orderId
     */
    public function OrderNotify($orderId) {
        global $config;
        $this->loadModel('Email');
        $this->loadModel('User');
        $orderData = $this->Db->getOneRow("SELECT `oa`.`order_id`,`oa`.`tel_number`,`od`.wepay_openid,`od`.client_id,`od`.serial_number,`od`.order_time,`oa`.user_name,`od`.order_amount,`oa`.address FROM `orders` `od` LEFT JOIN `orders_address` `oa` ON `oa`.order_id = `od`.order_id WHERE `oa`.order_id = $orderId;");
        $userInfo = $this->User->getUserInfo($orderData['client_id']);
        $subject = $this->settings['shopname'] . " - 订单付款通知 编号:" . $orderData['serial_number'];
        $orderInfo = array(
            'username' => $orderData['user_name'],
            'userphone' => $orderData['tel_number'],
            'time' => $orderData['order_time'],
            'amount' => $orderData['order_amount'],
            'list' => $this->Db->query("select pi.product_name as `name`,product_count as count from orders_detail od 
                left JOIN products_info pi on pi.product_id = od.product_id
                where od.order_id = $orderData[order_id];"),
            'address' => $orderData['address']
        );
        $this->Smarty->assign('toName', $userInfo->nickname);
        $this->Smarty->assign('fromName', $this->settings['shopname']);
        $this->Smarty->assign('fromAddress', $config->mail['account']);
        $this->Smarty->assign('order', $orderInfo);
        $content = $this->Smarty->fetch("email/order_notify_admin.html");
        $this->Email->send(explode(',', $this->settings['order_notify_email']), $this->settings['shopname'], $subject, $content);
    }

    /**
     * 商户订单付款通知 微信模板信息
     * @global type $config
     * @param type $orderId
     * @param type $openid
     */
    public function comNewOrderNotify($orderId) {
        global $config;
        if (isset($config->messageTpl['new_order_notify']) && $config->messageTpl['new_order_notify'] != '') {
            // 查找通知openid列表
            $openIds = explode(',', $this->getSetting('order_notify_openid'));
            if (!is_array($openIds) && count($openIds) <= 0) {
                return false;
            }

            $this->loadModel('WechatSdk');
            // 获取订单商品列表
            $orderProducts = $this->Db->query("select pi.product_name as `name`,product_count as `count` from orders_detail od 
                left JOIN products_info pi on pi.product_id = od.product_id
                where od.order_id = $orderId;");
            $orderInfos = array();
            // 获取订单信息
            $orderInfo = $this->getOrderInfo($orderId);
            foreach ($orderProducts as $oi) {
                $orderInfos[] = $oi['name'] . '(' . $oi['count'] . ')';
            }
            $products_str = '产品：\n';
            $orderDetail = $this->Db->query("SELECT `pi`.product_name,`sd`.product_count FROM `orders_detail` sd LEFT JOIN `products_info` pi on pi.product_id = sd.product_id WHERE `sd`.order_id = " . $orderId);;
            foreach ($orderDetail as $dt) {
                $products_str .= $dt['product_name'] . ' ' . $dt['product_count'] .'件\n';
            }
            $content = '新订单：'.$orderInfo['serial_number'].'\n'.$products_str . '金额：¥' . sprintf('%.2f', $orderInfo['order_amount']);
            foreach ($openIds as $openid) {
                Messager::sendNotification(WechatSdk::getServiceAccessToken(), $openid, $content, $this->getBaseURI() . "?/Order/expressDetail/order_id=$orderId");
                // 批量通知商户
                Messager::sendTemplateMessage($config->messageTpl['new_order_notify'], $openid, array(
                    'first' => '有一位顾客下单了，请尽快发货',
                    'keyword1' => $orderInfo['serial_number'],
                    'keyword2' => implode('、', $orderInfos),
                    'keyword3' => $orderInfo['product_count'] . '件',
                    'keyword4' => '¥' . sprintf('%.2f', $orderInfo['order_amount']),
                    'remark' => '点击详情 随时查看订单状态'
                        ), $this->getBaseURI() . "?/Order/expressDetail/order_id=$orderId");
            }
        }
    }

    /**
     * 用户订单付款通知 微信模板信息
     * @global type $config
     * @param type $orderId
     * @param type $openid
     */
    public function userNewOrderNotify($orderId, $openid) {
        global $config;
        if (isset($config->messageTpl['new_order_notify']) && $config->messageTpl['new_order_notify'] != '') {
            $this->loadModel('WechatSdk');
            /*
            $orderProducts = $this->Db->query("select pi.product_name as `name`,product_count as `count` from orders_detail od 
                left JOIN products_info pi on pi.product_id = od.product_id
                where od.order_id = $orderId;");
            $orderInfos = array();
            $orderInfo = $this->getOrderInfo($orderId);
            foreach ($orderProducts as $oi) {
                $orderInfos[] = $oi['name'] . '(' . $oi['count'] . ')';
            }*/
            $orderProducts = $this->Db->query("select pi.product_name as `name`,product_count as `count` from orders_detail od
                        left JOIN products_info pi on pi.product_id = od.product_id
                        where od.order_id = $orderId;");
            $orderInfos = array();
            // 获取订单信息
            $orderInfo = $this->getOrderInfo($orderId);
            foreach ($orderProducts as $oi) {
                $orderInfos[] = $oi['name'] . '(X' . $oi['count'] . ')';
            }
            error_log('send msg to user:'.$openid);
            return Messager::sendTemplateMessage($config->messageTpl['new_order_notify'], $openid, array(
                        'first' => '感谢您在' . $config->shopName . '购物',
                        'orderMoneySum' => $orderInfo['pay_amount'],
                        'orderProductName' => implode('、', $orderInfos),
                        'Remark' => '点击详情 随时查看订单状态'
                            ), $this->getBaseURI() . "?/Order/expressDetail/order_id=$orderId");
        }
    }

    /**
     * 微信支付退货处理
     * 退货前提是必须支付成功
     * @param type $orderId
     * @return boolean
     */
    public function orderRefund($orderId, $refund_fee = false) {
        global $config;
        $orderId = intval($orderId);
        $orderInfo = $this->getOrderInfo();
        if ($orderId > 0) {

            $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

            $totalFee = floatval($this->Db->getOne("SELECT `order_amount` FROM `orders` WHERE `order_id` = $orderId;")) * 100;

            $idReq = $this->Dao->select()->from(TABLE_ORDER_REQS)->where("order_id = $orderId AND `wepay_serial` <> ''")->getOne() > 0;


            if ($idReq !== false && count($idReq) > 0) {
                // 众筹退款
                foreach ($idReq as $req) {
                    // req-
                    $postData = array(
                        "appid" => APPID,
                        "mch_id" => PARTNER,
                        "transaction_id" => $req['wepay_serial'],
                        "out_trade_no" => 'req' - $req['id'],
                        "out_refund_no" => 'req' - $req['id'],
                        "total_fee" => $req['amount'],
                        "refund_fee" => $req['amount'],
                        "op_user_id" => PARTNER,
                        "nonce_str" => $this->createNoncestr()
                    );

                    $sign = $this->createSign($postData);

                    $postData["sign"] = $sign;

                    $reqPar = $this->toXML($postData);

                    $r = $this->curlPost($url, $reqPar, 50);
                }
            } else {

                if (!$refund_fee) {
                    $refund_fee = $totalFee;
                } else {
                    $refund_fee *= 100;
                }

                if ($refund_fee > $totalFee) {
                    // 支持部分退款，但是不允许大于总金额
                    return false;
                } else {

                    $postData = array(
                        "appid" => APPID,
                        "mch_id" => PARTNER,
                        "transaction_id" => $orderInfo['wepay_serial'],
                        "out_trade_no" => $config->out_trade_no_prefix . $orderId,
                        "out_refund_no" => $orderId . $refund_fee,
                        "total_fee" => $totalFee,
                        "refund_fee" => $refund_fee,
                        "op_user_id" => PARTNER,
                        "nonce_str" => $this->createNoncestr()
                    );

                    $sign = $this->createSign($postData);

                    $postData["sign"] = $sign;

                    $reqPar = $this->toXML($postData);

                    $r = $this->curlPost($url, $reqPar, 50);
                    
                    return $r;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * 获取订单信息
     * @param type $orderId
     * @return type
     */
    public function getOrderInfo($orderId) {
        return $this->Dao->select()->from(TABLE_ORDERS)->where("order_id = $orderId")->getOneRow();
    }

    /**
     * 生成随机字符串
     * @param type $length
     * @return type
     */
    protected function createNoncestr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str.= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            //$str .= $chars[ mt_rand(0, strlen($chars) - 1) ];  
        }
        return $str;
    }

    /**
     * 数组转换XML
     * @param type $arr
     * @return string
     */
    public function toXML($arr) {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml.="<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml.="<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     * 
     * @param type $postData
     * @return type
     */
    private function createReqStr($postData) {
        $reqPar = "";

        ksort($postData);

        foreach ($postData as $k => $v) {
            if ("spbill_create_ip" != $k) {
                $reqPar .= $k . "=" . urlencode($v) . "&";
            } else {
                $reqPar .= $k . "=" . str_replace(".", "%2E", $v) . "&";
            }
        }

        $reqPar = substr($reqPar, 0, strlen($reqPar) - 1);

        return $reqPar;
    }

    /**
     * 
     * @param type $postData
     * @return type
     */
    function createSign($postData) {
        ksort($postData);

        $signPars = "";

        foreach ($postData as $k => $v) {
            if ("" != $v && "sign" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }

        $signPars .= "key=" . PARTNERKEY;

        $sign = strtoupper(md5($signPars));

        return $sign;
    }

    /**
     * curl POST 
     * 
     * @param   string  url 
     * @param   array   数据 
     * @param   int     请求超时时间 
     * @param   bool    HTTPS时是否进行严格认证 
     * @return  string 
     */
    function curlPost($url, $data = array(), $timeout = 30) {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // 财付通caKey路径
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, CERT_PATH);
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, PARTNER);

        //默认格式为PEM，可以注释
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, CERT_KEY_PATH);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); //data with URLEncode  
        $ret = curl_exec($ch);
        curl_close($ch);
        error_log("result=".$ret);
        $xml =  simplexml_load_string($ret);
        error_log("xml=".$xml->return_code);
        //$retObj = json_decode(json_encode($xml),TRUE);
        return $xml->return_code;
   
    }
    
    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
    public function FromXml($xml)
    {   
        if(!$xml){
            throw new WxPayException("xml数据异常！");
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);        
        return $this->values;
    }

    /**
     * 更新订单状态
     * @param type $orderId
     * @param type $status
     * @return type
     */
    public function updateOrderStatus($orderId, $status, $refundAmount = false) {
        if ($status == 'refunded' && $refundAmount) {
            return $this->Dao->update(TABLE_ORDERS)->set(array('status' => $status, 'order_refund_amount' => $refundAmount))->where("`order_id` = $orderId")->exec();
        } else {
            return $this->Dao->update(TABLE_ORDERS)->set(array('status' => $status))->where("`order_id` = $orderId")->exec();
        }
    }

    /**
     * 获取订单收货地址信息
     * @param type $orderId
     */
    public function getOrderAddr($orderId) {
        return $this->Dao->select()->from(TABLE_ORDER_ADDRESS)->where("order_id=$orderId")->getOneRow();
    }

    /**
     * 获取订单未退款金额
     * @param type $orderId
     * @return type
     */
    public function getUnRefunded($orderId) {
        return $this->Dao->select('order_amount - order_refund_amount')->from(TABLE_ORDERS)->where("order_id = $orderId")->getOne();
    }

    /**
     * 订单退款
     * @param type $orderId
     * @return type
     */
    public function getRefunded($orderId) {
        return $this->Dao->select('order_refund_amount')->from(TABLE_ORDERS)->where("order_id = $orderId")->getOne();
    }

    /**
     * 获取订单代付总额
     * @param type $orderId
     */
    public function getOrderReqAmount($orderId) {
        $amount = 0.00;
        $ret = $this->Dao->select()->from('order_reqpay')->where("order_id = $orderId AND `wepay_serial` <> '' ")->exec();
        foreach ($ret as $r) {
            $amount += round($r['amount'], 2);
        }
        return $amount > 0 ? $amount : 0;
    }

    /**
     * 获取代付列表
     * @param type $orderId
     * @return type
     */
    public function getOrderReqList($orderId) {
        $ret = $this->Dao->select()->from('order_reqpay')->where("order_id = $orderId AND `wepay_serial` <> '' ")->exec();
        foreach ($ret as &$f) {
            $f['dt'] = $this->Util->dateTimeFormat($f['dt']);
            $f['user'] = $this->User->getUserInfoRaw($f['openid']);
        }
        return $ret;
    }

    /**
     * 获取订单代付参与数量
     * @param type $orderId
     */
    public function getOrderReqCount($orderId) {
        $ret = $this->Dao->select("COUNT(`id`)")->from('order_reqpay')->where("order_id = $orderId AND `wepay_serial` <> '' ")->getOne();
        return $ret > 0 ? $ret : 0;
    }

    /**
     * 过期订单回收
     * @param type $Uid
     * @return boolean
     */
    public function orderReclycle($Uid = false) {
        if (!$Uid) {
            return false;
        }
        $expDay = $this->getSetting('order_cancel_day');
        $expDate = date('Y-m-d', strtotime('-' . $expDay . ' DAY'));
        $orderIds = $this->Dao->select("GROUP_CONCAT(order_id)")->from(TABLE_ORDERS)->where("order_time <= '$expDate'")
                ->aw("client_id = $Uid")
                ->aw("`status` = 'unpay'")
                ->getOne();
        if ($orderIds != '') {
            // 删除订单
            $this->Dao->delete()->from(TABLE_ORDERS)->where("order_id IN ($orderIds)")->exec();
            $this->Dao->delete()->from(TABLE_ORDERS_DETAILS)->where("order_id IN ($orderIds)")->exec();
            $this->Dao->delete()->from(TABLE_ORDER_ADDRESS)->where("order_id IN ($orderIds)")->exec();
        }
    }

    /**
     * 添加订单评论
     * @param type $openid
     * @param type $orderid
     * @param type $content
     * @param type $stars
     */
    public function addComment($openid, $orderid, $content, $stars) {
        $this->loadModel('User');
        if ($this->User->checkUserExt($openid) && $orderid > 0 && is_numeric($stars)) {
            $ret = $this->Dao->insert(TABLE_ORDERS_COMMENT, 'openid,starts,content,mtime,orderid')
                            ->values(array($openid, $stars, $content, Dao::FIELD_NOW, $orderid))->exec();
            if ($ret) {
                return $this->Dao->update(TABLE_ORDERS)->set(array('is_commented' => 1))->where("order_id = $orderid")->exec();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 获取评论
     * @param type $orderid
     */
    public function getComment($orderid) {
        if ($orderid > 0) {
            return $this->Dao->select()->from(TABLE_ORDERS_COMMENT)->where("orderid=$orderid")->getOneRow();
        } else {
            return NULL;
        }
    }

    /**
     * 获取评论列表
     * @param type $pageno
     * @param type $pagesize
     */
    public function getCommentList($pageno = 0, $pagesize = 20) {
        if ($pageno >= 0 && $pagesize > 20) {
            return $this->Dao->select()->from(TABLE_ORDERS_COMMENT)->limit($pagesize * $pageno, $pagesize)->orderby('mtime')->desc()->exec();
        } else {
            return NULL;
        }
    }

    /**
     * 获取未评价订单
     * @param type $openid
     */
    public function getUnCommentList($openid) {
        $odlist = $this->Dao->select('pd.product_id,pd.product_name,ods.order_id')
                        ->from(TABLE_ORDERS_DETAILS)->alias('ods')
                        ->leftJoin(TABLE_ORDERS)->alias('od')
                        ->on("od.order_id = ods.order_id")
                        ->leftJoin(TABLE_PRODUCTS)->alias('pd')
                        ->on("pd.product_id = ods.product_id")
                        ->where("od.wepay_openid = '$openid'")
                        ->aw("od.status = 'received'")
                        ->aw('ods.is_commented = 0')->exec();
        return $odlist;
    }

    /**
     * 检查订单归属
     * @param type $openId
     * @param type $orderId
     */
    public function checkOrderBelong($openId, $orderId) {
        $ret = $this->Db->getOneRow("SELECT COUNT(*) AS count FROM `orders` WHERE `wepay_openid` = '$openId' AND `order_id` = $orderId;");
        if ($ret['count'] > 0) {
            return true;
        }
        return false;
    }

    /**
     * 减除库存
     * @param type $orderId
     */
    public function cutInstock($orderId)
    {
    
    	$this->loadModel('Stock');
        $order_info = $this->GetSimpleOrderInfo($orderId);
        $orderDetail = $this->GetOrderDetails($orderId);
        //$time = time();
        $deliver_time = $order_info[0]['exptime'];
        $check_time = strtotime(substr($deliver_time, 0, 10)); //'2015-01-01'
                                              error_log('cutInStock for date:'.$check_time);
        foreach ($orderDetail as $dt) {
          
          // $this->Db->query("UPDATE product_spec SET `instock` = instock -$dt[product_count]  where id = $dt[product_price_hash_id];");
         	$this->Stock->sold_product_on_date($dt[product_price_hash_id], $check_time, $dt[product_count]);
        }
        return true;
    }

    
    /**
    *订单查询
    */
    public function queryOrderList($query){
    
        $SQL = sprintf("SELECT * FROM `orders`%s ORDER BY `order_id` DESC;", $query);
        $orderList = $this->Db->query($SQL);
        return $orderList;
    }
    
    
    /**
     * 说明：增加该订单关联的商品优惠券集
     * 输入参数：订单标识，优惠券数组
     * 输出参数： true/false 
     */
    public function add_coupons($order_id, $coupon_ids){
        return $this->Db->update(TABLE_ORDER)->set(array(
            'use_coupon' => $coupon_ids
        ))->where('order_id',$order_id)->exec();
    }
    
    
    /**
     *说明：计算订单实际需要支付的金额
     *输入参数：订单标识
     *输出参数： float/false 
     */
    public function calc_amount($order_id,$coupon_ids){
        $this->loadModel('Coupons');
        //1、从订单表中查询实际使用的优惠券
        $order_info = $this->GetOrderDetail($order_id);
        $total_price = $order_info['order_amount'];
        //2、查询可用的订单优惠券
        
        //原始的总价
        $order_amount = $order_info['order_amount'];
        error_log('order_amount====>'.$order_amount);
        error_log('coupon_ids====>'.$coupon_ids);
        
        $amount = 0;
        if(strpos($coupon_ids,",")>0){ //如果是优惠券数组
            $coupon_id_arr = explode(',',$coupon_ids);
            $total_reduce_amount = 0;
            foreach ($coupon_id_arr as $key => $coupon_id){
                $coupon_info =  $this->Coupons->get_coupon_info($coupon_id);
                $reduce_amount = $this->cal_single_coupon_reduce_amount($order_amount,$coupon_info);
                error_log('coupon_id:【'.$coupon_id.'】reduce_amount====>'.$reduce_amount);
                $total_reduce_amount = $reduce_amount + $total_reduce_amount;
            }
            error_log('total reduce_amount====>'.$total_reduce_amount);
            error_log('after reduce order amount====>'.$amount);
            $amount = $order_amount - $total_reduce_amount;
        }else{
            $coupon_info =  $this->Coupons->get_coupon_info(intval($coupon_ids));
            error_log('coupon_info discount_type====>'.$coupon_info['discount_type']);
            error_log('coupon_info discount_value====>'.$coupon_info['discount_val']);
            $reduce_amount = $this->cal_single_coupon_reduce_amount($order_amount,$coupon_info);
            $amount =  $order_amount - $reduce_amount;
            error_log('reduce_amount====>'.$reduce_amount);
            error_log('after reduce order amount====>'.$amount);
        }
        
        return $amount;
        
    }
    
    /**
     * 修改了mOrder中计算订单优惠价格的方法，以及测试用例
     * 根据优惠券id计算减少的价格
     * @param int $order_id 订单编号
     * @param int $coupon_id  优惠券编号 
     * @param int $uid  用户编号
     * @param boolean $is_user_coupon 是否为用户优惠券，默认为false
     **/
    public function cal_reduce_amount_by_coupon_id($order_amount,$coupon_id,$uid = null,$is_user_coupon = false){
    	$this->loadModel('Coupons');
    	error_log('order_id====>'.$order_id);
//     	$order_info = $this->GetSimpleOrderInfo($order_id);
//     	error_log("order_info====>".json_encode($order_info));
//     	if(!$order_info){
//     		return -5;
//     	}
    	$coupon_info =  $this->Coupons->get_coupon_info(intval($coupon_id));
    	error_log("coupon_info====>".$coupon_info);
    	if(!$coupon_info){
    		return -2;
    	}
    	
    	$time = time();
    	if($time > $coupon_info['effective_end']){ //订单已过期
    		return -1;
    	}
    	
    	if($is_user_coupon){ //如果为用户类优惠券
    		$this->loadModel('UserCoupon');
    		$user_coupon_info = $this->UserCoupon->get_user_coupon_info($uid,$coupon_id);
    		
    		if(!$user_coupon_info){
    			return -3;
    		}
    		
    		if($user_coupon_info['is_used']==1){  //优惠券已经使用了
    			return -4;
    		}
    	}
    	
//     	$order_amount = $order_info['order_amount'];
    	
    	$amount = $this->cal_single_coupon_reduce_amount($order_amount,$coupon_info);
    	
    	error_log('cal_reduce_amount_by_coupon_id  total reduce_amount =====>'.$amount);
    	return $amount;
    	
    }
    
    /**
     * 计算单个优惠券折扣的方法 
     */
    public function cal_single_coupon_reduce_amount($price_amount,$coupon_info){
        $discount_val = $coupon_info['discount_val'];
        $discount_type = $coupon_info['discount_type'];
        error_log('discount_val========>'.$discount_val);
        error_log('discount_type========>'.$discount_type);
        if($discount_type == 1){  //折扣比例价格
            $reduce_amount = $price_amount * (100-$discount_val) * 0.01;
        }else if($discount_type == 3){  //每满减
        	$coupon_terms = $coupon_info['coupon_terms'];
        	$reduce_count = 0;  //阶梯减的份数
        	error_log('coupon_terms====>'.$coupon_terms);
        	if(!empty($coupon_terms)){
        		$coupon_terms_arr = json_decode($coupon_terms,true);
        		foreach ($coupon_terms_arr as $key => $val){
        			if($val['column'] == 'selected_mod_amount'){
        				error_log('term_value====>'.$val['value']);
        				error_log('price_amount====>'.$price_amount);
        				error_log('remainder====>'.$price_amount%($val['value']/100));
        				
        				//总数的字符串形式
        				$price_amount_str = strval($price_amount);
        				//判断是否有小数点
        				if(strpos($price_amount_str,'.')){
        					$price_amount_str_dot_arr = explode('.',$price_amount_str);
        					$dot_str = $price_amount_str_dot_arr[1];
        				}else{
        					$dot_str ="0";
        				}
        				//小数点的长度
        				$len = strlen($dot_str);
        				//阶乘的技术计算
        				$mi = 1;
        				if($len > 0){
        					for($i=0;$i<$len;$i++){
        						$mi = $mi * 10;
        					}
        				}
        				
        				//只有满减时候才计算,先取余数
        				$remainder = $price_amount % ($val['value']/100);
        				//根据余数然后加入小数点
        				$remainder = $remainder+intval($dot_str)/$mi;
        				//减少的总份数
        				$reduce_count =($price_amount-$remainder)/($val['value']/100);
//         				//只有满减时候才计算,先取余数
//         				$remainder = $price_amount%($val['value']/100);
//         				//减少的总分数
//         				$reduce_count =($price_amount-$remainder)/($val['value']/100);
        				error_log('remainder====>'.$remainder.",reduce_count===>".$reduce_count);
        				break;
        			}
        		}
        		
        		$reduce_amount = $reduce_count * ($discount_val/100);
        	}
        	
        	
        }else if($discount_type == 4){  //加X换购B，直接去折扣值的负数,都是以分计算的
        	$reduce_amount = -$discount_val/100;
        }else if($discount_type == 5){ //买M件送N件,直接计算总价
        	$reduce_amount = 0;
        }else{  //月
            //因为优惠券中的金额都是以分为单位，所以在计算时候需要除以100
            $reduce_amount = $discount_val / 100;
        }
        return $reduce_amount;
    }
    
    public function getOrderInfoBySeriNum($num){
    
         return $this->Dao->select()->from('orders')->where("serial_number='$num'")->getOne(false);
    
    }

}
