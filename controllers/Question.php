<?php



/**
 * 分享
 */
class Question extends Controller {

    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
    }


  /*
  *
  * 授权获取用户信息
  */
  public function authorize($query){
     
   
      
     $questionId = $query->questionId;
     $orderId= $query->orderId;
   
     $this->loadModel('User');  
     $redirect_uri = $this->uri;
     $isDebug = false;
     if($isDebug){
          $appid = "wx189ae9fa8816b131";  
          $secret = "36f5f430c591acbae3505fe877733283";  
     }else{
          $appid = "wx0404a4b543bf52d0";  
          $secret = "f62f597a8a8a241316a3b47ed25bdbc8";  
     }

     $code = $_GET["code"];  
     $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';  
  
     $ch = curl_init();  
     curl_setopt($ch,CURLOPT_URL,$get_token_url);  
     curl_setopt($ch,CURLOPT_HEADER,0);  
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );  
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);  
     $res = curl_exec($ch);  
     curl_close($ch);  
     $json_obj = json_decode($res,true);  
  
//根据openid和access_token查询用户信息  
     $access_token = $json_obj['access_token'];  
     $openid = $json_obj['openid'];  
     $this->User->wechatAutoReg($openid);
     $uinfo = $this->User->getUserInfo($openid);
     $backUrl="http://survey.qiezilife.com/Weixin/FrontIndex/index?orderId=$orderId&id=$questionId&uinfo=".json_encode($uinfo);
     header("Location:".$backUrl);
        
  }
  
  public function coupon(){
  
      global $config;
      $this->loadModel('Coupons'); 
      $this->loadModel('User'); 
      $this->loadModel('mQuestion'); 
      $this->loadModel('UserCoupon'); 
      $this->loadModel('WechatSdk'); 

      
      $questionId = $_POST['questionId'];
      $openId = $_POST['openid'];
      $orderId = $_POST['orderId'];
      $uinfo = $this->User->getUserInfo($openId);
      if($uinfo){
         $uid = $uinfo['uid'];
         $couponId = 22;
         $couponInfo = $this->Coupons->get_coupon_info($couponId);
         if($couponInfo){
             $couponInfo['coupon_value'] =  $couponInfo['discount_val']/100;
         }
         $isSend = $this->mQuestion->isSendCoupon($uid,$orderId);
         if(!$isSend){
             $this->mQuestion->createQuestion($uinfo['uid'],$questionId,$couponId,$orderId);
             $this->UserCoupon->insertUserCoupon($couponId, $uid, true,'question');  
             $content = "偷偷送你（".$couponInfo['coupon_value']."元）~不要告诉别人呦\n适用范围：CheersLife全部商品\n使用规则：在下单时选中优惠券抵用即可\n快来享用健康美食吧~";
             $url = $config->domain.'?/Coupon/user_coupon/';   
             Messager::sendNotification(WechatSdk::getServiceAccessToken(), $openId, $content, $url);
         }
        echo json_encode($couponInfo); 
      }
         
  }

  public function  sendQuestion(){
  
      $this->loadModel('Distribute');
      $this->loadModel('mOrder');
      $this->loadModel('mQuestion');
      $this->loadModel('User'); 
      $disList = $this->Distribute->getReachedList();
      
      $sendMin = 1;
      $questionId = 3;
      $time =date('Y-m-d H:i:s',time());
      if($disList){
         foreach($disList as $key=>$val){
           
             $orderInfo =  $this->mOrder->getOrderInfoBySeriNum($val['order_serial_no']);
             $question = $this->mQuestion->isHasSendCoupon($orderInfo['client_id'],$orderInfo['order_id'],$questionId);
        	 $uinfo = $this->User->getUserInfo($orderInfo['client_id']);
        	 if(!$question){
        	        $min=floor((strtotime($time)-strtotime($val['update_time']))%86400/60);
          			$date=floor((strtotime($time)-strtotime($orderTime))/86400);
           			 if($min >=$sendMin || $date >=1){
        			    //发送做题通知
        			   //$this->sendNotif($questionId,$orderInfo['order_id'],$uinfo['client_wechat_openid']);
        	      }
             }
         }
          
      }
      
  
  }
  
  public function sendNotif($questionId,$order_id,$open_id){
  
      $url = "http://survey.qiezilife.com/Weixin/FrontIndex/login?id=$questionId&orderId=$order_id";
      $this->loadModel('WechatSdk');
      global $config;
      if ($config->messageTpl['cash_pay_notify'] != '') {

           $code = Messager::sendTemplateMessage($config->messageTpl['question_notify'], $openid, array(
                        'first' => '恭喜您即将获得1张代金券，面值5元。>>立即查看' ,
                        'keyword1' => 'CheersLife 商城所有的商品',
                        'keyword2' => '做完本次问卷调查',
            
                        'remark' => '代金券可在 CheersLife 商城、支付时抵扣等额现金'
                            ), $url);
           
                return $code;
      }
  
  }
  

}
