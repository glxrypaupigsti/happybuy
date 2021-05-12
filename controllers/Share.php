<?php



/**
 * 分享
 */
class Share extends Controller {

    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
    }

   /**
   * 分享页面
   */
   public function share_view(){
      $this->loadModel('User');
      $this->loadModel('JsSdk');
      $this->loadModel('mShare');
      $this->loadModel('mShareSetting');
      $this->loadModel('Coupons');      

       if(!Controller::inWechat() && !$this->debug){
            $this->show('./index/error.tpl');
            die(0);
        }
        $openid = $this->getOpenId();
        $this->User->wechatAutoReg($openid); 
      
      $uinfo = $this->User->getUserInfo($openid);
      $uid = $uinfo['uid'];
      $signPackage = $this->JsSdk->GetSignPackage();
      $this->assign('signPackage', $signPackage);
      $this->assign('uid', $uid);
      $this->assign('time', time());

      $list = $this->mShare->getUserShareTakeList("where create_share_uid = ".$uid." order by coupon_money desc  limit 20");
         foreach ($list as   $key => $val) {
           $u = $this->User->getUserInfo($val['uid']);
           $list[$key]['uinfo'] = $u;
           $list[$key]['coupon_value'] = $val['coupon_money']/100;

        
        }
        
       
      $user_share_coupon_id =  mShareSetting::$user_share_coupon_id;
      $where = " where key_m = '".$user_share_coupon_id."'";
      $setting = $this->mShareSetting->getShareSetting($where);
      $coupnId =$setting['value_m'];
      $couponInfo = $this->Coupons->get_coupon_info($coupnId);
      if($couponInfo){

        $couponInfo['coupon_value'] =  $couponInfo['discount_val']/100;
        error_log("===================couponInfo=======".json_encode($couponInfo));
      }
      $this->assign('couponInfo', $couponInfo);
      $this->assign('list', $list);
      $this->assign('uinfo', $uinfo);
      $this->show('./share/invite_friends.tpl');
   }
   
   /**
   *分享须知页面
   */
   public function share_note_view(){
      $this->loadModel('User');
      $this->loadModel('mShareSetting');
      $this->loadModel('Coupons');
      if(!Controller::inWechat() && !$this->debug){
            $this->show('./index/error.tpl');
            die(0);
        }
      $user_share_coupon_id =  mShareSetting::$user_share_coupon_id;
      $where = " where key_m = '".$user_share_coupon_id."'";
      $setting = $this->mShareSetting->getShareSetting($where);
      $coupnId =$setting['value_m'];
      $couponInfo = $this->Coupons->get_coupon_info($coupnId);
      if($couponInfo){

        $couponInfo['coupon_value'] =  $couponInfo['discount_val']/100;
          error_log("===================couponInfo=======".json_encode($couponInfo));
        }
       $this->assign('couponInfo', $couponInfo);
       $openid = $this->getOpenId();
       $this->User->wechatAutoReg($openid); 
       $this->show('./share/invite_note.tpl');
   }
   /**
   * 已分享的页面
   */
   public function share_wallet_view($data){
      global $config;
      $this->loadModel('JsSdk');
      $this->loadModel('User');
      $type = $data->type;
      $share_uid = $data->uid;
      $from_uid = $data->from_uid;
      $time = $data->time;
      $this->loadModel('UserCoupon');
      $this->loadModel('Coupons');
      $this->loadModel('mShare');
      $this->loadModel('mShareSetting');
      $this->loadModel('WechatSdk');
      
      
      $signPackage = $this->JsSdk->GetSignPackage();
	  $share_des =array('以后下午茶有着落了','亲，爱你','我的手气最好么','健康下午茶？嗯，可以试试','比手气，你们都不行','谢谢老板','发财啦','人生第一桶金','发红包的好帅','谢谢老板您破费了','感动死了','专业抢红包20年','我抢的红包最大么？','麻麻再也不用担心我没下午茶吃了','抢红包我最专业！','抢红包专业博士毕业~');
      if(!Controller::inWechat() && !$this->debug){
            $this->show('./index/error.tpl');
            die(0);
        }
        $openid = $this->getOpenId();
        $this->User->wechatAutoReg($openid); 
      
      $uinfo = $this->User->getUserInfo($openid);
      $shareRecord = $this->mShare->getShareByUid($share_uid,$time);
      $uid = $uinfo['uid'];
      
      if($uinfo['client_phone'] == ''){
          //绑定手机号
      }else{
         //发用户优惠券

       $checkTake = $this->mShare->checkUserShareTake($share_uid,$uinfo['uid']);
       if($checkTake){
         $this->assign('isTake', 1);
       }
         //检查是否可领取 
         $valid = $shareRecord['is_valid'];
         if($valid == '1'){
             $this->assign('valid',1);
         }else{
         
          if($checkTake || $shareRecord['uid'] == $uid){
            //已经领取  
             $this->assign('isTake', 1);
        }else{
          $coupList = $this->Coupons->get_shared_coupon_list();
          $des_count = rand(0,count($share_des)-1);
          $des = $share_des[$des_count];
          
          $count = count($coupList);
          $selectCount = rand(0, $count-1);
          $type = $shareRecord['type'];
            if($type == '0'){
              //分享次数设置 
              $user_share_count =  mShareSetting::$user_share_count;
              $where = " where key_m = '".$user_share_count."'";
              $setting = $this->mShareSetting->getShareSetting($where);
              $settingCount =$setting['value_m'];
              
              $list = $this->mShare->getUserShareTakeList(' where share_id = '.$shareRecord['id']);
              $totalCount = 0;
              if($list) $totalCount = count($list);

              error_log("=======totalCount===$totalCount====settingCount===$settingCount=======");

              if($totalCount <$settingCount){
               
               $this->UserCoupon->insertUserCoupon($coupList[$selectCount]['id'],$uid, true,'share');  
               $this->mShare->createShareUserTake($uid,$des,$coupList[$selectCount]['id'],$coupList[$selectCount]['discount_val'],$shareRecord['id'],$from_uid,$shareRecord['uid']); 
               $coupList[$selectCount]['coupon_value'] =  (int) ($coupList[$selectCount]['discount_val'] / 100);
               $this->assign('couponInfo', $coupList[$selectCount]);
               $this->assign('isTake', 2);
               $content = "偷偷送你（".(int) ($coupList[$selectCount]['discount_val'] / 100)."元）~不要告诉别人呦\n适用范围：CheersLife全部商品\n使用规则：在下单时选中优惠券抵用即可\n快来享用健康美食吧~";
     		   $url = $config->domain.'?/Coupon/user_coupon/';   
     		    Messager::sendNotification(WechatSdk::getServiceAccessToken(), $openid, $content, $url);
               
              }else{
                 $dataArray =   array(
                             'is_valid' => 1
                          );
                 //超出设置分享的次数
                 $this->mShare->updateShare($shareRecord['id'],$dataArray);
                 $this->assign('valid',1);
              }
               
            }
   

       }
       }// valid  end
    
        

        $takeList = $this->mShare->getUserShareTakeList("where create_share_uid = ".$shareRecord['uid']." order by coupon_money desc  limit 20");
      
        foreach ($takeList as   $key => $val) {
           $u = $this->User->getUserInfo($val['uid']);
           $takeList[$key]['uinfo'] = $u;
           $takeList[$key]['coupon_value'] = $val['coupon_money']/100;
        }
        $this->assign('takeList', $takeList);
      }

     
      $user_share_coupon_id =  mShareSetting::$user_share_coupon_id;
      $where = " where key_m = '".$user_share_coupon_id."'";
      $setting = $this->mShareSetting->getShareSetting($where);
      $coupnId =$setting['value_m'];
      $couponInfo = $this->Coupons->get_coupon_info($coupnId);
      if($couponInfo){

        $couponInfo['coupon_value'] =  $couponInfo['discount_val']/100;
       }
      $this->assign('setCouponInfo', $couponInfo);
     
      $this->assign('signPackage', $signPackage);
      $this->assign('share_uid', $share_uid);
      $this->assign('from_uid', $from_uid);
       $this->assign('time', $time);
      $this->assign('type', $type);
      $this->assign('uinfo', $uinfo);
      $this->show('./share/shared_wallet.tpl');
   
   }
   
   
   
   public function ajaxCreateShare(){
       global $config;
       $this->loadModel('mShare');
       $this->loadModel('User');
       $this->loadModel('mShareSetting');
       $this->loadModel('UserCoupon');
       $this->loadModel('mShareSetting');
       $this->loadModel('Coupons');
       $this->loadModel('WechatSdk');
   
       if(!Controller::inWechat() && !$this->debug){
            $this->show('./index/error.tpl');
            die(0);
        }
        $openid = $this->getOpenId();
        $this->User->wechatAutoReg($openid); 
        $type = 0;//0 用户中心分享 1表示订单分享 
        $uinfo = $this->User->getUserInfo($openid);
        $uid = $uinfo['uid'];
        $check_time = time();
        $share = $this->mShare->todayShare($uid);
        $id = 0;
        $coupnId = '';
        if($type == '0'){
               //用户优惠券分享
              $user_share_coupon_id =  mShareSetting::$user_share_coupon_id;
              $where = " where key_m = '".$user_share_coupon_id."'";
              $setting = $this->mShareSetting->getShareSetting($where);
              $coupnId =$setting['value_m'];
              
           }
        if($share){
           $id = $share['id'];
        }else{
           $id = $this->mShare->createShare($uid,0,0,$type,$coupnId);

        }
        
      $couponInfo = $this->Coupons->get_coupon_info($coupnId);
      if($couponInfo){
        $couponInfo['coupon_value'] =  $couponInfo['discount_val']/100;
      }
      $this->UserCoupon->insertUserCoupon($coupnId, $uid, true,'share');  
      $content = "偷偷送你（".$couponInfo['coupon_value']."元）~不要告诉别人呦\n适用范围：CheersLife全部商品\n使用规则：在下单时选中优惠券抵用即可\n快来享用健康美食吧~";
      $url = $config->domain.'?/Coupon/user_coupon/';   
      Messager::sendNotification(WechatSdk::getServiceAccessToken(), $openid, $content, $url);
        
      echo $id;
        
   }
   
   
   public function ajaxBindPhone($data){

       $this->loadModel('User');
       $openid = $this->getOpenId();
       $uinfo = $this->User->getUserInfo($openid);
       $uid = $uinfo['uid'];
       $phone = $data->phone;
       error_log("=====phone============".$phone);
       $dataArray =  array(
                             'client_phone' =>  $phone
                          );
       $id = $this->User->updateUserInfo($uid,$dataArray);
       echo $id;
   }

}
