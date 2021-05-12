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
class mCashPay extends Model {


    public function createOrder($openid, $amount,$discountAmount) {


        $this->loadModel('User');

        $uid = $this->User->getUidByOpenId($openid);


        $orderStatus = 'unpay';
        

        $serial_number = "";

        $orderId = $this->Dao->insert('cash_pay', '`status`,`serial_number`,`uid`,`order_time`,`amount`,`discount_amount`')
                        ->values(array($orderStatus,$serial_number,$uid, time(), $amount, $discountAmount))->exec();
		
		
		$serial_number = $this->generateOrderNum($orderId);
		$serArray = array('serial_number'=>$serial_number);
		$this->updateOrder($orderId,$serArray);

        return $orderId;
    }

	public function generateOrderNum($orderId){
	
	   $len = strlen($orderId);
       $num = "000000";
       $orderNum =date("Ymdhis").substr($num,$len).$orderId;
       return $orderNum ;
	
	}


    
    public function updateOrder($orderId,$data = array()){
    
        return $this->Dao->update('cash_pay')->set($data)->where("id =".$orderId)->exec(); 
    }
    
    
    public function checkCode($code) {
    
            $c = $this->Dao->select('')->count('*')->from('cash_pay')->where("code = '$code'")->getOne(false);
            return $c > 0;
     
    }
    
    public function getCode(){
      $code = rand(100000,999999);
      $isExt = $this->checkCode($code);
      if($isExt){
      	 $code = rand(100000,999999);
      	 $this->getCode($code);
      }else{
       return $code;
      }
    }
    
    
    public function getCrashPayInfo($orderId){
        $SQL = sprintf("SELECT * FROM cash_pay where id = $orderId");
        return  $this->Db->getOneRow($SQL,false);
        
    }
    
   public function paySuccessNotify($id,$openid){
    
          $this->loadModel('WechatSdk');
          $pay = $this->getCrashPayInfo($id);
          global $config;
          if ($config->messageTpl['cash_work_notify'] != '') {

           $code = Messager::sendTemplateMessage($config->messageTpl['cash_work_notify'], $openid, array(
                        'first' => '你有一个客户选择微信现金支付' ,
                        'keyword1' => '在线支付'.$pay['discount_amount'],
                        'keyword2' => '总金额'.$pay['amount'],
                        'keyword3' => $pay['discount_amount'],
                        'remark' => '你可以点击查看支付详情'
                            ), $this->getBaseURI() . "?/CashPay/detail/id=$id");
           
                return $code;
          }
    }
 
 public function payUserNotify($id,$openid){
          $this->loadModel('WechatSdk');
          $pay = $this->getCrashPayInfo($id);
 		  $payId = $pay['id'];
            global $config;

   return Messager::sendTemplateMessage($config->messageTpl['cash_pay_notify'], $openid, array(
                        'first' => '感谢您选择CheersLife在线支付',
                        'orderMoneySum' => $pay['discount_amount'],
                        'orderProductName' => '点沁',
                        'Remark' => '点击详情 随时查看支付详情'
                            ), $this->getBaseURI() . "?/CashPay/pay_success/id=$payId");
 }

}
