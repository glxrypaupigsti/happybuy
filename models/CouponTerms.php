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
class CouponTerms extends Model {
    
	/**
	 * 获取优惠条件信息
	 * @param type $verifed
	 * @return type
	 */
    public function getCouponTermsInfo($id){
    	return $this->Dao->select()->from(TABLE_SHOP_COUPONS_TERMS)->where('id='.$id)->getOneRow();
    }
    
    /**
     * 获取优惠条件信息
     * @param type $verifed
     * @return type
     */
    public function checkCouponTermsNameExist($term_name){
    	return $this->Dao->select()->from(TABLE_SHOP_COUPONS_TERMS)->where('term_name='.$term_name)->getOneRow();
    }
    
    /**
     * 获取优惠条件信息
     * @param type $verifed
     * @return type
     */
    public function checkCouponTermsExists($term_table,$term_column,$term_operate){
    	return $this->Dao->select()->from(TABLE_SHOP_COUPONS_TERMS)
    							   ->where('term_table="'.$term_table.'"')
    							   ->aw('term_column="'.$term_column.'"')
    							   ->aw('term_operate="'.$term_operate.'"')
    							   ->getOneRow();
    }
    
    /**
     * 获取优惠条件列表
     * @param type $verifed
     * @return type
     */
    public function getCouponTermsList(){
    	return $this->Dao->select()->from(TABLE_SHOP_COUPONS_TERMS)->exec();
    }
    
    /**
     * 更新优惠条件列表
     * @param type $verifed
     * @return type
     */
    public function updateCouponTerms($data,$id){
    	return $this->Dao->update(TABLE_SHOP_COUPONS_TERMS)->set(array(
    			'term_name' => $data['term_name'],
    			'term_detail' => $data['term_detail'],
    			'term_table' => $data['term_table'],
    			'term_column' => $data['term_column'],
    			'term_operate' => $data['term_operate']
    	))->where('id',$id)->exec();
    }
    /**
     * 更新优惠条件列表
     * @param type $verifed
     * @return type
     */
    public function insertCouponTermsList($data,$uid=0){
    	$time =time();
    	return $this->Dao->insert(TABLE_SHOP_COUPONS_TERMS,'term_name,term_table,term_column,term_operate,term_detail,uid,add_time')
    				->values(array(
    						$data['term_name'],$data['term_table'],$data['term_column'],$data['term_operate'],$data['term_detail'],$uid,$time
    				))->exec();
    }
    
    /**
     * 删除优惠条件列表
     * @param type $verifed
     * @return type
     */
    public function deleteCouponTerms($id){
    	return $this->Dao->delete()->from(TABLE_SHOP_COUPONS_TERMS)->where('id',$id)->exec();
    }



    
}
