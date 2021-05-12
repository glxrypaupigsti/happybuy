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
class WdminAdmin extends Model {
    
    /**
     * 生成admin加密密文
     * @global type $config
     * @param type $admin_account
     * @param type $pwd
     * @return type
     */
    public function encryptPassword($admin_account, $pwd) {
        global $config;
        return hash('sha384', $pwd . $config->admin_salt . hash('md4', $config->admin_salt2[intval($admin_account)]));
    }

    /**
     * 校验登陆提交密码
     * @param type $pwd_db
     * @param type $name_submit
     * @param type $pwd_submit
     * @return type
     */
    public function pwdCheck($pwd_db, $name_submit, $pwd_submit) {
        return $pwd_db == $this->encryptPassword($name_submit, $pwd_submit);
    }

    /**
     * 生成登陆token
     * @global type $config
     * @param type $ip
     * @param type $id
     * @param type $pwd
     * @return type
     */
    public function encryptToken($ip, $id, $pwd) {
        global $config;
        return hash('sha384', $pwd . $config->admin_salt . hash('md4', $id . $ip));
    }

    /**
     * 
     * @param type $ip
     */
    public function updateAdminState($ip, $id) {
        $this->Db->query("UPDATE `admin` SET `admin_last_login` = NOW(),`admin_ip_address` = '$ip' WHERE id = $id;");
    }
    
    
    public function getAdminIdFromCookie(){
    	return $this->pCookie('admin_id');
    }

}
