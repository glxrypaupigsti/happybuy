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
class mShare extends Model {


    /**
    * 增加一条分享记录
    */
    public function createShare($uid,$share_money,$is_valid,$type,$couponId){
    
      $add_time = time();	
      $id = $this->Dao->insert('share', '`uid`,`add_time`,`share_money`,`is_valid`,`type`,`coupon_id`')
                        ->values(array($uid,$add_time,$share_money,$is_valid,$type,$couponId))->exec();
      return $id;
    }
    
    /**
    */
    public function todayShare($uid){
       	 
       	 $start_time = strtotime(date('Y-m-d',time()).' 00:00:00');
       	 $end_time = strtotime(date('Y-m-d',time()).' 23:59:59');
         $c = $this->Dao->select()->from('share')->where("uid = '$uid'")->aw('add_time >='.$start_time)->aw('add_time <='.$end_time)->getOne(false);
         return $c;
    }
	
	/*
	* 根据id修改分享记录
	*/
	public function updateShare($id,$data = array()){
    
        return $this->Dao->update('share')->set($data)->where("id =".$id)->exec(); 
    }
    public function getShareByUid($uid,$time){

	   $start_time = strtotime(date('Y-m-d',$time).' 00:00:00');
       $end_time = strtotime(date('Y-m-d',$time).' 23:59:59');
       $SQL = sprintf("SELECT * FROM share where uid =".$uid." and add_time >=".$start_time." and add_time<=".$end_time);
       return  $this->Db->getOneRow($SQL,false);
    }
    
  
    
    /**
    * 获取所有的分享数据
    */
    public function  getShareList(){
        return $this->Dao->select()->from('share')->exec(false);
    }
    
    /*
    * 增加一条用户领取记录
    */
    public function createShareUserTake($uid,$des,$coupon_id,$coupon_money,$share_id,$from_uid,$createUid){

      $id = $this->Dao->insert('share_user_take', '`uid`,`add_time`,`des`,`coupon_id`,`coupon_money`,`share_id`,`from_uid`,`create_share_uid`')
                        ->values(array($uid,time(),$des,$coupon_id,$coupon_money,$share_id,$from_uid,$createUid))->exec(false);
       return $id;
    }
    /*
    * 检查用户是否领取了该分享优惠
    */
    public function checkUserShareTake($share_uid,$uid){
    
         $c = $this->Dao->select('')->count('*')->from('share_user_take')->where("create_share_uid = '$share_uid'")->aw('uid ='.$uid)->getOne(false);
         return $c > 0;
    }
    
   
    
    
    /**
    * 查询用户分享记录 列表
    */
    public function getUserShareTakeList($query){
        
        $SQL = sprintf("SELECT * FROM share_user_take %s", $query);
        return $this->Db->query($SQL);
    }
    
    
}
