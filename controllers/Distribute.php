<?php

// 支付授权目录 112.124.44.172/wshop/
// 支付请求示例 index.php
// 支付回调URL http://112.124.44.172/wshop/?/Order/payment_callback
// 维权通知URL http://112.124.44.172/wshop/?/Service/safeguarding
// 告警通知URL http://112.124.44.172/wshop/?/Service/warning

/**
 * 配送类
 */
class Distribute extends Controller {
	
	const TPL = './views/wdminpage/';
	
	const STATUS_NOT_DELIEVERY = 'not_delievery';
	const STATUS_DELIEVERING = 'delievering';
	const STATUS_DELIEVERED = 'delievered';
	const STATUS_REACHED = 'reached';
	const STATUS_NOT_REACHED = 'not_reached';
	const STATUS_CANCEL = 'cancel';

    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        $this->loadModel('mOrderDistribute');
        $this->loadModel('WdminAdmin');
    }
    
    /**
     * ajax创建配送单
     */
    public function ajax_create_distribute_list($Q){
    	$order_id = $Q->order_id;
    	$state = $this->mOrderDistribute->create_distribute_list_by_order_id($order_id);
    	echo $state;
    }
    
    /**
     * 开始制作，至改变配送单的状态为配送中
     * @param unknown $Q  
     */
    public function begin_to_make($Q){
    	$id = $Q->id;
    	$operater_id = $this->WdminAdmin->getAdminIdFromCookie();
    	$status = self::STATUS_DELIEVERING;
    	echo $this->mOrderDistribute->update_distribute_status($id,$status,$operater_id);
    }
    
    /**
     * 发货
     */
    public function order_delievery(){
    	$this->loadModel('mOrder');
    	$id = $this->pPost('id');
    	$distribute_code = $this->pPost('distribute_code');
    	$courier = $this->pPost('courier');
    	if(empty($distribute_code)){
    		$this->echoMsg('-1','快递不能为空');
    		die(0);
    	}
    	
    	if(empty($courier)){
    		$this->echoMsg('-1','配送人员不能为空');
    		die(0);
    	}
    	$operater_id = $this->WdminAdmin->getAdminIdFromCookie();
    	//配货信息
    	$distribute_info = $this->mOrderDistribute->get_distribute_info($id);
    	//1、更改订单的状态为发货中，订单状态
    	$order_status = 'delivering';
    	error_log('distribute_info===>'.$this->toJson($distribute_info));
    	$this->mOrder->updateOrderStatusBySerialNo($distribute_info['order_serial_no'],$order_status);
    	//2、更新运送信息
    	$state = $this->mOrderDistribute->update_express_info($id,self::STATUS_DELIEVERED,$distribute_code,$courier,$operater_id);
    	if($state > 0){
	    	$this->echoMsg('1','发货成功');
    	}else{
    		$this->echoMsg('-1','发货异常,请联系管理员');
    	}
    }
    
    
    /**
     * 送达
     */
    public function delievery_reached($Q){
    	$this->loadModel('mOrder');
    	$id = $Q->id;
    	$distribute_info = $this->mOrderDistribute->get_distribute_info($id);
    	//1、更新配送状态为已送达
    	$operater_id = $this->WdminAdmin->getAdminIdFromCookie();
    	$this->mOrderDistribute->update_distribute_status($id,self::STATUS_REACHED,$operater_id);
    	//2、修改订单状态为已完成(received)
    	$order_status = 'received';
    	error_log('distribute_info===>'.$this->toJson($distribute_info));
    	$state = $this->mOrder->updateOrderStatusBySerialNo($distribute_info['order_serial_no'],$order_status);
    	if($state > 0){
    		$this->echoMsg('1','送达成功');
    	}else{
    		$this->echoMsg('-1','送达异常：'.$state);
    	}
    }
    
    /**
     * 取消配送单，以及未送达的配置
     */
    public function delievery_reset(){
    	$this->loadModel('mOrder');
    	$operater_id = $this->WdminAdmin->getAdminIdFromCookie();
    	$id = $this->pPost('id');
    	$exp_time = $this->pPost('exp_time');
    	$status = $this->pPost('status');
    	
    	if(empty($exp_time)){
    		$this->echoMsg(-1,'必须填入配送时间');
    		die(0);
    	}
    	
    	$distribute_info = $this->mOrderDistribute->get_distribute_info($id);
    	//1、更新配送状态为取消或者未送达
    	$this->mOrderDistribute->update_distribute_status($id,$status,$operater_id);
    	//2、重新创建一个配送单
    	$state  = self::STATUS_NOT_DELIEVERY;
    	$order_status  = 'payed';
    	if($status == 'not_reached'){ //未送达时候更改状态为配货中
    		$state  = self::STATUS_DELIEVERING;
    		$order_status  = 'delivering';
    	}
    	$this->mOrderDistribute->create_distribute_list($distribute_info['order_serial_no'],$distribute_info['address_id'],$state,$exp_time);
    	//3、更新订单的重新发送时间为新的时间
    	$this->mOrder->updateOrderExpTimeBySerialNo($distribute_info['order_serial_no'],$exp_time);
    	//更改订单的状态
    	$state = $this->mOrder->updateOrderStatusBySerialNo($distribute_info['order_serial_no'],$order_status);
    	
    	$this->echoMsg(1,'success');
    }
    
    /**
     * 重新设置发货时间 的弹出框
     */
    public function reset_express_time($Q){
    	$this->assign('distribute_id', $Q->id);
    	$this->assign('status', $Q->status);
    	$this->show(self::TPL.'distribute/reset_express_time.tpl');
    }
    
    
    /**
     * 发货的弹出框
     */
    public function ajax_delievery_order($Q){
    	//设置的快递信息
    	$expressCode = include dirname(__FILE__) . '/../config/express_code.php';
    	$expressCouries = include dirname(__FILE__) . '/../config/express_couries.php';
    	$selected_couries = explode(',',$this->settings['exp_couriers']);
    	$selected_exps = explode(',',$this->settings['expcompany']);
    	$couries = array();
    	$exps = array();
    	foreach ($selected_couries as $skey => $sval){
    		foreach ($expressCouries as $key => $val){
    			if($sval == $key){
    				$couries[]=array(
    					'key' => $sval,
    					'value' => $val
    				);
    				break;
    			}
    		}
    	}
    	
    	foreach ($selected_exps as $sekey => $seval){
    		foreach ($expressCode as $ekey => $eval){
    			if($seval == $ekey){
    				$exps[]=array(
    					'key' => $seval,
    					'value' => $eval
    				);
    				break;
    			}
    		}
    	}
    	
    	$this->assign('distribute_id', $Q->id);
    	$this->assign('status', $Q->status);
    	$this->assign('couries', $couries);
    	$this->assign('exps', $exps);
    	$this->show(self::TPL.'distribute/delivery_order.tpl');
    }
    
    
    public function ajax_load_distribute_list($Q){
    	$this->loadModel('mOrderDistribute');
    	$status = $Q->status;
    	if(!$status){ //如果没有则默认选择未
    		$status = 'not_delievery';
    	}
    	$day = $Q->day;
    	if(!$day){ //如果没有则默认选择今天
    		$day = 0;
    	}
    	
    	switch($day){
    		case 366 : //所有
    			break;
    		case 7 :
    			//数据库中的配送时间格式为2015-12-01 14:00-15:00
    			$where[] = 'YEARWEEK(left(exp_time,10)) = YEARWEEK(now())';
    			break;
    		case 30 :
    			//数据库中的配送时间格式为2015-12-01 14:00-15:00
    			$where[] = 'left(exp_time,7) = date_format(now(),"%Y-%m")';
    			break;
    		default :
    			$where[] = 'TO_DAYS(left(exp_time,10))-TO_DAYS(now()) = ' .$day;
    			break;
    	}
    	
    	if($status !== 'all'){
    		$where[] = 'status="'.$status.'"';
    	}
    	
    	$pageSize = 10;
    	
    	if($Q->pageSize){
    		$pageSize = $Q->pageSize;
    	}
    	
    	//增加限制机制，防止每页取的数据过多
    	if($pageSize>10){
    		$pageSize = 10;
    	}
    	
    	$page = $Q->page;
    	if(!$page){
    		$page = 1;
    	}
    	$offset = ($page - 1) * $pageSize;
    	
    	//查询的条件
    	$condition = '';
    	$count_sql = 'select count(1) from ' .TABLE_ORDER_DISTRIBUTE;
    	if(count($where)>0){
    		$condition = implode(' and ',$where);
    		$count_sql = $count_sql.' where '.$condition;
    	}
//     	$list = $this->mOrderDistribute->get_distribute_list(implode(' and ',$condition));
    	$list = $this->mOrderDistribute->get_distribute_list($condition,'add_time desc',$offset,$pageSize);
    	//获取所有的总数
    	$count = $this->Db->getOne($count_sql);
    	
    	$data = array(
    			'day' => $day,  //日期
    			'status' => $status, //状态
    			'list' => $list,  //列表
    			'total' => $count
    	);
    	$this->echoJson($data);
    }

}
