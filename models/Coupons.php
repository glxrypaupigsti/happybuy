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

class Coupons extends Model{
	
	/**
	 * 说明： 获取单个优惠券信息
	 * 输入参数：查询条件coupon_id
	 * 输出参数：  获取单个优惠券信息
	 */
	public function get_coupon_info($id){
		return $this->Dao->select()->from(TABLE_SHOP_COUPONS)->where('id='.$id)->getOneRow();
	}
	
	/**
	 * 说明： 根据输入查询条件筛选有效优惠券
	 * 输入参数：查询条件
	 * 输出参数： 可用优惠券列表
	 */
	public function get_coupons_list($effective_start, $effective_end, $where, $order_by){
		$now = time();
		return $this->Dao->select()->from(TABLE_SHOP_COUPONS)
						 ->where('effective_end>'.$effective_start)
						 ->aw('effective_end<'.$effective_end)
						 ->exec();
	}
	
	/**
	 * 说明： 获取当前时间下所有的激活的优惠券
	 * 输入参数：查询条件
	 * 输出参数： 可用优惠券列表
	 */
	public function get_current_available_coupon_list($time){
		return $coupon_list =  $this->Dao->select()->from(TABLE_SHOP_COUPONS)
									->where('effective_start<'.$time)
									->aw('effective_end>'.$time)
									->aw('coupon_type = 0')
									->aw('(coupon_stock > 0 or coupon_stock < -1)')
									->aw('is_activated = 1')
									->exec();
	}
	
	/**
	 * 说明： 根据coupon_type查询所有的优惠券信息
	 * 输入参数：查询条件
	 * 输出参数： 可用优惠券列表
	 */
	public function get_coupon_list_by_type($coupon_type,$orderby='id asc'){
		return $this->Dao->select()->from(TABLE_SHOP_COUPONS)
					->where('coupon_type='.$coupon_type)
					->aw('effective_end >'.time())
					->aw('is_activated=1')
					->orderby($orderby)
					->exec();
		
	}
	
	/**
	 * 说明： 根据输入条件获取商品优惠券
	 * 输入参数：查询条件
	 * 输出参数： 可用的商品优惠券列表
	 */
	public function get_avaliable_coupons_for_product($product_info,$user_info,$time){
		$this->loadModel('Coupons');
		$coupon_list = $this->get_current_available_coupon_list($time);
		error_log("coupon list ===>".json_encode($coupon_list));
		$product_coupon_list = array();
		if(count($coupon_list)>0){
			foreach($coupon_list as $key => $val){
				$passed = false;
				$applied = json_decode($val['applied'], true);
				error_log("applied ===>".json_encode($applied['products']));
				//查看是否有应用于分类的优惠券
				if(!empty($applied['categorys'])){
					error_log("current is cateory coupon");
					$categorys = array();
					foreach ($applied['categorys'] as $catkey => $cat){
						if(!empty($cat['id'])){
							$categorys[] = intval($cat['id']);
						}
					}
					if(in_array($product_info['product_cat'],$categorys)){
						$passed = true;
					}
					
				}
				
				//查看是否有应用于商品的优惠券
				if(!empty($applied['products'])){
					error_log("current is product coupon");
					$products = array();
					foreach ($applied['products'] as $pkey => $p){
						if(!empty($p['id'])){
							$products[] = intval($p['id']);
						}
					}
					
					error_log("product_id is : ".$product_info['product_id']);
					error_log("product_ids is : ".json_encode($products));
					if(in_array($product_info['product_id'] , $products)){
						$passed = true;
						error_log($product_info['product_id']."is in ".json_encode($products));
					}
					
				}
				
				if (!$passed){
					continue;
				}
				// b. check if $uid meets terms
				$terms = json_decode($val['coupon_terms'],true);
				foreach ($terms AS $tk => $this_term) {
					$passed = $this->check_product_term($this_term, $product_info, $user_info);
					if (!$passed){
						break;
					}
				}
				// c. add this coupon
				if ($passed) {
					$product_coupon_list[] = $val;
				}
			}
		}
		return $product_coupon_list;
	}
	
	
	/**
	 * 说明： 验证商品和用户信息是否符合优惠券使用条件
	 * 输入参数：使用条件，商品和用户信息
	 * 输出参数： true/false
	 */
	public function check_product_term($term, $product_info, $user_info){
			$passed = false;
			error_log("check_product_term ===>".$term['table'].".".$term['column']);
			// only check if user matches the term
			switch($term['table'].".".$term['column']) {
				case 'client.client_credit':  //用户积分条件,比较积分和限定值
					$origin_value = intval($user_info['client_credit']);
					break;
				case 'client.client_level': //用户积分条件
					$origin_value = intval($user_info['client_level']);
					break;
				case 'product_info.selected_amount':  //商品原价满减条件,单位转化为分
					$origin_value = 100 * $product_info['market_price'];
					break;
				case 'product_info.selected_mod_amount': //商品没满减条件,单位转化为分
					$origin_value = 100*$product_info['market_price'];
					break;
				case 'product_info.selected_quantity': //商品数量满M送y,单位转化为分
					$origin_value = $product_info['product_quantity'];
					break;
			}
			error_log("origin_value ===>".$origin_value.",dest_value===>".$dest_value);
			$dest_value = $term['value'];
			$operate = $term['operate'];
			$passed = $this->get_compare_result($operate,$origin_value,$dest_value);
			return $passed;
	}
	
