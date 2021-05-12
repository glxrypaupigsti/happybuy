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

class iWechat extends Controller {
    
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
    }
    
    public function index(){
        
    }
    
    public function request_base_token($Query)
    {
        $redirect_uri = $this->uri;
        
        $code = WechatSdk::getAccessCode($redirect_uri, "snsapi_base");
        if (FALSE == $code) {
            echo 'Failed to get access code';
            exit(0);
        }
        $token = WechatSdk::getAccessToken($code);
        
        if (!$token->access_token) {
            echo 'Failed to get access token';
            exit(0);
        } else {
            $this->sCookie('uopenid', $token->openid);
            $this->sCookie('uaccesstoken', $token->access_token, $token->expires);
            $ret_url = urldecode($Query->ret_url);
            error_log('redirect base back to:'.$ret_url);
            header("location:" . $ret_url);
            exit(0);
        }
    }
    
    public function request_info_token($Query)
    {
        $redirect_uri = $this->uri;

        $code = WechatSdk::getAccessCode($redirect_uri, "snsapi_userinfo");
        if (FALSE == $code) {
            echo 'Failed to get access code';
            exit(0);
        }
        $token = WechatSdk::getAccessToken($code);
        
        if (!$token->access_token) {
            echo 'Failed to get access token';
            exit(0);
        } else {
            $this->sCookie('uinfoaccesstoken', $token->access_token, $token->expires);
            $ret_url = urldecode($Query->ret_url);
            error_log('redirect to:'.$ret_url);
            header("location:" . $ret_url);
        }
    }
    
    public function send_promot()
    {
        $this->loadModel('User');
        $this->loadModel('WechatSdk');
        error_log('send promot');
        
        $users = $this->User->getAllOpenIds();
        if ($users) {
            $msg = array(
                         'first' => "Merry X'mas",
                         'name' => '平安夜全场半价优惠',
                         'expDate' => '今天下午17:00',
                         'remark' => '快来品尝CheersLife健康下午茶吧！',
                         );
            foreach ($users AS $val) {
                //if ('oalpuuArTvfjghRVOrkdCyDmtQ68' == $val['client_wechat_openid'])
                if (0)
                {
                    Messager::sendTemplateMessage('9CioV89Op8FOT95hTduoMaxf0IBhfbrcB99WH3p479k', $val['client_wechat_openid'], $msg, $this->getBaseURI() . "?/Index");
                }
                
            }
        }
    }
    
}
