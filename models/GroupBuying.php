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
class GroupBuying extends Model {

    /**
     * 获取参团列表
     * @param type $groupId
     * @return type
     */
    public function getUserList($groupId) {
        if (!is_numeric($groupId) || $groupId > 0) {
            return NULL;
        }
        return $this->Dao->select()->from(TABLE_GROUP_BUYING_FRIENDS)->where("groupid = $groupId")->exec();
    }

    /**
     * 获取团购列表
     * @return type
     */
    public function getList() {
        return $this->Dao->select()->from(TABLE_GROUP_BUYING)->exec();
    }

    /**
     * 添加团购
     * @param type $id
     * @param type $pid
     * @param type $limit
     */
    public function addGrougBuy($id = 0, $pid, $limit) {
        if ($id < 0) {
            // delete
        }
    }

}
