<?php
    
    class Sku extends Model
    {
        public function get_all_skus()
        {
            // get all active prds
            $prd_list = $this->Dao->select()->from(TABLE_PRODUCTS)->where('`delete` = 0')->exec(false);
            //error_log('prd_list:'.json_encode($prd_list));
            if ($prd_list) {
                $sku_list = array();
                foreach ($prd_list AS $val) {
                    $this_prd_skus = $this->Dao->select()->from(TABLE_PRODUCT_SPEC)->where('`product_id` = '.$val['product_id'])->exec(false);
                    foreach ($this_prd_skus AS $spec) {
                        $sku_name = $val['product_name'];
                        if ($spec['spec_det_id1'] > 0) {
                            $spec1_info = $this->Dao->select()->from(TABLE_SPEC)->where('`id` = '.$spec['spec_det_id1'])->getOneRow(false);
                            $sku_name .= '-'.$spec1_info['det_name'];
                        }
                        if ($spec['spec_det_id2'] > 0) {
                            $spec2_info = $this->Dao->select()->from(TABLE_SPEC)->where('`id` = '.$spec['spec_det_id2'])->getOneRow(false);
                            $sku_name .= '-'.$spec2_info['det_name'];
                        }
                        $sku_list[] = array('id' => $spec['id'], 'name'=>$sku_name);
                    }
                }
            }
            
            return $sku_list;
        }
        
    }
?>