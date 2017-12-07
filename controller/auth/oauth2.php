<?php
class Packet{
	private $wxapi;
    private $app_id;
    private $app_secret;
    private $app_mchid;
	private $paykey;
    function __construct($app_id,$app_secret,$app_mchid,$paykey){
    $this->app_id=$app_id;
	$this->app_secret=$app_secret;
	$this->app_mchid=$app_mchid;
	$this->paykey=$paykey;
    }
    function _route($fun,$param = ''){
		/*
		/*
   private $app_id = 'wx52935cacc9402378';
    private $app_secret = 'a33d900bfcc2b9971ab2647bb6084fdf';
    private $app_mchid = '1235609602';
	'wx52935cacc9402378','a33d900bfcc2b9971ab2647bb6084fdf','1235609602',"youshanmeidiQAZWSX112358youshanm"
*/
		
		$this->wxapi = new Wxapi($this->app_id,$this->app_secret,$this->app_mchid,$this->paykey);
		switch ($fun)
		{
			case 'userinfo':
				return $this->userinfo($param);
				break;
			case 'wxpacket':
				return $this->wxpacket($param);
				break;			
			default:
				exit("Error_fun");
		}		
    }	
    /**
     * 用户信息
     * 
     */	
	private function userinfo($param){ 
		$get = $param['param'];
		$code = $param['code'];	
		if($get=='access_token' && !empty($code)){
			$json = $this->wxapi->get_access_token($code);
			//file_put_contents('log.txt', print_R($json,true).'----');
			if(!empty($json)){
				$userinfo = $this->wxapi->get_user_info($json['access_token'],$json['openid']);
				return $userinfo;
			}
		}else{
			$this->wxapi->get_authorize_url($param['url'],'STATE');
		}
	}


    /**
     * 微信红包
     * 
     */		
	private function wxpacket($param){
		return $this->wxapi->pay($param['openid'],$param['dataarray'],$param['pem']);
	}
}


class Wxapi {
    private $app_id;
    private $app_secret;
    private $app_mchid;
	private $paykey;
    function __construct($app_id,$app_secret,$app_mchid,$paykey){
    $this->app_id=$app_id;
	$this->app_secret=$app_secret;
	$this->app_mchid=$app_mchid;
	$this->paykey=$paykey;
    }
    /**
     * 微信支付
     * 
     * @param string $openid 用户openid
     */
    public function pay($re_openid,$dataarray,$pem)
    {
		
        include_once('WxHongBaoHelper.php');
        $commonUtil = new CommonUtil();
        $wxHongBaoHelper = new WxHongBaoHelper($this->paykey,$pem['pem1'],$pem['pem2'],$pem['pem3']);
        $wxHongBaoHelper->setParameter("nonce_str", $this->great_rand());//随机字符串，丌长于 32 位
        $wxHongBaoHelper->setParameter("mch_id", $this->app_mchid);//商户号
        $wxHongBaoHelper->setParameter("wxappid", $this->app_id);
        $wxHongBaoHelper->setParameter("re_openid", $re_openid);//相对于医脉互通的openid
        $wxHongBaoHelper->setParameter("client_ip", '127.0.0.1');//调用接口的机器 Ip 地址
		foreach($dataarray as $key => $value)
		{
		   $wxHongBaoHelper->setParameter($key,$value);
		}
        $postXml = $wxHongBaoHelper->create_hongbao_xml();
		
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $responseXml = $wxHongBaoHelper->curl_post_ssl($url, $postXml);
		//file_put_contents('log.txt',print_r($responseXml,TRUE),FILE_APPEND);
		return  simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
		//file_put_contents('log.txt', print_R($responseObj,true));
		//return $responseObj->return_code;

		return;
    }
    /**
     * 获取微信授权链接
     * 
     * @param string $redirect_uri 跳转地址
     * @param mixed $state 参数
     */
    public function get_authorize_url($redirect_uri = '', $state = '')
    {
		
        $redirect_uri = urlencode($redirect_uri); 
		//snsapi_base snsapi_userinfo
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->app_id}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect"; 
		
        echo "<script language='javascript' type='text/javascript'>";  
        echo "window.location.href='$url'";  
        echo "</script>";    
    }       
    
    /**
     * 获取授权token
     * 
     * @param string $code 通过get_authorize_url获取到的code
     */
    public function get_access_token($code = '')
    {
        $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->app_id}&secret={$this->app_secret}&code={$code}&grant_type=authorization_code";
        $token_data = $this->http($token_url);
        if(!empty($token_data[0]))
        {
            return json_decode($token_data[0], TRUE);
        }
        
        return FALSE;
    }   

    /**
     * 获取授权后的微信用户信息
     * 
     * @param string $access_token
     * @param string $open_id
     * */
    public function get_user_info($access_token = '', $open_id = '')
    {
        if($access_token && $open_id)
        {
			$access_url = "https://api.weixin.qq.com/sns/auth?access_token={$access_token}&openid={$open_id}";
			$access_data = $this->http($access_url);
			$access_info = json_decode($access_data[0], TRUE);
			if($access_info['errmsg']!='ok'){
				exit('页面过期');
			}
            $info_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$open_id}&lang=zh_CN";
            $info_data = $this->http($info_url);  		
            if(!empty($info_data[0]))
            {
                return json_decode($info_data[0], TRUE);
            }
        }
        
        return FALSE;
    }   	
    /**
     * Http方法
     * 
     */ 
    public function http($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $output = curl_exec($ch);//输出内容
        curl_close($ch);
        return array($output);
    }   
   


    /**
     * 生成随机数
     * 
     */     
    public function great_rand(){
        $str = '1234567890abcdefghijklmnopqrstuvwxyz';
        for($i=0;$i<30;$i++){
            $j=rand(0,35);
            $t1 .= $str[$j];
        }
        return $t1;    
    }
}

?>