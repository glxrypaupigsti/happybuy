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
class StatOverView extends Model {

    //put your code here

    public function getOverViewDatas() {
        $data = array();

        $QueryMonth = date('Y-m');
        $QueryDay = date('Y-m-d');
        $QueryYestoDay = date('Y-m-d', strtotime('-1 day'));

        // 新增粉丝
        $data['newfans'] = (int) $this->Db->getOne("SELECT SUM(dv) AS `sc` FROM `wechat_subscribe_record` WHERE DATE_FORMAT(`date`,'%Y-%m-%d') = '$QueryDay' AND `dv` > 0 GROUP BY DATE_FORMAT(`date`,'%Y-%m-%d');");
        // 取消关注粉丝
        $data['runfans'] = abs((int) $this->Db->getOne("SELECT SUM(dv) AS `sc` FROM `wechat_subscribe_record` WHERE DATE_FORMAT(`date`,'%Y-%m-%d') = '$QueryDay' AND `dv` < 0 GROUP BY DATE_FORMAT(`date`,'%Y-%m-%d');"));
        // 总粉丝
        $data['allfans'] = (int) $this->Db->getOne("SELECT SUM(dv) AS `sc` FROM `wechat_subscribe_record`;");
        // 新增会员
        $data['newuser'] = (int) $this->Db->getOne("SELECT COUNT(*) AS `sc` FROM `clients` WHERE DATE_FORMAT(`client_joindate`,'%Y-%m-%d') = '$QueryDay' AND `deleted` = 0;");
        // 新增代理
        $data['newcoms'] = (int) $this->Db->getOne("SELECT COUNT(*) AS `sc` FROM `companys` WHERE DATE_FORMAT(`join_date`,'%Y-%m-%d') = '$QueryDay' AND `deleted` = 0 AND `verifed` = 1;");
        // 总会员
        $data['alluser'] = (int) $this->Db->getOne("SELECT COUNT(*) AS `sc` FROM `clients` WHERE `deleted` = 0;");
        // 总代理
        $data['allcoms'] = (int) $this->Db->getOne("SELECT COUNT(*) AS `sc` FROM `companys` WHERE `deleted` = 0 AND `verifed` = 1;");
        // 今日成交
        $data['saletoday'] = (float) $this->Db->getOne("SELECT SUM(pay_amount) AS `sc` FROM `orders` WHERE DATE_FORMAT(`order_time`,'%Y-%m-%d') = '$QueryDay' AND `status` IN ('received', 'payed');");
        // 昨日成交
        $data['saleyestoday'] = (float) $this->Db->getOne("SELECT SUM(pay_amount) AS `sc` FROM `orders` WHERE DATE_FORMAT(`order_time`,'%Y-%m-%d') = '$QueryYestoDay' AND `status` IN ('received', 'payed');");
        // 本月成交
        $data['salemonth'] = (float) $this->Db->getOne("SELECT SUM(pay_amount) AS `sc` FROM `orders` WHERE DATE_FORMAT(`order_time`,'%Y-%m') = '$QueryMonth' AND `status` IN ('received', 'payed');");
        // 总成交
        $data['saletotal'] = (float) $this->Db->getOne("SELECT SUM(pay_amount) AS `sc` FROM `orders` WHERE `status` IN ('received', 'payed');");
        // 代理今日成交
        $data['pxsaletoday'] = (float) $this->Db->getOne("SELECT SUM(pay_amount) AS `sc` FROM `orders` WHERE DATE_FORMAT(`order_time`,'%Y-%m-%d') = '$QueryDay' AND `wepay_serial` <> '' AND `company_com` <> 0;");
        // 代理昨日成交
        $data['pxsaleyestoday'] = (float) $this->Db->getOne("SELECT SUM(pay_amount) AS `sc` FROM `orders` WHERE DATE_FORMAT(`order_time`,'%Y-%m-%d') = '$QueryYestoDay' AND `wepay_serial` <> '' AND `company_com` <> 0;");
        // 代理本月成交
        $data['pxsalemonth'] = (float) $this->Db->getOne("SELECT SUM(pay_amount) AS `sc` FROM `orders` WHERE DATE_FORMAT(`order_time`,'%Y-%m') = '$QueryMonth' AND `wepay_serial` <> '' AND `company_com` <> 0;");
        // 代理总成交
        $data['pxsaletotal'] = (float) $this->Db->getOne("SELECT SUM(pay_amount) AS `sc` FROM `orders` WHERE `wepay_serial` <> '' AND `company_com` <> 0;");
        // 今日新增订单
        $data['neworder'] = (int) $this->Db->getOne("SELECT COUNT(*) AS `sc` FROM `orders` WHERE DATE_FORMAT(`order_time`,'%Y-%m-%d') = '$QueryDay';");
        // 本月新增订单
        $data['neworder_month'] = (int) $this->Db->getOne("SELECT COUNT(*) AS `sc` FROM `orders` WHERE DATE_FORMAT(`order_time`,'%Y-%m') = '$QueryMonth';");
        // 本月已付款或已收货或快递中
        $data['valorder_month'] = (int) $this->Db->getOne("SELECT COUNT(*) AS `sc` FROM `orders` WHERE DATE_FORMAT(`order_time`,'%Y-%m') = '$QueryMonth' AND `status` <> 'canceled' AND `status` <> 'closed';");
        // 昨日新增订单
        $data['neworderyes'] = (int) $this->Db->getOne("SELECT COUNT(*) AS `sc` FROM `orders` WHERE DATE_FORMAT(`order_time`,'%Y-%m-%d') = '$QueryYestoDay';");
        // 今日新增订单 已付款
        $data['neworderpayed'] = (int) $this->Db->getOne("SELECT COUNT(*) AS `sc` FROM `orders` WHERE DATE_FORMAT(`order_time`,'%Y-%m-%d') = '$QueryDay' AND `wepay_serial` <> '';");
        // 昨日新增订单 已付款
        $data['neworderpayedyes'] = (int) $this->Db->getOne("SELECT COUNT(*) AS `sc` FROM `orders` WHERE DATE_FORMAT(`order_time`,'%Y-%m-%d') = '$QueryYestoDay' AND `wepay_serial` <> '';");
        // 订单已付款
        $data['orderpayed'] = (int) $this->Db->getOne("SELECT COUNT(*) AS `sc` FROM `orders` WHERE `status` = 'payed';");
        // 订单已发货
        $data['orderexped'] = (int) $this->Db->getOne("SELECT COUNT(*) AS `sc` FROM `orders` WHERE `status` = 'delivering';");
        // 订单退货申请
        $data['ordercanceled'] = (int) $this->Db->getOne("SELECT COUNT(*) AS `sc` FROM `orders` WHERE `status` = 'canceled' AND `wepay_serial` <> '';");
        // 本月订单
        $data['ordermonth'] = (int) $this->Db->getOne("SELECT COUNT(*) AS `sc` FROM `orders` WHERE DATE_FORMAT(`order_time`,'%Y-%m') = '$QueryMonth';");
        // 商品分类总数
        $data['catotal'] = (int) $this->Db->getOne("SELECT COUNT(*) AS `sc` FROM `product_category`;");
        // 商品总数
        $data['pdtotal'] = (int) $this->Db->getOne("SELECT COUNT(*) AS `sc` FROM `products_info` WHERE `delete` = 0;");
        // 平均商品浏览
        $data['pdtotalavg'] = (int) $this->Db->getOne("SELECT AVG(`product_readi`) AS `sc` FROM `products_info` WHERE `delete` = 0;");
        // 商品平均价格
        $data['pdpriceavg'] = sprintf('%.2f', $this->Db->getOne("SELECT AVG(`sale_prices`) AS `sc` FROM `product_onsale` `pos` LEFT JOIN `products_info` `pi` ON `pi`.product_id = `pos`.product_id WHERE pi.`delete` = 0"));
        return $data;
    }

}
