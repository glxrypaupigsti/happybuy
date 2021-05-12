<?php



/**
 * 购物车
 */
class CashPay extends Controller {

    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
    }

 public function welcome_view(){
 
     $this->loadModel('User');
    $openid = $this->getOpenId();
   if(!Controller::inWechat() && !$this->debug){

            $this->show('./index/error.tpl');
            die(0);
     }
    $this->User->wechatAutoReg($openid);
   $this->show('./pay/settlement.tpl');
 }

  public function pay_view(){

    $this->loadModel('User');
    $openid = $this->getOpenId();
   if(!Controller::inWechat() && !$this->debug){

            $this->show('./index/error.tpl');
            die(0);
     }
    $this->User->wechatAutoReg($openid);

     $this->loadModel('JsSdk');
     $this->loadModel('User');
         
     $openid = $this->getOpenId();
     $uinfo = $this->User->getUserInfo($openid);
     $signPackage = $this->JsSdk->GetSignPackage();
     $this->Smarty->assign('signPackage', $signPackage);
     $discountMode = 0.9;
     $this->assign('discountMode',$discountMode);
     $this->show('./pay/pay_price.tpl');

  }
  
  public function deal_view($data){
  
      $id = $data->id;
      $this->assign('id',$id);
      $this->show('./pay/pay_deal.tpl');
  
  }
  
  public function checkPayOrder(){
     $this->loadModel('mCashPay');
     $id = $_POST['id'];
     $orderInfo = $this->mCashPay->getCrashPayInfo($id);
     if($orderInfo['status'] == 'payed'){
         $this->echoMsg(1,'处理成功');
     }else{
         $this->echoMsg(-1,'处理失败');
     }
     
  }
  
  public function ajaxCreatePay(){
         $this->loadModel('User');
    
    $this->loadModel('mCashPay');
    $amount = $_POST['amount'];
    $discountMode = 0.9;
            
    $openid = $this->getOpenId();
    $uinfo = $this->User->getUserInfo($openid);
    $discountAmount = $amount*$discountMode;
    $openid = $this->getOpenId();
    $id = $this->mCashPay->createOrder($openid,$amount,$discountAmount);
    $this->echoMsg(1,$id);
 }

 public function ajaxPay(){
 
      global $config;
      $this->loadModel('mCashPay');
      $orderId = $_POST['orderId'];
      $orderInfo = $this->mCashPay->getCrashPayInfo($orderId);
   error_log("orderInfo===".json_encode($orderInfo));
    if(!$orderInfo){
      
      $this->echoMsg(-1,'订单不存在');
      die(0);
    }
 
   if($orderInfo['status'] == 'payed'){
    $this->echoMsg(-1,'订单已支付');
    die(0);
   }

     $amount = $orderInfo['discount_amount']*100;
     $openid = $this->getOpenId();
  
     $nonceStr = $this->Util->createNoncestr();

     $timeStamp = strval(time());

        $pack = array(
            'appid' => APPID,
            'body' => $config->shopName,
            'timeStamp' => $timeStamp,
            'mch_id' => PARTNER,
            'nonce_str' => $nonceStr,
            'notify_url' => $config->order_wxpay_notify,
            'out_trade_no' => 'dq'. $orderInfo['serial_number'],
            'spbill_create_ip' => '',
            'total_fee' => $amount,
            'trade_type' => 'JSAPI',
            'openid' => $openid
        );

        $pack['sign'] = $this->Util->paySign($pack);

        $xml = $this->Util->toXML($pack);
        error_log("========ret=========".$xml);
        $ret = Curl::post('https://api.mch.weixin.qq.com/pay/unifiedorder', $xml);

        error_log("========ret=========".$ret);

        $postObj = json_decode(json_encode(simplexml_load_string($ret, 'SimpleXMLElement', LIBXML_NOCDATA)));

        error_log("================postObj==========".$postObj->prepay_id);
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
        $this->echoMsg(2,$packJs);
 }
 
 public function pay_success($data){
 

    $this->loadModel('User');
    $openid = $this->getOpenId();
   if(!Controller::inWechat() && !$this->debug){

            $this->show('./index/error.tpl');
            die(0);
     }
      $this->User->wechatAutoReg($openid);
 
      $this->loadModel('mCashPay');
      $id = $data->id;
      $pay = $this->mCashPay->getCrashPayInfo($id);
      $this->assign('code',$pay['code']);
      $this->assign('id',$pay['id']);
      $this->assign('amount',$pay['discount_amount']);
      $this->show('./pay/pay_success.tpl');
 }
 
  public function genOrderTrackQRCode($Q)
  {
        include_once(dirname(__FILE__) . "/../lib/phpqrcode/qrlib.php");
        global $config;
        $track_url = $config->domain . '?/CrashPay/detail/id='.$Q->id;
        QRcode::png($track_url, null, QR_ECLEVEL_L, 5);//($data, $filename, $ecc, $size, 2);
   }
   
   public function detail($data){
   
       $this->loadModel('mCashPay');
       $this->loadModel('User');
       $id = $data->id;
       $pay = $this->mCashPay->getCrashPayInfo($id);
       $uinfo = $this->User->getUserInfo($pay['uid']);
       $pay['order_time'] = date("Y-m-d h:i:s", $pay['order_time']);
       $this->assign('pay',$pay);
       $this->assign('uinfo',$uinfo);
       $this->show('./pay/pay_detail.tpl');
   }
    
    public function send_sum_notify()
    {
        global $config;
        $this->loadModel('Dao');
        $this->loadModel('WechatSdk');
        error_log('send sum notify');
        
        // HARDCODE for now
        $openids = array(
                         'oalpuuDbXtJL4oIBzDBNW8n9ZdoY', // 点沁收银
                         'oalpuuArTvfjghRVOrkdCyDmtQ68', // Jun
                         'oalpuuFNxIZEs-OOJW3ud1gMpSeM', // Victor
                         'oalpuuAzN2ODy7vmz7L0qrvXY3Yk', // Draco
                         );
        
        $time = strtotime(date('Y-m-d', time()));
        $today_amount = $this->Dao->select('')->sum('discount_amount')->from('crash_pay')->where('amount > 1.00 AND status="payed" AND order_time > '.$time)->getOne();
        $today_transactions = $this->Dao->select('')->count()->from('crash_pay')->where('amount > 1.00 AND status="payed" AND order_time > '.$time)->getOne();
        $all_amount = $this->Dao->select('')->sum('discount_amount')->from('crash_pay')->where('amount > 1.00 AND status="payed" ')->getOne();
        $all_transactions = $this->Dao->select('')->count()->from('crash_pay')->where('amount > 1.00 AND status="payed" ')->getOne();

        //error_log('today:'.$today_amount.':'.$today_transactions.' all:$'.$all_amount.':'.$all_transactions);
        $msg = array(
                     'first' => "当日交易流水汇总",
                     'keyword1' => date('Y-m-d', time()),
                     'keyword2' => $today_amount,
                     'keyword3' => $today_transactions,
                     'keyword4' => $all_amount,
                     'keyword5' => $all_transactions,
                     'remark' => '',
                     );
        foreach ($openids AS $val) {
            Messager::sendTemplateMessage($config->messageTpl['settlement_notify'], $val, $msg, $this->getBaseURI() . "?/CashPay/list_days");
            
        }
    }

}
