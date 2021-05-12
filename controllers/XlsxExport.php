<?php

/**
 * @author <ycchen@iwshop.cn>
 */
class XlsxExport extends Controller {

    const TPL = './views/wdminpage/xlsx_export/';

    // 当前excel活动页
    private $sheet;

    /**
     * 导出类别名称
     * @var type 
     */
    private $convName = array(
        0 => '转换1 ',
        1 => '转换2 ',
        2 => '订单导出1',
        3 => '订单导出2'
    );

    /**
     * 导出模板映射
     * @var type 
     */
    private $convTemplates = array(
        0 => 'sample_1.xlsx',
        1 => 'sample_2.xlsx',
        2 => 'sample_1.xlsx',
        3 => 'sample_2.xlsx'
    );

    /**
     * 导出起始下标
     * @var type 
     */
    private $exportOffset = array(
        0 => 3,
        1 => 2,
        2 => 3,
        3 => 2
    );

    /**
     * 导出函数映射
     */
    private $exportFuncs = array(
        0 => 0,
        1 => 1,
        2 => 0,
        3 => 1
    );

    /**
     * 
     * @param type $ControllerName
     * @param type $Action
     * @param type $QueryString
     */
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
    }

    /**
     * 微店数据导出
     * @param type $Query
     */
    public function exportTransform($Query) {
        $Query->islocal = isset($Query->islocal) ? $Query->islocal : 0;

        if ($Query->islocal == 1) {
            $this->Smarty->assign('ids', $Query->odlist);
        }
        $this->Smarty->assign('islocal', $Query->islocal);
        
        $this->show(self::TPL . 'export_transform.tpl');
    }

    /**
     * 苏宁订单格式导入到html列表
     */
    public function ajaxXlsTransform() {
        if (!empty($_FILES)) {
            $dirname = dirname(__FILE__) . "/../exports/orders_export/export_files_upload/";
            $tempFile = $_FILES['jUploaderFile']['tmp_name'];
            $targetPath = $dirname;
            $targetFileName = md5($_COOKIE['_uid'] . time()) . '.' . strtolower(end(explode('.', $_FILES['jUploaderFile']['name'])));
            $targetFile = str_replace('//', '/', $targetPath) . $targetFileName;

            $state = move_uploaded_file($tempFile, $targetFile);
            chmod($targetFile, 0644);
        }
        if ($state) {
            #$this->loadModel('Product');

            include dirname(__FILE__) . '/../lib/PHPExcel/Classes/PHPExcel.php';

            include dirname(__FILE__) . '/../lib/PHPExcel/Classes/PHPExcel/Reader/Excel2007.php';

            $PHPReader = new PHPExcel_Reader_Excel2007();

            if (!$PHPReader->canRead($targetFile)) {
                $PHPReader = new PHPExcel_Reader_Excel5();
                if (!$PHPReader->canRead($targetFile)) {
                    echo 0;
                    exit(0);
                }
            }

            // 源格式
            $PHPExcelSource = $PHPReader->load($targetFile);
            $this->sheet = $PHPExcelSource->getActiveSheet();
            $allRow = $this->sheet->getHighestRow();
            $orderList = array();
            // 第三行开始读取
            for ($offset = 3; $offset <= $allRow; $offset++) {
                #$product_code = $this->getCellValue("L$offset");
                #$user_name = $this->getCellValue("X$offset");
                #$product_subname = $this->Product->getProductSubnameByCode($product_code);
                // 在备注中提取身份证号码
                if (preg_match('/(\d{17}([0-9]|X))/i', $this->getCellValue("AN$offset"), $r)) {
                    $personId = $r[1];
                } else {
                    $personId = '';
                }
                $orderList[] = array(
                    'serial_number' => $this->getCellValue("A$offset"),
                    'user_name' => $this->getCellValue("Y$offset"),
                    'addr' => $this->getCellValue("Z$offset"),
                    'tel_number' => $this->getCellValue("AB$offset"),
                    'product_name' => $this->getCellValue("N$offset"),
                    'product_code' => '',
                    'product_count' => $this->getCellValue("R$offset"),
                    'product_discount_price' => sprintf('%.2f', $this->getCellValue("S$offset")),
                    'total' => sprintf('%.2f', $this->getCellValue("U$offset")),
                    'order_time' => $this->getCellValue("F$offset"),
                    'ids' => $personId
                );
            }
            $this->Smarty->assign('fullwidth', true);
            $this->Smarty->assign('orderlist', $orderList);
            $this->show('./views/wdminpage/orders/order_list_export_table.tpl');
        } else {
            echo 0;
        }
    }

    // 导出转换或微店订单导出
    public function exportOrderListWithType() {

        include dirname(__FILE__) . '/../lib/PHPExcel/Classes/PHPExcel.php';

        include dirname(__FILE__) . '/../lib/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';

        include dirname(__FILE__) . '/../lib/PHPExcel/Classes/PHPExcel/Reader/Excel2007.php';

        $datas = $_POST['data'];
        $exportLink = array();
        foreach ($datas as $expType => $data) {
            $expType = intval(str_replace('_', '', $expType));
            // exptype 下标即为导出类别
            if (count($data) > 0) {
                // 数组有数据
                $templateName = dirname(__FILE__) . '/../exports/orders_export/order_exp_sample/' . $this->convTemplates[$expType];

                $PHPReader = new PHPExcel_Reader_Excel2007();

                if (!$PHPReader->canRead($templateName)) {
                    $PHPReader = new PHPExcel_Reader_Excel5();
                    if (!$PHPReader->canRead($templateName)) {
                        echo '无法识别的Excel文件！';
                        return false;
                    }
                }

                $PHPExcel = $PHPReader->load($templateName);

                $func = "genXlsxFileType" . $this->exportFuncs[$expType];

                // 导出操作
                $exportLink[$expType] = array(
                    'set' => 1,
                    'name' => $this->convName[$expType],
                    'link' => $this->$func($data, $PHPExcel, $PHPExcel->getActiveSheet(), $this->exportOffset[$expType], $expType)
                );
            } else {
                $exportLink[$expType] = array(
                    'set' => 0
                );
                continue;
            }
        }
        // 输出转换地址列表
        $this->echoJson($exportLink);
    }
    
    public function exportStockChangeLog($Q)
    {
        include dirname(__FILE__) . '/../lib/PHPExcel/Classes/PHPExcel.php';
        include dirname(__FILE__) . '/../lib/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';
        include dirname(__FILE__) . '/../lib/PHPExcel/Classes/PHPExcel/Reader/Excel2007.php';
        
        // load ingredient stock changelog data
        $this->loadModel('Stock');
        $stock_ids = explode(',', $Q->ids);
        if (count($stock_ids) > 0) {
            // load excel template
            $templateName = dirname(__FILE__) . '/../exports/orders_export/order_exp_sample/stock_changelog.xlsx';
            
            $PHPReader = new PHPExcel_Reader_Excel2007();
            if (!$PHPReader->canRead($templateName)) {
                $PHPReader = new PHPExcel_Reader_Excel5();
                if (!$PHPReader->canRead($templateName)) {
                    echo '无法识别的Excel文件！';
                    return false;
                }
            }
            $PHPExcel = $PHPReader->load($templateName);
            
            // write changelog data
            $sheet = $PHPExcel->getActiveSheet();
            $i = 3;
            
            foreach ($stock_ids AS $val) {
                $stock_info = $this->Stock->get_product_stock_detail_by_id($val);error_log('$stock_info:'.json_encode($stock_info));
                $remain = $stock_info['produce'] + $stock_info['instock'] - $stock_info['sold'] - $stock_info['loss'];
                $sheet->setCellValue('A'.$i, date('Y-m-d', $stock_info['stock_date']));
                $sheet->setCellValue('B'.$i, $stock_info['sku_name']);
                $sheet->setCellValue('C'.$i, $stock_info['avaliable']);
                $sheet->setCellValue('D'.$i, $stock_info['instock']);
                $sheet->setCellValue('E'.$i, $stock_info['produce']);
                $sheet->setCellValue('F'.$i, $stock_info['sold']);
                $sheet->setCellValue('G'.$i, $stock_info['loss']);
                $sheet->setCellValue('H'.$i, $remain);
                $i++;
            }
            // Rename worksheet
            $sheet->setTitle('变动明细');
            
            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="产品库存历史明细.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0
            $objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }
    }
    
    public function exportIngredientChangeLog($Q)
    {
        include dirname(__FILE__) . '/../lib/PHPExcel/Classes/PHPExcel.php';
        include dirname(__FILE__) . '/../lib/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';
        include dirname(__FILE__) . '/../lib/PHPExcel/Classes/PHPExcel/Reader/Excel2007.php';
        
        // load ingredient stock changelog data
        $this->loadModel('Stock');
        $data = $this->Stock->load_change_history_by_ids($Q->ids);
        
        if (count($data) > 0) {
            $this->loadModel('Stock');
            $ingd = $this->Stock->get_ingredient($data[0]['ingd_id']);
            // load excel template
            $templateName = dirname(__FILE__) . '/../exports/orders_export/order_exp_sample/ingd_changelog.xlsx';
            
            $PHPReader = new PHPExcel_Reader_Excel2007();
            if (!$PHPReader->canRead($templateName)) {
                $PHPReader = new PHPExcel_Reader_Excel5();
                if (!$PHPReader->canRead($templateName)) {
                    echo '无法识别的Excel文件！';
                    return false;
                }
            }
            $PHPExcel = $PHPReader->load($templateName);

            // write changelog data
            $sheet = $PHPExcel->getActiveSheet();
            $i = 3;
            foreach ($data as $key => $val) {
                switch ($val['change_type']) {
                    case '1':
                        $op_str = '入库';
                        $initial_stock = $val['instock'] - $val['change_val'];
                        break;
                    case '2':
                        $op_str = '出库';
                        $initial_stock = $val['instock'] + $val['change_val'];
                        break;
                    case '3':
                        $op_str = '减计';
                        $initial_stock = $val['instock'] + $val['change_val'];
                        break;
                }
                switch ($ingd['ingd_unit']) {
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
                $sheet->setCellValue('A'.$i, date('Y-m-d H:M:s', $val['change_time']));
                $sheet->setCellValue('B'.$i, $initial_stock.$unit_str);
                $sheet->setCellValue('C'.$i, $val['instock'].$unit_str);
                $sheet->setCellValue('D'.$i, $op_str);
                $sheet->setCellValue('E'.$i, $val['change_val']);
                $sheet->setCellValue('F'.$i, $val['change_price']/100.0);
                $sheet->setCellValue('G'.$i, $val['vendor']);
                $sheet->setCellValue('H'.$i, $val['change_user']);
                $i++;
            }
            
            // Rename worksheet
            $sheet->setTitle('变动明细');

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$ingd['ingd_name'].'.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0
            $objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }
    }

    /**
     * * -> 广州仓 转换函数
     * @global type $config
     * @param type $data
     * @param type $PHPExcel
     * @param type $Sheet
     * @param type $offset
     * @param type $expType
     * @return type
     */
    private function genXlsxFileType1($data, $PHPExcel, $Sheet, $offset, $expType = 1) {
        global $config;
        $this->loadModel('AddressApart');
        $date = date('Y-m-d');
        $orderId = '熹贝' . date('Ymd');
        $contactCount = 0;
        $contactCountA = array();

        $Sheet->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);

        foreach ($data as $index => $da) {

            if (!in_array($da['name'], $contactCountA)) {
                $contactCountA[++$contactCount] = $da['name'];
            }

            $Sheet->setCellValue("A$offset", $orderId . sprintf('%03s', $contactCount));


            $Sheet->setCellValueExplicit("B$offset", $date, PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->setCellValueExplicit("I$offset", $date, PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->setCellValueExplicit("J$offset", $date, PHPExcel_Cell_DataType::TYPE_STRING);

            $Sheet->setCellValue("C$offset", $da['name']);
            $Sheet->getStyle("C$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $Sheet->setCellValue("D$offset", $da['name']);
            $Sheet->getStyle("D$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $Sheet->setCellValue("P$offset", 0);
            $Sheet->getStyle("P$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $Sheet->setCellValue("Q$offset", $da['pcount']);
            $Sheet->getStyle("Q$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $Sheet->setCellValue("AT$offset", "熹贝母婴");

            $Sheet->setCellValue("K$offset", "熹贝跨境商贸");

            $Sheet->setCellValueExplicit("E$offset", $da['tels'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->getStyle("E$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $Sheet->setCellValue("H$offset", $da['addr']);
            $Sheet->getStyle("H$offset")->getFont()->setSize(10);
            $Sheet->getStyle("H$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            // 地址拆分
            if ($expType == 1) {
                $addr = $this->AddressApart->apart($da['addr'], AddressApart::ADDR_TYPE_SIMP);
            } else {
                $addr = $this->AddressApart->apart($da['addr'], AddressApart::ADDR_TYPE_FULL);
            }

            $Sheet->setCellValue("AC$offset", $addr[0]);

            $Sheet->setCellValue("AD$offset", $addr[1]);

            $Sheet->setCellValue("AE$offset", $addr[2]);

            $Sheet->setCellValueExplicit("L$offset", $da['pdcode'], PHPExcel_Cell_DataType::TYPE_STRING);

            $offset++;
        }
        // 写入文件
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);
        $fileName = date('Y-md') . '-' . $this->convName[$expType] . '-' . uniqid() . '.xlsx';
        $objWriter->save(dirname(__FILE__) . '/../exports/orders_export/export_files/' . $this->convGbk($fileName));
        return "http://" . $_SERVER['HTTP_HOST'] . $config->shoproot . 'exports/orders_export/export_files/' . $fileName;
    }

    /**
     * * -> 保税仓 转换函数
     * @global type $config
     * @param type $data
     * @param type $PHPExcel
     * @param type $Sheet
     * @param type $offset
     * @param type $expType
     * @return type
     */
    private function genXlsxFileType0($data, $PHPExcel, $Sheet, $offset, $expType = 0) {
        global $config;

        $Sheet->getColumnDimension('G')->setWidth(55);
        $Sheet->getColumnDimension('J')->setWidth(55);

        foreach ($data as $da) {
            $Sheet->getRowDimension($offset)->setRowHeight(30);
            $Sheet->setCellValueExplicit("A$offset", $da['orderid'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->getStyle("A$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $Sheet->setCellValueExplicit("B$offset", $da['date'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->getStyle("B$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $Sheet->setCellValue("C$offset", '保税仓');
            $Sheet->getStyle("C$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $Sheet->setCellValue("D$offset", "13360030356");
            $Sheet->getStyle("D$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $Sheet->setCellValue("E$offset", $da['name']);
            $Sheet->getStyle("E$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $Sheet->setCellValueExplicit("F$offset", $da['pids'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->getStyle("F$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $Sheet->setCellValue("G$offset", $da['addr']);
            $Sheet->getStyle("G$offset")->getFont()->setSize(10);
            $Sheet->getStyle("G$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $Sheet->setCellValueExplicit("H$offset", $da['tels'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->getStyle("H$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $Sheet->setCellValueExplicit("I$offset", $da['pdcode'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->getStyle("I$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $Sheet->setCellValueExplicit("J$offset", $da['pdname'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->getStyle("J$offset")->getFont()->setSize(10);
            $Sheet->getStyle("J$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $Sheet->setCellValue("K$offset", $da['pcount']);
            $Sheet->getStyle("K$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $Sheet->setCellValueExplicit("L$offset", sprintf('%.2f', $da['pric_sig']), PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->getStyle("L$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $Sheet->setCellValueExplicit("M$offset", sprintf('%.2f', $da['pric_tot']), PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->getStyle("M$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            if ($da['pric_tot'] >= 500) {
                $Sheet->getStyle("M$offset")->getFont()->setBold(true);
            }

            $Sheet->setCellValueExplicit("N$offset", $da['yunfei'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->getStyle("N$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $Sheet->setCellValueExplicit("O$offset", $da['poscode'], PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->getStyle("O$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $Sheet->setCellValueExplicit("Q$offset", 0, PHPExcel_Cell_DataType::TYPE_STRING);
            $Sheet->getStyle("Q$offset")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $offset++;
        }
        // 写入文件
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);
        $fileName = date('Y-md') . '-' . $this->convName[$expType] . '-' . uniqid() . '.xlsx';
        $objWriter->save(dirname(__FILE__) . '/../exports/orders_export/export_files/' . $this->convGbk($fileName));
        return "http://" . $_SERVER['HTTP_HOST'] . $config->shoproot . 'exports/orders_export/export_files/' . $fileName;
    }

    /**
     * 获取单元格内容值
     * @param type $addr
     * @return type
     */
    private function getCellValue($addr) {
        $cell = $this->sheet->getCell($addr)->getValue();
        //富文本转换
        if ($cell instanceof PHPExcel_RichText) {
            $cell = $cell->__toString();
        }
        return $cell;
    }

    /**
     * iconv编码转换gbk
     * @param type $fileName
     * @return type
     */
    private function convGbk($fileName) {
        if (DIRECTORY_SEPARATOR == '\\') {
            // window 中文文件名转换
            return iconv("utf-8", "gbk//IGNORE", $fileName);
        } else {
            return $fileName;
        }
    }

}
