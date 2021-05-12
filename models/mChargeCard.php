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

class mChargeCard extends Model{
	
	/**
	 * 说明： 获取单个充值卡信息
	 * 输入参数：查询条件charge_card_id
	 * 输出参数：  获取单个充值卡信息
	 */
	public function get_charge_card_info($id){
		return $this->Dao->select()->from(TABLE_SHOP_CHARGE_CARD)->where('id',$id)->getOneRow();
	}
	
	/**
	 * 说明： 根据输入查询条件筛选有效充值卡
	 * 输入参数：查询条件
	 * 输出参数： 可用充值卡列表
	 */
	public function get_charge_card_list($where = null, $order_by = 'add_time desc'){
		$now = time();
		return $this->Dao->select()->from(TABLE_SHOP_CHARGE_CARD)->where($where)->OrderBy($order_by)->exec();
	}
	
	
	/**
	 * 说明： 创建充值卡
	 * 输入参数：充值卡信息
	 * 输出参数： 充值卡id/false
	 */
	public function create_charge_card($charge_card_data){
		$time = time();
		return $this->Dao->insert(TABLE_SHOP_CHARGE_CARD,'uid,add_time,serial_no,charge_code,amount,sale_price,is_delivered,is_activated,is_used')
						 ->values(array(
						 		$charge_card_data['uid'],$time,$charge_card_data['serial_no'],$charge_card_data['charge_code'],
						 		$charge_card_data['amount'],$charge_card_data['sale_price'],$charge_card_data['is_delivered'],
						 		$charge_card_data['is_activated'],$charge_card_data['is_used']						 		
						 ))->exec();
	}
	
	/**
	 * 说明： 根据充值卡标识删除充值卡
	 * 输入参数：充值卡标识
	 * 输出参数： 输出参数： true/false
	 */
	public function delete_charge_card($charge_card_id){
		return $this->Dao->delete()->from(TABLE_SHOP_CHARGE_CARD)->where('id='.$charge_card_id)->exec();
	}
	
	
	/**
	 * 说明： 更新充值卡属性
	 * 输入参数：充值卡标识，新属性信息
	 * 输出参数： true/false
	 */
	public function update_charge_card($charge_card_id,$new_data){
		return $this->Dao->update(TABLE_SHOP_CHARGE_CARD)->set(
				array(
					'amount' => $new_data['amount'],
					'sale_price' => $new_data['sale_price'],
					'is_delivered' => $new_data['is_delivered'],
					'is_used' => $new_data['is_used']
				)
		)->where('id='.$charge_card_id)->exec();
	} 
	
	/**
	 * 说明： 使用充值卡
	 * 输入参数：充值卡标识，新属性信息
	 * 输出参数： true/false
	 */
	public function change_charge_card_state($charge_card_id){
		return $this->Dao->update(TABLE_SHOP_CHARGE_CARD)->set(
				array(
						'is_delivered' => 1,
						'is_used' => 1
				)
		)->where('id',$charge_card_id)->exec();
	}
	
	/**
	 * 说明： 使用充值卡
	 * 输入参数：充值卡标识，新属性信息
	 * 输出参数： true/false
	 */
	public function use_charge_card($id,$use_state){
		return $this->Dao->update(TABLE_SHOP_CHARGE_CARD)->set(array(
						'is_used' => $use_state
				))->where('id='.$id)->exec();
	}
	
	/**
	 * 说明： 制卡
	 * 输入参数：充值卡标识，新属性信息
	 * 输出参数： true/false
	 */
	public function delivered_charge_card($id,$delivered_state){
		return $this->Dao->update(TABLE_SHOP_CHARGE_CARD)->set(
				array(
						'is_delivered' => $delivered_state
				)
		)->where('id='.$id)->exec();
	}
	
	/**
	 * 说明： 激活卡
	 * 输入参数：充值卡标识，新属性信息
	 * 输出参数： true/false
	 */
	public function activated_charge_card($id,$activated_state){
		return $this->Dao->update(TABLE_SHOP_CHARGE_CARD)->set(
				array(
						'is_activated' => $activated_state
				)
		)->where('id='.$id)->exec();
	}
	
	/**
	 * 说明： 生成充值卡密码
	 * 输出参数： true/false
	 */
	public function create_voucher_code(){
		$CODE_LEN = 12;
		
		$BYTE_LEN = 512 ;
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		
		if (function_exists('openssl_random_pseudo_bytes')) {
			$random_string = openssl_random_pseudo_bytes($BYTE_LEN);
		}else{
			$random_string = substr(str_shuffle(str_repeat($pool, 5)), 0, $BYTE_LEN);
		}
		
		$md5 = md5($random_string);
		$sha1 = sha1($md5); 
		$start = mt_rand(0, 25);
		$code = substr($sha1, $start, $CODE_LEN);
		return $code;
	}
	
	/**
	 * 说明： 根据充值卡密码查询充值卡信息
	 * 输出参数： true/false
	 */
	public function get_charge_card_by_code($charge_code){
		return $this->Dao->select()->from(TABLE_SHOP_CHARGE_CARD)->where('charge_code',$charge_code)->getOneRow();
	}
	
	
}