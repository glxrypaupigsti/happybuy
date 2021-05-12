<?php

/**
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class Wdmin extends Controller {

    const COOKIE_EXP = 28800;
    const LIST_LIMIT = 100;
    const loginKeyK = '4s5mpxa';

    /**
     * 
     * @param type $ControllerName
     * @param type $Action
     * @param type $QueryString
     */
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        $this->loadModel('Session');
    }

    /**
     * 平台首页
     * @todo loginKey校验
     */
    public function index() {
        if ($this->pCookie('loginKey')) {
            #$this->recycle();
            if (is_numeric($this->pCookie('lev'))) {
                $authStr = urldecode($this->pCookie('auth'));
                $this->cacheId = $authStr;
                $this->Smarty->cache_lifetime = 7200;
                if (!$this->isCached()) {
                    $authArr = array();
                    foreach (explode(',', $authStr) as $a) {
                        $authArr[$a] = 1;
                    }
                    $this->Smarty->assign('admin_level', $this->pCookie('lev'));
                    $this->Smarty->assign('Auth', $authArr);
                    $weekarray = array("日", "一", "二", "三", "四", "五", "六");
                    $this->Smarty->assign('today', date("n月j号 星期") . $weekarray[date('w')]);
                }
                $this->show();
            }
        } else {
            header('Location:' . $this->root . '?/Wdmin/login');
            exit(0);
        }
    }

    /**
     * 退出登录清空cookie
     */
    public function logOut() {
        foreach ($_COOKIE as $k => $v) {
            setcookie($k, NULL);
        }
        header('Location:?/Wdmin/login/');
    }

    /**
     * ajax check login code scan status
     * perform++
     */
    public function checkLogin() {
        $this->Session->start();
        $ip = $this->getIp();
        $this->loadModel('WdminAdmin');
        $admin_acc = addslashes($this->post('admin_acc'));
        $admin_pwd = addslashes($this->post('admin_pwd'));
        // 保存登录账户
        $this->sCookie('admin_acc', $admin_acc, self::COOKIE_EXP);
        // company login <deprecated>
        if (preg_match('#^pa.\d+#', $admin_acc)) {
            // company login
            $this->loadModel('mCompany');
            $admin_acc = str_replace('pa', '', $admin_acc);
            $id = $this->mCompany->validatePwd($admin_acc, $admin_pwd);
            if ($id) {
                $loginKey = $this->WdminAdmin->encryptToken($ip, $id, $admin_pwd);
                $this->Session->set('loginKey', $loginKey);
                $this->sCookieHttpOnly('admin_id', $id, self::COOKIE_EXP);
                $this->sCookieHttpOnly('loginKey', $loginKey, self::COOKIE_EXP);
                $this->sCookieHttpOnly('comid', $this->Util->digEncrypt($id), self::COOKIE_EXP);
                $this->sCookieHttpOnly('lev', 1, self::COOKIE_EXP);
                $this->echoJson(array('status' => 1));
            } else {
                $this->echoJson(array('status' => 0));
            }
        } else {
            // admin login
            $real_pwd = $this->Dao->select()->from('admin')->where("admin_account = '$admin_acc'")->getOneRow();
            if ($real_pwd) {
                // 校验成功
                if ($this->WdminAdmin->pwdCheck((string) $real_pwd['admin_password'], $admin_acc, $admin_pwd)) {
                    // 更新管理员登录状态
                    $this->WdminAdmin->updateAdminState($ip, $real_pwd['id']);
                    // 权限密钥
                    $loginKey = $this->WdminAdmin->encryptToken($ip, $id, $admin_pwd);
                    $this->Session->set('loginKey', $loginKey);
                    $this->sCookieHttpOnly('admin_id', $real_pwd['id'], self::COOKIE_EXP);
                    // 下发管理员权限表
                    $this->sCookieHttpOnly('auth', $real_pwd['admin_auth'], self::COOKIE_EXP);
                    $this->sCookieHttpOnly('loginKey', $loginKey, self::COOKIE_EXP);
                    $this->sCookieHttpOnly('lev', 0, self::COOKIE_EXP);
                    // 删除cookie
                    $this->sCookie('admin_acc', '', 1);
                    // 成功
                    $this->echoJson(array('status' => 1));
                } else {
                    // 失败
                    $this->echoJson(array('status' => 0));
                }
            } else {
                // 失败
                $this->echoJson(array('status' => 0));
            }
        }
        $this->sCookie('admin_acc', null);
    }

    /**
     * login page
     */
    public function login() {
        $this->Smarty->assign('rand', (int) rand(1, 8));
        $this->Smarty->assign('ip', $this->getIp());
        $this->show();
    }


    /**
     * 获取订单列表
     * ajaxLoadOrderlist
     */
    public function ajaxLoadOrderlist($Query) {

        $this->cacheId = hash('md4', serialize($Query));

        if (!$this->isCached()) {
            $this->loadModel('mOrder');
            global $config;
            $express = include dirname(__FILE__) . '/../config/express_code.php';
            !isset($Query->page) && $Query->page = 0;
            // where
            if (isset($Query->status)) {
                if ($Query->status == 'all') {
                    $WHERE = '';
                } else {
                    if ($Query->status == 'canceled') {
                        // 退货而且已经支付才需要审核，否则直接关闭订单
                        $WHERE = " WHERE status = '$Query->status' ";
                    } else {
                        $WHERE = " WHERE status = '$Query->status' ";
                    }
                }
            } else {
                $Query->status = 'payed';
                $WHERE = " WHERE status = '$Query->status' ";
            }

            if (isset($Query->cid) && is_numeric($Query->cid)) {
                $WHERE .= " WHERE client_id = $Query->cid ";
            }

            if (isset($Query->month) && !empty($Query->month) && $Query->status != 'canceled') {
                if ($Query->status == 'all') {
                    $WHERE .= " WHERE DATE_FORMAT(order_time,'%Y-%c') = '$Query->month' ";
                } else if ($Query->status == 'delivering') {
//                     $WHERE .= "AND DATE_FORMAT(send_time,'%Y-%c') = '$Query->month' ";
                    $WHERE .= "AND DATE_FORMAT(order_time,'%Y-%c') = '$Query->month' ";
                } else {
                    $WHERE .= "AND DATE_FORMAT(order_time,'%Y-%c') = '$Query->month' ";
                }
            }

            $Limit = $Query->page * self::LIST_LIMIT . "," . self::LIST_LIMIT;
            $SQL = sprintf("SELECT * FROM `orders`%s ORDER BY `order_id` DESC LIMIT $Limit;", $WHERE);

            $orderList = $this->Db->query($SQL);

            if ($Query->status == 'canceled') {
                foreach ($orderList as &$od) {
                    if ($od['order_amount'] < 1) {
                        $od['refundable'] = $od['order_amount'];
                    } else {
                        $od['refundable'] = $this->mOrder->getUnRefunded($od['order_id']);
                    }
                }
            }

            /**
             * 加工
             */
            foreach ($orderList as $index => $order) {
                // company
                if ($order['company_com'] > 0) {
                    $orderList[$index]['company'] = $this->Db->getOneRow("SELECT `id`,`name` FROM `companys` WHERE `id` = $order[company_com];");
                }
                $address_id = $orderList[$index]['address_id'];
                // address
                $address = $this->Db->query("SELECT * FROM `user_address` WHERE id = $address_id;");
                $orderList[$index]['address'] = $address[0];
                $orderList[$index]['order_time'] = $this->Util->dateTimeFormat($orderList[$index]['order_time']);
                $orderList[$index]['statusX'] = $config->orderStatus[$orderList[$index]['status']];
                $orderList[$index]['expressName'] = $express[$orderList[$index]['express_com']];
                // product info
                $orderList[$index]['data'] = $this->Db->query("SELECT catimg,`pi`.product_name,`pi`.product_id,`sd`.product_count,`sd`.product_discount_price FROM `orders_detail` sd LEFT JOIN `vproductinfo` pi on pi.product_id = sd.product_id WHERE `sd`.order_id = " . $order['order_id']);
            }

            $this->Smarty->assign('olistcount', count($orderList));
            $this->Smarty->assign('orderlist', $orderList);
            $this->Smarty->assign('orderlist_str', json_encode($orderList));
        }

        if (isset($Query->export)) {
            $this->show('wdminpage/orders/ajaxloadorderlist_export.tpl');
        } else {
            if (isset($Query->cid) && is_numeric($Query->cid)) {
                $this->show('wdminpage/orders/ajaxloadorderlist_' . $Query->status . '_customer.tpl');
            } else {
                $this->show('wdminpage/orders/ajaxloadorderlist_' . $Query->status . '.tpl');
            }
        }
    }

    /**
     * 快递查询api
     * @see http://www.kuaidiapi.cn/
     * @param type $Query
     */
    public function ajaxLoadOrderExpress($Query) {
        $this->cacheId = $Query->com . $Query->nu;
        if (!$this->isCached()) {
            $typeCom = $Query->com; //快递公司
            $typeNu = $Query->nu;  //快递单号
            $url = "http://www.kuaidiapi.cn/rest/?uid=23350&key=7614261fa71a4948ad73795e88d958af&order=$typeNu&id=$typeCom";
            $this->Smarty->assign('res', json_decode(Curl::get($url), true));
        }
        $this->show();
    }

    public function recycle() {
        $dirArr = array(
            dirname(__FILE__) . '/../uploads/product_hpic_tmp/',
            dirname(__FILE__) . '/../uploads/banner_tmp/',
            dirname(__FILE__) . '/../uploads/gmess_tmp/'
        );
        foreach ($dirArr as $dir) {
            $dirs = dir($dir);
            if ($dirs && is_readable($dirs)) {
                try {
                    while ($file = $dirs->read()) {
                        $file = $dir . $file;
                        if (is_file($file)) {
                            if (time() - filemtime($file) > 86400) {
                                unlink($file);
                            }
                        }
                    }
                } catch (Exception $ex) {
                    continue;
                }
            }
        }
    }
    
    public function updateOrderStatus($Q)
    {
        $id = $Q->id;
        $this->loadModel('mOrder');
        $this->loadModel('mUserAddress');
        
        $order_info = $this->mOrder->get_order_info_by_id($id);
        $openid = $this->getOpenId();
        
        $this->loadModel('Product');
        $orderProductsList = $this->Db->query("SELECT `catimg`,`pi`.product_name,`pi`.product_id,`sd`.product_count,`sd`.product_discount_price,`sd`.product_price_hash_id FROM `orders_detail` sd LEFT JOIN `products_info` pi on pi.product_id = sd.product_id WHERE `sd`.order_id = " . $id);
        $order_info['address'] = $this->mUserAddress->get_user_address_by_id($order_info['address_id']);
        $this->assign('orderdetail', $order_info);
        $this->Smarty->assign('productlist', $orderProductsList);
        
        $this->Smarty->cache_lifetime = 0;
        
        // get distribute status
        $this->loadModel('mOrderDistribute');
        $distri_info = $this->mOrderDistribute->get_distribute_info_by_order($order_info['serial_number']);
        $distri_status = '';
        $btnArray = array();
        switch ($distri_info['status']) {
            case 'not_delievery':
                $distri_status = '等待配货';
                $btnArray[] = array('title' => '配货完成', 'status' => 'delievering');
                break;
            case 'delievering':
                $distri_status = '等待发货';
                $btnArray[] = array('title' => '发货', 'status' => 'delievered');
                break;
            case 'delievered':
                $distri_status = '已发货';
                $btnArray[] = array('title' => '已送达', 'status' => 'reached');
                //$btnArray[] = array('title' => '未送达', 'status' => 'not_reached');
                break;
            case 'reached':
                $distri_status = '已送达';
                break;
            case 'not_reached':
                $distri_status = '未送达';
                break;
            case 'cancel':
                $distri_status = '已取消';
                break;
                
        }
        $this->assign('distri_id', $distri_info['id']);
        $this->assign('distri_status', $distri_status);
        $this->assign('next_op', $btnArray);
        $this->show('./order/updateStatus.tpl');
    }

}
