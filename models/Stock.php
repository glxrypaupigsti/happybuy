<?php
    
    class Stock extends Model
    {
        public function update_product($newData, $where)
        {
            // update next day "instock" val
            $ret = $this->Dao->update(TABLE_PRODUCT_STOCK)->set($newData)->where($where)->exec(false);
            if ($ret) {
                $target_day = $this->Dao->select()->from(TABLE_PRODUCT_STOCK)->where($where)->getOneRow();

                $nextday = strtotime(date('Y-n-j', $target_day['stock_date']+24*3600));
                $nextday_stock = $this->Dao->select()->from(TABLE_PRODUCT_STOCK)->where('`sku_id` = '.$target_day['sku_id'])->aw('`stock_date` = '.$nextday)->getOneRow();
                if ($nextday_stock) {
                    $new_stock = array();
                    $new_stock['instock'] = $target_day['produce']+$target_day['instock']-$target_day['sold']-$target_day['loss'];
                    $this->update_product($new_stock, '`id`='.$nextday_stock['id']);
                    unset($new_stock);
                }
            }
            
            return $ret;
        }
        
        public function get_product_stock_detail_by_id($id)
        {
            return $this->Dao->select()->from(TABLE_PRODUCT_STOCK)->where('`id` = '.$id)->getOneRow();
        }
        
        public function add_product($data)
        {
            if (!$data) return false;
            $columns = 'stock_date,sku_id,sku_name,avaliable,produce,instock';
            
            // normalize date
            $date_val = strtotime(date('Y-n-j', $data['stock_date']));
            // check if a record of target date exists
            $target_record = $this->Dao->select()->from(TABLE_PRODUCT_STOCK)->where('`stock_date` = '.$date_val)->aw('`sku_id` = '.$data['sku_id'])->getOneRow(false);
            if (!$target_record) {
                // calc instock from yesterday
                $yesterday = $this->Dao->select()->from(TABLE_PRODUCT_STOCK)->where('`stock_date` = '.($date_val-24*3600))->aw('`sku_id` = '.$data['sku_id'])->getOneRow(false);
                if ($yesterday) {
                    $stock = $yesterday['produce'] + $yesterday['instock'] - $yesterday['sold'] - $yesterday['loss'];
                    $data['instock'] = $stock;
                } else {
                    $data['instock'] = 0;
                }
                $data['stock_date'] = $date_val;
                //error_log('add prd data:'.json_encode($data));
                return $this->Dao->insert(TABLE_PRODUCT_STOCK, $columns)->values($data)->exec(false);
            } else {
                // already exists
                return false;
            }
        }
        
        public function get_product_instock_by_sku_and_date($sku_id, $date)
        {
            // normalize $date to beginning of a day
            $date_val = strtotime(date('Y-n-j', $date));
            // find record
            $data = array();
            $stock_info = $this->Dao->select()->from(TABLE_PRODUCT_STOCK)->where('`sku_id` = '.$sku_id)->aw('`stock_date` = '.$date_val)->getOneRow(false);
            if ($stock_info) {
                $data['stock'] = $stock_info['produce'] + $stock_info['instock'] - $stock_info['sold'] - $stock_info['loss'];
                $data['avaliable'] = $stock_info['avaliable'];
            } else {
                // request stock info of future?
                if ($date_val > time()) {
                    // future time
                    $data['stock'] = 100;
                    $data['avaliable'] = 100;
                } else {
                    $data['stock'] = 0;
                    $data['avaliable'] = 0;
                }
            }
            
            return $data;
        }
        
        public function sold_product_on_date($sku_id, $date, $sold_count)
        {
            // normalize date
            $date_val = strtotime(date('Y-n-j', $date));
            // check if a record of target date exists
            $target_record = $this->Dao->select()->from(TABLE_PRODUCT_STOCK)->where('`stock_date` = '.$date_val)->aw('`sku_id` = '.$sku_id)->getOneRow(false);
            
            if ($target_record) {
                // calc total sale-able
                $total = $target_record['produce'] + $target_record['instock'] - $target_record['loss'];
                // make sure sold doesn't exceed
                $sold = $target_record['sold'] + $sold_count;
                if ($sold > $total) {
                    error_log('sold['.$sold.'] exceeds total['.$total.']');
                    $sold = $total;
                }
                return $this->Dao->update(TABLE_PRODUCT_STOCK)->set(array('sold' => $sold))->where('`stock_date` = '.$date_val)->aw('`sku_id` = '.$sku_id)->exec(false);
            } else {
                // create new stock info
                $default_stocks = 999;
                $sku = $this->Dao->select()->from(TABLE_PRODUCT_SPEC)->where('id = '.$sku_id)->getOneRow();
                $spec = $this->Dao->select()->from(TABLE_SPEC)->where('id = '.$sku['spec_det_id1'])->getOneRow();
                $product = $this->Dao->select()->from(TABLE_PRODUCTS)->where('product_id = '.$sku['product_id'])->getOneRow();
                $columns = 'stock_date,sku_id,sku_name,avaliable,produce,instock,sold';
                $data = array('stock_date' => $date_val, 'sku_id' => $sku_id, 'sku_name' => $product['product_name'].'-'.$spec['det_name'],
                              'avaliable' => 0, 'produce' => $default_stocks, 'instock' => 0, 'sold' => $sold_count);
                return $this->Dao->insert(TABLE_PRODUCT_STOCK, $columns)->values($data)->exec(false);
            }
        }
        
        // Ingredients management
        public function get_all_ingredients()
        {
            return $this->Dao->select()->from(TABLE_INGREDIENTS_STOCK)->exec();
        }
        
        public function get_ingredient($id)
        {
            return $this->Dao->select()->from(TABLE_INGREDIENTS_STOCK)->where('id = '.$id)->getOneRow();
        }

        public function add_ingredient($data)
        {
            if (!$data) return false;
            $columns = 'ingd_name,ingd_unit,ingd_threshold,instock,ingd_cat';

            // check if a record of target date exists
            $target_record = $this->Dao->select()->from(TABLE_INGREDIENTS_STOCK)->where('`ingd_name` = `'.$data['ingd_name'].'`')->getOneRow(false);
            //error_log('ingd data:'.json_encode($data). '  checked:'.json_encode($target_record));
            if (!$target_record) {
                return $this->Dao->insert(TABLE_INGREDIENTS_STOCK, $columns)->values($data)->exec();
            } else {
                // already exists
                return false;
            }
        }
        
        public function load_ingredient_change_history($id)
        {
            if (!$id) return false;
            
            return $this->Dao->select()->from(TABLE_INGREDIENTS_STOCK_HISTORY)->where('`ingd_id` = '.$id)->orderby('change_time DESC')->exec(false);
        }

        public function load_change_history_by_id($id)
        {
            if (!$id) return false;
            
            return $this->Dao->select()->from(TABLE_INGREDIENTS_STOCK_HISTORY)->where('`id` = '.$id)->getOneRow();
        }
        
        public function load_change_history_by_ids($ids)
        {
            if (!$ids) return false;
            
            return $this->Dao->select()->from(TABLE_INGREDIENTS_STOCK_HISTORY)->where('id IN ('.$ids.')')->orderby('change_time DESC')->exec();
        }
        
        public function checkin_ingredient($data)
        {
            if (!$data) return false;
            
            $columns = 'ingd_id,change_type,change_val,change_price,spec,barcode,vendor,change_time,change_user,change_note,uid,add_time';
            $ret = $this->Dao->insert(TABLE_INGREDIENTS_STOCK_HISTORY, $columns)->values($data)->exec(false);
            if ($ret > 0) {
                // update stock summary
                $ingd_stock = $this->Dao->select()->from(TABLE_INGREDIENTS_STOCK)->where('id = '.$data['ingd_id'])->getOneRow(false);
                $new_stock = $ingd_stock['instock'] + $data['change_val'];
                error_log('update stock to:'.$new_stock);
                $this->Dao->update(TABLE_INGREDIENTS_STOCK)->set(array('instock' => $new_stock, 'last_update' => time()))->where('id = '.$data['ingd_id'])->exec(false);
                $this->Dao->update(TABLE_INGREDIENTS_STOCK_HISTORY)->set(array('instock' => $new_stock))->where('id = '.$ret)->exec(false);
            }
            
            return $ret;
        }
        
        public function checkout_ingredient($data)
        {
            if (!$data) return false;
            
            $columns = 'ingd_id,change_type,change_val,spec,barcode,vendor,change_time,change_user,change_note,uid,add_time';
            $ret = $this->Dao->insert(TABLE_INGREDIENTS_STOCK_HISTORY, $columns)->values($data)->exec(false);
            if ($ret > 0) {
                // update stock summary
                $ingd_stock = $this->Dao->select()->from(TABLE_INGREDIENTS_STOCK)->where('id = '.$data['ingd_id'])->getOneRow(false);
                $new_stock = $ingd_stock['instock'] - $data['change_val'];
                error_log('update stock to:'.$new_stock);
                $this->Dao->update(TABLE_INGREDIENTS_STOCK)->set(array('instock' => $new_stock, 'last_update' => time()))->where('id = '.$data['ingd_id'])->exec(false);
                $this->Dao->update(TABLE_INGREDIENTS_STOCK_HISTORY)->set(array('instock' => $new_stock))->where('id = '.$ret)->exec(false);
            }
            
            return $ret;
        }
        
        public function writedown_ingredient($data)
        {
            if (!$data) return false;

            $columns = 'ingd_id,change_type,change_val,change_price,spec,barcode,vendor,change_time,change_user,change_note,uid,add_time';
            $ret = $this->Dao->insert(TABLE_INGREDIENTS_STOCK_HISTORY, $columns)->values($data)->exec(false);
            if ($ret > 0) {
                // update stock summary
                $ingd_stock = $this->Dao->select()->from(TABLE_INGREDIENTS_STOCK)->where('id = '.$data['ingd_id'])->getOneRow(false);
                $new_stock = $ingd_stock['instock'] - $data['change_val'];
                $this->Dao->update(TABLE_INGREDIENTS_STOCK)->set(array('instock' => $new_stock, 'last_update' => time()))->where('id = '.$data['ingd_id'])->exec(false);
                $this->Dao->update(TABLE_INGREDIENTS_STOCK_HISTORY)->set(array('instock' => $new_stock))->where('id = '.$ret)->exec(false);
            }
            
            return $ret;
        }
        
    }
?>