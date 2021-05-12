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
class mProductSpec extends Model {

    /**
     * 
     * @param type $id
     * @param type $spec_name
     * @param type $spec_remark
     * @param type $dets
     * @return boolean
     */
    public function alterSpec($id, $spec_name, $spec_remark, $dets, $append = false) {
        # $this->Db->debug = true;
        if (empty($id)) {
            $id = 'NULL';
            $SQL1 = "INSERT INTO `wshop_spec` (`spec_name`,`spec_remark`) VALUES ('$spec_name','$spec_remark');";
        } else {
            $id = intval($id);
            if ($id <= 0) {
                return false;
            }
            $SQL1 = "REPLACE INTO `wshop_spec` (`id`,`spec_name`,`spec_remark`) VALUES ($id,'$spec_name','$spec_remark');";
        }

        // bug!

        if ($id == 'NULL') {
            $id = $this->Db->query($SQL1);
            // 如果重名，追加内容
            if (!$id) {
                $id = intval($this->Db->getOne("SELECT id FROM `wshop_spec` WHERE `spec_name` = '$spec_name' AND `spec_remark` = '$spec_remark' LIMIT 1;"));
            }
        } else {
            $this->Db->query($SQL1);
        }

        if ($id !== false) {
            $ids = array();
            if (!$append) {
                // 追加模式
                $this->Db->query("DELETE FROM `wshop_spec_det` WHERE spec_id = $id;");
            }
            foreach ($dets as &$det) {
                if (empty($det['id'])) {
                    $det['id'] = 'NULL';
                }
                if ($det['name'] != '') {
                    array_push($ids, $this->Db->query("INSERT INTO `wshop_spec_det` (`id`,`spec_id`,`det_name`,`det_sort`) VALUES ($det[id], $id, '$det[name]', $det[sort]);"));
                }
            }
            return $id . '-' . implode('-', $ids);
        } else {
            return false;
        }
    }

    /**
     * 获取规格列表
     */
    public function getSpecList() {
        $ret = $this->Db->query("SELECT * FROM `wshop_spec`;");
        foreach ($ret as &$spec) {
            $spec['dets'] = $this->Db->query("SELECT * FROM `wshop_spec_det` WHERE `spec_id` = $spec[id];");
        }
        return $ret;
    }

    /**
     * 获取单个规格信息
     * @param type $id
     * @return boolean
     */
    public function getSpecData($id) {
        if (!empty($id) && is_numeric($id)) {
            $ret = $this->Db->getOneRow("SELECT * FROM `wshop_spec` WHERE `id` = $id;");
            if ($ret) {
                $ret['dets'] = $this->Db->query("SELECT * FROM `wshop_spec_det` WHERE `spec_id` = $id;");
                return $ret;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 获取商品规格名
     * @param type $id
     * @return type
     */
    public function getProductSpecName($id) {
        $r1 = $this->Dao->select()->from('product_spec')->where("id = $id")->getOneRow();
        $r2 = $this->Dao->select()->from('wshop_spec_det')->where("id = " . $r1['spec_det_id1'])->getOneRow();
        $r3 = $this->Dao->select()->from('wshop_spec_det')->where("id = " . $r1['spec_det_id2'])->getOneRow();
        $r4 = $this->Dao->select()->from('wshop_spec')->where("id = " . $r2['spec_id'])->getOneRow();
        return $r4['spec_name'] . '(' . $r2['det_name'] . $r3['det_name'] . ')';
    }

    /**
     * 查询规格对应的市场价格
     * @param unknown $spec_id  
     **/
    public function get_spec_sale_price($spec_id){
    	$ret = $this->Dao->select()->from(TABLE_PRODUCT_SPEC)->where('id='.$spec_id)->getOneRow();
        if ($ret) {
            // HACK:check current weekday to find prefix should be added
            /*
            $weekday = date('N', strtotime($_COOKIE['deliver_date']));
            if (4 == $weekday) {
                // Thursday is "HALF-day" sale
                $discount = 1;
            } else {
                $discount = 1;
            }
            */
            $discount = 0.9;
            // adjust sale price
            $ret['sale_price'] = $ret['sale_price']*$discount;
        }
        return $ret;
    }
    
    /**
     * 键商品的库存
     * @param unknown $spec_id
     **/
   	public function dec_spec_instock($spec_id,$dec_num){
   		$query = 'update ' . TABLE_PRODUCT_SPEC .'set instock = instock-'.intval($dec_num).' where id = '.$spec_id;
   		return $this->Db->query($query,false);
   	}
    
    
}
