<?php

// 支付授权目录 112.124.44.172/wshop/
// 支付请求示例 index.php
// 支付回调URL http://112.124.44.172/wshop/?/Order/payment_callback
// 维权通知URL http://112.124.44.172/wshop/?/Service/safeguarding
// 告警通知URL http://112.124.44.172/wshop/?/Service/warning

/**
 * 优惠券类
 */
class Coupon extends Controller {
    
    const TPL = './views/wdminpage/';

    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        $this->loadModel('CouponTerms');
        $this->loadModel('Coupons');
    }
    
    public function loadCouponDetail($Q){
        $coupon_info = $this->Coupons->get_coupon_info($Q->id);
        foreach($coupon_info as $key => $val){
            if($key == 'effective_start' or $key == 'effective_end' or $key == 'available_start' or $key == 'available_end'){
                $coupon_info[$key] = date('Y-m-d H:i:s',$val);
            }
        }
        if(intval($coupon_info['effective_end'])< $time){
            $coupon_info['is_expired'] = "已过期";
            $coupon_info['expired_state'] = 1;
        }else{
            $coupon_info['is_expired'] = "正常";
            $coupon_info['expired_state'] = 0;
        }
        
        if($coupon_info['coupon_type'] == 0){
            $coupon_info['coupon_type_desc'] = "商品券";
        }else if($coupon_info['coupon_type'] == 1){
            $coupon_info['coupon_type_desc'] = "订单券";
        }else{
            $coupon_info['coupon_type_desc'] = "用户券";
        }
        
        if($coupon_info['is_activated'] == 0){
            $coupon_info['is_activated_desc'] = "未激活";
        }else{
            $coupon_info['is_activated_desc'] = "已激活";
        }

        if($coupon_info['coupon_stock'] < 0){
            $coupon_info['coupon_stock_desc'] = "不限量";
        }else{
            $coupon_info['coupon_stock_desc'] = $coupon_info['coupon_stock'];
        }
        
        if($coupon_info['coupon_stock_left'] < 0){
            $coupon_info['coupon_stock_left_desc'] = "不限量";
        }else{
            $coupon_info['coupon_stock_left_desc'] = $coupon_info['coupon_stock_left'];
        }
        
        $discount_type = $coupon_info['discount_type'];
        if($discount_type == 0){
            $coupon_info['discount_type_desc'] = '固定金额';
        }else if($discount_type == 1){
            $coupon_info['discount_type_desc'] = '折扣比例';
        }else if($discount_type == 2){
            $coupon_info['discount_type_desc'] = '满x减y';
        }else if($discount_type == 3){
            $coupon_info['discount_type_desc'] = '每满x减y';
        }else if($discount_type == 4){
            $coupon_info['discount_type_desc'] = '加x换购B';
        }else if($discount_type == 5){
            $coupon_info['discount_type_desc'] = '买M件送N件';
        }
        $applied = $coupon_info['applied'];
        
        //应用的
        if(!empty($applied)){
            $applied_arr = json_decode($applied,true);
            $applied_type = $applied_arr['applied_type'];
            $applied_subtype = $applied_arr['subtype'];
            $applied_categorys = $applied_arr['categorys'];
            $applied_products = $applied_arr['products'];
            if(!empty($applied_categorys)){ //应用于分类列表
                
                $this->assign('select_applied_categorys', $applied_categorys);
            }
            if(!empty($applied_products)){ //应用的商品列表
        
                $this->assign('select_applied_products', $applied_products);
            }
        }
        
        //[{"id":"25","name":"用户积分条件","table":"client","column":"client_credit","operate":">","value":"88"},{"id":"26","name":"用户优惠等级条件","table":"client","column":"client_level","operate":">","value":"99"}]
        $coupon_terms = $coupon_info['coupon_terms'];
        if(!empty($coupon_terms)){
            $coupon_terms_list = json_decode($coupon_terms,true);
            foreach ($coupon_terms_list as $key => &$val){
                $desc = '';
                if($val['table'] == 'client'){
                    $desc  = $desc . '用户';
                }else if($val['table'] == 'product'){
                    $desc  = $desc . '商品';
                }else if($val['table'] == 'order'){
                    $desc  = $desc . '订单';
                }
                
                if($val['column'] == 'client_credit'){
                    $desc  = $desc . '积分';
                }else if($val['column'] == 'client_level'){
                    $desc  = $desc . '等级';
                }else if($val['column'] == 'selected_amount'){
                    $desc  = $desc . '满';
                }else if($val['column'] == 'selected_mod_amount'){
                    $desc  = $desc . '每满';
                }else if($val['column'] == 'selected_quantity'){
                    $desc  = $desc . '数量';
                }else if($val['column'] == 'order_amount'){
                    $desc  = $desc . '总价';
                }
                if($val['table'] == 'client' || $val['column'] == 'order_amount' || $val['column'] == 'selected_quantity'){
                    if($val['operate'] == '>'){
                        $desc  = $desc . '大于';
                    }else if($val['operate'] == '>='){
                        $desc  = $desc . '大于等于';
                    }else if($val['operate'] == '<'){
                        $desc  = $desc . '小于';
                    }else if($val['operate'] == '<='){
                        $desc  = $desc . '小于等于';
                    }
                }
                
                $desc = $desc . $val['value'];
                $val['term_desc']  = $desc;
                
            }
            
            $this->assign('select_coupon_terms', $coupon_terms_list);
        }
        
        //[{"id":"110","name":"long","img":"144548598756285da377d85.jpg"}]
        $bundled = $coupon_info['bundled'];
        if(!empty($bundled)){
            $this->assign('select_bundled', json_decode($bundled,true));
        }
        //{"limit":"1","coupons":""}
        $limit = $coupon_info['coupon_limit'];
        if(!empty($limit)){
            $limit_arr = json_decode($limit,true);
            $this->assign('select_coupon_limit', $limit_arr['limit']);
        }
        $this->assign('coupon', $coupon_info);
        
        $this->show(self::TPL . 'coupon/ajaxload_coupondetail.tpl');
    }
    
    
    public function edit_coupon($Q){
        if ($Q->id > 0) {
            $coupon_info = $this->Coupons->get_coupon_info($Q->id);
            foreach($coupon_info as $key => $val){
                if($key == 'effective_start' or $key == 'effective_end' or $key == 'available_start' or $key == 'available_end'){
                    $coupon_info[$key] = date('Y-m-d H:i:s',$val);
                }
            }
            //{"applied_type":"0","subtype":"0","categorys":"","products":"[{"id":"108","name":"果粒橙","img":"1445414448562746303f613.png"},{"id":"101","name":"纸杯蛋糕","img":"14453082385625a74ea369f.jpg"},{"id":"99","name":"榴莲千层酥","img":"14452458625624b3a67a5b0.jpg"},{"id":"121","name":"ada","img":"1446023344563090b0d5e72.png"}]"
            $applied = $coupon_info['applied'];
            
            //应用的
            if(!empty($applied)){
                $applied_arr = json_decode($applied,true);
                $applied_type = $applied_arr['applied_type'];
                $applied_subtype = $applied_arr['subtype'];
                $applied_categorys = $applied_arr['categorys'];
                $applied_products = $applied_arr['products'];
                error_log('applied========>'.$applied);
                $this->assign('select_applied_products_str', json_encode($applied_products));
                
                if(!empty($applied_type)){ 
                    $this->assign('select_applied_type', $applied_type);
                }
                if(!empty($applied_subtype)){  //优惠券应用于商品的子类，是应用于商品还是商品分类
                    $this->assign('select_applied_subtype', $applied_subtype);
                }
                if(!empty($applied_categorys)){ //应用于分类列表
                    $this->assign('select_applied_categorys', $applied_categorys);
                }
                if(!empty($applied_products)){ //应用的商品列表
                    
                    $this->assign('select_applied_products', $applied_products);
                }
            }
            
            //[{"id":"25","name":"用户积分条件","table":"client","column":"client_credit","operate":">","value":"88"},{"id":"26","name":"用户优惠等级条件","table":"client","column":"client_level","operate":">","value":"99"}]
            $coupon_terms = $coupon_info['coupon_terms'];
            if(!empty($coupon_terms)){
                $this->assign('select_coupon_terms', json_decode($coupon_terms,true));
            }
            
            //[{"id":"110","name":"long","img":"144548598756285da377d85.jpg"}]
            $bundled = $coupon_info['bundled'];
            if(!empty($bundled)){
                $this->assign('select_bundled', json_decode($bundled,true));
            }
            //{"limit":"1","coupons":""}
            $limit = $coupon_info['coupon_limit'];
            if(!empty($limit)){
                $limit_arr = json_decode($limit,true);
                $this->assign('select_coupon_limit', $limit_arr['limit']);
            }
            
            $this->assign('mod', 'edit');
            $this->assign('coupon', $coupon_info);
        }
        
        $coupon_terms = $this->CouponTerms->getCouponTermsList();
        error_log('coupon_terms===>'.json_encode($coupon_terms));
        if(count($coupon_terms)>0){
            $order_coupon_terms = array();
            $product_coupon_terms = array();
            $user_coupon_terms = array();
            
            foreach ($coupon_terms as $ckey => $cval){
                if($cval['term_table'] == 'product_info'){
                    $product_coupon_terms[] = $cval;
                }else if ($cval['term_table'] == 'client'){
                    $user_coupon_terms[] =  $cval;
                }else if ($cval['term_table'] == 'order'){
                    $order_coupon_terms[] =  $cval;
                }
            }
            
            //将用户的限制条件添加到商品和订单券中
            foreach ($user_coupon_terms as $uckey => $ucval){
                $product_coupon_terms[] = $ucval;
                $order_coupon_terms[] = $ucval;
            }
            
            $this->assign('product_coupon_terms', $product_coupon_terms);
            $this->assign('order_coupon_terms', $order_coupon_terms);
            $this->assign('user_coupon_terms', $user_coupon_terms);
            
            $this->assign('coupon_terms', $coupon_tersm);
            $this->assign('coupon_terms_str', $this->toJson($coupon_tersm));
        }
        
        $this->loadModel('Product');
        $product_cats = $this->Product->getAllCategories();
        $this->assign('product_cats', $product_cats);
        $this->assign('product_cats_str', $this->toJson($product_cats));
        $this->assign('mod', 'add');
        $this->show(self::TPL . 'coupon/edit_coupon.tpl');
    }
    
    
    public function copy_coupon(){
        
    }
    
    /** 
     * 发放优惠券
     */
    public function give_coupon(){
        global  $config;
        $this->loadModel('UserCoupon');
        $this->loadModel('User');
        $this->loadModel('WechatSdk');
        $coupon_id = $this->pPost('coupon_id');
        $uids = $this->pPost('uids');
        $uid_arr = explode(',',$uids);
        $usr_count = count($uid_arr);
        
        if(empty($uids)){
            $this->echoMsg(-1,"请先择要用户");   
            die(0);         
        }
        
        $coupon_info = $this->Coupons->get_coupon_info($coupon_id);
        if($coupon_info['is_activated'] == 0){
            $this->echoMsg(-1,"优惠券还未激活不能发放");
            die(0);
        }
        if($coupon_info['effective_end']<time()){
            $this->echoMsg(-1,"优惠券已过期不能发放");
            die(0);
        }
        if($coupon_info['coupon_stock_left']>0 and $coupon_info['coupon_stock_left'] < $usr_count){
            $this->echoMsg(-1,"优惠券库存数量小于发放的用户数量");
            die(0);
        }

        $discount_val = $coupon_info['discount_val'];
        $discount_type = $coupon_info['discount_type'];
        if($discount_type == 1){
            $coupon_unit_desc = "折";
            $discount_val = $discount_val / 10;
        }else{
            $coupon_unit_desc = "元";
            $discount_val = $discount_val / 100;
        }

        error_log('uid_arr========================>'.json_encode($uid_arr));
        foreach ($uid_arr as $key => $uid){
            if(!empty($uid)){
                //插入
                $result = $this->UserCoupon->insertUserCoupon($coupon_id, $uid,true,'system');
                //发送微信通知
                $user_info = $this->User->getUserInfoRaw($uid);
                $openid = $user_info['client_wechat_openid'];
                Messager::sendNotification(WechatSdk::getServiceAccessToken(), $openid, "亲，终于等到你~ 这张（".$discount_val.$coupon_unit_desc."）优惠券给你预留好久了\n适用范围：CheersLife全部商品\n使用规则：在下单时选中优惠券抵用即可\n快来享用健康美食吧~", $config->domain.'?/Coupon/user_coupon/');
            }
        }
        
        if($coupon_info['coupon_stock_left']>0){
            $coupon_stock = intval($coupon_info['coupon_stock_left']) - $usr_count;
            if($coupon_stock<0){ //防止出现负数
                $coupon_stock = 0;
            }
            $state = $this->Coupons->dec_coupon_stock_left($coupon_id,$coupon_stock);
        }
        
        $this->echoMsg(1,"success");
        
    }
    
    /**
     * cookie
     * 保存优惠券
     * @return <float>
     */
    public function save_coupon(){
        $this->loadModel('WdminAdmin');
        //遍历来转换时间
        foreach ($_POST as $key => $val){
            if($key == 'effective_start' or $key == 'effective_end' or $key == 'available_start' or $key == 'available_end'){
                $_POST[$key] = strtotime($val);
            }
        }
        $admin_id = $this->WdminAdmin->getAdminIdFromCookie();
        $id = $this->pPost('id');
        $_POST['uid'] = $admin_id;
        $time = time();
        if($id>0){  //更新
            //插入日志
            $origin_coupon_info = $this->Coupons->get_coupon_info($id);
            $log = $origin_coupon_info['coupon_log'];
            error_log("error-Log====>".$log);
//          $log_arr = json_decode($log,true);
            
//          $logs[] = array(
//                  "admin_id" => $admin_id,
//                  "add_time" => $time,
//                  "operate" => 'update'
//          );
//          foreach($log_arr as $key => $val){
//              $logs[] = array(
//                  "admin_id" => $val['admin_id'],
//                  "add_time" => $val['add_time'],
//                  "operate" => $val['operate']
//              );
//          }
            
//          $_POST['coupon_log'] = json_encode($logs);
            $state = $this->Coupons->update_coupon($id,$_POST);
            echo $state;
        }else{
            $log[]  = array(
                    'admin_id' => $admin_id,
                    'add_time' => $time,
                    'operate'  => 'insert'
            );
            $_POST['coupon_log'] = json_encode($log);
            $state =  $this->Coupons->create_coupon($_POST);
            echo $state;
        }
        
        
    }
    
    
    /**
     * 删除优惠券信息
     */
    public function delete_coupon($Q){
        $this->loadModel('UserCoupon');
        $id = $Q->id;
        $coupon_info = $this->Coupons->get_coupon_info($id);
        if(!$coupon_info){
            $this->echoMsg(-1,"该优惠券不存在");
            return;
        }
        //删除优惠券信息
        //删除优惠券的图片信息
        $this->Coupons->remove_coupon($id);
        //删除用户优惠券中的所有该类优惠券
        $this->UserCoupon->deleteUserCouponByCouponId($id);
        echo 1;
    }
    
    /**
     * 删除用户优惠券信息
     */
    public function delete_user_coupon($Q){
        $this->loadModel('UserCoupon');
        $id = $Q->id;
        echo $this->UserCoupon->deleteUserCouponById($id);
    }
    
    
    /**
     * 批量删除用户优惠券信息
     */
    public function batch_delete_user_coupon($Q){
        $this->loadModel('UserCoupon');
        $ids = $Q->ids;
        if(empty($ids)){
            $this->echoMsg(-1,'请选择要删除的记录');
            die(0);
        }
        $state = $this->UserCoupon->batch_delete_user_coupon($ids);
        if($state > 0){
            $this->echoMsg(1,'删除成功');
        }else{
            $this->echoMsg(-1,'删除异常');
        }
    }
    
    
    /**
     * 删除优惠券信息
     */
    public function activate_coupon($Q){
        $id = $Q->id;
        $coupon_info = $this->Coupons->get_coupon_info($id);
        if(!$coupon_info){
            $this->echoMsg(-1,"该优惠券不存在");
            return;
        }
        echo $this->Coupons->activate_coupon($id);
    }


    /**
     * cookie
     * 编辑使用条件
     * @return <float>
     */
    public function edit_coupon_terms($Q) {
        if ($Q->id > 0) {
            $coupon_terms_info = $this->CouponTerms->getCouponTermsInfo($Q->id);
            $this->assign('coupon_terms', $coupon_terms_info);
        }
        $this->show(self::TPL . 'coupon/edit_coupon_terms.tpl');
    }
    
    /**
     * 保存优惠条件
     * @return <float>
     */
    public function save_coupon_terms(){
        
        $id = $this->post('id');
        error_log("id======>".$id);
        $code = 0;
        $msg = 'success';
        if($id > 0){
            $terms_exist = $this->CouponTerms->checkCouponTermsExists($this->post('term_table'),$this->post('term_column'),$this->post('term_operate'));
            if($terms_exist > 0){
                $code = -2;
                $msg = '该类型的优惠条件已经存在';
            }
            
            if($code == 0){
                $code = $this->CouponTerms->updateCouponTerms($_POST,$id);
            }
            echo $this->echoMsg($code,$msg);
        }else{
            
            $term_name_exist = $this->CouponTerms->checkCouponTermsNameExist($this->post('term_name'));
            
            if($term_name_exist){
                $code = -1;
                $msg = '优惠条件名称已经存在';
            }
            
            
            $terms_exist = $this->CouponTerms->checkCouponTermsExists($this->post('term_table'),$this->post('term_column'),$this->post('term_operate'));
            if($terms_exist){
                $code = -2;
                $msg = '该类型的优惠条件已经存在';
            }
            
            if($code == 0){
                $code =  $this->CouponTerms->insertCouponTermsList($_POST);
            }
            echo $this->echoMsg($code,$msg);
            
        }
        
    }
    
    public function delete_coupon_terms(){
        $this->loadModel('CouponTerms');
        $id = $this->post('id');
        echo $this->CouponTerms->deleteCouponTerms($id);
    }
    
    
    /**
     * 上传产品图片
     * ImageUpload
     */
    public function ImageUpload() {
        global $config;
        $this->loadModel('ImageUploader');
        
        //创建带有日期格式的目录
        $date = date('Y-m-d',time());
        $data_arr = explode('-',$date);
//      $data_dir_str = $data_arr[0].'/'.$data_arr[1].'/'.$data_arr[2].'/';
        $data_dir_str = "";
                
        $link_base = $config->couponPicLink . $data_dir_str;

        
        $this->ImageUploader->dir = $config->couponPicRoot . $data_dir_str;
        $targetFileName = $this->ImageUploader->upload();
        $arr = array(
                "s" => $targetFileName !== false,
                "pic" => $config->couponPicLink . $data_dir_str . $targetFileName,
                "imgn" => $data_dir_str.$targetFileName,
                "link" => $config->couponPicLink . $data_dir_str . $targetFileName
        );
        $this->echoJson($arr);
    }
    
    
    
    
    
    
    
    
   //======================前台相关============================
    //用户优惠券
    public function coupon_list($data){
    
    
       $this->loadModel('User');
       $this->loadModel('Coupons');
       $openid = $this->pCookie('uopenid');
     
        
        if(!Controller::inWechat() && !$this->debug){

            $this->show('./index/error.tpl');
            die(0);
        }
        $this->User->wechatAutoReg($openid);
    
  
       $uinfo = $this->User->getUserInfo($openid);
       $couponId = $data->couponId;
       if ($openid == '') {
            die(0);
        } else {
            
            $list = $this->Coupons->get_avaliable_coupons_for_order(time(),$uinfo['uid'],1);
            if($list){
               $isSelect = 0;
               foreach ($list AS $key => $val) {
                   if($val['discount_type'] == 1){
                      $list[$key]['coupon_value'] =  (int) ($val['coupon_value'] / 10);
                       $list[$key]['unit'] = "折";
                   }else{
                        $list[$key]['coupon_value'] =  (int) ($val['coupon_value'] / 100);
                        $list[$key]['unit'] = "元";
                   }
                 
                   if($val['coupon_id'] == $couponId &&  $isSelect != 1){
                       $list[$key]['select'] = 1;
                         $isSelect = 1;
                       error_log("test=======".$val['coupon_id']);
                   }
               }
               $this->assign('couponList', $list);
            }
            
            
        }
        $time = $data->time;
        $isbalance = $data->isbalance;
        $this->assign('couponId', $couponId);
        $this->assign('time', $time);
        $this->assign('isbalance', $isbalance);
        $this->show();
    }
    
    //ajax 获取优惠券列表
    public function ajaxCouponlist($Query){
     
       
      
        $this->show();
    
    }
    
    
    public function ajaxUserCouponList($Query){
        $this->loadModel('User');
        $this->loadModel('Coupons');
        $this->loadModel('UserCoupon');
        
        $openid = $this->pCookie('uopenid');

        if ($openid == '') {
            die(0);
        } else {
            $uinfo = $this->User->getUserInfo($openid);
            $uid = $uinfo['client_id'];
//             $uid = 116;

            error_log('=========uid========='.$uid);
            !isset($Query->page) && $Query->page = 0;
            $limit = (5 * $Query->page) . ",5";
            
            $state = $Query->state;
            if($state == 0){
                //未使用
               $couponList =  $this->UserCoupon->getUserCouponListByState($uid,0);
            }else if($state == 1){
                //已使用
                $couponList =  $this->UserCoupon->getUserCouponListByState($uid,1);
                
            }else{
                //已过期
                $couponList =  $this->UserCoupon->getUserExpiredCouponList($uid);
            }
            echo json_encode($couponList);
            //             $uid = 116;
//          $this->assign('couponList', $couponList);
//          $this->assign('state', $state);
//          $this->show('./coupon/ajaxusercoupon.tpl');
            
        }
        
        
    }
    
    
    public function user_coupon(){
    
        if(!Controller::inWechat() && !$this->debug){

            $this->show('./index/error.tpl');
            die(0);
        }
        $this->loadModel('User');
        $this->loadModel('Coupons');
        $openid = $this->pCookie('uopenid');
        $this->User->wechatAutoReg($openid);
        $this->show('./coupon/user_coupon.tpl');
    
    }
    
   
    
    

}
