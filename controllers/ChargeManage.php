<?php

// 支付授权目录 112.124.44.172/wshop/
// 支付请求示例 index.php
// 支付回调URL http://112.124.44.172/wshop/?/Order/payment_callback
// 维权通知URL http://112.124.44.172/wshop/?/Service/safeguarding
// 告警通知URL http://112.124.44.172/wshop/?/Service/warning

/**
 * 充值管理类
 */
class ChargeManage extends Controller {
	
	const TPL = './views/wdminpage/';

    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        $this->loadModel('mChargeCard');
        $this->loadModel('mUserChargeCard');
        
    }
    
    /**
     * 编辑充值卡
     * @return <float>
     */
    public function edit_charge_card($Q){
    	if ($Q->id > 0) {
    		$charge_card_info = $this->mChargeCard->get_charge_card_info($Q->id);
    		$this->assign('charge_card', $charge_card_info);
    	}
    	$this->show(self::TPL . 'charge/edit_charge_card.tpl');
    }

    
    /**
     * 保存充值卡
     * @return <float>
     */
    public function save_charge_card(){
    	$id = intVal($this->post('id'));
    	$this->loadModel('WdminAdmin');
    	$adminId = $this->WdminAdmin->getAdminIdFromCookie();
    	if($id > 0){
    		echo $this->mChargeCard->update_charge_card($id,$_POST);
    	}else{
    		$num = intVal($this->post('num'));
            // TODO: as only 3 digits is used to distiguish each card in one batch
            // so we need to check if request $num > 999
            
            // create common 5 digits serial prefix for this batch based on time
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $set_36 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $set_62 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            $current_time = time();
            $y = intval(date('y', $current_time));
            $m = intval(date('m', $current_time));
            $d = intval(date('j', $current_time));
            $h = intval(date('G', $current_time));
            $min = intval(date('i', $current_time));
            $s = intval(date('s', $current_time));
            $prefix = $chars[$m] . $set_36[$d] . $set_36[$h] . $set_62[$min] . $set_62[$s];
            
            // create code
			for($i=0;$i<$num;$i++){
                $serial = sprintf("%03d", $i);
                $serial = $prefix . $serial;
				$charge_code = $this->mChargeCard->create_voucher_code();
                $_POST['serial_no'] = $serial;
				$_POST['charge_code'] = $charge_code;
				$_POST['uid'] = $adminId ;
				$_POST['is_used'] = 0 ;
                $_POST['is_activated'] = 0 ;
				$_POST['is_delivered'] = 0 ;
				$this->mChargeCard->create_charge_card($_POST);
			}
			echo 1;    		
    	}
    	
    }
    
    /**
     * 删除充值卡
     * @return <float>
     */
    public function delete_charge_card(){
    	$this->loadModel('mChargeCard');
    	$id = $this->post('id');
    	echo $this->mChargeCard->delete_charge_card($id);
    }
    
    /**
     * 制卡操作
     * @return <float>
     */
    public function deliever_card(){
    	$this->loadModel('mChargeCard');
    	$id = $this->post('id');
    	$state = 1;
    	echo $this->mChargeCard->delivered_charge_card($id,$state);
    }
    
    /**
     * 激活操作
     * @return <float>
     */
    public function activated_card(){
    	$this->loadModel('mChargeCard');
    	$id = $this->post('id');
    	$state = 1;
    	echo $this->mChargeCard->activated_charge_card($id,$state);
    }
    
    /**
     * 根据输入的charge_code来充值
     * @return <float>
     */
    public function charge_by_charge_code($Q){
//     	$charge_code = $this->pPost('charge_code');
		$this->loadModel('UserChargeLog');
    	$charge_code = $Q->charge_code;
    	if(empty($charge_code)){
    		return $this->echoMsg(-1,'请输入充值卡密');
    	}
    	$this->loadModel('User');
    	$user_open_id = $this->getOpenId();
//     	$user_open_id = 'oalpuuEWUPFARit-_0_qJJwjoI3k';
		$uid = $this->User->getUidByOpenId($user_open_id);   
		
		$user_info = $this->User->getUserInfoRaw($uid);
		error_log('userinfo===>'.json_encode($user_info));
		if(!$user_info){
			return $this->echoMsg(-1,'用户不存在!');
		}	
    	//判断充值卡是否存在
		$charge_card_info = $this->mChargeCard->get_charge_card_by_code($charge_code);
    	if(!$charge_card_info){
    		return $this->echoMsg(-1,'该充值卡不存在，请确认充值卡密是否正确');
    	}
    	

    	if($charge_card_info['is_activated'] == 0){
    		return $this->echoMsg(-1,'该充值卡还未激活');
    	}
    	
    	if($charge_card_info['is_used']){
    		return $this->echoMsg(-1,'该充值卡已经被使用');
    	}
    	
    	$origin_money = $user_info['client_money'];
    	//面额,以分单位
    	$amount = $origin_money + $charge_card_info['amount']/100;
    	//更新充值卡的状态为使用
    	$this->mChargeCard->change_charge_card_state($charge_card_info['id']);
    	//更新用户的余额 
    	$this->User->updateUserMoneyByOpenId($user_open_id,$amount);
    	//插入重置记录
    	$chage_log_data = array(
    		'client_uid' => intval($user_info['client_id']),
    		'charge_type' => 'charge_code',
    		'amount' => $charge_card_info['amount'],
    		'pay_amount' => $charge_card_info['sale_price']
    	);
    	$this->UserChargeLog->create_user_charge_log($chage_log_data);
    	return $this->echoMsg(1,'充值成功');
    }
    
    public function delete_charge_log(){
    	$this->loadModel('UserChargeLog');
    	$id = $this->post('id');
    	echo $this->UserChargeLog->remove_charge_log($id);
    }
    
    
     /**
     * 我的充值页面
     */
  
    public function charge_card() {
        $this->assign('title', '充值卡充值');
        $this->show('./uc/charge_card.tpl');
    }

}