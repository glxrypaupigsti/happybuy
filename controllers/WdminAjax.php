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
class WdminAjax extends Controller {

    /**
     * 查看订单<发货>
     */
    const ORDER_EXP = 0;

    /**
     * 查看订单
     */
    const ORDER_VIE = 1;

    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        $this->loadModel('Db');
    }

    /**
     * groupSendImageUpload
     */
    public function BannerImageUpload() {
        $this->loadModel('ImageUploader');
        $this->ImageUploader->dir = dirname(__FILE__) . '/../uploads/banner/';
        $file = $this->ImageUploader->upload();
        if ($file !== false) {
            $this->echoJson(array('s' => 1, 'img' => $file, 'link' => '/uploads/banner/' . $file));
        } else {
            $this->echoJson(array('s' => 0));
        }
    }

    /**
     * 上传群发封面图片
     * groupSendImageUpload
     */
    public function groupSendImageUpload() {
        $this->loadModel('ImageUploader');
        $this->ImageUploader->dir = dirname(__FILE__) . '/../uploads/gmess_tmp/';
        $file = $this->ImageUploader->upload();
        if ($file !== false) {
            $this->echoJson(array('s' => 1, 'img' => $file, 'link' => '/uploads/gmess_tmp/' . $file));
        } else {
            $this->echoJson(array('s' => 0));
        }
    }

    /**
     * 创建群发页面
     * @param type $Query
     */
    public function alterGmessPage() {
        $this->loadModel('mGmess');
        global $config;

        // 是否为更新
        $msgId = (int) $this->post('msgid');

        // 微信素材id
        $thumbMediaId = '';

        if (is_file(dirname(__FILE__) . '/../uploads/gmess_tmp/' . $this->post('catimg'))) {
            @rename(dirname(__FILE__) . '/../uploads/gmess_tmp/' . $this->post('catimg'), dirname(__FILE__) . '/../uploads/gmess/' . $this->post('catimg'));
        }
		error_log("begin to alter Gmess ======>msgId:".$msgId);
        if ($msgId > 0) {
            $oldData = $this->mGmess->getGmess($msgId);
        	error_log("update Gmess ======>oldData:".json_encode($oldData));
            if ($oldData['catimg'] != $this->post('catimg')) {
                @unlink(dirname(__FILE__) . '/../uploads/gmess/' . $oldData['catimg']);
            }
            $rst = $this->mGmess->alterGmess($msgId, addslashes($_POST['title']), addslashes($_POST['content']), addslashes($_POST['desc']), $this->post('catimg'), $thumbMediaId);
            #$this->Dao->echoSql();
            $this->echoJson(array(
                'status' => $rst ? 1 : 0
            ));
        } else {
        	error_log("add Gmess ======>msgId:".$msgId);
            $rst = $this->mGmess->alterGmess(0, addslashes($_POST['title']), addslashes($_POST['content']), addslashes($_POST['desc']), $this->post('catimg'), $thumbMediaId);
            error_log("update Gmess ======>rst:".json_encode($oldData));
            $ret = array();
            if ($rst) {
                $ret['status'] = 1;
                $ret['url'] = "http://" . $this->server('HTTP_HOST') . "$config->shoproot?/Gmess/view/id=" . $rst;
                $ret['msgid'] = $rst;
            } else {
                $ret['status'] = 0;
            }
            $this->echoJson($ret);
        }
    }

    // 获取订阅者列表
    public function ajaxGetSubScribelist() {
        $this->loadModel('WechatSdk');
        $access_token = WechatSdk::getServiceAccessToken();
        $list = WechatSdk::getWechatSubscriberList($access_token);
        $list['accesstoken'] = $access_token;
        $this->echoJson($list);
    }

    // 上传群发统计数据
    public function UploadGroupSendStatData() {
        $SQL = sprintf("INSERT INTO `gmess_send_stat` (msg_id,send_date,send_count,receive_count,msg_type) "
                . " VALUES (%s,NOW(),%s,%s,'images');", $_POST['msgid'], $_POST['total'], $_POST['success']);
        $rst = $this->Db->query($SQL);
        echo $rst ? 1 : 0;
    }

    /**
     * 获取商品详细信息
     * @param type $Query
     */
    public function ajaxGetProductInfo($Query) {
        $id = intval($Query->id);
        $res = $this->Db->getOneRow("SELECT * FROM `products_info` pi LEFT JOIN product_onsale po ON po.product_id = pi.product_id WHERE pi.`product_id` = $id;");
        $res['images'] = $this->Db->query("SELECT * FROM `product_images` WHERE `product_id` = " . $res['product_id']);
        $this->echoJson($res);
    }

    /**
     * 获取订单详情
     * @param type $Query
     */
    public function loadOrderDetail($Query) {
        $this->cacheId = hash('md4', serialize($Query));
        if (!$this->isCached()) {
            // cache
            global $config;
            $id = intval($Query->id);
            $this->loadModel('mOrder');
            $express = include dirname(__FILE__) . '/../config/express_code_prefix.php';
            $express_noprefix = include dirname(__FILE__) . '/../config/express_code.php';

            $expressEs = $this->Dao->select("value")->from('wshop_settings')->where("`key` = 'expcompany'")->getOne();
            $expressEs = explode(',', $expressEs);

            $openid = $this->getSetting('order_express_openid');
            $openids = explode(',', $openid);
            $exps = $this->Dao->select()->from(TABLE_USER)->where("client_wechat_openid in ('" . implode("','", $openids) . "')")->exec();
            
            $this->assign('expressStaff', $exps);

            foreach ($express as $k => &$od) {
                if (!in_array($k, $expressEs)) {
                    unset($express[$k]);
                }
            }

            if (!isset($Query->mod)) {
                $Query->mod = self::ORDER_EXP;
            } else {
                $Query->mod = intval($Query->mod);
            }
            // get data
            $data = $this->mOrder->GetOrderDetail($id);
            $data['statusX'] = $config->orderStatus[$data['status']];
            $data['expressName'] = $express_noprefix[$data['express_com']];
            // assign
            $peisong_code = 'KD'.$data['serial_number'];
            $this->Smarty->assign('peisong_code', $peisong_code);
            $this->Smarty->assign('expressCompany', $express);
            $this->Smarty->assign('data', $data);
            $this->Smarty->assign('mod', $Query->mod);
        }
        $this->show('wdminpage/orders/ajaxloadorderdetail.tpl');
    }

    /**
     * 添加备注
     */
    public function addOrderNotes($Query){
        $this->loadModel('mOrder');
        $order_id = $Query->id;
        $order = $this->mOrder->get_order_info_by_id($order_id);
        $this->Smarty->assign('order_id', $order_id);
        $this->Smarty->assign('order', $order);
        $this->Smarty->assign('order_note', $order['notes']);
        $this->show('wdminpage/orders/update_order_notes.tpl');
    }


    /**
     * 修改备注
     */
    public function updateNotes(){
        $this->loadModel('mOrder');
        $order_id = $this->pPost('order_id');
        $notes = $this->pPost('notes');
        if(empty($notes)){
            $this->echoMsg(-1,'备注不能为空');
            die(0);
        }


        $array =  array(
            'notes' => $notes
        );
        $state = $this->mOrder->updateOrder($order_id,$array);
        $msg = 'success';
        if($state < 0){
            $state = -1;
            $msg = '更新异常';
        }
        $this->echoMsg($state,$msg);
    }
    
    public function printOrder($Query)
    {
        $this->cacheId = hash('md4', serialize($Query));
        if (!$this->isCached()) {
            // cache
            global $config;
            $id = intval($Query->id);
            $this->loadModel('mOrder');

            // get data
            $data = $this->mOrder->GetOrderDetail($id);
            $data['statusX'] = $config->orderStatus[$data['status']];
            // assign
            $peisong_code = 'KD'.$data['serial_number'];
            $this->Smarty->assign('peisong_code', $peisong_code);
            $this->Smarty->assign('data', $data);
        }
        $this->show('wdminpage/orders/ajaxPrintOrder.tpl');
    }

    /**
     * 管理后台加载报表页
     * @param type $Query
     */
    public function ajaxLoadStatHome() {
        $QueryDate = date("Y-m-d");
        $QueryMonth = date("Y-m");
        // 日销售
        $DaySaleData = $this->Db->query("SELECT * FROM `vstatdaysalesumraw` WHERE `day` = '$QueryDate';");
        // 月销售
        $MonthSaleData = $this->Db->query("SELECT * FROM `vstatmonthsalesumraw` WHERE `month` = '$QueryMonth';");
        // 微信总关注
        $wechatSub = $this->Db->query("SELECT SUM(dv) AS sc FROM `wechat_subscribe_record`;");
        // 微信今天关注
        $wechatSubDay = $this->Db->query("SELECT SUM(dv) AS sc FROM `wechat_subscribe_record` WHERE DATE_FORMAT(`date`,'%Y-%m-%d') = '$QueryDate';");
        // 微信关注记录
        $WechatSubscribeStat = $this->Db->query("SELECT * FROM `vwechatsubscribestat` WHERE DATE_FORMAT(`date`,'%Y-%m') = '$QueryMonth';");
        $data_wechatsc = array();
        foreach ($WechatSubscribeStat as $_record) {
            $data_wechatsc['day'][] = intval(date("d", strtotime($_record['date'])));
            $data_wechatsc['count'][] = $_record['count'];
        }

        $wecahtCount = isset($data_wechatsc['count']) ? implode(',', $data_wechatsc['count']) : '';
        $wecahtDay = isset($data_wechatsc['day']) ? implode(',', $data_wechatsc['day']) : '';

        $this->Smarty->assign('wecahtCount', $wecahtCount);
        $this->Smarty->assign('wecahtDay', $wecahtDay);
        $this->Smarty->assign('daysale', $DaySaleData[0]);
        $this->Smarty->assign('monthsale', $MonthSaleData[0]);
        $this->Smarty->assign('wechatSubDay', $wechatSubDay[0]['sc']);
        $this->Smarty->assign('wechatSubTotal', $wechatSub[0]['sc']);
        $this->show();
    }

    /**
     * 更新商品信息
     */
    public function updateProduct() {
        $this->loadModel('Product');
        $this->sCookie('lastSerial', $this->post('product_serial'));
        $id = $this->Product->modifyProduct($_POST);
        echo $id ? $id : 0;
    }

    /**
     * 更新自动回复内容
     * @todo opt
     */
    public function setAutoReplys() {
        $data = $this->pPost('data');
        $gmess = $data['gmess'];
        if ($data['rel'] == 0 && $data['reltype'] != 0) {
            // 新建gmess
            $SQL = sprintf("INSERT INTO `gmess_page` (`title`,`content`,`desc`,`catimg`,`createtime`) VALUES ('%s','%s','%s','%s',NOW());", addslashes($gmess['title']), addslashes($gmess['content']), addslashes($gmess['desc']), $gmess['catimg']);
            $rst = $this->Db->query($SQL);
            $SQL = "REPLACE INTO `wechat_autoresponse` (`id`,`key`,`message`,`rel`,`reltype`) VALUES ($data[id],'$data[key]','$data[message]','$rst','$data[reltype]')";
            $ret = $this->Db->query($SQL);
            echo $ret;
        } else {
            $SQL = sprintf("UPDATE `wechat_autoresponse` SET `key` = '%s',`message` = '%s',`rel` = %s,`reltype` = %s WHERE `id` = %s;", $data['key'], $data['message'], $data['rel'], $data['reltype'], $data['id']);
            $ret = $this->Db->query($SQL);
            if ($data['rel'] > 0) {
                // 更新gmess
                $SQL = sprintf("UPDATE `gmess_page` SET `title` = '%s',`content` = '%s',`desc` = '%s',`catimg` = '%s' WHERE `id` = %s;", addslashes($gmess['title']), addslashes($gmess['content']), addslashes($gmess['desc']), $gmess['catimg'], $gmess['id']);
                $rst1 = $this->Db->query($SQL);
                echo $rst1 + $ret;
            } else {
                echo $ret;
            }
        }
    }

    /**
     * 添加自动回复内容
     */
    public function addAutoReplys() {
        $key = $_POST['key'];
        $SQL = "INSERT INTO `wechat_autoresponse` (`key`,`message`) VALUES ('$key','');";
        echo $this->Db->query($SQL);
    }

    /**
     * 删除自动回复关键字
     */
    public function deleteAutoReplys() {
        $id = intval($_POST['id']);
        $ret = $this->Db->query("DELETE FROM `wechat_autoresponse` WHERE `id` = $id;");
        echo $ret == false ? 0 : 1;
    }

    /**
     * 更新系统设置
     */
    public function setSettings() {
        $SQL = sprintf("UPDATE `shop_settings` SET `shop_name` = '%s',`wechat_subscribe_welcome` = '%s',`company_profit_percent` = '%s';", $_POST['shop_name'], $_POST['wechat_subscribe_welcome'], $_POST['company_profit_percent']);
        echo $this->Db->query($SQL);
    }

    /**
     * 获取微代理未结算数据
     * @return type
     */
    public function getCompanyCslist() {
        $this->loadModel('mCompany');
        $list = $this->mCompany->getCompanyCashs();
        #var_dump($list);
        $this->Smarty->assign('olist', count($list));
        $this->Smarty->assign('list', $list);
        $this->show();
    }

    public function companyCash($Query) {
        $this->loadModel('mCompany');
        $list = $this->mCompany->getCompanyCashs($Query->id);
        $this->Smarty->assign('list', $list[0]);
        $this->show();
    }

    public function cashCompanyReq() {
        $uid = $_POST['uid'];
        $this->Db->query(sprintf("UPDATE `companys` SET `bank_name` = '%s',`bank_account` = '%s',`bank_personname` = '%s' WHERE `uid` = '$uid';", $_POST['bank_name'], $_POST['bank_account'], $_POST['bank_personname']));
        echo $this->Db->query("UPDATE `company_income_record` SET `is_reqed` = 1 WHERE `is_seted` = 0 AND `is_reqed` = 0 AND `com_id` = '$uid';");
    }

    public function cashCompany() {
        $this->loadModel('mCompany');
        echo $this->mCompany->cashCompany($_POST['uid']);
    }

    /**
     * 删除商品
     */
    public function deleteProduct() {
        $id = intval($this->pPost('id'));
        $this->loadModel('Product');
        $this->loadModel('Carts');
        //先删除商品在删除购物车中的商品
        $this->Product->deleteProduct($id);
        echo $this->Carts->del_cart_product_by_product_id($id);
    }

    /**
     * 会员列表
     */
    public function ajaxUserList($Query) {
        !isset($Query->gid) && $Query->gid = '';
        $this->Smarty->caching = false;
        if ($Query->gid != '') {
            $Ext = " AND `client_groupid` = $Query->gid";
        } else {
            $Ext = '';
        }
        if ($this->pCookie('comid')) {
            $comid = $this->Util->digDecrypt($this->pCookie('comid'));
            $SQL = "SELECT
	cl.*,cl.client_id AS cid,
	(
		SELECT
			count(*)
		FROM
			`orders`
		WHERE
			client_id = cl.client_id
	) AS `order_count`
        FROM
                company_users cu
        LEFT JOIN clients cl ON cl.client_id = cu.uid$Ext
        WHERE
                cu.comid = $comid AND cl.deleted = 0;";
        } else {
            $SQL = "SELECT
	*, cl.client_id AS cid,
	(
		SELECT
			count(*)
		FROM
			`orders`
		WHERE
			client_id = cl.client_id
	) AS `order_count`
        FROM
                `clients` cl
        LEFT JOIN `client_info_exts` cx ON cx.client_id = cl.client_id WHERE cl.deleted = 0$Ext
        ORDER BY
	cl.client_id DESC;";
        }
        $list = $this->Db->query($SQL);
        foreach ($list AS &$l) {
            $l['client_sex'] = $this->sexConv($l['client_sex']);
        }
        $this->Smarty->assign('iscom', isset($comid));
        $this->Smarty->assign('list', $list);
        $this->show('wdminpage/customers/ajaxuserlist.tpl');
    }

    /**
     * 性别eng转换
     * @param type $sex
     * @return string
     */
    private function sexConv($sex) {
        $s = array('f' => '女', 'm' => '男');
        if (array_key_exists($sex, $s)) {
            return $s[$sex];
        } else {
            return '未知';
        }
    }

    /**
     * 获取横幅数据
     */
    public function getBannerData() {
        $dat = $this->Db->query("SELECT * FROM `ws_banner`;");
        $this->echoJson($dat);
    }

    public function SaveBannerData() {
        echo $this->Db->query(sprintf("UPDATE `ws_banner` SET `banner_image` = '%s',`banner_href` = '%s' WHERE `relid` = %s;", $this->post('img'), $this->post('href'), $this->post('relid')));
    }

    /**
     * 获取素材
     * @param type $Query
     */
    public function ajaxGmess($Query) {
        global $config;
        $id = intval($Query->id);
        $page = $this->Db->getOneRow("SELECT * FROM `gmess_page` WHERE `id` = $id;");
        $page['href'] = "http://" . $this->server('HTTP_HOST') . "$config->shoproot?/Gmess/view/id=" . $page['id'];
        $this->Smarty->assign('gm', $page);
        $this->show();
    }

    /**
     * 修改自动回复
     */
    public function updateAutoReply() {
        echo $this->Db->query(sprintf("UPDATE `wechat_autoresponse` SET `rel` = %s WHERE `id` = %s;", $this->post('rel'), $this->post('id')));
        # echo sprintf("UPDATE `wechat_autoresponse` SET `rel` = %s WHERE `id` = %s;", $this->post('rel'), $this->post('id'));
    }

    /**
     * 获取订单分类统计数据
     */
    public function ajaxGetOrderStatnums() {
        $this->loadModel('SqlCached');
        // file cached
        $cacheKey = 'ajaxGetOrderStatnums';
        $fileCache = new SqlCached();
        $ret = $fileCache->get($cacheKey);
        if (-1 === $ret) {
            $status = array('payed' => 0, 'canceled' => 0, 'delivering' => 0, 'all' => 0, 'unpay' => 0, 'refunded' => 0, 'received' => 0,'closed' => 0);
            foreach ($status as $key => &$value) {
                if ($key == 'all') {
                    $WHERE = ';';
                } else {
                    if ($key == 'canceled') {
                        // 退货而且已经支付才需要审核，否则直接关闭订单
                        $WHERE = " WHERE status = '$key';";
                    } else {
                        $WHERE = " WHERE status = '$key';";
                    }
                }
                $sql = "select count(*) from `orders`$WHERE;";
                $ret = $this->Db->getOne($sql);
                $value = intval($ret);
            }
            $fileCache->set($cacheKey, $status);
            $this->echoJson($status);
        } else {
            $this->echoJson($ret);
        }
    }

    public function ajaxGetProductStatnums() {
        $this->loadModel('SqlCached');
        // file cached
        $cacheKey = 'ajaxGetProductStatnums';
        $fileCache = new SqlCached();
        $ret = $fileCache->get($cacheKey);
        if (-1 === $ret) {
            $ret = array();

            $ret['pdcount2'] = intval($this->Db->getOne("SELECT COUNT(*) FROM products_info WHERE `delete` = 1;"));
            $ret['pdcount'] = intval($this->Db->getOne("SELECT COUNT(*) FROM products_info WHERE `delete` = 0;"));
            $ret['cacount'] = intval($this->Db->getOne("SELECT COUNT(*) FROM product_category;"));
            $ret['spcount'] = intval($this->Db->getOne("SELECT COUNT(*) FROM wshop_spec;"));
            $ret['secount'] = intval($this->Db->getOne("SELECT COUNT(*) FROM product_serials;"));
            $ret['brcount'] = intval($this->Db->getOne("SELECT COUNT(*) FROM product_brand;"));

            $fileCache->set($cacheKey, $ret);
            $this->echoJson($ret);
        } else {
            $this->echoJson($ret);
        }
    }

    public function ajaxGetCompanyStatNums() {
        $this->loadModel('SqlCached');
        // file cached
        $cacheKey = 'ajaxGetCompanyStatNums';
        $fileCache = new SqlCached();
        $ret = $fileCache->get($cacheKey);
        if (-1 === $ret) {
            $ret = array();

            $ret['count1'] = intval($this->Db->getOne("SELECT COUNT(*) FROM companys WHERE `verifed` = 1 AND `deleted` = 0;"));
            $ret['count2'] = intval($this->Db->getOne("SELECT COUNT(*) FROM companys WHERE `verifed` = 0 AND `deleted` = 0;"));
            $ret['count3'] = intval($this->Db->getOne("SELECT COUNT(distinct `com_id`) FROM `company_income_record` cir LEFT JOIN `companys` cs ON cs.id = cir.com_id WHERE `is_seted` = 0 AND cs.deleted = 0;"));

            $fileCache->set($cacheKey, $ret);
            $this->echoJson($ret);
        } else {
            $this->echoJson($ret);
        }
    }

    /**
     * ajax编辑用户 | 添加用户
     */
    public function ajaxAlterCustomer() {
        if ($this->post('id') == '0') {
            // add
            $field = array();
            $values = array();
            $data = $this->post('data');
            foreach ($data as &$d) {
                $field[] = "`$d[name]`";
                $values[] = "'$d[value]'";
            }
            $SQL = sprintf("INSERT INTO `clients` (%s) VALUES (%s);", implode(',', $field), implode(',', $values));
            $ret = $this->Db->query($SQL);
            if ($ret !== false) {
                echo 1;
            } else {
                echo 0;
            }
        } else {
            // update
            $id = intval($this->post('id'));
            if ($id > 0) {
                $set = array();
                $gid = false;
                $data = $this->post('data');
                foreach ($data as &$d) {
                    if ($d['name'] == 'client_groupid') {
                        $gid = $d['value'];
                    }
                    $set[] = "`$d[name]` = '$d[value]'";
                }
                $set = implode(',', $set);
                $sql = "UPDATE `clients` SET $set WHERE `client_id` = $id";
                // 移动用户分组
                if ($gid > 0) {
                    $this->loadModel('User');
                    $openid = $this->User->getOpenIdByUid($id);
                    if ($openid != "") {
                        $this->loadModel('WechatSdk');
                        WechatSdk::moveUserGroup($openid, $gid);
                    }
                }
                echo $this->Db->query($sql);
            }
        }
        #echo $SQL;
    }

    /**
     * ajax删除用户
     */
    public function ajaxDeleteCustomer() {
        $id = intval($this->post('id'));
        if ($id > 0) {
        
        
           $order_list_sql = "select order_id,serial_number from orders where client_id = ".$id;
           $order_list = $this->Db->query($order_list_sql);
           if($order_list){
            
             $this->echoMsg(-1,'该用户有订单信息，不允许删除');
             die(0);   
           }
           //删除用户信息
           $sql = 'delete from clients where client_id = '.$id;
           $this->Db->query($sql);
           //删除地址信息
           $address_sql = 'delete from user_address where uid = '.$id;
           $this->Db->query($address_sql);
           $this->echoMsg(1,删除成功);
        }
    }

    /**
     * ajax获取订单列表byIds，数据导出专用
     * @param type $Query
     */
    public function ajaxOrderByIdsExporting($Query) {
        #global $config;
        if (isset($Query->ids)) {

            $this->loadModel('mOrder');
            $this->loadModel('mProductSpec');

            // 订单分割基数
            #$spBase = 500;

            $this->Smarty->caching = false;

            $SQL = "SELECT
                    `oda`.user_name,`oda`.address AS `addr`,`oda`.tel_number,`pi`.product_name,`pi`.product_code,
                    `od`.product_discount_price,`od`.product_count,`od`.product_discount_price * `od`.product_count AS `total`,
                    `ods`.order_time,`ods`.serial_number,`ods`.leword,`ods`.order_yunfei,`oda`.postal_code,`ods`.order_id,
                    `od`.product_price_hash_id
            FROM
                    `orders_detail` `od`
            LEFT JOIN `products_info` `pi` ON `pi`.product_id = `od`.product_id
            LEFT JOIN `orders` `ods` ON `ods`.order_id = `od`.order_id
            LEFT JOIN `orders_address` `oda` ON `oda`.order_id = `od`.order_id
            WHERE
                    `od`.`order_id` IN ($Query->ids)
            ORDER BY
            `od`.`detail_id` DESC";

            $orderList = $this->Db->query($SQL);

            $orderIds = array();

            foreach ($orderList as $index => $order) {

                // 收货地址
                $orderList[$index]['address'] = $this->mOrder->getOrderAddr($order['order_id']);
                $orderList[$index]['pdname'] = $orderList[$index]['product_name'] . ' ' . $this->mProductSpec->getProductSpecName($order['product_price_hash_id']);

                if (!in_array($orderList[$index]['order_id'], $orderIds)) {
                    $orderIds[] = $orderList[$index]['order_id'];
                } else {
                    $orderList[$index]['order_yunfei'] = 0;
                }
            }

            $this->Smarty->assign('islocal', true);
            $this->Smarty->assign('orderlist', $orderList);
            $this->show('./views/wdminpage/orders/order_list_export_table.tpl');
        } else {
            echo 0;
        }
    }

    /**
     * 编辑用户分组名
     */
    public function ajaxAlterUserGroup() {
        $this->loadModel('WechatSdk');
        $this->echoJson(WechatSdk::alterUserGroup($this->post('id'), $this->post('name')));
    }

    /**
     * 添加用户分组
     */
    public function ajaxAddUserGroup() {
        $this->loadModel('WechatSdk');
        $this->echoJson(WechatSdk::addUserGroup($this->post('name')));
    }

    public function ajaxGetWechatMenu() {
        $this->loadModel('WechatSdk');
        $this->Smarty->assign('menu', WechatSdk::getMenu());
        $this->show();
    }

    public function ajaxSetWechatMenu() {
        $this->loadModel('WechatSdk');
        $rst = WechatSdk::setMenu($this->pPost('menu'));
        $this->echoJson($rst);
    }
    
    public function ajaxClearWechatMenu() {
        $this->loadModel('WechatSdk');
        $rst = WechatSdk::deleteMenu();
        $this->echoJson($rst);
    }

    public function bindMenu() {
        echo $this->Db->query(sprintf("INSERT INTO `wshop_menu` (`relid`,`reltype`,`relcontent`) VALUE ('%s','%s','%s');", $this->pPost('relid'), $this->pPost('reltype'), strip_tags($this->pPost('relcontent'))));
    }

    public function getMenu() {
        $r = $this->Db->getOneRow("SELECT * FROM `wshop_menu` WHERE `id` = " . $this->pPost('id'));
        $this->echoJson($r);
    }

    public function ajaxDeleteBanner() {
        $id = $this->pPost('id');
        if ($id < 0) {
            $this->loadModel('Banners');
            echo $this->Banners->modiBanner($id) ? 1 : 0;
        }
    }

    /**
     * 编辑banner
     */
    public function modiBanner() {
        $id = $this->pPost('id');
        $name = $this->pPost('name');
        $relId = $this->pPost('relId');
        $sort = $this->pPost('sort');
        $img = $this->pPost('img');
        $pos = $this->pPost('pos');
        $type = $this->pPost('type');
        $href = $this->pPost('href');
        $exp = $this->pPost('exp');
        $this->loadModel('Banners');
        echo $this->Banners->modiBanner($id, $name, $img, $pos, $type, $relId, $sort, $href, $exp);
    }

    /**
     * 删除会员等级
     */
    public function deleteLevel() {
        $this->loadModel('UserLevel');
        $id = $this->post('id');
        echo $this->UserLevel->delete($id);
    }

    /**
     * 编辑会员等级
     */
    public function modUserLevel() {
        $this->loadModel('UserLevel');
        $id = intval($this->post('id'));
        error_log("params========>".json_encode($_POST));
        if ($id > 0) {
            echo $this->UserLevel->addLevel($id, $this->post('name'), $this->post('credit'), $this->post('discount'), $this->post('feed'), $this->post('upable'));
        } else {
            echo $this->UserLevel->addLevel(false, $this->post('name'), $this->post('credit'), $this->post('discount'), $this->post('feed'), $this->post('upable'));
        }
    }

    /**
     * ajax会员选择弹出框
     */
    public function ajax_customer_select($Q) {
    	if($Q->coupon_id){
    		$this->Smarty->assign('coupon_id', $Q->coupon_id);
    	}
        $this->show();
    }

    public function ajax_customer_select_in($Q) {
        $this->loadModel('User');
        $this->cacheId = $Q->id;
        $keyword = '';
        if($Q->keyword){
        	$keyword = urldecode($Q->keyword);
        	
        }
        error_log('keyword==========>'.urldecode($Q->keyword));
        
        $list = $this->User->getUserList($Q->id,$keyword);
        $this->Smarty->assign('list', $list);
        $this->show();
    }

    public function ajaxDeleteSection() {
        $id = $this->post('id');
        echo $this->Dao->delete()->from(TABLE_HOME_SECTION)->where("id = $id")->exec() !== false ? 1 : 0;
    }
    
    /*
     * ajaxLoadProductsStocklist
     */
    public function ajaxLoadProductsStocklist($Q)
    {
        $per_page = 7;
        $this->cacheId = hash('md4', serialize($Q));
        
        if (!$this->isCached()) {
            $this->loadModel('Stock');
            global $config;
            
            if (isset($Q->start_date) && is_numeric($Q->end_date)) {
                $start = strtotime($Q->start_date);
                $end = strtotime($Q->end_date);
                $WHERE .= " WHERE stock_date >= $start AND stock_date <= $end ";
            }
            if (isset($Q->name)) {
                $WHERE .= " WHERE `sku_name` ='".$Q->name."'";
            }
            $limit = $Q->page * $per_page . "," . $per_page;
            $SQL = sprintf("SELECT * FROM `product_instock` %s ORDER BY `stock_date` DESC LIMIT $limit;", $WHERE);
            
            $stockList = $this->Db->query($SQL);
            
            $this->Smarty->assign('olistcount', count($stockList));
            $this->Smarty->assign('stockList', $stockList);
        }
        
        if (isset($Query->export)) {
            $trs = $this->show('wdminpage/stocks/ajaxloadorderlist_export.tpl');
        } else {
            $trs = $this->show('wdminpage/stocks/ajax_products.tpl');
        }
    }
    public function getTotalProductsStock()
    {
        $this->loadModel('Stock');
        global $config;
        if (isset($Q->start_date) && is_numeric($Q->end_date)) {
            $start = strtotime($Q->start_date);
            $end = strtotime($Q->end_date);
            $WHERE .= " WHERE stock_date >= $start AND stock_date <= $end ";
        }
        if (isset($Q->name)) {
            $WHERE .= " WHERE `sku_name` ='".$Q->name."'";
        }
        $SQL = sprintf("SELECT * FROM `product_instock` %s ORDER BY `stock_date` DESC;", $WHERE);
        $stockList = $this->Db->query($SQL);
        
        echo json_encode(array('err' => 0, 'total' => count($stockList)));
    }
    
    public function editProductStockDetail($Q)
    {
        $this->cacheId = hash('md4', serialize($Q));
        if (!$this->isCached()) {
            // cache
            global $config;
            $id = intval($Q->id);
            $this->loadModel('Stock');
            $detail = $this->Stock->get_product_stock_detail_by_id($id);
            // "loss" is only allowed for yesterday or today
            $today = strtotime(date('y-n-j', time()));
            $yesterday = $today - 24*3600;
            if ($detail AND ($detail['stock_date'] >= $yesterday)) {
                $this->Smarty->assign('loss_editable', true);
            } else {
                $this->Smarty->assign('loss_editable', false);
            }

            $this->Smarty->assign('stock', $detail);
        }
        
        $this->show('wdminpage/stocks/ajaxEditProductStock.tpl');
    }
    
    public function update_stock($Q)
    {
        // do validation
        
        // update record
        $id = $_POST['prd_stockid'];
        $data = array();
        $data['avaliable'] = intval($_POST['avaliable']);
        $data['produce'] = intval($_POST['produce']);
        $data['loss'] = intval($_POST['loss']);
        $data['user_note'] = $_POST['user_note'];
        
        $this->loadModel('Stock');
        $stock = $this->Stock->get_product_stock_detail_by_id($id);
        if (!$stock) {
            $result = array('err' => -1, 'msg' => '数据错误。请检查', 'url' => null);
        }
        
        if (($data['avaliable'] < 0) OR ($data['produce'] < 0) OR ($data['loss'] < 0)) {
            $result = array('err' => -1, 'msg' => '数据错误。请检查', 'url' => null);
            echo json_encode($result);
            die(0);
        }
        if (($data['avaliable'] + $data['produce'] + $stock['instock']) < $data['loss']) {
            $result = array('err' => -1, 'msg' => '数据错误。请检查', 'url' => null);
            echo json_encode($result);
            die(0);
        }
        
        $ret = $this->Stock->update_product($data, 'id = '.$id);
        //error_log('ret:'.json_encode($ret));
        
        if ($ret) {
            $result = array('err' => 0, 'msg' => 'Update Successfully', 'url' => null);
        } else {
            $result = array('err' => -1, 'msg' => 'Update Failed', 'url' => null);
        }
        
        echo json_encode($result);
    }
    
    public function add_product_stock()
    {
        // do validation
        
        // add stock record
        $data = array();
        $data['stock_date'] = strtotime($_POST['stock_date']);
        $data['sku_id'] = intval($_POST['sku_id']);
        $data['sku_name'] = $_POST['sku_name'];
        $data['avaliable'] = intval($_POST['avaliable']);
        $data['produce'] = intval($_POST['produce']);
        
        $this->loadModel('Stock');
        $ret = $this->Stock->add_product($data);
        
        if ($ret) {
            $result = array('err' => 0, 'msg' => 'Added Successfully', 'url' => null);
        } else {
            $result = array('err' => -1, 'msg' => 'Add Failed', 'url' => null);
        }
        
        echo json_encode($result);
    }
    
    public function alert_share_setting(){
        $this->loadModel('mShareSetting');
        
        
        $couponId = $_POST['paid_award_coupon'];
        $share_count = $_POST['share_count'];
        $percents = $_POST['percents'];
        $user_share_coupon_id =  mShareSetting::$user_share_coupon_id;
        $user_share_count =  mShareSetting::$user_share_count;
        $order_share_percent =  mShareSetting::$order_share_percent;
        
        

        $this->mShareSetting->updateShareSetting($user_share_coupon_id,$couponId);
        $this->mShareSetting->updateShareSetting($user_share_count,$share_count);
        $this->mShareSetting->updateShareSetting($order_share_percent,$percents);
        echo 0;
    }
    
    public function ajaxShareList(){
    
         $this->loadModel('mShare');
         $this->loadModel('User');
         $this->loadModel('Coupons');
         $list = $this->mShare->getShareList();
         foreach ($list as   $key => $val) {
             $u = $this->User->getUserInfo($val['uid']);
             $list[$key]['uinfo'] = $u;
             $query = "where share_id = ".$val['id'];
             $takes = $this->mShare->getUserShareTakeList($query);
         	 $list[$key]['share_count'] = count($takes);
         	 $couponInfo = $this->Coupons->get_coupon_info($val['coupon_id']);
             if($couponInfo){
                 $list[$key]['coupon_value'] =  $couponInfo['discount_val']/100;
              }
         }
         
         $this->assign('share_list', $list);
         $this->show('wdminpage/share/ajax_share_list.tpl');
    }
    
    public function loadIngredientsStock($Q)
    {
        $this->loadModel('ProductProportion');
        $per_page = 10;
        $this->cacheId = hash('md4', serialize($Q));
        
        if (!$this->isCached()) {
            $this->loadModel('Stock');
            global $config;

            if (isset($Q->name)) {
                $WHERE .= " WHERE `ingd_name` ='".$Q->name."'";
            }
            $limit = $Q->page * $per_page . "," . $per_page;
            $SQL = sprintf("SELECT * FROM `" . TABLE_INGREDIENTS_STOCK . "` %s ORDER BY `id` DESC LIMIT $limit;", $WHERE);
            $stockList = $this->Db->query($SQL);
            //error_log('ings:'.json_encode($stockList));
            if ($stockList) {
                foreach ($stockList AS $key=>$val) {
                    switch($val['ingd_unit']){
                        case '0':
                            $stockList[$key]['unit_str'] = '克'; break;
                        case '1':
                            $stockList[$key]['unit_str'] = '公斤'; break;
                        case '2':
                            $stockList[$key]['unit_str'] = '毫升'; break;
                        case '3':
                            $stockList[$key]['unit_str'] = '升'; break;
                        case '4':
                            $stockList[$key]['unit_str'] = '个'; break;
                    }
                    $item_id = $val['id'];
                    //判断是否已经有了比例数据
                    $item_proportion_list = $this->ProductProportion->get_proportion_list_by_item_id($item_id,'single');
                    if(count($item_proportion_list)>0){
                        $stockList[$key]['has_proportion'] = 1;
                    }else{
                        $stockList[$key]['has_proportion'] = 0;
                    }
                }

                $count = $this->Dao->select('')->count()->from(TABLE_INGREDIENTS_STOCK)->where()->getOne();
                $this->Smarty->assign('total', $count);
            }
            
            $this->Smarty->assign('olistcount', count($stockList));
            $this->Smarty->assign('stockList', $stockList);
        }
        
        if (isset($Q->export)) {
            $trs = $this->show('wdminpage/stocks/ajaxloadorderlist_export.tpl');
        } else {
            $trs = $this->show('wdminpage/stocks/ajax_ingredients.tpl');
        }
    }
    
    public function add_ingredient()
    {
        // do validation
        $ingd_name = trim($_POST['ingd_name']);
        if (strlen($ingd_name) < 1) {
            echo json_encode(array('err' => -1, 'msg' => '请设置食材名称', 'url' => null));
            exit;
        }
        $threshold = intval($_POST['ingd_threshold']);
        if ($threshold < 1) {
            echo json_encode(array('err' => -1, 'msg' => '请设置警戒库存', 'url' => null));
            exit;
        }
        
        // add stock record
        $data = array();
        $data['ingd_name'] = $ingd_name;
        $data['ingd_unit'] = intval($_POST['ingd_unit']);
        $data['ingd_threshold'] = $threshold;
        //
        $data['instock'] = 0;
        $data['ingd_cat'] = 0;
        
        $this->loadModel('Stock');
        $ret = $this->Stock->add_ingredient($data);
        
        if ($ret) {
            $result = array('err' => 0, 'msg' => 'Added Successfully', 'url' => null);
        } else {
            $result = array('err' => -1, 'msg' => 'Add Failed', 'url' => null);
        }
        
        echo json_encode($result);
    }
    
    public function loadIngredientChangelog($Q)
    {
        $per_page = 10;
        $this->cacheId = hash('md4', serialize($Q));
        
        if (!$this->isCached()) {
            $this->loadModel('Stock');
            global $config;
            
            if (isset($Q->id)) {
                $WHERE .= " WHERE `ingd_id` = ".$Q->id;
            }

            $ingredient = $this->Stock->get_ingredient($Q->id);
            if ($ingredient) {
                $unit_str = '';
                switch ($ingredient['ingd_unit']) {
                    case '0':
                        $unit_str = '克'; break;
                    case '1':
                        $unit_str = '公斤'; break;
                    case '2':
                        $unit_str = '毫升'; break;
                    case '3':
                        $unit_str = '升'; break;
                    case '4':
                        $unit_str = '个'; break;
                }
                $limit = $Q->page * $per_page . "," . $per_page;
                $SQL = sprintf("SELECT * FROM `" . TABLE_INGREDIENTS_STOCK_HISTORY . "` %s ORDER BY change_time DESC, add_time DESC LIMIT $limit;", $WHERE);
                $stockList = $this->Db->query($SQL);
                
                if ($stockList) {
                    foreach ($stockList AS $key=>$val) {
                        switch ($val['change_type']) {
                            case '1':
                                $stockList[$key]['change_type_str'] = '入库';
                                $stockList[$key]['initial_stock'] = $val['instock'] - $val['change_val'];
                                break;
                            case '2':
                                $stockList[$key]['change_type_str'] = '出库';
                                $stockList[$key]['initial_stock'] = $val['instock'] + $val['change_val'];
                                break;
                            case '3':
                                $stockList[$key]['change_type_str'] = '减计';
                                $stockList[$key]['initial_stock'] = $val['instock'] + $val['change_val'];
                                break;
                        }
                        $stockList[$key]['unit_str'] = $unit_str;
                        
                    }
                    $count = $this->Dao->select('')->count()->from(TABLE_INGREDIENTS_STOCK_HISTORY)->where("`ingd_id` = ".$Q->id)->getOne();
                    $this->Smarty->assign('total', $count);
                }
            }
            
            $this->Smarty->assign('olistcount', count($stockList));
            $this->Smarty->assign('stockList', $stockList);
        }
        
        if (isset($Query->export)) {
            $trs = $this->show('wdminpage/stocks/ajaxloadorderlist_export.tpl');
        } else {
            $trs = $this->show('wdminpage/stocks/ajax_ingredient_changelog.tpl');
        }
    }
    
    public function checkin_ingredient()
    {
        // do validation
        $change_username = trim($_POST['change_user']);
        if (strlen($change_username) < 1) {
            echo json_encode(array('err' => -1, 'msg' => '请设置采购人', 'url' => null));
            exit;
        }
        $val = intval($_POST['change_val']*1000);
        if ($val < 1) {
            echo json_encode(array('err' => -1, 'msg' => '请设置入库数量', 'url' => null));
            exit;
        }
        $price = intval($_POST['change_price']*100);
        if ($price <= 0) {
            echo json_encode(array('err' => -1, 'msg' => '请设置采购金额', 'url' => null));
            exit;
        }
        
        $vendor = trim($_POST['vendor']);
        if (strlen($vendor) < 1) {
            echo json_encode(array('err' => -1, 'msg' => '请设置采购供应商', 'url' => null));
            exit;
        }
        
        // add stock record
        $this->loadModel('WdminAdmin');
        $data = array();
        $data['ingd_id'] = $_POST['ingd_id'];
        $data['change_type'] = 1;
        $data['change_val'] = $_POST['change_val'];
        $data['change_price'] = $price;
        $data['spec'] = $_POST['spec'];
        $data['barcode'] = $_POST['barcode'];
        $data['vendor'] = $vendor;
        $data['change_time'] = strtotime($_POST['change_time']);
        $data['change_user'] = $change_username;
        $data['change_note'] = $_POST['change_note'];
        $data['uid'] = $this->WdminAdmin->getAdminIdFromCookie();
        $data['add_time'] = time();
        //
        
        $this->loadModel('Stock');
        $ret = $this->Stock->checkin_ingredient($data);
        
        if ($ret) {
            $result = array('err' => 0, 'msg' => 'Added Successfully', 'url' => null);
        } else {
            $result = array('err' => -1, 'msg' => 'Add Failed', 'url' => null);
        }
        
        echo json_encode($result);
    }
    
    public function checkout_ingredient()
    {
        // do validation
        $change_username = trim($_POST['change_user']);
        if (strlen($change_username) < 1) {
            echo json_encode(array('err' => -1, 'msg' => '请设置领用人', 'url' => null));
            exit;
        }
        $val = intval($_POST['change_val']*1000);
        if ($val < 1) {
            echo json_encode(array('err' => -1, 'msg' => '请设置出库数量', 'url' => null));
            exit;
        }
        
        $this->loadModel('Stock');
        $ingd = $this->Stock->get_ingredient($_POST['ingd_id']);
        if (!$ingd) {
            echo json_encode(array('err' => -1, 'msg' => '无效食材记录', 'url' => null));
            exit;
        }
        
        if ($ingd['instock'] < $_POST['change_val']) {
            echo json_encode(array('err' => -1, 'msg' => '无效出库数量', 'url' => null));
            exit;
        }
        
        // add stock change record
        $this->loadModel('WdminAdmin');
        $data = array();
        $data['ingd_id'] = $_POST['ingd_id'];
        $data['change_type'] = 2;
        $data['change_val'] = $_POST['change_val'];
        $data['spec'] = $_POST['spec'];
        $data['barcode'] = $_POST['barcode'];
        $data['vendor'] = $_POST['vendor'];
        $data['change_time'] = strtotime($_POST['change_time']);
        $data['change_user'] = $change_username;
        $data['change_note'] = $_POST['change_note'];
        $data['uid'] = $this->WdminAdmin->getAdminIdFromCookie();
        $data['add_time'] = time();
        //
        
        $ret = $this->Stock->checkout_ingredient($data);
        
        if ($ret) {
            $result = array('err' => 0, 'msg' => 'Added Successfully', 'url' => null);
        } else {
            $result = array('err' => -1, 'msg' => 'Add Failed', 'url' => null);
        }
        
        echo json_encode($result);
    }
    
    public function writedown_ingredient()
    {
        // do validation
        $change_username = trim($_POST['change_user']);
        if (strlen($change_username) < 1) {
            echo json_encode(array('err' => -1, 'msg' => '请设置减计人', 'url' => null));
            exit;
        }
        $val = intval($_POST['change_val']*1000);
        if ($val < 1) {
            echo json_encode(array('err' => -1, 'msg' => '请设置减计数量', 'url' => null));
            exit;
        }
        $price = intval($_POST['change_price']*100);
        if ($price <= 0) {
            echo json_encode(array('err' => -1, 'msg' => '请设置减计金额', 'url' => null));
            exit;
        }
        
        $this->loadModel('Stock');
        $ingd = $this->Stock->get_ingredient($_POST['ingd_id']);
        if (!$ingd) {
            echo json_encode(array('err' => -1, 'msg' => '无效食材记录', 'url' => null));
            exit;
        }
        
        if ($ingd['instock'] < $_POST['change_val']) {
            echo json_encode(array('err' => -1, 'msg' => '无效减计数量', 'url' => null));
            exit;
        }
        
        // add stock change record
        $this->loadModel('WdminAdmin');
        $data = array();
        $data['ingd_id'] = $_POST['ingd_id'];
        $data['change_type'] = 3;
        $data['change_val'] = $_POST['change_val'];
        $data['change_price'] = $price;
        $data['spec'] = $_POST['spec'];
        $data['barcode'] = $_POST['barcode'];
        $data['vendor'] = $_POST['vendor'];
        $data['change_time'] = strtotime($_POST['change_time']);
        $data['change_user'] = $change_username;
        $data['change_note'] = $_POST['change_note'];
        $data['uid'] = $this->WdminAdmin->getAdminIdFromCookie();
        $data['add_time'] = time();
        //
        
        $ret = $this->Stock->writedown_ingredient($data);
        
        if ($ret) {
            $result = array('err' => 0, 'msg' => 'Added Successfully', 'url' => null);
        } else {
            $result = array('err' => -1, 'msg' => 'Add Failed', 'url' => null);
        }
        
        echo json_encode($result);
    }

    public function update_dist_status($Q)
    {
        $id = $Q->id;
        $status = $Q->status;
        
        $this->loadModel('User');
        $openID = $this->getOpenId();
        $uid = $this->User->getUidByOpenId($openID);
        
        $this->loadModel('mOrderDistribute');
        $ret = $this->mOrderDistribute->update_distribute_status($id, $status, $uid);
        if ($ret) {
            $result = array('err' => 0, 'msg' => 'Update Successfully', 'url' => null);
            // update order status if needed
            if ($status == 'delievered') {
                //更改对应订单的状态为发货中
                $this->loadModel('mOrder');
                $distribute_info = $this->mOrderDistribute->get_distribute_info($id);
                $order_status = 'delivering';
                $this->mOrder->updateOrderStatusBySerialNo($distribute_info['order_serial_no'], $order_status);
                // 获取起点和终点的坐标，生成地图，并推送到外送人员手机上
                $start_lat = 31.24596585383; $start_lng = 121.51389359593;
                $dest_addr = $this->Db->getOneRow("SELECT * FROM `user_address` WHERE id = " . $distribute_info['address_id']);
                $url = "http://api.map.baidu.com/geocoder/v2/?address=".'上海市浦东新区'.$dest_addr['address']."&output=json&ak=0NnLgeO4V61jARaU0PMOT0OB";
                $ret = json_decode(Curl::get($url));
                $status = $ret->status;
                if ($status == 0) {
                    $location = $ret->result->location;
                    $dest_lat = $location->lat; $dest_lng = $location->lng;
                    // now generate map
                    $center_lat = ($start_lat+$dest_lat)/2;
                    $center_lng = ($start_lng+$dest_lng)/2;
                    //$map_url = 'http://api.map.baidu.com/staticimage/v2?ak=0NnLgeO4V61jARaU0PMOT0OB&width=640&height=980&center='.$center_lng.','.$center_lat.'&labels='.$start_lng.','.$start_lat.'|'.$dest_lng.','.$dest_lat.'&zoom=18&labelStyles='.urlencode('起点,1,17,0xffffff,0xff00ff,1|'.$dest_addr['address'].',1,17,0xffffff,0x0000ff,1');
                    $map_url = 'http://api.map.baidu.com/staticimage/v2?ak=0NnLgeO4V61jARaU0PMOT0OB&width=640&height=980&center='.$center_lng.','.$center_lat.'&markers='.$start_lng.','.$start_lat.'|'.$dest_lng.','.$dest_lat.'&zoom=18&markerStyles=l,A,0x00ff00|l,B,0x0000FF';
                    error_log('map url:'.$map_url);
                    // send map to deliver
                    error_log('notify openid:'.$openID);
                    $access_token = WechatSdk::getServiceAccessToken();
                    $ret = Messager::sendNotification($access_token, $openID, '订单：'.$distribute_info['order_serial_no'].'\n派送地址：'.$dest_addr['address'].'\n点击查看派送地图', $map_url);
                }
            } else if ($status == 'reached') {
                //更改对应订单的状态为已送达
                $this->loadModel('mOrder');
                $distribute_info = $this->mOrderDistribute->get_distribute_info($id);
                $order_status = 'received';
                $this->mOrder->updateOrderStatusBySerialNo($distribute_info['order_serial_no'], $order_status);
            }
        } else {
            $result = array('err' => -1, 'msg' => 'Update Failed', 'url' => null);
        }
        
        echo json_encode($result);
    }
    
        
    public function detailShare($data){
    
    	$share_id = $data->id;
    	$this->loadModel('User');
    	$this->loadModel('mShare');
    	$query = " where share_id = ".$share_id;
    	$list = $this->mShare->getUserShareTakeList($query);
    	foreach ($list as   $key => $val) {
    	   $u = $this->User->getUserInfo($val['uid']);
    	   $fromU = $this->User->getUserInfo($val['from_uid']);
    	   $list[$key]['uinfo'] = $u;
    	   $list[$key]['fromUinfo'] = $fromU;
    	}
        $this->assign('list', $list);
        $this->show('wdminpage/share/take_share_list.tpl');
    	
    }
}
