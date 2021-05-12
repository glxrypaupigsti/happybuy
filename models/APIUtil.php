<?php

include_once 'Curl.php';
include_once 'DigCrypt.php';

/**
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <liao@qiezilife.com>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.icheerslife.com
 */
class APIUtil extends Model {

    /**
     * 说明： 生成app_id
     * 算法: 前缀(qz)+3位随机数字+13位随机字符串
     * 输出参数： String
     */
    public function gen_app_id(){
        $prefix = "qz";
        $random_num_str = $this->create_random_number_str();
        $random_suffix_str = $this->create_random_str(13);
        return $prefix.$random_num_str.$random_suffix_str;
    }

    /**
     * 说明： 生成app_secret
     * 算法：app_id + 6为随机盐(需要存储),通过MD5加密，再通过shal加密md5字符串得到sha1串，随机取其中任意一个，拼凑成40位长度的字符串
     * 输出参数： String
     */
    public function gen_app_secret($app_id,$salt){
        $code_len = 40;
        $str = $app_id+'_'+$salt;
        $md5 = md5($str);
        $sha1 = sha1($md5);
        $code = '';
        $str_len = strlen($sha1);
        for($i=0;$i<$code_len;$i++){
            $start = rand(0,$str_len);
            $code = $code.substr($sha1,$start,1);
        }
        return $code;
    }



    /**
     * 说明： 生成merchant_no
     * 算法： 随机的10位数字串
     * 输出参数： String
     */
    public function gen_merchant_no(){
        return $this->create_random_number_str(10);
    }

    /**
     * 说明： 生成merchant_account
     * 算法： $merchant_no与@连接再接$merchant_no
     * 输出参数： String
     */
    public function gen_merchant_account($merchant_no){
        return $merchant_no.'@'.$merchant_no;
    }

    /**
     * 说明： 生成merchant_password
     * 算法： 随机的6位字符串
     * 输出参数： String
     */
    public function gen_merchant_password(){
        return $this->create_random_str(6);
    }

    /**
     * 说明： 生成access_token
     * 算法：app_secret + app_secret + 随机盐 +当前时间戳->Md5->des加密
     * 输出参数： String
     */
    public function gen_access_token($app_id,$app_secret,$salt,$refresh=false){
        $time = time();
        $source_str = $app_id .'_'. $app_secret .'_' . $salt .'_' . $time;
        //生成refresh_token
        if($refresh){
            $source_str = '_refresh';
        }
        $md5 = md5($source_str);
        $access_token = $this->des_encrypt($md5);
        $access_token = str_replace("=","-",$access_token);
        $access_token = str_replace("/","-",$access_token);
        return $access_token;
    }

    /**
     * 说明： 生成签名
     * 将参数按照key的首字母进行升序排列组合成一个字符串,然后通过sha1加密算法得到签名
     * $special_keys 需要特殊处理的key集合，格式为array('key1','key2');
     * $special_keys_values 需要特殊处理的key集合的值，格式为array('key1'=>val1,'key2'=>val2);,必须与$special_keys中的key一一对应
     *
     * 输出参数： String
     */
    public function gen_signature($post_params,$params_key_arr,$special_keys=array(),$special_keys_values=array()){
        foreach($post_params as $key => $val){
            if(in_array($key,$params_key_arr)){
                $key_arr[] = $key;
            }
        }
        $signature = '';
        if(count($key_arr) > 0){
            sort($key_arr);
            foreach($key_arr as $key2 => $val2){
                if($special_keys and in_array($val2,$special_keys)){
                    $data[$val2] = $special_keys_values[$val2];
                }else{
                    $data[$val2] = $post_params[$val2];
                }
            }

            $source_str_arr = '';
            foreach($data as $dk => $dv){
                $source_str_arr[] = $dk.'='.$dv;
            }
            $source_str = implode('&',$source_str_arr);
            error_log('source_str====>'.$source_str) ;
            $signature = sha1($source_str);
        }
        return $signature;
    }



    /**
     * 说明： 随机串算法
     * 输出参数： String
     */
    public function create_random_str($code_len=12){

        $BYTE_LEN = 512 ;

        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        if (function_exists('openssl_random_pseudo_bytes')) {
            $random_string = openssl_random_pseudo_bytes($BYTE_LEN);
        }else{
            $random_string = substr(str_shuffle(str_repeat($pool, 5)), 0, $BYTE_LEN);
        }

        $code = $this->md5_sha1_encrypt_str($random_string,$code_len);
        return $code;
    }

    /**
     * 说明： 获取指定长度的随机数字
     * 输出参数： String
     */
    public function create_random_number_str($len=3){
        $num_str = '';
        for($i=0;$i<$len;$i++){
            $random_num =rand(0,9);
            $num_str = $num_str . $random_num;
        }
        return $num_str;
    }


    /**
     * 说明： 通过md5和sha1加密然后截取指定长度的字符串
     * 输出参数： String
     */
    public function md5_sha1_encrypt_str($str,$code_len){
        $md5 = md5($str);
        $sha1 = sha1($md5);
        $start = mt_rand(0, 25);
        $code = substr($sha1, $start, $code_len);
        return $code;
    }


    /**
     * 说明： DES加密
     * 输出参数： String
     */
    function des_encrypt($encrypt,$key=""){
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB),MCRYPT_RAND);
        $passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt, MCRYPT_MODE_ECB, $iv);
        $encode = base64_encode($passcrypt);
        return $encode;
    }

    /**
     * 说明： DES解密
     * 输出参数： String
     */
    function des_decrypt($decrypt,$key="") {
        $decoded = base64_decode ( $decrypt );
        $iv = mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ),MCRYPT_RAND);
        $decrypted = mcrypt_decrypt ( MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_ECB, $iv );
        return $decrypted;
    }


}
