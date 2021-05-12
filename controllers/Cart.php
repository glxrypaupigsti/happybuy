<?php



/**
 * 购物车
 */
class Cart extends Controller {

    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
    }

  
  /**
  *  跳转订单页面
  */
  public function cart(){

        $this->show();
  }

public function index_order($data){

      $this->loadModel('Carts');
      $this->loadModel('User');
      $this->loadModel('Product');
      $this->loadModel('mUserAddress');
      $this->loadModel('JsSdk');
      $this->loadModel('Coupons');
      $this->loadModel('mOrder');
      
      
       $openid = $this->getOpenId();
       $time = $data->time;
       $isbalance = $data->isbalance;
       if($isbalance == ''){
         $isbalance=1;
       }
       if($time){

       	 $time = urldecode($time);
       }
       if(!Controller::inWechat() && !$this->debug){

            $this->show('./index/error.tpl');
            die(0);
        }
        $this->User->wechatAutoReg($openid);
       
       $uinfo = $this->User->getUserInfo($openid);
        $uid = $uinfo['uid'];
      
       $productList = $this->Carts->get_cart_products($uid);
       foreach ($productList as   $key => $val) {
           $product = $this->Product->getById($val['product_id']);
                 //error_log("product".json_encode($product));
           $productList[$key]['pinfo'] =  $this->Product->getProductInfoWithSpec($val['product_id'],$val['spec_id']);
        }

    
      $amount = $this->Carts->calc_cart_amount($uid);
      error_log("amount===".json_encode($amount));
      $address = $this->mUserAddress->enableUserAddress($uinfo['uid']);
      
      
       $signPackage = $this->JsSdk->GetSignPackage();
        // 收货地址接口Json包
        $addrsignPackage = array(
            "appId" => APPID,
            "scope" => "jsapi_address",
            "signType" => "sha1",
            "addrSign" => isset($addrsign) ? $addrsign : false,
            "timeStamp" => (string) $timestamp,
            "nonceStr" => (string) $nonceStr
        );
      

      $reduceAmount = 0;
      $orderCoupons = $this->Coupons->get_avaliable_coupons_for_order(time(),$uinfo['uid'],0);
      if($orderCoupons){
      
           $couponAmount = $this->mOrder->cal_reduce_amount_by_coupon_id($amount,$orderCoupons[0]['id'],$uid,false);
           error_log("===============couponAmount============".$couponAmount);
           if($couponAmount > 0){

             $orderCoupons[0]['coupon_value'] =  round($couponAmount,2);

             $reduceAmount = $reduceAmount+ $orderCoupons[0]['coupon_value'];
             $this->assign('orderCoupons', $orderCoupons[0]);
           }
      	  
      		
      }
      
      $couponId ="";
      //获取用户优惠券开关
      $awardSettings = $this->Dao->select("value")->from('wshop_settings')->where("`key` = 'award_settings'")->getOne();
      $award = json_decode($awardSettings, true);
      $userCouponSwitch = $award['user_coupon_switch'];
      $userCoupons ='';
      if($userCouponSwitch == 1){ 
          $userCoupons = $this->Coupons->get_avaliable_coupons_for_order(time(),$uinfo['uid'],1);
          $weekarray=array("日","一","二","三","四","五","六");
     
       $date = $_COOKIE['deliver_date'];
       $todayDate = date("Y-m-d");


//       if($weekarray[date('w',strtotime($date))] == '四'){
//                //预订是礼拜四
//
//                $couponId ='';
//                $data = '';
//                $userCoupons = '';
//
//       }else{

            $couponList =  $this->UserCoupon->getUserCouponListByState($uinfo['uid'],0);
            error_log("==========".json_encode($couponList));
            foreach($couponList as $key=>$val){
                if($val['discount_type'] == 1 && $data->couponId!= -1){
                    $couponId = $val['coupon_id'];
                }
            }

         $useCoupon = $this->Coupons->get_coupon_info($couponId);
         $couponAmount = $this->mOrder->cal_reduce_amount_by_coupon_id($amount,$couponId,$uid,false);
         if($couponAmount > 0){

            $reduceAmount = $reduceAmount+$couponAmount;
            $this->assign('coupon',$useCoupon);
         }
//       }
    }
      
   if($data && $couponId != -1){
         if($couponId !='') $reduceAmount=0;
         $couponId = $data->couponId;
         $useCoupon = $this->Coupons->get_coupon_info($couponId);
         $couponAmount = $this->mOrder->cal_reduce_amount_by_coupon_id($amount,$couponId,$uid,false);
         if($couponAmount > 0){

            $reduceAmount = $reduceAmount+$couponAmount;
            $this->assign('coupon',$useCoupon);
         }
         //$amount = round($amount-$couponAmount,2);
       
         
      }
  
     
      $amount = round($amount - $reduceAmount,2);
      if($amount <= 0){
        $amount = 0;
      }
      if($userCoupons){
            $this->assign('userCoupons', $userCoupons);
      }
      $this->Smarty->assign('signPackage', $signPackage);
      $this->assign('couponId', $couponId);
      $this->Smarty->assign('userInfo', (array) $uinfo);
      $this->assign('address', $address);
      $this->assign('amount',$amount);
      $this->assign('time',$time);
      $this->assign('isbalance', $isbalance);
      $this->assign('product_list', $productList);
      $this->show('./order/order.tpl');
   }
 
 public function ajaxDelCart(){
     $this->loadModel('User');
     $this->loadModel('Carts');
     $openid = $this->getOpenId();
     $uinfo = $this->User->getUserInfo($openid);
     $uid = $uinfo['uid'];
     $this->Carts->del_cart($uid);
     $this->echoMsg(1,"删除成功");
  }
   
   
   /**
   *   购物车增加商品
   */
   public function add_product_to_cart(){
    
        $this->loadModel('Carts');
        $this->loadModel('User');
        
        $this->loadModel('Product');
        $openid = $this->getOpenId();

        $uinfo = $this->User->getUserInfo($openid);

        $uid = $uinfo['uid'];
        error_log("openId=================".$openid."===============uid=".$uid);
        $cartData = $this->carDataRepack(json_decode($_POST['data'], true));
        foreach ($cartData as $data) {
            
            $count = $data['count'];
            $productId = $data['pid'];
            $spid = $data['spid'];
            $this->Carts->update_cart_product($uid,$productId,$count,$spid);
            
        }


        $productList = $this->Carts->get_cart_products($uid);
        $productArray = array();
        $cartArray = array();
        $total = 0;
        foreach($productList as $plist){

            $key = 'p'.$plist['product_id'].'m'.$plist['spec_id'];
            $value =(int) $plist['product_quantity'];
            $productArray[$key] = $value;
            $productSpecs =  $this->Product->getProductSpecs($plist['product_id']);
            if($productSpecs){
                $total=$total+$value*$productSpecs[0]['sale_price'];
            }

        }

        $topCats = $this->Product->getCatList(0);
        foreach($topCats as   $c_k => $c_v) {  

                foreach($productList as   $p_k => $p_v) {  

                  if($c_v['cat_id'] == $p_v['product_cat']){

                       $topCats[$c_k]['count'] = $topCats[$c_k]['count']+$p_v['product_quantity'];

                  }
                }
         } 

        $cartArray['cartData'] = $productArray;
        $cartArray['total'] = $total;
        $cartArray['topCats'] = $topCats;
       // $result = json_encode($cartArray);
      //  $this->$result;
         $this->echoJson($cartArray);
        //error_log("result===========================================".$result);
      
   } 
   
   
   /*
   *
   */
   
   public function removeProduct($data){
   
       $productId = $data->product_id;
     $specId = $data->spec_id;
       $this->loadModel('Carts');
       $this->loadModel('User');
       $openid = $this->getOpenId();
   
       error_log("productId===============".$productId);
       $uinfo = $this->User->getUserInfo($openid);
       $uid = $uinfo['uid'];
       $this->Carts->remove_product($uid,$productId,$specId);
   }
  
   /**
  * 检查和同步购物车
  */
  public function checkCart(){
  
     $this->loadModel('Carts');
     $this->loadModel('User');
     $openid = $this->getOpenId();
     $uinfo = $this->User->getUserInfo($openid);
     $uid = $uinfo['uid'];

     $productList = $this->Carts->get_cart_products($uid);
     $productArray = array();
     $cartArray = array();

     foreach($productList as $plist){

            $key = 'p'.$plist['product_id'].'m'.$plist['spec_id'];
            $value =(int) $plist['product_quantity'];
            $productArray[$key] = $value;
     }
    $cartArray['cartData'] = $productArray;
    $this->echoJson($cartArray);
     
  }
  

  
  
   
  public function carDataRepack($cartData) {
        $matchs = array();
        $ret = array();
        foreach ($cartData as $key => $count) {
            preg_match("/p(\d+)m(\d+)/is", $key, $matchs);
            $ret[] = array('pid' => intval($matchs[1]), 'spid' => intval($matchs[2]), 'count' => intval($count));
        }
        return $ret;
    }

}
