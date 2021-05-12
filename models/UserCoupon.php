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
class UserCoupon extends Model {
	
	
	public function get_user_coupon_info($uid,$coupon_id,$is_used = 0){
		
		return $this->Dao->select()->from(TABLE_USER_COUPON)->where('uid='.$uid)->aw('coupon_id='.$coupon_id)->aw('is_used='.$is_used)->getOneRow();
		
	}
	
	
	public function get_all_user_coupon($where,$orderby='id desc'){
		$this->loadModel('User');
		$this->loadModel('Coupons');
		$list = $this->Dao->select()->from(TABLE_USER_COUPON)->where($where)->orderby($orderby)->exec(false);
		$time = time();
		foreach ($list as $key => &$val){
			$val['user_info'] = $this->User->getUserInfoRaw($val['uid']);
			$coupon_info = $this->Coupons->get_coupon_info($val['coupon_id']);
			if($val['is_used'] == 1){
				$val['is_used_desc']  = '已使用';
			}else{
				$val['is_used_desc']  = '未使用';
			}
			if($coupon_info['effective_end']<$time){
				$val['expire_desc'] = '已过期';
			}else{
				$val['expire_desc'] = '正常';
			}
			$val['coupon_name'] = $coupon_info['coupon_name'];
			
			switch ($val['come_from']){
				case 'order':
					$val['come_from_desc'] = '下单奖励';
					break;
				case 'reg':
					$val['come_from_desc'] = '注册奖励';
					break;
				case 'share':
					$val['come_from_desc'] = '分享奖励';
					break;
				case 'system':
					$val['come_from_desc'] = '后台发放';
					break;
				default:
					$val['come_from_desc'] = '下单奖励';
					break;
			}
		}
		return $list;
	}
    
    /**
     * 获取用户已使用和未使用的优惠券
     * @param  $uid
     * @param number $is_used
     * @return array  
     * 
     */
    public function getUserCouponListByState($uid,$is_used = 0){
    	$list = $this->Dao->select()->from(TABLE_USER_COUPON)->where('uid='.$uid)->aw('is_used='.$is_used)->exec(false);
    	if(count($list)>0){
    		$this->loadModel('Coupons');
    		$time = time();
    		foreach ($list as $key => $val){
    			$coupon_info = $this->Coupons->get_coupon_info($val['coupon_id']);
    			if($coupon_info and $coupon_info['effective_end']>$time){
    				$val['effective_start'] = $coupon_info['effective_start'];
    				$val['effective_end'] = $coupon_info['effective_end'];
    				$val['coupon_name'] = $coupon_info['coupon_name'];
    				$val['coupon_value'] = $coupon_info['discount_val'];
					$val['discount_type'] = $coupon_info['discount_type'];
    				if($coupon_info['discount_type'] == 1){ //折扣比例
    					$val['coupon_unit'] = '%';
						$val['coupon_unit_desc'] = '折';
    				}else{   //折扣值
    					$val['coupon_unit'] = '￥';
    					$val['coupon_unit_desc'] = '元';
    				}
    				$user_coupon_list[] = $val;
    			}
    		}
    	}
    	return $user_coupon_list;
    }
    
    
    /**
     * 订单结算时候获取可用的优惠券
     * @param  $uid
     * @param number $is_used
     * @return array
     *
     */
    public function getAvailableUserCouponList($uid,$is_used = 0){
    	$list = $this->Dao->select()->from(TABLE_USER_COUPON)->where('uid='.$uid)->aw('is_used='.$is_used)->exec(false);
    	if(count($list)>0){
    		$this->loadModel('Coupons');
    		$time = time();
    		foreach ($list as $key => $val){
    			$coupon_info = $this->Coupons->get_coupon_info($val['coupon_id']);
    			if($coupon_info and $coupon_info['effective_end']>$time and $coupon_info['effective_start']<$time){
    				$val['effective_start'] = $coupon_info['effective_start'];
    				$val['effective_end'] = $coupon_info['effective_end'];
    				$val['coupon_name'] = $coupon_info['coupon_name'];
    				$val['coupon_value'] = $coupon_info['discount_val'];
    				$val['discount_type'] = $coupon_info['discount_type'];
					if($coupon_info['discount_type'] == 1){ //折扣比例
						$val['coupon_unit'] = '%';
						$val['coupon_unit_desc'] = '折';
					}else{   //折扣值
						$val['coupon_unit'] = '￥';
						$val['coupon_unit_desc'] = '元';
					}
    
    				$user_coupon_list[] = $val;
    			}
    		}
    	}
    	return $user_coupon_list;
    }
    
    
    
