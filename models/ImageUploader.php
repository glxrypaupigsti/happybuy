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
class ImageUploader extends Model {

    public $dir = '';

    /**
     * 文件上传处理
     * @todo 文件上传安全过滤
     * @return string|boolean
     */
    public function upload() {
        if (!empty($_FILES)) {
            $tempFile = $_FILES['jUploaderFile']['tmp_name'];
            $namex = explode(".", $_FILES['jUploaderFile']['name']);
            $targetFileName = uniqid(time()) . '.' . $namex[1];
            error_log('dir===>'.$this->dir);
            if(!is_dir($this->dir)){
            	error_log('dir not exist  begin to create');
            	$this->mkdirs($this->dir);
            }
            
            $targetFile = str_replace('//', '/', $this->dir) . $targetFileName;
            if (move_uploaded_file($tempFile, $targetFile)) {
                chmod($targetFile, 0755);
                return $targetFileName;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    
    /**
     *	递归创建目录
     **/
	function mkdirs($dir, $mode = 0777) 
	{ 
	    $dirArray = explode("/",$dir); 
	    $dirArray = array_filter($dirArray); 
	    
	    $created = ""; 
	    foreach($dirArray as $key => $value){ 
	        if(!empty($created)){ 
	            $created .= "/".$value; 
	            if(!is_dir($created)){ 
	                mkdir($created,$mode); 
	            } 
	        }else{ 
	            if(!is_dir($value)){ 
	                mkdir($value,$mode); 
	            } 
	            $created .= $value; 
	        } 
	    } 
	}

}
