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
class Product extends Model {

    /**
     * 图片上传标记
     * @var type 
     */
    private $imageUploadMark = 'product_hpic2__';
    // upyun对象
    private $upyun = false;
    private $catImg = false;

    /**
     * @param type $dataS
     * @return boolean
     */
    public function modifyProduct($dataS) {

        $ret = false;

        $id = intval($dataS['product_id']);
        if ($id == 0) {
            // 新建商品
            $id = 'NULL';
        }

        // 检查商品首图是否需要上传，并且加入上传列表
        foreach ($dataS['product_infos'] as &$d) {
            if ($d['name'] == 'catimg' && $d['value'] != '') {
                if (preg_match("/$this->imageUploadMark/is", $d['value'])) {
                    $dataS['product_images'][-1] = $d['value'];
                    $d['value'] = str_replace('product_hpic2__', '', $d['value']);
                }
            }
        }

        $product_cat = false;
        $product_desc = '';

        if ($id == 'NULL') {
            // 新建商品
            $field = array();
            $values = array();
            foreach ($dataS['product_infos'] as &$d) {
                if ($d['name'] == 'product_cat') {
                    $product_cat = $d['value'];
                }
                if ($d['name'] == 'product_desc') {
                    $product_desc = $d['value'];
                }
                if ($d['value'] != '') {
                    $field[] = "`$d[name]`";
                    $values[] = "'$d[value]'";
                }
            }
            $SQL = sprintf("INSERT INTO `products_info` (%s) VALUES (%s);", implode(',', $field), implode(',', $values));
            // 插入商品数据
            $id = $this->Db->query($SQL);
            $ret = $id > 0;
        } else {
            $this->catImg = $this->Dao->select('catimg')->from(TABLE_PRODUCTS)->where("product_id = $id")->getOne();
            // 修改商品信息
            if ($id > 0) {
                $set = array();
                foreach ($dataS['product_infos'] as &$d) {
                    if ($d['name'] == 'product_cat') {
                        $product_cat = $d['value'];
                    }
                    if ($d['name'] == 'product_desc') {
                        $product_desc = $d['value'];
                    }
                    $set[] = sprintf("`$d[name]` = '%s'", addslashes($d['value']));
                }
                $set = implode(',', $set);
                $SQL = "UPDATE `products_info` SET $set WHERE `product_id` = '$id';";
            }
            $ret = $this->Db->query($SQL);
        }

        // 静态化商品描述html
        $stati_dir = dirname(__FILE__) . '/../html/products/';

        if (!is_dir($stati_dir)) {
            mkdir($stati_dir, 0755);
        }

        file_put_contents($stati_dir . $id . '.html', $product_desc);

        // 处理集团折扣
        if (isset($dataS['entDiscount'])) {
            foreach ($dataS['entDiscount'] as $entDiscount) {
                $this->Db->query("REPLACE INTO `product_enterprise_discount` (`entId`,`productId`,`discount`) VALUES ($entDiscount[ent],$id,$entDiscount[discount])");
            }
        }

        $SQL1 = sprintf("REPLACE INTO `product_onsale` (`product_id`,`discount`,`sale_prices`) VALUES (%s,%s,%s)"
                , $id
                , $dataS['product_discount']
                , $dataS['product_prices']);

        $this->Db->query($SQL1);

        // 处理商品价格表
        $this->handleProductSpec($id, $dataS['spec']);

        // 处理产品图片
        $this->handleProductImages($id, $dataS['product_images']);

        // 暂时禁用此功能
        #$this->buildCatSearch($id, $product_cat);

        return $id;
    }

