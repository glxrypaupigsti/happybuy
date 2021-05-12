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
class mUserAddress extends Model {
	
	/**
	 * 获取上一次使用的地址
	 */
	public function enableUserAddress($uid) {
		return $this->Dao->select ()->from ( 'user_address' )->where ( 'uid', $uid )->aw ( 'is_delete != 1' )->aw ( 'enable = 1' )->getOneRow ();
	}
	
	/**
	 * 获取用户所有的地址
	 */
	public function get_user_address_list($uid) {
		return $this->Dao->select ()->from ( 'user_address' )->where ( 'uid', $uid )->aw ( 'is_delete != 1' )->orderby( 'enable' )->desc()->exec (false);
	}
	
	/**
	 * 获取用户地址信息
	 **/
	public function get_user_address_by_id($id) {
		return $this->Dao->select ()->from ( 'user_address' )->where ( 'id', $id )->getOneRow();
	}
	
	/**
	 * 更新用户能地址信息
	 **/
	public function update_address($uid, $data = array()) {
		return $this->Dao->update ( 'user_address' )->set ( $data )->where ( "uid =" . $uid )->exec ();
	}
	
	/**
	 * 添加地址信息
	 **/
	public function add_user_address($array) {
		return $this->Dao->insert ( "user_address", '`uid`,`user_name`,`province`,`city`,`address`,`postal_code`,`enable`,`area`,`phone`' )->values ( $array )->exec ();
	}
	/**
	 * 更新地址信息
	 */
	public function update_address_by_id($id, $data = array()) {
		return $this->Dao->update ( 'user_address' )->set ( $data )->where ( "id =" . $id )->exec ();
	}
	
	/**
	 * 删除地址信息
	 */
	public function del_address_by_id($id) {
		return $this->Dao->delete ()->from ( 'user_address' )->where ( "id =" . $id )->exec ();
	}
	
	/**
	 * 获取地址列表
	 */
	public function get_address_list($where = '', $orderby = 'id desc') {
		return $this->Dao->select ()->from ( 'user_address' )->where ( $where )->orderby ( $orderby )->exec ();
	}
}