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
class Uc extends Controller {

    const COOKIEXP = 36000;

    /**
     * 
     * @param type $ControllerName
     * @param type $Action
     * @param type $QueryString
     */
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        $this->loadModel('User');
    }

    /**
     * 登陆页面
     * @param type $Query
     */
    public function login() {
        $this->show();
    }

    /**
     * user Home
     * 用户中心首页
     */
   public function home() {

 	   $this->loadModel('mOrder');
       $this->loadModel('UserLevel');
       $this->loadModel('UserCoupon');
       $this->loadModel('User');
        // get openid
       $Openid = $this->getOpenId();


        if(!Controller::inWechat() && !$this->debug){

            $this->show('./index/error.tpl');
            die(0);
        }

        // 微信自动注册
        $this->User->wechatAutoReg($Openid);
        $uinfo = $this->User->getUserInfo($Openid);
        // get uid
        $Uid = $uinfo['uid'];
        	
               
        

        // page cacheid
        $this->cacheId = $Openid;

        if (!$this->isCached()) {
          
            // 回收过期订单
            $this->mOrder->orderReclycle($Uid);
            if (!$Uid) {
                // uid cookie 过期或者未注册
                    if (!empty($Openid)) {
                        if (!$this->User->checkUserExt($Openid)) {
                            // 用户在微信里面 但是居然不存在这个用户
                            $this->redirect($this->root . '?/Uc/wechatPlease');
                        } else {
                            // 获取uid
                            $Uid = $this->User->getUidByOpenId($Openid);
                        }
                        $userInfo = $this->User->getUserInfoRaw($Uid);
                    }
            } else {
                // 用户已注册
                $userInfo = $this->User->getUserInfoRaw($Uid);
                // 刷新微信头像
                if (time() - strtotime($userInfo['client_head_lastmod']) > 432000 && Controller::inWechat()) {
                    $AccessCode = WechatSdk::getAccessCode($this->uri, "snsapi_userinfo");
                    if ($AccessCode !== FALSE) {
                        // 获取到accesstoken和openid
                        $Result = WechatSdk::getAccessToken($AccessCode);
                        // 微信用户资料
                        $WechatUserInfo = WechatSdk::getUserInfo($Result->access_token, $Result->openid);
                    }
                    $head = preg_replace("/\/0/", "", $WechatUserInfo->headimgurl);
                    $this->Db->query("UPDATE `clients` SET `client_head` = '$head',`client_head_lastmod` = NOW() WHERE `client_wechat_openid` = '$Result->openid';");
                }
                
                
            }


            // 刷新uid cookie
            $this->sCookie('uid', $Uid, Uc::COOKIEXP);

			
			$query = "where client_id = ".$Uid;
			$count = 0;
			$couponCount = 0;
			$orderList = $this->mOrder->queryOrderList($query);
            error_log("==========orderList===================".json_encode($orderList));
			if($orderList){
				$count = count($orderList);
			}
            $couponList = $this->UserCoupon->getUserCouponListByState($Uid,0);
          	if($couponList){
          					$couponCount = count($couponList);
          	
          	}	
            $this->assign('level', $this->UserLevel->getLevByUid($Uid));
            $this->assign('count_envs', $this->Db->getOne("SELECT COUNT(`id`) AS `count` FROM `client_envelopes` WHERE `uid` = '$Uid' AND `count` > 0 AND `exp` > NOW();"));
            $this->assign('count_like', $this->Db->getOne("SELECT COUNT(`id`) AS `count` FROM `client_product_likes` WHERE `openid` = '$Openid';"));
            $this->assign('count', $count);
            $this->assign('couponCount', $couponCount);
            
            $this->assign('userinfo', $userInfo);
        }
		
	
		
        $this->show();
    }

    public function wechatPlease() {
        $this->show();
    }

    public function companyReg() {
        $this->assign('title', '代理申请');
        $this->assign('openid', $this->getOpenId());
        $this->show();
    }

    public function envslist() {
        $this->loadModel('Envs');
        $Openid = $this->getOpenId();
        // 微信注册
        $this->User->wechatAutoReg($Openid);
        $envs = $this->Envs->getUserEnvs($this->getUid());
        $this->assign('envs', $envs);
        $this->assign('title', '我的红包');
        $this->show();
    }

    /**
     * companySpread
     */
    public function companySpread() {
        // 统计数据 
        $uid = $this->pCookie('uid');
        $this->loadModel('User');
        # $userInfo = $this->User->getUserInfo();
        if (!$this->isCompany($uid)) {
            header('Location:' . $this->root . '?/WechatWeb/proxy/');
        } else {
            $comid = $this->Dao->select('id')->from('companys')->where("uid=$uid")->getOne();
            $userInfo = $this->User->getUserInfoRaw();
            $this->assign('userinfo', $userInfo);
            $spreadData = $this->Db->getOneRow("select sum(readi) as readi,sum(turned) as turned from company_spread_record WHERE com_id = '$comid';");
            // 转化率
            $spreadData['turnrate'] = sprintf('%.2f', $spreadData['readi'] > 0 ? ($spreadData['turned'] / $spreadData['readi']) : 0);
            // 总收益
            $spreadData['incometot'] = $this->Db->getOne("SELECT sum(amount) AS amount FROM `company_income_record` WHERE com_id = '$comid';");
            $spreadData['incometot'] = $spreadData['incometot'] > 0 ? $spreadData['incometot'] : 0;
            // 今日收益
            $spreadData['incometod'] = $this->Db->getOne("SELECT sum(amount) AS amount FROM `company_income_record` WHERE com_id = '$comid' AND to_days(date) = to_days(now());");
            $spreadData['incometod'] = $spreadData['incometod'] > 0 ? $spreadData['incometod'] : 0;
            // 昨日收益
            $spreadData['incometotyet'] = $this->Db->getOne("SELECT sum(amount) AS amount FROM `company_income_record` WHERE com_id = '$comid' AND to_days(date) = to_days(now()) - 1;");
            $spreadData['incometotyet'] = $spreadData['incometotyet'] > 0 ? $spreadData['incometotyet'] : 0;
            // 名下用户总数
            $spreadData['ucount'] = $this->Db->getOne("SELECT count(*) AS amount FROM `company_users` WHERE comid = '$comid';");
            $spreadData['ucount'] = $spreadData['ucount'] > 0 ? $spreadData['ucount'] : 0;
            // 名下用户列表
            $spreadData['ulist'] = $this->Dao->select()->from('clients')->where('client_comid=' . $comid)->exec();
            foreach ($spreadData['ulist'] as &$l) {
                $r = $this->Db->getOneRow("SELECT COUNT(*) as count, SUM(amount) as am FROM `company_income_record` WHERE com_id = $comid AND client_id = $l[client_id];");
                $l['od'] = $r['count'];
                $l['oamount'] = $r['am'] > 0 ? sprintf('%.2f', $r['am']) : '0.00';
            }
            $this->assign('stat_data', $spreadData);
            $this->assign('title', '我的推广');
            $this->show();
        }
    }

    /**
     * 订单列表
     * @param type $Query
     */
    public function orderlist($Query) {
        $this->loadModel('JsSdk');
        $openid = $this->getOpenId();
        if(!Controller::inWechat()){

            $this->show('./index/error.tpl');
            die(0);
        }
        $this->User->wechatAutoReg($openid);
        $this->Smarty->caching = false;
        !isset($Query->status) && $Query->status = '';
        $signPackage = $this->JsSdk->GetSignPackage();
        $this->assign('signPackage', $signPackage);
        $this->assign('status', $Query->status);
        $this->assign('title', '我的订单');
        $this->show();
    }

    /**
     * Ajax订单列表
     * @param type
     */
    public function ajaxOrderlist($Query) {

        $openid = $this->pCookie('uopenid');

        if ($openid == '') {
            die(0);
        } else {
            !isset($Query->page) && $Query->page = 0;
            $limit = (5 * $Query->page) . ",5";
            $this->cacheId = $openid . $limit . $Query->status;
            $this->Smarty->cache_lifetime = 5;

            if (!$this->isCached()) {
                global $config;
                $this->loadModel('Product');
                if ($Query->status == '' || !$Query->status) {
                    $SQL = "SELECT * FROM `orders` WHERE `wepay_openid` = '$openid' ORDER BY `order_time` DESC LIMIT $limit;";
                } else {
                    if ($Query->status == 'canceled') {
                        $SQL = "SELECT * FROM `orders` WHERE `wepay_openid` = '$openid' AND `status` = '$Query->status'  ORDER BY `order_time` DESC LIMIT $limit;";
                    } else if ($Query->status == 'received') {
                        // 待评价订单列表
                        $SQL = "SELECT * FROM `orders` WHERE `wepay_openid` = '$openid' AND `status` = '$Query->status' AND `is_commented` = 0 ORDER BY `order_time` DESC LIMIT $limit;";
                    } else {
                        // 其他普通列表
                        $SQL = "SELECT * FROM `orders` WHERE `wepay_openid` = '$openid' AND `status` = '$Query->status' ORDER BY `order_time` DESC LIMIT $limit;";
                    }
                }
                $orders = $this->Db->query($SQL);
                foreach ($orders AS &$_order) {
                    // 是否为代付
                    $_order['isreq'] = $_order['status'] == 'reqing';
                    $_order['isreq'] = $_order['isreq'] || $this->Dao->select('')->count()->from(TABLE_ORDER_REQS)->where("order_id = $_order[order_id] AND `wepay_serial` <> ''")->getOne() > 0;
                    $_order['statusX'] = $config->orderStatus[$_order['status']];
                    $_order['order_time'] = $this->Util->dateTimeFormat($_order['order_time']);
                    $_order['data'] = $this->Db->query("SELECT catimg,`pi`.product_name,`pi`.product_id,`sd`.product_count,`sd`.product_discount_price,`sd`.product_price_hash_id "
                            . "FROM `orders_detail` sd LEFT JOIN `products_info` pi on pi.product_id = sd.product_id WHERE `sd`.order_id = " . $_order['order_id']);
                    // 整理商品数据
                    foreach ($_order['data'] as &$data) {
                        $d = $this->Product->getProductInfoWithSpec($data['product_id'], $data['product_price_hash_id']);
                        $data['spec1'] = $d['det_name1'];
                        $data['spec2'] = $d['det_name2'];
                    }
                }
                $this->assign('orders', $orders);
            }
        }
        $this->show();
    }

    /**
     * 查看订单详情
     * @param type $orderid
     */
    public function viewOrder() {
        $this->show();
    }

    /**
     * 判断是否微代理
     */
    private function isCompany($openid) {
        return $this->Db->query("SELECT `uid` FROM `companys` WHERE `uid` = '$openid';");
    }

    public function selectOrderAddress($Query) {
        !isset($Query->body) && $Query->body = 'false';
        $uid = $this->pCookie('uid');
        $ret = $this->Db->query("SELECT * FROM `client_order_address` WHERE `client_id` = $uid;");
        $this->assign('addrs', $ret);
        $this->assign('bodyonly', $Query->body == 'true' ? true : false);
        $this->show();
    }

    public function ajaxAddAddress() {
        $uid = $this->pCookie('uid');
        $name = $this->post('name');
        $tel = $this->post('tel');
        $addr = $this->post('addr');
        $ret = $this->Db->query("INSERT INTO `client_order_address` (client_id,`name`,`tel`,`address`) VALUES ($uid,'$name','$tel','$addr');");
        echo $ret;
    }

    /**
     * 我的收藏页面
     */
    public function uc_likes() {
        $this->assign('title', '我的收藏');
        $this->show();
    }

    /**
     * 获取收藏列表
     * @param type $Query
     */
    public function ajaxLikeList($Query) {
        $openid = $this->getOpenId();
        $this->cacheId = $openid . $Query->page;
        if (!$this->isCached()) {
            !isset($Query->page) && $Query->page = 0;
            $limit = ($Query->page * 10) . ',10';
            $this->loadModel('User');
            $likeList = $this->User->getUserLikes($openid, $limit);
            if ($likeList !== false) {
                $this->assign('loaded', count($likeList));
                $this->assign('likeList', $likeList);
            } else {
                $this->assign('loaded', 0);
            }
        }
        $this->show();
    }

    /**
     * ajax编辑收藏
     */
    public function ajaxAlterProductLike() {
        $this->loadModel('User');
        $openid = $this->getOpenId();
        $id = $this->post('id');
        if ($id > 0 && $openid != '') {
            // add
            echo $this->User->addUserLike($openid, $id);
        } else if ($id < 0 && $openid != '') {
            // delete
            $id = abs($id);
            echo $this->User->deleteUserLike($openid, $id);
        } else {
            echo 0;
        }
    }

    /**
     * ajax获取用户分组
     */
    public function getAllGroup() {
        $this->loadModel('SqlCached');
        // file cached
        $cacheKey = 'ucajaxGetCategroys';
        $fileCache = new SqlCached();
        $ret = $fileCache->get($cacheKey);
        if (-1 === $ret) {
            $this->loadModel('UserLevel');
            $lev = $this->UserLevel->getList();
            $levs = array();
            foreach ($lev as $l) {
                $levs[] = array('dataId' => $l['id'], 'name' => $l['level_name']);
            }
            $cats = $this->toJson($levs);
            $fileCache->set($cacheKey, $cats);
            echo $cats;
        } else {
            echo $ret;
        }
    }
    
   public function ajaxSendCode(){
    	
    	$phone = $_POST['phone'];
   	    
   	    $fp = fopen("http://qiezilife.com/SmsService/SmsServlet?phone=".$phone, 'r');
        stream_get_meta_data($fp);
        while(!feof($fp)) {
            $result .= fgets($fp, 1024);
        }
        fclose($fp);
        
        $resp = json_decode($result, true);
        $state = $resp['state'];
        $code = $resp['code'];
        $this->sCookie($phone,$code,60);
        if($state == 0){
        
        	$this->echoMsg(1,'验证码已发送');
        }else{
            $this->echoMsg(1,'验证码发送失败');
        }
    }
    
    public function ajaxVeriCode(){
    
        $openid = $this->getOpenId();
    
        $this->loadModel('User');

   		$code = $_POST['code'];
   		$phone = $_POST['phone'];
   		$session_code = $this->pCookie($phone);

   		error_log("===============code========".$code."=========phone".$phone."=======session_code====".$session_code);
    	if($code == ''){
            $this->echoMsg(-1,"请填写验证码");
        
        }else if($code != $session_code){
            $this->echoMsg(-1,"验证码错误或已过期");
        }else{

          $userInfo = $this->User->getUserInfoRaw($openid);
          if($userInfo){
             $data = array('client_phone' => $phone);
             $this->User->updateUserInfo($userInfo['client_id'], $data);
          }
          
          
          //设置注册奖励
          $uid = $userInfo['client_id'];
          $awardSettings = $this->Dao->select("value")->from('wshop_settings')->where("`key` = 'award_settings'")->getOne();
          error_log('awardSettings ================>'.$awardSettings);
          if(!empty($awardSettings)){
              $awards = json_decode($awardSettings, true);
              if ($awards['reg_award'] AND ($awards['reg_award']['type']>0)) {
                  global $config;
                  //
                  $awardType = $awards['reg_award']['type'];
                  $value = $awards['reg_award']['value'];
                  $this->loadModel('Coupons');
                  $this->loadModel('WechatSdk');
                  if($awardType==1){ //奖励的是优惠券
                      $this->loadModel('UserCoupon');
                      $rtnCode = $this->UserCoupon->regAwardCoupon($value,$uid);
                      error_log('reg award coupn info , rtn_code is ================>'.$rtnCode);
                      if ($rtnCode > 0) {
                          $this_coupon = $this->Coupons->get_coupon_info(($value));
                          if($this_coupon['discount_type'] == 1){
                             $discount_val = intval($this_coupon['discount_val']/10);
                            Messager::sendNotification(WechatSdk::getServiceAccessToken(), $openid, "亲，终于等到你~ 这张（".$discount_val."折）优惠券给你预留好久了\n适用范围：CheersLife全部商品\n使用规则：在下单时选中优惠券抵用即可\n快来享用健康美食吧~", $config->domain.'?/Coupon/user_coupon/');
                          
                          }else{
                            $discount_val = intval($this_coupon['discount_val']/100);
                            Messager::sendNotification(WechatSdk::getServiceAccessToken(), $openid, "亲，终于等到你~ 这张（".$discount_val."元）优惠券给你预留好久了\n适用范围：CheersLife全部商品\n使用规则：在下单时选中优惠券抵用即可\n快来享用健康美食吧~", $config->domain.'?/Coupon/user_coupon/');
                            
                          }

                          // send weixin msg
                      }
                  }else if($awardType==2){  //奖励的是账户余额
                      //因为用户是初次注册，此时的余额还为0，所以不需要先查询余额。
                      $money = $value/100;
                      $this->User->updateUserMoneyByOpenId($openid,$money);
                      error_log('reg award is money ================>'.$money);
                      // send weixin msg
                      Messager::sendNotification(WechatSdk::getServiceAccessToken(), $openid, "亲，终于等到你~ 这张（".$money."元）优惠券给你预留好久了\n适用范围：CheersLife全部商品\n使用规则：在下单时选中优惠券抵用即可\n快来享用健康美食吧~", $config->domain.'?/Coupon/user_coupon/');
                  }
              }
          }
       
          $this->echoMsg(1,"绑定成功");
        }
        
        
    }
    
    /**
     * 批量发送微信消息   
     */
    public function ajax_batch_send_wechat_msg(){
    	
    	global $config;
    	$open_ids = $this->pPost('open_ids');
    	$msg_type = $this->pPost('msg_type');
    	$content = $this->pPost('msg_content');
    	$url =$this->pPost('msg_url');
    	if(empty($content)){
    		$this->echoMsg(-1,'请输入推送的消息内容');
    		die(0);
    	}
    	
    	if($msg_type == 1){
    		if(empty($url)){
    			$this->echoMsg(-1,'请输入连接地址');
    			die(0);
    		}
    	}
    	if($url){
    		$url = 'http://'.$this->getBaseURI() .'/'. urldecode($url);
    	}
    	$open_ids_arr = explode(",",$open_ids);
    	$access_token = WechatSdk::getServiceAccessToken();
    	error_log('token====>'.$access_token);
    	if($msg_type == 1){
    		error_log('========================send url link msg========================');
    		foreach ($open_ids_arr as $key => $val){
    			$ret = Messager::sendNotification($access_token, $val, $content, $url);
    			error_log('【'.$val . '】 send status=====>'.$this->toJson($ret));
    		}
    	}else{
    		error_log('========================send text msg========================');
    		foreach ($open_ids_arr as $key => $val){
    			$ret = Messager::sendText($access_token, $val, $content);
    			error_log('【'.$val . '】 send status=====>'.$this->toJson($ret));
    		}
    	}
    	$this->echoMsg(1,"发送成功");
    }
}
