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
class mShareSetting extends Model {

	 public static $user_share_coupon_id = 'user_share_coupon_id';
	 public static $user_share_count = 'user_share_count';
	 public static $order_share_percent = 'order_share_percent';

    public function getShareSetting($query){
         $SQL = sprintf("SELECT * FROM share_setting %s", $query);
         error_log("==============SQL========".$SQL);
       	return  $this->Db->getOneRow($SQL);
    }
    
    public function updateShareSetting($id,$data){
        $mSql = sprintf("UPDATE `share_setting` SET `value_m` = $data WHERE `key_m` = '$id';");
        
        return $this->Db->query($mSql);
    } 
    
    
}