	/**
	 * 说明： 根据输入条件获取订单优惠券
	 * 输入参数：查询条件
	 * 输出参数： 订单优惠券列表
	 */
	public function get_avaliable_coupons_for_order($time, $uid,$select_coupon_type=0){
		$this->loadModel('UserCoupon');
		$this->loadModel('Carts');
		if($select_coupon_type == 0){  //只查询订单全
			// 		$order_info = $this->Dao->select()->from(TABLE_ORDERS)->where('order_id='.$order_id)->getOneRow();
			$user_info = $this->Dao->select()->from(TABLE_USER)->where('client_id',$uid)->getOneRow();
			//查询适用于订单类的优惠券
			$coupon_list =  $this->Dao->select()->from(TABLE_SHOP_COUPONS)
			->where('effective_start<'.$time)
			->aw('effective_end>'.$time)
			->aw('coupon_type = 1')
			->aw('(coupon_stock > 0 or coupon_stock < -1)')
			->aw('is_activated = 1')
			->orderby('discount_val desc')
			->exec();
			
			
			$order_amount = $this->Carts->calc_cart_amount($uid);
			error_log("order_amount ===>".$order_amount);
			
			
			$coupons = array();
			foreach ($coupon_list AS $key => $val) {
				$terms = json_decode($val['coupon_terms'],true);
				foreach ($terms AS $tk => $this_term) {
					error_log("this_term ===>".json_encode($this_term));
					$passed = $this->check_order_term($this_term, $order_amount, $user_info);
					if (!$passed) break;
				}
				if ($passed) {
					$coupons[] = $val;
				}
			}

			return $coupons;
		}else{  //只查询用户券
			//计算用户的优惠券
// 			$coupons = $this->UserCoupon->getUserCouponListByState($uid,0);
			$coupons = $this->UserCoupon->getAvailableUserCouponList($uid,0);
			if(empty($coupons)){
				$coupons = "";
			}
			return $coupons;
		}

	}
	
	
	
	
	
	/**
	 * 说明：验证订单和用户信息是否符合订单优惠券使用条件
	 * 输入参数：优惠券使用条件，订单、用户信息
	 * 输出参数： true/false
	 */
	public function check_order_term($term, $order_amount, $user_info){
		error_log("goto check_order_term method");
		$passed = false;
		// only check if user matches the term
		switch($term['table'].".".$term['column']) {
			case 'client.client_credit':  //用户积分条件,比较积分和限定值
				$origin_value = $user_info['client_credit'];
				break;
			case 'client.client_level': //用户积分条件
				$origin_value = $user_info['client_level'];
				break;
			case 'order.order_amount':  //订单总价
				$origin_value = 100*$order_amount;
				break;
			case 'order.selected_amount': //订单满减
				$origin_value = 100*$order_amount;
				break;
			case 'order.selected_mod_amount': //订单每满减
				$origin_value = 100*$order_amount;
				break;
		}
		$dest_value = $term['value'];
		$operate = $term['operate'];
		error_log("origin_value===> ".$origin_value.",dest_value===>".$dest_value);
		$passed = $this->get_compare_result($operate,$origin_value,$dest_value);
		return $passed;
	}
	
