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

class UserChargeLog extends Model{
	
	/**
	 * 说明： 获取单个用户充值卡信息
	 * 输入参数：查询条件user_charge_log_id
	 * 输出参数：  获取单个用户充值卡信息
	 */
	public function get_user_charge_log($id){
		return $this->Dao->select()->from(TABLE_USER_CHARGE_LOG)->where('id',$id)->getOneRow();
	}
	
	/**
	 * 说明： 根据输入查询条件筛选有效用户充值卡
	 * 输入参数：查询条件
	 * 输出参数： 可用用户充值卡列表
	 */
	public function get_user_charge_log_list($where = null , $order_by = 'charge_time desc'){
		$now = time();
		$this->loadModel('User');
		$list = $this->Dao->select()->from(TABLE_USER_CHARGE_LOG)->where($where)->OrderBy($order_by)->exec();
		foreach($list as $key => &$val){
			$user_info = $this->User->getUserInfoRaw($val['client_uid']);
			$val['user_name']= $user_info['client_nickname'];
			$val['charge_type_desc']= $this->get_charge_type_name_by_type($val['charge_type']);
			$val['charge_time_format'] = date('Y-m-d H:i:s',$val['charge_time']);
		}
		return $list;
	}
	
	/**
	 * 说明： 创建用户充值卡
	 * 输入参数：用户充值卡信息
	 * 输出参数： 用户充值卡id/false
	 */
	public function create_user_charge_log($charge_log_data){
		$time = time();
		return $this->Dao->insert(TABLE_USER_CHARGE_LOG,'client_uid,charge_type,amount,pay_amount,charge_time')
						 ->values(array(
						 		$charge_log_data['client_uid'],$charge_log_data['charge_type'],$charge_log_data['amount'],$charge_log_data['pay_amount'],$time
						 ))->exec();
	}
	
	/**
	 * 说明： 根据用户充值卡标识删除用户充值卡
	 * 输入参数：用户充值卡标识
	 * 输出参数： 输出参数： true/false
	 */
	public function remove_charge_log($charge_log_id){
		return $this->Dao->delete()->from(TABLE_USER_CHARGE_LOG)->where('id='.$charge_log_id)->exec();
	}
	
	/**
	 * 根据充值类型获取充值方式的描述信息
	 * @param unknown $type
	 * @return string  
	 */
	public function get_charge_type_name_by_type($type){
		$type_name = '';
		switch ($type){
			case 'charge_code' :
				$type_name = '充值卡';
				break;
			case 'wx_pay' :
				$type_name = '微信支付';
				break;
			case 'balance' :
				$type_name = '余额支付';
				break;
		}
		return $type_name;
	}
	
	
}