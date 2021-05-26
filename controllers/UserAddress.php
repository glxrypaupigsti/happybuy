<?php



/**
 * 用户地址
 */
class UserAddress extends Controller {

    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
    }
    
    
    
    public function list_address($data){
    
 
        $this->loadModel('User');
        $this->loadModel('mUserAddress');
        
        $openid = $this->getOpenId();
        if(!Controller::inWechat() && !$this->debug){

            $this->show('./index/error.tpl');
            die(0);
        }
        $this->User->wechatAutoReg($openid);
        
        $uinfo = $this->User->getUserInfo($openid);
        
        $address = $this->mUserAddress->get_user_address_list($uinfo['uid']);
        
        $couponId = $data->couponId;
        $time = $data->time;
        $isbalance = $data->isbalance;
        
        $this->assign('couponId',$couponId);
        $this->assign('time',$time);
        $this->assign('isbalance',$isbalance);
        
        $this->assign('address',$address);
        $this->show('./uc/select_address.tpl');
    
    }
    
    
      
    public function edit_address($data){
    
        $this->loadModel('User');
     
        
        $openid = $this->getOpenId();
        
        if(!Controller::inWechat() && !$this->debug){

            $this->show('./index/error.tpl');
            die(0);
        }
        $this->User->wechatAutoReg($openid);
        
        $uinfo = $this->User->getUserInfo($openid);
        if($uinfo['client_phone'] != ''){
        	
            $this->assign('phone',$uinfo['client_phone']);
        	
        }
        
        $couponId = $data->couponId;
        $time = $data->time;
        $isbalance = $data->isbalance;
        
        
        
        $this->assign('couponId',$couponId);
        $this->assign('time',$time);
        $this->assign('isbalance',$isbalance);
        $this->show('./uc/fill_address.tpl');
    
    }
    
    public function ajaxEditAddress(){
       $openid = $this->getOpenId();
        $this->loadModel('User');
        $this->loadModel('mUserAddress');
        $id = $_POST['id'];
        $uinfo = $this->User->getUserInfo($openid);
        $uid = $uinfo['uid'];
        $updateArray =   array(
                    'enable' => 0
                );
        $this->mUserAddress->update_address($uinfo['uid'],$updateArray);
        $update =   array(
                    'enable' => 1
                );
       $id = $this->mUserAddress->update_address_by_id($id,$update);
       $this->echoMsg(1,'修改成功');
    }
    
 
    
    public function remove_address(){
    
      	$this->loadModel('User');

        $this->loadModel('mUserAddress');
        $id = $_POST['id'];
        $userAddress = $this->mUserAddress->get_user_address_by_id($id);
        
        $deleArray =   array(
                    'is_delete' => 1
                );
        $this->mUserAddress->update_address_by_id($id,$deleArray);
        
        if($userAddress && $userAddress['enable'] == 1){
      
            $openid = $this->getOpenId();
            $uinfo = $this->User->getUserInfo($openid);
            $address = $this->mUserAddress->get_user_address_list($uinfo['uid']);
            if($address){
                 $update =   array(
                    'enable' => 1
                );
               $id = $this->mUserAddress->update_address_by_id($address[0]['id'],$update);
            }
          
        }
        $this->echoMsg(1,'修改成功');
    }
  
    
    public function add_address(){
        
        
        //陆家嘴点沁的位置
        $det_lat = 31.24596585383;
        $det_lng = 121.51389359593;
        $maxDistance = 2;//km
        
        
        
        $openid = $this->getOpenId();

        $this->loadModel('User');
        $this->loadModel('mUserAddress');
        $user_name = $_POST['user_name'];
        $phone = $_POST['phone'];
        $city = $_POST['city'];
        $area = $_POST['area'];
        $address = $_POST['address'];
        $uinfo = $this->User->getUserInfo($openid);
        
        $array = array(
                    'uid' => $uinfo['uid'],
                    'user_name' => $user_name,
                    'province' => "",
                    'city' => $city,
                    'address' => $address,
                    'postal_code' => "",
                    'enable' => 1,
                    'area' => $area,
                    "phone"=> $phone
                );

       
        $updateArray =   array(
               
                    'enable' => 0
                  
                );

       $this->mUserAddress->update_address($uinfo['uid'],$updateArray);
            
       $id = $this->mUserAddress->add_user_address($array);
       
       echo $id;
    }
    
  public function cal_distance($lat,$lng,$e_lat,$e_lng){
 
        $c_url =  "http://api.map.baidu.com/direction/v1/routematrix?output=json&origins=$lat,$lng&destinations=$e_lat,$e_lng&ak=0NnLgeO4V61jARaU0PMOT0OB&mode=walking";
        $c_ret = Curl::get($c_url);
        $c_obj=json_decode($c_ret); 
        $c_status = $c_obj->status;
        error_log("======================".$c_ret);
        if($c_status == 0){

          $res = $c_obj->result;
          $dataArray = $res->elements;

           $objDistance = $dataArray[count($dataArray)-1]->distance->value;
           return  $objDistance/1000;

        }
   }

}