    /**
     * 处理商品价格表
     * @param int $id 商品id
     * @param array $specs 价格表列表
     */
    public function handleProductSpec($id, $specs) {
        if (isset($specs) && is_array($specs)) {
            foreach ($specs AS $index => $spec) {
                $detids = explode('-', $spec['sid']);
                if ($detids[0] == 0) {
                    // 不允许空规格
                    continue;
                }
                if (empty($spec['market_price'])) {
                    $spec['market_price'] = 0;
                }
                if (empty($spec['instock'])) {
                    $spec['instock'] = 0;
                }
                if (empty($spec['price'])) {
                    $spec['price'] = 0;
                }
                if ($spec['id'] == 0) {
                    // 新增
                    $this->Dao->insert(TABLE_PRODUCT_SPEC, '`product_id`,`spec_det_id1`,`spec_det_id2`,`sale_price`,`market_price`,`instock`')
                            ->values(array($id, $detids[0], $detids[1], $spec['price'], $spec['market_price'], $spec['instock']))->exec();
                } else if ($spec['id'] > 0) {
                    // 大于0 修改
                    $this->Dao->update(TABLE_PRODUCT_SPEC)->set(array(
                        'product_id' => $id,
                        'spec_det_id1' => $detids[0],
                        'spec_det_id2' => $detids[1],
                        'sale_price' => $spec['price'],
                        'market_price' => $spec['market_price'],
                        'instock' => $spec['instock']
                    ))->where("id=" . $spec['id'])->exec();
                } else {
                    // 小于0 删除
                    $this->Dao->delete()->from(TABLE_PRODUCT_SPEC)->where("id=" . abs($spec['id']))->exec();
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 处理商品图片数据
     * @global type $config
     * @param type $id
     * @param type $images
     */
    public function handleProductImages($id, $images) {

        global $config;

        // 目录检查
        if (!is_dir($config->productPicRoot)) {
            mkdir($config->productPicRoot, 644);
        }

        if (!is_dir($config->productPicRootTmp)) {
            mkdir($config->productPicRootTmp, 644);
        }

        // 旧数据
        $oldImage = $this->getProductImages($id);
        $oldImages = array();

        // 对比旧数据
        foreach ($oldImage as $od) {
            $oldImages[intval($od['image_sort'])] = $od['image_path'];
        }
        $oldImages[-1] = $this->catImg;

        // 差异数据
        $diff = array_diff($images, $oldImages);

        foreach ($diff as $sort => $image) {
            // 如果是新上传的数据
            if (preg_match("/$this->imageUploadMark/is", $image)) {
                $image = str_replace($this->imageUploadMark, '', $image);
                $_path = $config->productPicRoot . $image;
                $_tmpath = $config->productPicRootTmp . $image;
                // 移动图片文件
                if (is_file($_tmpath)) {
                    rename($_tmpath, $_path);
                    if ($config->usecdn) {
                        $this->cdnImageUpload($_path, $image);
                    }
                }
                if ($sort != -1) {
                    $this->Dao->insert('product_images', '`product_id`,`image_path`,`image_type`,`image_sort`')->values(array($id, $image, 0, $sort))->exec();
                } else {
                    // 如果是商品首图，而且重新上传了图片，则删除旧图片
                    is_file($config->productPicRoot . $this->catImg) && @unlink($config->productPicRoot . $this->catImg);
                }
            } else {
                // 如果不是新上传的，那就是删除图片
                if ($image != $oldImages[$sort]) {
                    $this->Dao->delete()->from('product_images')->where("product_id = $id")->aw("`image_sort` = $sort")->aw("image_path = '$oldImages[$sort]'")->exec();
                    is_file($config->productPicRoot . $oldImages[$sort]) && @unlink($config->productPicRoot . $oldImages[$sort]);
                }
            }
        }
    }

    /**
     * buildCatSearch
     * @param type $pid
     * @param type $catId
     * @return type
     */
    public function buildCatSearch($pid, $catId) {
        $cats = array($catId);
        while ($catId > 0) {
            $parent = $this->Dao->select('cat_parent')->from(TABLE_PRODUCT_CATEGORY)->where('cat_id = ' . $catId)->getOne();
            if ($parent !== false && $parent > 0) {
                $cats[] = $parent;
                $catId = $parent;
            } else {
                break;
            }
        }
        $this->Dao->update(TABLE_PRODUCTS)->set(array('product_catsearch' => implode(',', $cats)))->where('product_id = ' . $pid)->exec();
        return implode(',', $cats) . '<br />';
    }

    /**
     * upyun图片上传
     * @global type $config
     * @param type $source
     * @param type $target
     */
    public function cdnImageUpload($source, $target) {

        global $config;

        require_once(dirname(__FILE__) . '/../lib/upyun/upyun.class.php');

        if (!$this->upyun) {
            $this->upyun = new UpYun($config->upyunBucket, $config->upyunOperator, $config->upyunPassword);
        }

        if (is_file($source)) {
            // 上传
            $fh = fopen($source, 'rb');
            $this->upyun->writeFile($config->cdnProductPicRoot . $target, $fh, true);
            fclose($fh);
        }
    }

    /**
     * 获取商品信息
     * @param type $productId
     */
    public function getById($productId) {
        return $this->getProductInfo($productId);
    }

    /**
     * 獲取已刪除商品
     * @param type $limit
     * @param boolean $cache
     */
    public function getDeletedProducts($limit = 100, $cache = true) {
        $SQL = "SELECT po.*,ps.sale_prices,psl.serial_name FROM `products_info` po "
                . "LEFT JOIN `product_onsale` ps ON po.product_id = ps.product_id "
                . "LEFT JOIN `product_serials` psl ON psl.id = po.product_serial WHERE `delete` = 1 LIMIT $limit;";
        return $this->Db->query($SQL, $cache);
    }

    /**
     * 废弃
     * @param <string> $limitStr
     * @return <array> list
     */
    public function getProductList($orderby = false, $limitStr = false, $cat = 0) {
        !$orderby && $orderby = '`product_id` DESC';
        !$limitStr && $limitStr = '25';
        if ($cat != 0) {
            $SQL = "SELECT `cat_id` FROM `product_category` WHERE `cat_parent` = $cat ORDER BY cat_order ASC;";
            $Lst = $this->Db->query($SQL, false);
            $subcat = array($cat);
            foreach ($Lst as $l) {
                $subcat[] = $l['cat_id'];
            }
            $whereCat = "`product_cat` IN (" . implode(',', $subcat) . ") AND `delete` <> 1";
        } else {
            $whereCat = "`delete` <> 1";
        }
        $SQL = sprintf("SELECT po.*,ps.sale_prices,psl.serial_name,(SELECT SUM(product_count) "
                . "FROM `orders_detail` "
                . "WHERE `orders_detail`.product_id = `po`.product_id) AS sale_count,psc.cat_name "
                . "FROM `products_info` po "
                . "LEFT JOIN `product_onsale` ps ON po.product_id = ps.product_id "
                . "LEFT JOIN `product_serials` psl ON psl.id = po.product_serial "
                . "LEFT JOIN `product_category` psc ON psc.cat_id = po.product_cat "
                . "WHERE $whereCat ORDER BY %s LIMIT %s;", $orderby, $limitStr);
        return $this->Db->query($SQL, false);
    }

    /**
     * 获取商品列表
     * @param type $cat
     * @param type $page
     * @param type $orderby
     * @param type $searchKey
     * @return boolean
     */
    public function getProductSlist($cat = false, $page = 0, $limit = 10, $orderby = 'po.`product_id` DESC', $home = false, $searchKey = '', $brand = 0) {
        if ($cat) {
            $_SQL_limit = $page * $limit . "," . $limit;
            $orderby = trim(urldecode($orderby));
            if (isset($searchKey) && $searchKey != '') {
                $searchKey = urldecode($searchKey);
                $_SQL = sprintf("SELECT * FROM `products_info` po LEFT JOIN `product_onsale` ps ON po.product_id = ps.product_id WHERE po.`product_name` LIKE '%%$searchKey%%' AND po.`delete` = 0 ORDER BY %s LIMIT %s;", $orderby, $_SQL_limit);
            } else if ($home) {
                $_SQL = sprintf("SELECT * FROM `products_info` po LEFT JOIN `product_onsale` ps ON po.product_id = ps.product_id WHERE po.`product_cat` = $cat AND `product_brand` = $brand AND po.`delete` = 0 AND po.`show_inhome` = 1 ORDER BY %s LIMIT %s;", $orderby, $_SQL_limit);
            } else {
                $_SQL = sprintf("SELECT * FROM `products_info` po LEFT JOIN `product_onsale` ps ON po.product_id = ps.product_id WHERE po.`product_cat` = $cat AND `product_brand` = $brand AND po.`delete` = 0 ORDER BY %s LIMIT %s;", $orderby, $_SQL_limit);
            }
            $productList = $this->Db->query($_SQL);
            if (count($productList) == 0) {
                return false;
            } else {
                return $this->Db->query($_SQL);
            }
        } else {
            return false;
        }
    }

    /**
     * 
     * @param type $page
     * @param type $limit
     * @param type $orderby
     * @param type $brand
     * @return boolean
     */
    public function getPdlistByBrand($page = 0, $limit = 10, $orderby = 'po.`product_id` DESC', $brand = 0) {
        $_SQL_limit = $page * $limit . "," . $limit;
        $orderby = trim(urldecode($orderby));
        $_SQL = sprintf("SELECT * FROM `products_info` po LEFT JOIN `product_onsale` ps ON po.product_id = ps.product_id WHERE `product_brand` = $brand AND po.`delete` = 0 ORDER BY %s LIMIT %s;", $orderby, $_SQL_limit);
        $productList = $this->Db->query($_SQL);
        if (count($productList) == 0) {
            return false;
        } else {
            return $this->Db->query($_SQL);
        }
    }

    /**
     * 获取商品分类信息
     * @param type $catid
     * @return <string>
     */
    public function getCatInfo($catid, $cache = true) {
        $catid = addslashes(intval($catid));
        $SQL = "SELECT * FROM `product_category` WHERE `cat_id` = $catid LIMIT 1;";
        $res = $this->Db->query($SQL, $cache);
        return $res ? $res[0] : null;
    }

    /**
     * 获取商品分类列表
     * @param type $catParent
     * @param type $limit
     * @return <array>
     */
    public function getCatList($catParent = 0, $limit = 20) {
        $SQL = "SELECT * FROM `product_category` WHERE `cat_parent` = $catParent  ORDER BY cat_order DESC LIMIT $limit;";
        return $this->Db->query($SQL);
    }

    /**
     * 获取所有产品分类列表，递归
     * @param type $catParent
     * @return type
     */
    public function getAllCats($catParent = 0) {
        $this->Load->model('SqlCached');
        $key = 'product-getAllCats' . $catParent;
        $ret = $this->SqlCached->get($key);
        if ($ret !== -1) {
            return $ret;
        } else {
            $SQL = "SELECT `cat_name` AS `name`,`cat_id` FROM `product_category` WHERE `cat_parent` = $catParent ORDER BY cat_order DESC;";
            $Lst = $this->Db->query($SQL, false);
            $this->Db->getOne();
            foreach ($Lst as &$l) {
            	//该类别对应的子类的数量的查询语句
            	$subCatCountSql = "select count(1) from product_category where cat_parent = $l[cat_id] and cat_id <> 100 and cat_id <> 101";
            	$subCatCount = $this->Db->getOne($subCatCountSql);
            	
            	$countSql = "select count(1) from products_info a where a.product_cat = $l[cat_id] and `delete` = 0";
            	$catProductCount = $this->Db->getOne($countSql);
            	
            	if($subCatCount > 0 ){ //如果该分类中有子类，则查询所有子类的商品数字
            		$subCatsProductCountSql = "select count(1) from products_info a where a.delete = 0 and a.product_cat in(".
            				"select cat_id from product_category where cat_parent = $l[cat_id] and cat_id <> 100 and cat_id <> 101".
            				")";
            		$subCatsProductCount = $this->Db->getOne($subCatsProductCountSql);
            		$catProductCount = $catProductCount + $subCatsProductCount;
            	}
            	
                $l['dataId'] = intval($l['cat_id']);
                $l['children'] = $this->getAllCats(intval($l['cat_id']));
                // 商品数量
                $l['pdcount'] = $catProductCount;
//                 $l['pdcount'] = $this->Dao->select('')->count('*')->from(TABLE_PRODUCTS)->where("product_cat = $l[cat_id]")->aw('`delete` <> 1')->getOne();
                $l['open'] = 'true';
                $l['hasChildren'] = count($l['children']) > 0;
                $l['name'] .= ' (' . $l['pdcount'] . ')';
                
                unset($l['cat_id']);
            }
            $this->SqlCached->set($key, $Lst);
            return $Lst;
        }
    }
    
    /**
     *  直接获取所有的分类 
     */
    public function  getAllCategories() {
    	$this->Load->model('SqlCached');
    	$key = 'product-all-category';
    	$ret = $this->SqlCached->get($key);
    	if ($ret !== -1) {
    		return $ret;
    	} else {
    		$SQL = "SELECT `cat_name` AS `name`,`cat_parent`,`cat_id` FROM `product_category` ORDER BY cat_order DESC";
    		$Lst = $this->Db->query($SQL, false);
    		$this->SqlCached->set($key, $Lst);
    		return $Lst;
    	}
    	
    }

    /**
     * 获取所有品牌列表
     * @return type
     */
    public function getAllBrands($del = 0) {
        $SQL = "SELECT * FROM `product_brand` WHERE `deleted` = $del ORDER BY `sort` ASC;";
        $Lst = $this->Db->query($SQL, false);
        foreach ($Lst as &$l) {
            $l['dataId'] = intval($l['id']);
            $l['open'] = 'true';
            $l['name'] = $l['brand_name'];
            $l['hasChildren'] = false;
        }
        return $Lst;
    }

    /**
     * 删除商品
     * @param type $id
     * @return type
     */
    public function deleteProduct($id) {
        return $this->Db->query("UPDATE `products_info` SET `delete` = 1 WHERE `product_id` = $id;");
    }

    /**
     * 删除商品 包括图片
     * @global type $config
     * @param type $id
     * @return type
     */
    public function removeProduct($id) {

        global $config;

        $info = $this->getProductInfo($id);

        @unlink($config->productPicRoot . $info['catimg']);

        foreach ($info['images'] as $img) {
            @unlink($config->productPicRoot . $img['image_path']);
        }

        unset($info);

        $this->Db->query("DELETE FROM `product_images` WHERE `product_id` = $id;");

        return $this->Db->query("DELETE FROM `products_info` WHERE `product_id` = $id;");
    }

    /**
     * 获取商品信息
     * @param type $productId
     * @return null
     */
    public function getProductInfo($productId, $cache = true) {
        if (is_numeric($productId)) {
            $productId = intval($productId);
            $productInfo = $this->Db->getOneRow(sprintf("SELECT po.*,ps.sale_prices,ps.discount FROM `products_info` po LEFT JOIN `product_onsale` ps ON po.product_id = ps.product_id WHERE po.product_id = '%s';", $productId), $cache);
            // 图片列表
            $productInfo['images'] = $this->Db->query("SELECT * FROM `product_images` WHERE `product_id` = $productId ORDER BY image_sort ASC;", $cache);
            // 价格表
            $productInfo['specs'] = $this->Db->query("SELECT sp.id,sp.instock,sp.market_price,sp.spec_det_id1 AS id1, wd.det_name AS name1, sp.spec_det_id2 AS id2, wd1.det_name AS name2, sp.sale_price FROM `product_spec` sp LEFT JOIN `wshop_spec_det` wd ON wd.id = sp.spec_det_id1 LEFT JOIN `wshop_spec_det` wd1 ON wd1.id = sp.spec_det_id2 WHERE `product_id` = $productId", $cache);
            #var_dump($productInfo['specs']);
            return $productInfo;
        } else {
            return false;
        }
    }

    /**
     * 获取产品系列列表
     */
    public function getSerials($start = false) {
        if ($start) {
            $where = " WHERE sort >= $start AND `deleted` = 0";
        } else {
            $where = " WHERE `deleted` = 0";
        }
        return $this->Db->query("SELECT * FROM `product_serials`$where ORDER BY sort ASC;");
    }

    /**
     * 获取系列信息
     * @param type $id
     * @return boolean
     */
    public function getSerialInfo($id) {
        if (!is_numeric($id)) {
            return false;
        }
        return $this->Db->getOneRow("SELECT * FROM `product_serials` WHERE id = $id;");
    }

    /**
     * 随机获取商品列表
     * @param type $limit
     * @return type
     */
    public function randomGetProducts($cat, $notId, $limit = 10) {
        $catParent = $this->Dao->select('cat_parent')->from(TABLE_PRODUCT_CATEGORY)->where("cat_id=$cat")->getOne();
        $slev = $this->Dao->select('cat_id')->from(TABLE_PRODUCT_CATEGORY)->where("cat_parent=$catParent")->aw("cat_id <> $cat")->exec();
        $sIn = array();
        foreach ($slev as $s) {
            $sIn[] = $s['cat_id'];
        }
        $sIn = implode(',', $sIn);
        $plist = $this->Db->query("SELECT po.*,ps.sale_prices,ps.discount "
                . "FROM `products_info` po "
                . "LEFT JOIN `product_onsale` ps ON po.product_id = ps.product_id "
                . "WHERE po.delete <> 1 AND po.product_online <> 0 AND po.product_id <> $notId AND po.product_cat IN ($sIn) ORDER BY RAND() LIMIT $limit;");
        foreach ($plist as &$p) {
            $p['images'] = $this->getProductImages($p['product_id']);
        }
        return $plist;
    }

    /**
     * 获取商品图片列表
     * @param type $productId
     * @param type $limit
     */
    public function getProductImages($productId, $limit = null) {
        if ($limit) {
            $limit = "LIMIT $limit;";
        }
        return $this->Db->query("SELECT * FROM `product_images` WHERE `product_id` = $productId ORDER BY `image_sort` ASC $limit");
    }

    /**
     * 获取所有规格列表
     * @return type
     */
    public function getSpecs() {
        $specs = $this->Db->query("SELECT * FROM `wshop_spec`;");
        foreach ($specs as &$sc) {
            $sc['dets'] = $this->Db->query("SELECT * FROM `wshop_spec_det` WHERE `spec_id` = " . $sc['id'] . " ORDER BY det_sort ASC;");
        }
        return $specs;
    }

    /**
     * getCategoryByLevel
     * @param type $levelId
     * @return type
     */
    public function getCategoryByLevel($levelId, $parentId = false) {
        $w = '';
        if ($parentId) {
            $w = "AND `cat_parent` = $parentId";
        }
        $SQL = "SELECT * FROM `product_category` WHERE `cat_level` = $levelId $w ORDER BY cat_order DESC;";
        # echo $SQL;
        return $this->Db->query($SQL);
    }

    /**
     * 
     * @param type $catId
     * @param type $level
     * @return type
     */
    public function getCatIdUtilLevel($catId, $level) {
        $info = $this->getCatInfo($catId);
        if ($info['cat_level'] == $level) {
            return $catId;
        } else {
            return $this->getCatIdUtilLevel($info['cat_parent'], $level);
        }
    }

    public function getProductSalePrices($id) {
        
    }

    /**
     * getProductSpecs
     * @param type $id
     * @return boolean
     */
    public function getProductSpecs($id) {
        if (is_numeric($id)) {
            $id = intval($id);
            $SQL = "SELECT
                    `sp`.id,
                    spd1.id AS spd1id,
                    spd1.spec_name AS spd1name,
                    sp.spec_det_id1,
                    wsd1.det_name AS det_name1,
                    spd2.id AS spd2id,
                    spd2.spec_name AS spd2name,
                    sp.spec_det_id2,
                    wsd2.det_name AS det_name2,
                    sp.sale_price,
                    sp.market_price,sp.instock
            FROM
                    product_spec `sp`
            LEFT JOIN wshop_spec_det wsd1 ON wsd1.id = sp.spec_det_id1
            LEFT JOIN wshop_spec_det wsd2 ON wsd2.id = sp.spec_det_id2
            LEFT JOIN wshop_spec spd1 ON spd1.id = wsd1.spec_id
            LEFT JOIN wshop_spec spd2 ON spd2.id = wsd2.spec_id
            WHERE
                    product_id = $id ORDER BY sale_price ASC;";
            $ret = $this->Db->query($SQL, false);
            
            if ($ret) {
                // HACK:check current weekday to find discount should be applied
                if (isset($_COOKIE['deliver_date'])) {
                    $weekday = date('N', strtotime($_COOKIE['deliver_date']));
                } else {
                    $weekday = 1;
                }
                /*
                if (4 == $weekday) {
                    // Thursday is "HALF-day" sale
                    $discount = 1;
                } else {
                    $discount = 1;
                }
                 */
                $discount = 0.9;
                // adjust sale price
                foreach ($ret AS $key => $val) {
                    $ret[$key]['sale_price'] = $ret[$key]['sale_price']*$discount;
                }
            }
            return $ret;
        } else {
            return false;
        }
    }

    /**
     * getProductSpecs
     * @param type $id
     * @return boolean
     */
    public function getProductSpecsDistinct($id) {
        if (is_numeric($id)) {
            $id = intval($id);
            $SQL = "SELECT
                    spd1.id AS spd1id,
                    spd1.spec_name AS spd1name,
                    sp.spec_det_id1,
                    wsd1.det_name AS det_name1,
                    spd2.id AS spd2id,
                    spd2.spec_name AS spd2name,
                    sp.spec_det_id2,
                    wsd2.det_name AS det_name2,
                    sp.sale_price,sp.instock
            FROM
                    product_spec `sp`
            LEFT JOIN wshop_spec_det wsd1 ON wsd1.id = sp.spec_det_id1
            LEFT JOIN wshop_spec_det wsd2 ON wsd2.id = sp.spec_det_id2
            LEFT JOIN wshop_spec spd1 ON spd1.id = wsd1.spec_id
            LEFT JOIN wshop_spec spd2 ON spd2.id = wsd2.spec_id
            WHERE
                    product_id = $id ORDER BY sale_price ASC;";
            $ret = $this->Db->query($SQL);
            $ret1 = array('a', 'b');
            foreach ($ret as $r) {
                $ret1['a']['spd1name'] = $r['spd1name'];
                $ret1['a']['sps'][$r['spec_det_id1']] = array('det_name1' => $r['det_name1'], 'spec_det_id1' => $r['spec_det_id1']);
                $ret1['b']['spd2name'] = $r['spd2name'];
                $ret1['b']['sps'][$r['spec_det_id2']] = array('det_name2' => $r['det_name2'], 'spec_det_id2' => $r['spec_det_id2']);
            }
            return $ret1;
        } else {
            return false;
        }
    }

    /**
     * 检查商品存在
     * @param type $productId
     * @return type
     */
    public function checkExt($productId) {
        return $this->Dao->select()->from(TABLE_PRODUCTS)->where("product_id = $productId")->exec();
    }

    /**
     * 获取商品信息
     * @param type $productId
     * @param type $spid
     * @return boolean
     */
    public function getProductInfoWithSpec($productId, $spid) {
        if (is_numeric($productId)) {
            $productId = intval($productId);
            if (is_numeric($spid) && $spid > 0) {
                $SQL = "SELECT
                        po.*, sp.sale_price AS sale_prices, wsd1.det_name AS det_name1, wsd2.det_name AS det_name2
                FROM
                        `products_info` po
                LEFT JOIN `product_spec` sp ON po.product_id = sp.product_id AND sp.id = $spid
                LEFT JOIN wshop_spec_det wsd1 ON wsd1.id = sp.spec_det_id1
                LEFT JOIN wshop_spec_det wsd2 ON wsd2.id = sp.spec_det_id2
                WHERE
                        po.product_id = $productId;";
                $productInfo = $this->Db->getOneRow($SQL);
            } else {
                $productInfo = $this->Db->getOneRow(sprintf("SELECT po.*,ps.sale_prices,ps.discount FROM `products_info` po LEFT JOIN `product_onsale` ps ON po.product_id = ps.product_id WHERE po.product_id = '%s';", $productId));
            }
            if ($productInfo) {
                // HACK:check current weekday to find discount should be applied
                if (isset($_COOKIE['deliver_date'])) {
                    $weekday = date('N', strtotime($_COOKIE['deliver_date']));
                } else {
                    $weekday = 1;
                }
                /*
                if (4 == $weekday) {
                    // Thursday is "HALF-day" sale
                    $discount = 1;
                } else {
                    $discount = 1;
                }
                 */
                $discount = 0.9;
                // adjust sale price
                $productInfo['sale_prices'] = $productInfo['sale_prices']*$discount;
            }
            return $productInfo;
        } else {
            return false;
        }
    }

    /**
     * 获取商品简名 by 商品条码编号
     * @param type $code
     * @return type
     */
    public function getProductSubnameByCode($code) {
        return $this->Db->getOne("SELECT `product_subname` FROM `products_info` WHERE `product_code` = '$code';");
    }

    /**
     * 生成商品二维码
     * @global type $config
     * @param type $id
     * @param type $comid
     * @return string
     */
    public function getQrcode($id, $comid = false) {
        if (is_numeric($id)) {
            global $config;
            $ecc = 'H'; // L-smallest, M, Q, H-best  
            $size = 15; // 1-50  
            if (is_numeric($comid)) {
                $productURI = 'http://' . $config->domain . "?/vProduct/view/id=$id&com=$comid&showwxpaytitle=1";
            } else {
                $productURI = 'http://' . $config->domain . "?/vProduct/view/id=$id&showwxpaytitle=1";
            }
            $PNG_TEMP_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'product_qrcode' . DIRECTORY_SEPARATOR;
            $hashname = hash('md4', $productURI . $size . $ecc) . '.jpg';
            $filename = $PNG_TEMP_DIR . $hashname;
            $QrcodeURI = $config->shoproot . 'uploads/product_qrcode/' . $hashname;
            if (is_file($filename)) {
                return $QrcodeURI;
            } else {
                include_once dirname(__FILE__) . '/../lib/phpqrcode/qrlib.php';
                QRcode::png($productURI, $filename, $ecc, $size, 2);
                return $QrcodeURI;
            }
        }
    }

    /**
     * 获取最新商品列表
     * @param type $cat
     * @param type $limit
     * @return type
     */
    public function getNewEst($cat = false, $limit = 10) {
        if ($cat > 0) {
            $pds = $this->Dao->select("po.*,ps.sale_prices")
                            ->from(TABLE_PRODUCTS)->alias('po')
                            ->leftJoin(TABLE_PRODUCT_ONSALE)->alias('ps')->on('ps.product_id=po.product_id')
                            ->where('`delete` <> 1')
                            ->aw('`product_online` = 1')
                            ->aw('`product_cat` = ' . $cat)
                            ->orderby('product_id')
                            ->desc()->limit($limit)->exec();
        } else {
            $pds = $this->Dao->select("po.*,ps.sale_prices")
                            ->from(TABLE_PRODUCTS)->alias('po')
                            ->leftJoin(TABLE_PRODUCT_ONSALE)->alias('ps')->on('ps.product_id=po.product_id')
                            ->where('`delete` <> 1')
                            ->aw('`product_online` = 1')
                            ->orderby('product_id')
                            ->desc()->limit($limit)->exec();
        }
        return $pds;
    }

    /**
     * 获取最热商品列表
     * @param type $cat
     * @param type $limit
     * @return type
     */
    public function getHotEst($cat = 0, $limit = 10) {
        $pds = $this->Dao->select("po.*,ps.sale_prices")
                        ->from(TABLE_PRODUCTS)->alias('po')
                        ->leftJoin(TABLE_PRODUCT_ONSALE)->alias('ps')->on('ps.product_id=po.product_id')
                        ->where('`delete` <> 1')
                        ->orderby('product_readi')
                        ->desc()->limit($limit)->exec();
        return $pds;
    }

    /**
     * 从In字符串中获取商品列表
     * @param type $in
     * @return type
     */
    public function getIn($in) {
        $pds = $this->Dao->select("po.*,ps.sale_prices")
                ->from(TABLE_PRODUCTS)->alias('po')
                ->leftJoin(TABLE_PRODUCT_ONSALE)->alias('ps')->on('ps.product_id=po.product_id')
                ->where('`delete` <> 1')
                ->aw("po.product_id IN ($in)")
                ->orderBy('product_id')->desc()
                ->exec();
        return $pds;
    }

    /**
     * 获取商品列表
     * @param type $cat
     * @param type $page
     * @param type $limit
     * @param type $orderby
     * @return type
     */
    public function getList($cat = false, $page = 0, $limit = 10, $orderby = 'pds.`product_id` DESC', $searchKey = false) {
        if ($searchKey) {
            $searchKey = urldecode($searchKey);
            $pds = $this->Dao->select()->from(TABLE_PRODUCTS)->alias('pds')
                            ->leftJoin(TABLE_PRODUCT_ONSALE)->alias('pdos')->on('pds.product_id = pdos.product_id')
                            ->where("pds.product_name LIKE '%$searchKey%'")
                            ->aw('pds.delete = 0')
                            ->orderBy($orderby)
                            ->limit($page, $limit)->exec();
            return $pds;
        }

        if ($cat === false) {
            $pds = $this->Dao->select()->from(TABLE_PRODUCTS)->alias('pds')
                            ->leftJoin(TABLE_PRODUCT_ONSALE)->alias('pdos')->on('pds.product_id = pdos.product_id')
                            ->where('pds.delete = 0')
                            ->orderBy($orderby)
                            ->limit($page, $limit)->exec();
        } else if (is_array($cat)) {
            $pds = $this->Dao->select()->from(TABLE_PRODUCTS)->alias('pds')
                            ->leftJoin(TABLE_PRODUCT_ONSALE)->alias('pdos')->on('pds.product_id = pdos.product_id')
                            ->where('pds.product_cat IN (' . implode(',', $cat) . ')')
                            ->aw('pds.delete = 0')
                            ->orderBy($orderby)
                            ->limit($page, $limit)->exec();
        } else {
            $pds = $this->Dao->select()->from(TABLE_PRODUCTS)->alias('pds')
                            ->leftJoin(TABLE_PRODUCT_ONSALE)->alias('pdos')->on('pds.product_id = pdos.product_id')
                            ->where("pds.product_cat = $cat")
                            ->aw('pds.delete = 0')
                            ->orderBy($orderby)
                            ->limit($page, $limit)->exec();
        }

        return $pds;
    }

    /**
     * getListByIds
     * @param type $ids
     * @return type
     */
    public function getListByIds($ids) {
        $pds = $this->Dao->select()->from(TABLE_PRODUCTS)->alias('pds')
                ->leftJoin(TABLE_PRODUCT_ONSALE)->alias('pdos')->on('pds.product_id = pdos.product_id')
                ->where("pds.product_id IN ($ids)")
                ->exec();
        return $pds;
    }

    /**
     * 增加商品阅读数
     * @param type $pid
     * @return boolean
     */
    public function upReadi($pid) {
        if (!is_numeric($pid)) {
            return false;
        }
        // readi
        return $this->Dao->update(TABLE_PRODUCTS)->set(array('product_readi' => Dao::VALUE_PLUS))->where("`product_id` = $pid")->exec();
    }
    
    /**
     * 增加商品阅读数
     * @param type $pid
     * @return boolean
     */
    public function upReadNum($num) {
    	if (!is_numeric($num)) {
    		return false;
    	}
    	// readi
    	return $this->Dao->update(TABLE_PRODUCTS)->set(array('product_readi' => $num))->where("`product_id` = $pid")->exec();
    }
    
    /**
     * 获取单个商品信息，不连表
     * @param type $pid
     * @return boolean
     */
    public function get_simple_product_info($product_id){
    	return $this->Dao->select()->from(TABLE_PRODUCTS)->where('product_id='.$product_id)->getOneRow();
    }

}
