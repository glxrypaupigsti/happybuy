<?php

/**
 * 首页板块
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class HomeSection extends Model {

    /**
     * 获取首页板块列表
     * @return type
     */
    public function gets() {
        $sections = $this->Dao->select()->from(TABLE_HOME_SECTION)
                ->where("(ttime IS NULL AND ftime IS NULL)")
                ->ow("(NOW() <= ttime aNd ftime <= NOW())")
                ->orderby('bsort')
                ->desc()
                ->exec();
        return $sections;
    }

    /**
     * 获取首页板块列表-全部
     * @return type
     */
    public function getAll() {
        return $this->Dao->select()
                        ->from(TABLE_HOME_SECTION)
                        ->orderby('bsort')
                        ->desc()
                        ->exec();
    }

}