	/**
	 * 说明： 优惠券数量减1
	 * 输入参数：优惠券id
	 
	 * 输出参数： true/false
	 */
	public function dec_coupon_stock_left($coupon_id,$coupon_stock){
		return $this->Dao->update(TABLE_SHOP_COUPONS)->set(array(
				'coupon_stock_left' => $coupon_stock
		))->where('id='.$coupon_id)->exec();
	}
	/**
	 * 说明： 优惠券数量减1
	 * 输入参数：优惠券id
	 * 输出参数： true/false
	 */
	public function dec_coupon_stock($coupon_id){
		return $this->Dao->update(TABLE_SHOP_COUPONS)->set(array(
				'coupon_stock_left' => Dao::VALUE_MINUS
		))->where('id='.$coupon_id)->exec();
	}
	
	/**
	 * 说明： 优惠券数量加1
	 * 输入参数：优惠券id
	 * 输出参数： true/false
	 **/
	public function inc_coupon_stock($coupon_id){
		return $this->Dao->update(TABLE_SHOP_COUPONS)->set(
				array('coupon_stock_left' => Dao::VALUE_PLUS)
		)->where('id='.$coupon_id)->exec();
	}
	
	/**
	 * 说明： 更改优惠券库存数量
	 * 输入参数：优惠券id，库存数
	 * 输出参数： true/false
	 */
	public function update_coupon_stock($coupon_id,$new_stock){
		return $this->Dao->update(TABLE_SHOP_COUPONS)->set(array(
				'coupon_stock' => $new_stock
		))->where('id='.$coupon_id)->exec();
	}
	
	/**
	 * 说明： 创建优惠券
	 * 输入参数：优惠券信息
	 * 输出参数： 优惠券id/false
	 */
	public function create_coupon($coupon_data){
		$columns = 'coupon_type,coupon_name,coupon_detail,coupon_cover,uid,available_start,available_end,add_time,update_time,effective_start,effective_end,discount_type,discount_val,applied,coupon_terms,coupon_stock,coupon_stock_left,coupon_limit,bundled,is_activated,coupon_log';
		$time = time();
		$data = array(
				$coupon_data['coupon_type'],$coupon_data['coupon_name'],$coupon_data['coupon_detail'],
				$coupon_data['coupon_cover'],$coupon_data['uid'],$coupon_data['available_start'],
				$coupon_data['available_end'],$time,$time,
				$coupon_data['effective_start'],$coupon_data['effective_end'],$coupon_data['discount_type'],
				$coupon_data['discount_val'],$coupon_data['applied'],$coupon_data['coupon_terms'],
				$coupon_data['coupon_stock'],$coupon_data['coupon_stock'],$coupon_data['coupon_limit'],
				$coupon_data['bundled'],0,$coupon_data['coupon_log']
				
		);
		
		return $this->Dao->insert(TABLE_SHOP_COUPONS,$columns)->values($data)->exec();
	}
	
	/**
	 * 说明： 根据优惠券标识删除优惠券
	 * 输入参数：优惠券标识
	 * 输出参数： 输出参数： true/false
	 */
	public function remove_coupon($coupon_id){
		return $this->Dao->delete()->from(TABLE_SHOP_COUPONS)->where('id='.$coupon_id)->exec();
	}
	
	
	
