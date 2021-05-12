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
class mOrderDistribute extends Model {
	
	/**
	 * 获取配送详情
	 * @param string $where
	 * @param string $orderby
	 **/
	public function get_distribute_info($id){
		return $this->Dao->select()->from(TABLE_ORDER_DISTRIBUTE)->where('id='.$id)->getOneRow();
	}
    
    public function get_distribute_info_by_order($serial)
    {
        return $this->Dao->select()->from(TABLE_ORDER_DISTRIBUTE)->where('order_serial_no='.$serial)->getOneRow(false);
    }
    
    /**
     * 获取所有的配送单
     * @param string $where
     * @param string $orderby  
     **/
    public function get_distribute_list($where='',$orderby='add_time desc',$offset,$pageSize){
    	$this->loadModel('mUserAddress');
    	$this->loadModel('mOrder');
    	$list = array();
    	if($offset>=0){
	    	$list = $this->Dao->select()->from(TABLE_ORDER_DISTRIBUTE)->where($where)->orderby($orderby)->limit("$offset,$pageSize")->exec(false);
    	}else{
    		$list = $this->Dao->select()->from(TABLE_ORDER_DISTRIBUTE)->where($where)->orderby($orderby)->exec(false);
    	}
    	foreach ($list as $key => &$val){
    		$val['products'] = $this->mOrder->GetOrderDetailBySerialNo($val['order_serial_no']);
    		$val['address'] = $this->mUserAddress->get_user_address_by_id($val['address_id']);
    		$order_data = $this->mOrder->GetOrderInfoBySerialNo($val['order_serial_no']);
			if($order_data){
				$order_data['discount_amount'] = $order_data['order_amount']-$order_data['online_amount']-$order_data['balance_amount'];
			}
    		$val['order_data'] = $order_data;
    	}
    	return $list;
    }
    
    
    /**
     * 创建配送单
     * @param unknown $order_id
     * @param unknown $address_id
     * @param unknown $exp_time  
     **/
    public function create_distribute_list($order_serial_no,$address_id,$status,$exp_time){
    	$time =time();
    	return $this->Dao->insert(TABLE_ORDER_DISTRIBUTE,'order_serial_no,address_id,status,exp_time,add_time,update_time')
    				->values(array($order_serial_no,$address_id,$status,$exp_time,$time,$time))->exec(false);
    }
    
    /**
     * 根据订单id添加配送单
     * @param unknown $order_id  
     * */
    public function create_distribute_list_by_order_id($order_id){
    	$this->loadModel('mOrder');
    	$order_info = $this->mOrder->get_order_info_by_id($order_id);
    	$state = -1;
    	error_log('order_info===>'.json_encode($order_info));
    	if($order_info['status'] == 'payed'){
    		$order_serial_no = $order_info['serial_number'];
    		$address_id =  $order_info['address_id'];
    		$exp_time =  $order_info['exptime'];
    		$status = 'not_delievery';
    		$state = $this->create_distribute_list($order_serial_no,$address_id,$status,$exp_time);
    	}
    	return $state;
//     	return $order_info;
    	
    }
    
    
    /**
     * 更新发货的快递信息
     * @param unknown $id
     * @param unknown $distribute_type
     * @param unknown $courier  
     * */
    public function update_express_info($id,$status,$express_code,$courier,$operater_id){
    	error_log("id===>".$id.",status===>".$status.",express_code===>".$express_code.",courier===>".$courier);
    	$time =time();
    	return $this->Dao->update(TABLE_ORDER_DISTRIBUTE)->set(array(
    			'express_code' => $express_code,
    			'status' => $status,
    			'courier' => $courier,
    			'operater_id' => $operater_id,
    			'update_time' => $time
    	))->where('id='.$id)->exec(false);
    }

    

    /**
     * 更新优惠券的状态
     * @param unknown $id
     * @param unknown $status
     * @param unknown $operater_id
     * */
    public function update_distribute_status($id,$status,$operater_id = 0){
    	$time =time();
    	return $this->Dao->update(TABLE_ORDER_DISTRIBUTE)->set(array(
    			'status' => $status,
    			'operater_id' => $operater_id,
    			'update_time' => $time
    	))->where('id='.$id)->exec(false);
    }

	/**
	 * 更新所有的serial_no为完成
	 */
	public function distribute_reached($serial_no,$status,$operater_id = 0){
		$time =time();
		return $this->Dao->update(TABLE_ORDER_DISTRIBUTE)->set(array(
			'status' => $status,
			'operater_id' => $operater_id,
			'update_time' => $time
		))->where('order_serial_no='.$serial_no)->exec(false);
	}

    
}
