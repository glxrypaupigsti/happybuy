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
class Carts extends Model {

    const MANT_ADD = '+';
    const MANT_DIS = '-';
	
	
	
	/**
	 *说明： 修改购物车的商品
	 */
	 public function update_cart_product($uid,$product_id,$quantity = 1,$spec_id){
	 
	     	 
	       $this->remove_product($uid,$product_id,$spec_id);
	     	     
	 	   return   $this->Dao->insert("shop_cart", '`uid`,`product_id`,`product_quantity`,`add_time`,`spec_id`')->values(array($uid, $product_id, $quantity, time(),$spec_id))->exec();
	 
	 }
	 
	 /**
	 * 检查购物是否存在某个商品
	 */
	 public function checkProductExt($product_id,$uid,$spec_id ){
	 
	    $ret = $this->Db->query("SELECT COUNT(*) AS count FROM `shop_cart` WHERE `product_id` = '$product_id'  and `spec_id` = $spec_id and `uid` = $uid;");
        return $ret[0]['count'] > 0;
	 }
	
	/**
	*删除购物车
	*/
	public function del_cart($uid){
	
	    return $this->Dao->delete()->from(TABLE_SHOP_CART)->where("uid=" . $uid)->exec();
	
	}
	
	/**
	 *根据商品id删除购物车中的商品
	 */
	public function del_cart_product_by_product_id($product_id){
		return $this->Dao->delete()->from(TABLE_SHOP_CART)->where("product_id=" . $product_id)->exec();
	}
	
	
	/**
	 * 说明： 删除一个商品
	 * 输入参数：用户标识，商品标识
	 * 输出参数： true/false
	 */
	public function remove_product($uid, $product_id,$spec_id){
		return $this->Dao->delete()->from(TABLE_SHOP_CART)->where("uid=" . $uid)->aw('product_id='.$product_id)->aw('spec_id='.$spec_id)->exec();
	}
	
	/**
	 * 说明： 更改商品数量
	 * 输入参数：用户标识，商品标识，数量
	 * 输出参数： true/false
	 */
	public function change_product_quantity($uid,$product_id,$quantity){
		return $this->Dao->update(TABLE_SHOP_CART)->set(array(
				'quantity' => $quantity
		))->where("uid=" . $uid)->aw('product_id='.$product_id)->exec();
	}
	
	/**
	 * 说明： 设置商品关联的优惠券
	 * 输入参数：用户标识，商品标识，优惠券标识
	 * 输出参数： true/false
	 */
	public function use_coupon_with_product($uid,$product_id,$coupon_id){
		return $this->Dao->update(TABLE_SHOP_CART)->set(array(
				'use_coupon' => $coupon_id
		))->where("uid=" . $uid)->aw('product_id='.$product_id)->exec();
	}
	
	/**
	 * 说明： 获取用户名下购物车中的所有商品
	 * 输入参数：用户标识
	 * 输出参数： 商品列表/false
	 */
	public function get_cart_products($uid){
		 $products = $this->Dao->select()->from(TABLE_SHOP_CART)->alias('od')
                            ->leftJoin(TABLE_PRODUCTS)->alias('po')
                            ->on('po.product_id = od.product_id')
                            ->where("od.uid = $uid")->exec(false);
         return $products;
	}
	
	/**
	 * 说明： 获取购物车中的所有优惠券
	 * 输入参数：用户标识
	 * 输出参数： 优惠券列表/false
	 */
	public function get_coupons($uid){
		return $this->Dao->select()->from(TABLE_SHOP_CART)->where('uid='.$uid)->exec();
	}
	
	
	
	
	
	/**
	 * 说明：根据购物车内商品数量和优惠券等，计算购物车金额
	 * 输入参数：用户标识
	 * 输出参数： float/false
	 */
	public function calc_cart_amount($uid){
		$this->loadModel('mProductSpec');
		$this->loadModel('Coupons');
		$this->loadModel('mOrder');
		$product_list = $this->get_cart_products($uid);
		$product_ids = array();
		$total_amount = 0;
		foreach ($product_list as $key => $val){
			///获取商品对应的价格
// 			$product_info = $this->Product->get_simple_product_info($val['product_id']);
			//获取商品的规格信息
			$product_spec_info = $this->mProductSpec->get_spec_sale_price($val['spec_id']);
			$price = $product_spec_info['sale_price'];
			error_log("product id : " .$val['product_id'].",spec_id : ".$val['spec_id'].",unit price:".$price.",product number is :".$val['product_quantity']);
			$product_total_price = $price * $val['product_quantity'];
			
			$coupon_id = $val['use_coupon'];
			error_log("coupon id ====> ".$coupon_id);
			if($coupon_id > 0){
				$reduce_amount = 0;
				//获取coupon信息
				$coupon_info = $this->Coupons->get_coupon_info($coupon_id);
				if($coupon_info){
					//获取折扣的价格
					$reduce_amount = $this->mOrder->cal_single_coupon_reduce_amount($product_total_price,$coupon_info);
				}
				
				//获取折扣之后的总价
				$product_total_price = $product_total_price - $reduce_amount;
			}
			error_log("reduce amount : ".$reduce_amount.",sing product total_amount : ".$product_total_price);
			$total_amount = $product_total_price + $total_amount;
			
		}
		error_log("all products total price : " . $total_amount);
		return $total_amount;
	}
	
	


}