	/**
	 * 说明： 更新优惠券属性
	 * 输入参数：优惠券标识，新属性信息
	 * 输出参数： true/false
	 */
	public function update_coupon($coupon_id,$coupon_data){
		$time = time();
		return $this->Dao->update(TABLE_SHOP_COUPONS)->set(
				array(
					'coupon_type' => $coupon_data['coupon_type'],
					'coupon_name' => $coupon_data['coupon_name'],
					'coupon_detail' => $coupon_data['coupon_detail'],
					'coupon_cover' => $coupon_data['coupon_cover'],
					'uid' => $coupon_data['uid'],
					'available_start' => $coupon_data['available_start'],
					'available_end' => $coupon_data['available_end'],
					'update_time' => $time,
					'effective_start' => $coupon_data['effective_start'],
					'effective_end' => $coupon_data['effective_end'],
					'discount_type' => $coupon_data['discount_type'],
					'discount_val' => $coupon_data['discount_val'],
					'applied' => $coupon_data['applied'],
					'coupon_terms' => $coupon_data['coupon_terms'],
					'coupon_stock' => $coupon_data['coupon_stock'],
					'bundled' => $coupon_data['bundled'],
					'coupon_limit' => $coupon_data['coupon_limit'],
					'is_activated' => 0,
					'coupon_log' => $coupon_data['coupon_log']
				)
		)->where('id='.$coupon_id)->exec();
	} 
	
	
	/** 
     * 删除优惠券信息 
     */
    public function delete_coupon($Q){
    	$this->loadModel('UserCoupon');
    	$id = $Q->id;
    	$coupon_info = $this->Coupons->get_coupon_info($id);
    	if(!$coupon_info){
    		$this->echoMsg(-1,"该优惠券不存在");
    		return;
    	}
    	//删除优惠券信息
		//删除优惠券的图片信息
		$this->Coupons->remove_coupon($id);
    	//删除用户优惠券中的所有该类优惠券
    	$this->UserCoupon->deleteUserCouponByCouponId($id);
    	echo 1; 
    }
    
	
	
	/** 
	 * 激活优惠券 
	 */
	public function activate_coupon($coupon_id){
		return $this->Dao->update(TABLE_SHOP_COUPONS)->set(array(
				'is_activated' => 1
		))->where('id='.$coupon_id)->exec();
	}
	
	/**
	 * 使用优惠券
	 * 
	 */
	public function use_selected_coupon($coupon_info,$uid,$time){
		if(!$coupon_info){
			return -7;
		}
		
		$this->loadModel('UserCoupon');
		$coupon_type = $coupon_info['coupon_type'];
		$coupon_id = $coupon_info['id'];
		
		$state = 1;
		if($coupon_type == 2){ //用户券
			error_log("=====================current is user coupon=======================");
			$user_coupon = $this->UserCoupon->get_user_coupon_info($uid,$coupon_id);
			if($user_coupon){ 
				error_log("use have this coupon");
				//用户券直接更改为使用
				$state = $this->UserCoupon->useCoupon($uid,$coupon_id);
			}else{
				$state = -2; //用户没有该优惠券
			}
			error_log("couponId =  $coupon_id after use coupon ,state is ============>".$state);
			
		}else{ //订单券或者商品券
			error_log("=====================current is order or product coupon=======================");
			$coupon_stock_left = $coupon_info['coupon_stock_left'];
			if($coupon_info['effective_end'] < $time){
				return -1;
			}
			error_log('coupon_stock_left');
			if($coupon_stock_left > 0){ //只有库存大于0时候才减库存
				error_log("current coupon_stock_left is big then zero ============>");
				$coupon_stock_left = $coupon_stock_left - 1;
				$state = $this->dec_coupon_stock_left($coupon_id,$coupon_stock_left);
				error_log("after use coupon ,state is ============>".$state);
			}
		}
		return $state;
		
	} 
	
	/** 
	 * 获取可用分享优惠券列表 
	 */
	public function get_shared_coupon_list(){
		$list = array();
		$sharedCouponIds = $this->Dao->select("value")->from('wshop_settings')->where("`key` = 'user_share_coupons'")->getOne();
		if(!empty($sharedCouponIds)){
			$list = $this->Dao->select()->from(TABLE_SHOP_COUPONS)->where('id in ('.$sharedCouponIds.')')->exec(false);
		}
		return $list;
	}

	
	public function get_compare_result($operate,$origin_value,$dest_value){
		$origin_value = $origin_value;
		switch ($operate){
			case '>' :
				$result  = $origin_value > $dest_value;
				break;
			case '>=' :
				$result  = $origin_value >= $dest_value;
				break;
			case '<' :
				$result  = $origin_value < $dest_value;
				break;
			case '<=' :
				$result  = $origin_value <= $dest_value;
				break;
		}
		return $result;
	}
	
	
}