    /**
     * 获取用户已过期的优惠券
     * @param  $uid
     * @param number $is_used
     * @return array
     *
     */
    public function getUserExpiredCouponList($uid){
    	$list = $this->Dao->select()->from(TABLE_USER_COUPON)->where('uid='.$uid)->exec(false);
    	if(count($list)>0){
    		$this->loadModel('Coupons');
    		$time = time();
    		foreach ($list as $key => &$val){
    			$coupon_info = $this->Coupons->get_coupon_info($val['coupon_id']);
    			if($coupon_info and $coupon_info['effective_end']<$time){
    				$val['effective_start'] = $coupon_info['effective_start'];
    				$val['effective_end'] = $coupon_info['effective_end'];
    				$val['coupon_name'] = $coupon_info['coupon_name'];
    				$val['coupon_value'] = $coupon_info['discount_val'];
					$val['discount_type'] = $coupon_info['discount_type'];
					if($coupon_info['discount_type'] == 1){ //折扣比例
						$val['coupon_unit'] = '%';
						$val['coupon_unit_desc'] = '折';
					}else{   //折扣值
						$val['coupon_unit'] = '￥';
						$val['coupon_unit_desc'] = '元';
					}
    				
    				$user_coupon_list[] = $val;
    			}
    		}
    	}
    	return $user_coupon_list;
    }
    
    
    /**
     * 使用优惠券
     * @param type $verifed
     * @return type
     */
    public function useCoupon($uid,$coupon_id){
    	
    	$this->loadModel('Coupons');
    	$time = time();
        $coupon_info = $this->Coupons->get_coupon_info($coupon_id);


    	
    	if(!$coupon_info){ //优惠券不存在
    		return -2;
    	}
    	if($coupon_info['effective_end'] < $time){ //已过期
    		return -1;
    	}
    	
    	$user_coupon_info = $this->get_user_coupon_info($uid,$coupon_id);
    	
    	if(!$user_coupon_info){ //没有该类优惠券
    		return -3;
    	}
    	
    	if($user_coupon_info['is_used'] == 1){ //已经使用了
    		return -4;
    	}
    	
        if($coupon_info['coupon_type'] == 2){
       
            $coupon_info = $this->get_user_coupon_info($uid,$coupon_id,0);

          return $this->Dao->update(TABLE_USER_COUPON)->set(array(
                'is_used' => 1
          ))->where('uid',$uid)->aw('id='.$coupon_info['id'])->exec();
        }else{
         return $this->Dao->update(TABLE_USER_COUPON)->set(array(
                'is_used' => 1
          ))->where('uid',$uid)->aw('coupon_id='.$coupon_id)->exec();

        }
    	
    	
    }
    
    /**
     * 用户领取优惠券
     * @param type $verifed
     * @return type
     */
    public function insertUserCoupon($coupon_id, $uid=0, $allow_multi = false,$come_from = 'order'){
    	
    	$this->loadModel('Coupons');
    	$time = time();
    	$coupon_info = $this->Coupons->get_coupon_info($coupon_id);
    	if(!$coupon_info){
    		return -2;
    	}
        if (!$allow_multi) {
            $user_coupon_info = $this->Dao->select()->from(TABLE_USER_COUPON)->where('uid='.$uid)->aw('coupon_id='.$coupon_id)->getOneRow();
            
            if($user_coupon_info){ //判断是否已经领取了该类优惠券
                return -1;
            }
        }
    	
    	return $this->Dao->insert(TABLE_USER_COUPON,'uid,coupon_id,add_time,come_from')->values(array($uid,$coupon_id,$time,$come_from))->exec();
    }
    
  
    
    /**
     * 删除优惠条件列表
     * @param type $verifed
     * @return type
     */
    public function deleteUserCoupon($uid,$coupon_id){
    	return $this->Dao->delete()->from(TABLE_USER_COUPON)->where('uid='.$uid)->aw('coupon_id='.$coupon_id)->exec();
    }
    /**
     * 删除优惠条件列表
     * @param type $verifed
     * @return type
     */
    public function deleteUserCouponById($id){
    	return $this->Dao->delete()->from(TABLE_USER_COUPON)->where('id='.$id)->exec();
    }
    
    
    /**
     * 根据优惠券id删除优惠券记录
     * @param type $verifed
     * @return type
     */
    public function deleteUserCouponByCouponId($coupon_id){
    	return $this->Dao->delete()->from(TABLE_USER_COUPON)->where('coupon_id='.$coupon_id)->exec();
    }
    
    
    /**
     * 奖励优惠券
     * @param type $verifed
     * @return type
     */
    public function regAwardCoupon($coupon_id,$uid){
    	$this->loadModel('Coupons');
    	$time = time();
    	$coupon_info = $this->Coupons->get_coupon_info($coupon_id);
    	if(!$coupon_info){
    		return -2;
    	}
    	
    	if($coupon_info['available_start'] > $time){  //优惠券发放时间未到
    		return -6;
    	}
    	
    	if($coupon_info['available_end'] < $time){  //优惠券发放时间已过期
    		return -5;
    	}
    	
    	if($coupon_info['effective_end'] < $time){  //优惠券已过期
    		return -3; 
    	}

    	if($coupon_info['is_activated'] == 0){  //优惠券未激活
    		return -4;
    	}
    	 
    	return $this->Dao->insert(TABLE_USER_COUPON,'uid,coupon_id,add_time')->values(array($uid,$coupon_id,$time))->exec();
    }

    /**
     * 
     * @param unknown $uid  
     **/
    public function del_user_coupon_by_uid($uid){
    	return $this->Dao->delete()->from(TABLE_USER_COUPON)->where('uid='.$uid)->exec();
    }
    
    /**
     * 批量删除用户优惠券信息
     * @param unknown $ids
     **/
    public function batch_delete_user_coupon($ids){
    	return $this->Dao->delete()->from(TABLE_USER_COUPON)->where('id in('.$ids.')')->exec();
    }


    
}
