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
class Index extends Controller {

    /**
     * 
     * @param type $ControllerName
     * @param type $Action
     * @param type $QueryString
     */
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
    }

    /**
     * 店铺首页
     * @param type $Q
     */
    public function index($Q) {

        $this->loadModel('User');
        $this->loadModel('WechatSdk');
        $this->loadModel('Product');
        $this->loadModel('Carts');
        $openId = $this->getOpenId();
        
        if(!Controller::inWechat() && !$this->debug){

            $this->show('./index/error.tpl');
            die(0);
        }
        //error_log("==============$openId==================".$openId);
        // 微信注册
        $this->User->wechatAutoReg($openId);
        
        if ($Q->rptk_success == 'normal') {
            error_log('rptk jump');
            //header("location:" . 'http://www.icheerslife.com/?/CrashPay/welcome_view');
        }
        
        $this->Smarty->cache_lifetime = 0;

        if (true OR !$this->isCached()) {
            
            $topCats = $this->Product->getCatList(0);
            if($topCats){
               $this->assign('catId', $topCats[0]['cat_id']);
             }
          
            $openid = $openId;//$this->getOpenId();
            $uinfo = $this->User->getUserInfo($openid);
            $uid = $uinfo['uid'];
            
            if ($Q->date) {
                $target_date = strtotime($Q->date);
                $_COOKIE['deliver_date'] = date('Y-m-d', $target_date);
                $this->sCookie('deliver_date', date('Y-m-d', $target_date), 3600*1);
            }
            
            $weekday_name = array('U', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日');
            // get selected time
            if (isset($_COOKIE['deliver_date'])) {
                $selected_time = strtotime($_COOKIE['deliver_date']);
                if (date('Y-m-d') == $_COOKIE['deliver_date']) {
                    // check if order window is closed for TODAY
                    $current_hour = idate('H');
                  
                }
            } else {
                $selected_time = time()+3600*24;
                $current_hour = idate('H');
             
            }
            $weekday = date('N', $selected_time);
            if (7 == $weekday) {
                $selected_time += 3600*24;
                $weekday++;
            }
            $weekday_str = $weekday_name[$weekday];

            $selected_date_str = date('Y-m-d', $selected_time);
            $today_str = date('Y-m-d');
            $tommrrow_str = date('Y-m-d', strtotime('+1 day'));
            if ($today_str == $selected_date_str) {
                $weekday_str = '今天';
            } else if ($tommrrow_str == $selected_date_str) {
                $weekday_str = '明天';
            }
            
            if (!isset($_COOKIE['deliver_date'])) {
                $this->sCookie('deliver_date', $selected_date_str, 3600*1);
            } else if ($_COOKIE['deliver_date'] != $selected_date_str) {
                $this->sCookie('deliver_date', $selected_date_str, 3600*1);
            }
            $this->assign('selected_day', $selected_date_str);
            $this->assign('selected_weekday', $weekday_str);
            
        // error_log("======cat===".json_encode($topCats));
            if ($uinfo['first_login'] == 1) {
                $this->assign('show_tip', true);
                // clean flag
                $data = array('first_login' => 0);
                $this->User->updateUserInfo($uinfo['client_id'], $data);
            } else {
                $this->assign('show_tip', false);
            }
            
            $productList = $this->Carts->get_cart_products($uid);
            $count = 0;
            $total = 0;
            if($productList){

                foreach($productList as   $k => $v) {
                    $count += $v['product_quantity'];
                    $productSpecs =  $this->Product->getProductSpecs($v['product_id']);
                    if($productSpecs){
                        $total=$total+$v['product_quantity']*$productSpecs[0]['sale_price'];
                    }
                }
                
                
                foreach($topCats as   $c_k => $c_v) {
                    
                    foreach($productList as   $p_k => $p_v) {
                        
                        if($c_v['cat_id'] == $p_v['product_cat']){
                            
                            $topCats[$c_k]['count'] = $topCats[$c_k]['count']+$p_v['product_quantity'];
                            
                        }
                    }
                } 
            }
         $this->assign('topcat', $topCats);
         $this->assign('count', $count);
         $this->assign('total', $total);
   
        }
        $this->show();
    }
    
    
    public final function ajax_list_item($Query){
        $this->loadModel('Product');
        $openid = $this->getOpenId();
        $this->loadModel('Carts');
        $this->loadModel('User');
        $this->loadModel('Stock');
        $uinfo = $this->User->getUserInfo($openid);
        $uid = $uinfo['uid'];
        
        // FIXME: $target_time should be set based on which day user select
        if (isset($_COOKIE['deliver_date'])) {
            $date = $_COOKIE['deliver_date'];
            $target_time = strtotime($date);
        } else {
            $target_time = time();
        }

        $this->Smarty->cache_lifetime = 0;
     
        if (isset($Query->id)) {
            $this->cacheId = intval($Query->id);
            
            $products = $this->Product->getNewEst($this->cacheId);
            
            //必须要添加一个规格
            foreach ($products as $key => $val) {
                $productSpecs = $this->Product->getProductSpecs($val['product_id']);
                if($productSpecs){
                    $products[$key]['pinfo'] = $productSpecs[0];
                    // get stock of given SKU for target date
                    $stock_info = $this->Stock->get_product_instock_by_sku_and_date($productSpecs[0]['id'], $target_time);
                    error_log('stock info:'.json_encode($stock_info));
                    $products[$key]['pinfo']['instock'] = $stock_info['stock']; // stocks can be sold for target date
                }
                $products[$key]['product_quantity'] = 0;
            }
            $count = 0; 
            $totalMoney = 0;
            $productList = $this->Carts->get_cart_products($uid);
            if($productList){
                 foreach($products as   $key => $val) {
                    
                      foreach($productList as   $k => $v) {
                          
                          if($val['product_id'] == $v['product_id']){
                          
                             $products[$key]['product_quantity'] = $v['product_quantity'];
                             $totalMoney = $totalMoney+$v['product_quantity']*$val['sale_price'];
                          }
                      }
                 }
                  
                //计算购物车总数
                 foreach($productList as   $k => $v) {     
                   $count = $count + $v['product_quantity'];
                 }
            }
            // HACK:check current weekday to find prefix should be added
            $weekday = date('N', strtotime($_COOKIE['deliver_date']));
            if (4 == $weekday) {
                // Thursday is "HALF-day" sale
                //$this->assign('prefix', '半价日-');
            }
            $this->assign('prefix', '');

            $this->assign('count', $count);
            $this->assign('total', $totalMoney);
            $this->assign('products', $products);
            $this->show('./index/ajax_list_item.tpl');
            // 分类下面无子分类
        }
   }
   
  public function ajaxGetCartProducts(){
  
     $this->loadModel('Carts');
     $this->loadModel('User');
     $this->loadModel('Product');
     $this->loadModel('Stock');
      
     $openid = $this->getOpenId();
     $uinfo = $this->User->getUserInfo($openid);
     $uid = $uinfo['uid'];
     $totalMoney = 0;
      
      $this->Smarty->cache_lifetime = 0;
      
      // FIXME: check_time should be set based on which day user select
      if (isset($_COOKIE['deliver_date'])) {
          $date = $_COOKIE['deliver_date'];
          $target_time = strtotime($date);
      } else {
          $target_time = time();
      }

     $productList = $this->Carts->get_cart_products($uid);
     foreach ($productList as   $key => $val) {
          $productSpecs = $this->Product->getProductSpecs($val['product_id']);
          if($productSpecs){
              $productList[$key]['pinfo'] = $productSpecs[0];
              // get stock of given SKU for target date
              $stock_info = $this->Stock->get_product_instock_by_sku_and_date($productSpecs[0]['id'], $target_time);
              $productList[$key]['pinfo']['instock'] = $stock_info['stock']; // stocks can be sold for target date
              
              $totalMoney = $totalMoney+$val['product_quantity']*$productSpecs[0]['sale_price'];
              
          }
      }
       $count = 0; 
      foreach($productList as   $k => $v) {     
          $count += $v['product_quantity'];      
      } 
     $this->assign('product_list', $productList);
     $this->assign('count', $count);
     $this->assign('total', $totalMoney);
     $this->show('./index/ajax_cart_item.tpl');
     
  }

   public function ajaxRemoveProduct(){

       $this->loadModel('Carts');
       $this->loadModel('User');
       $this->loadModel('Product');

       $productId = $_POST['product_id'];
       $specId = $_POST['spec_id'];

       
       $openid = $this->getOpenId();
   
       $uinfo = $this->User->getUserInfo($openid);
       $uid = $uinfo['uid'];
       $this->Carts->remove_product($uid,$productId,$specId);
       $productList = $this->Carts->get_cart_products($uid);
       foreach ($productList as   $key => $val) {
          $productSpecs = $this->Product->getProductSpecs($val['product_id']);
          if($productSpecs){
              $productList[$key]['pinfo'] = $productSpecs[0];
              $totalMoney = $totalMoney+$val['product_quantity']*$productSpecs[0]['sale_price'];
              
          }
      }
      $count = 0; 
      foreach($productList as   $k => $v) {     
          $count += $v['product_quantity'];      
      } 
      //error_log("=============productList==========".json_encode(count($productList)));
     $this->assign('product_list', $productList);
     $this->assign('count', $count);
     $this->show('./index/ajax_cart_item.tpl');
  }
  
  public function ajaxRemoveProductUPdateData(){
  
     $this->loadModel('Carts');
     $this->loadModel('User');
     $this->loadModel('Product');
     $openid = $this->getOpenId();
     $uinfo = $this->User->getUserInfo($openid);
     $uid = $uinfo['uid'];
     $totalMoney = 0;
     
     $productId = $_POST['pid'];
     $specId = $_POST['sid'];
     $this->Carts->remove_product($uid,$productId,$specId);
     
     $productList = $this->Carts->get_cart_products($uid);
     foreach ($productList as   $key => $val) {
          $productSpecs = $this->Product->getProductSpecs($val['product_id']);
          if($productSpecs){
              $productList[$key]['pinfo'] = $productSpecs[0];
              $totalMoney = $totalMoney+$val['product_quantity']*$productSpecs[0]['sale_price'];
              
          }
      }
     $count = 0; 
     foreach($productList as   $k => $v) {     
          $count += $v['product_quantity'];      
     }
     
    $topCats = $this->Product->getCatList(0);
    foreach($topCats as   $c_k => $c_v) {  

                foreach($productList as   $p_k => $p_v) {  

                  if($c_v['cat_id'] == $p_v['product_cat']){

                       $topCats[$c_k]['count'] = $topCats[$c_k]['count']+$p_v['product_quantity'];

                  }
                }
         } 
     
     $data =  array(
           'count' => $count,
           'total' =>$totalMoney,
           'topCats' =>$topCats
     );
    $this->echoJson($data);
     
  }
    
    public function getDeliverDateList()
    {
        $weekday_name = array('U', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日');
        $totalDays = 7;
        $current_time = time();
        $current_hour = intval(date('G', $current_time));
        //if ($current_hour > 16)
            $current_time += 3600*24;
        
        $delivers = array();
        for ($i=0; $i < $totalDays; $i++) {
            $time = $i*3600*24 + $current_time;
            $weekday = date('N', $time);
            if (7 == $weekday) continue;
            $date_str = date('Y-m-d', $time);
            $weekday_str = $weekday_name[$weekday];
            
            $day = array(
                         'date' => $date_str,
                         'weekday' => $weekday_str,
                         );
            $delivers[] = $day;
        }
        $today_str = date('Y-m-d');
        $tommrrow_str = date('Y-m-d', strtotime('+1 day'));
        if ($today_str == $delivers[0]['date']) {
            $delivers[0]['weekday'] = '今天';
            if ($tommrrow_str == $delivers[1]['date']) {
                $delivers[1]['weekday'] = '明天';
            }
        } else if ($tommrrow_str == $delivers[0]['date']) {
            $delivers[0]['weekday'] = '明天';
        }

        echo htmlspecialchars(json_encode($delivers), ENT_NOQUOTES);
    }
    
    public function setDeliverDate($Q)
    {
        if (!$Q->date)
            echo htmlspecialchars(json_encode(array('err' => -1)), ENT_NOQUOTES);
        // 1 hour by default
        error_log('set day cookie:'.$Q->date);
        $this->sCookie('deliver_date', $Q->date, 3600*1, '/');
        echo htmlspecialchars(json_encode(array('err' => 0)), ENT_NOQUOTES);
    }

}
