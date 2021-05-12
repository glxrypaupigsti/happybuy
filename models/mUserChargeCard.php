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

class mUserChargeCard extends Model{
	
	/**
	 * 说明： 获取单个用户充值卡信息
	 * 输入参数：查询条件user_charge_card_id
	 * 输出参数：  获取单个用户充值卡信息
	 */
	public function get_user_charge_card_info($id){
		return $this->Dao->select()->from(TABLE_SHOP_USER_CHARGE_CARD)->where('id',$id)->getOneRow();
	}
	
	/**
	 * 说明： 根据输入查询条件筛选有效用户充值卡
	 * 输入参数：查询条件
	 * 输出参数： 可用用户充值卡列表
	 */
	public function get_user_charge_card_list($where = null , $order_by = 'add_time desc'){
		$now = time();
		$list = $this->Dao->select()->from(TABLE_SHOP_USER_CHARGE_CARD)->where($where)->OrderBy($order_by)->exec();
		foreach($list as $key => &$val){
			$card_info = $this->Dao->select()->from(TABLE_SHOP_CHARGE_CARD)->where('id',$val['charge_card_id'])->getOneRow();
			$val['$card_info']= $card_info;
		}
		return $list;
	}
	
	
	/**
	 * 说明： 创建用户充值卡
	 * 输入参数：用户充值卡信息
	 * 输出参数： 用户充值卡id/false
	 */
	public function create_user_charge_card($charge_card_data){
		$time = time();
		return $this->Dao->insert(TABLE_SHOP_CHARGE_CARD,'client_uid,add_time,charge_card_id')
						 ->values(array(
						 		$charge_card_data['client_uid'],$time,$charge_card_data['charge_card_id']
						 ))->exec();
	}
	
	/**
	 * 说明： 根据用户充值卡标识删除用户充值卡
	 * 输入参数：用户充值卡标识
	 * 输出参数： 输出参数： true/false
	 */
	public function remove_charge_card($charge_card_id){
		return $this->Dao->delete()->from(TABLE_SHOP_CHARGE_CARD)->where('id='.$charge_card_id)->exec();
	}
	
	
	public function buy_charge_card(){
		
	}
	
	/**
	 * 说明： 通过charge_code来充值余额
	 * 输入参数：用户充值卡标识
	 * 输出参数： 输出参数： true/false
	 */
	public function charge_balance_by_code($charge_code,$uid){
		
		
		
		
	}
	
}