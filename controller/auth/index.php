<?php
Conf::$session_prefix = 'sdzzbabcd';
$optionsaaa = array(
        'component_appid' => 'wx02560241e9071ed8',
        'component_appsecret' => '0c79e1fa963cd80cc0be99b20a18faeb',
        'component_verify_ticket'=>file_get_contents('ComponentVerifyTicket.json')  //component_verify_ticket由公众平台每隔10分钟，持续推送给第三方平台方（在创建公众号第三方平台审核通过后，才会开始推送）。
);

include_once 'auth.class.php'; 
$weObj = new Auth($optionsaaa);
$auth_code = $_GET['auth_code'];
if(empty($auth_code)){
	//此外示例代授权发起
	$code = $weObj -> get_auth_code();	
	$url = $weObj -> getRedirect("http://www.weixinguanjia.cn/auth/index.html", $code);//此外的url为授权成功后的回调地址，修改成你自己的实际地址
	echo "<script language='javascript'>location.href='".$url."';</script>";exit;
}else{
	//此外示例代授权回调后获取公众号信息
	$wechats_info=$weObj->get_authorization_info($auth_code);//获取授权方信息
	$appid=$wechats_info['authorizer_appid'];
	$authorizer_access_token=$wechats_info['authorizer_access_token'];
    $authorizer_refresh_token=$wechats_info['authorizer_refresh_token'];

	//获取详细信息
	$gzhinfo=$weObj->get_authorizer_info($appid);
    $pubs= new Model("pubs");
	$pubs->where(array('uid'=>Session::get('mainuid')))->find();
	
	$datas['sqappid']=$appid;
	$datas['cusid']=$appid;
	$datas['authorizer_access_token']=$authorizer_access_token;
	$datas['authorizer_refresh_token']=$authorizer_refresh_token;
	$datas['isbind']=1;
	$datas['isval']=1;
	$datas['authcode']=$auth_code;
	$datas['nexttime']=date('Y-m-d H:i:s',time()+7100);
	$datas['nickname']=$gzhinfo['authorizer_info']['nick_name'];
	$datas['head_img']=$gzhinfo['authorizer_info']['head_img'];
	$datas['service_type_info']=$gzhinfo['authorizer_info']['service_type_info']['id']; //授权方公众号类型，0代表订阅号，1代表由历史老帐号升级后的订阅号，2代表服务号
	$datas['verify_type_info']=$gzhinfo['authorizer_info']['verify_type_info']['id'];//授权方认证类型，-1代表未认证，0代表微信认证，1代表新浪微博认证，2代表腾讯微博认证，3代表已资质认证通过但还未通过名称认证，4代表已资质认证通过、还未通过名称认证，但通过了新浪微博认证，5代表已资质认证通过、还未通过名称认证，但通过了腾讯微博认证
	$datas['uuid']=$gzhinfo['authorizer_info']['user_name'];
	$datas['alias']=$gzhinfo['authorizer_info']['alias'];//微信号
	$datas['qrcode_url']=$gzhinfo['authorizer_info']['qrcode_url'];//微信二维码
	
	if($pubs->has_id())
	{
	   $pubs->update(array('uid'=>Session::get('mainuid')),$datas);
	}
	//绑定送点券
	$user = new Model('user');
	$user->find(Session::get('mainuid'));
	if($user->is_bind < 1 && $user->isdirect == 1 && strtotime($user->rtime) > 1445961600){
		$user->is_bind = 1;
		$user->djq = $user->djq+300;
		$user->save();
	}
	
	echo "<script language='javascript'>alert('授权成功');location.href='http://".$_SERVER['HTTP_HOST']."/admin/ind.html';</script>";exit;
}

?>