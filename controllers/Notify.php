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
class Notify extends Controller {

    /**
     * notify bug to admin | dev
     */
    public function notifyBug() {
        include_once(dirname(__FILE__) . "/../models/WechatSdk.php");
        $stoken = WechatSdk::getServiceAccessToken();
        Messager::sendText($stoken, 'od2ZEuGcj5xSuk_YJuSpJ2wPsKp0', $_POST['message']);
    }

}
