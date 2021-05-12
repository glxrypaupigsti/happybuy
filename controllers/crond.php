<?php
    
    class crond extends Controller
    {
        
        public function __construct($ControllerName, $Action, $QueryString) {
            parent::__construct($ControllerName, $Action, $QueryString);
        }
        
        public function daily()
        {
            error_log('daily crond started');
            // update product stock
            $data = array();
            $data['stock_date'] = time();

            //'stock_date,sku_id,sku_name,avaliable,produce,instock'
            $this->loadModel('Sku');
            // get products list
            $sku_list = $this->Sku->get_all_skus();
            if ($sku_list) {
                $this->loadModel('Stock');
                foreach ($sku_list AS $val) {
                    $data['sku_id'] = $val['id'];
                    $data['sku_name'] = $val['name'];
                    $data['avaliable'] = 0;
                    $data['produce'] = 0;
                    $ret = $this->Stock->add_product($data);
                }
            }
        }
    }

?>