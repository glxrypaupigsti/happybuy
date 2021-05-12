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
class mQuestion extends Model {


    
    public function createQuestion($uid,$questionId,$couponId,$order_id){
    
      $add_time = time();	
      $id = $this->Dao->insert('question', '`uid`,`add_time`,`question_id`,`coupon_id`,`order_id`')
                        ->values(array($uid,$add_time,$questionId,$couponId,$order_id))->exec();
      return $id;
    }
    
 
     public function isSendCoupon($uid,$orderId){
     
         $c = $this->Dao->select()->from('question')->where("uid = '$uid'")->aw('order_id ='.$orderId)->getOne(false);
         return $c;
    }
    
 
    public function isHasSendCoupon($uid,$orderId,$questionId){
    
         $c = $this->Dao->select()->from('question')->where("uid = '$uid'")->aw('order_id ='.$orderId)->aw('question_id ='.$questionId)->getOne(false);
         return $c;
    }
    

    
    
}
