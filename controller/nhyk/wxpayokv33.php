<?php

$wid = Request::get(1);
$sid = Request::get(2);
$pt = 0;
$pt = Request::get(3);
unset($_GET[0]);
unset($_GET[1]);
unset($_GET[2]);

$payset = new Model('ai9me_pay_set');
$payset->find($sid);

include_once("/mnt/sdzzb/wxpay/WxPayHelperv33.php");

define(APPID, $payset->appId);  //appid
define(MCHID, $payset->partner); //受理商ID，身份标识
define(KEY, $payset->key);      //商户支付密钥Key。审核通过后，在微信发送的邮件中查看
define(APPSECRET, $payset->appSecret);
include_once("/mnt/sdzzb/wxpay/WxPayHelperv33.php");



//使用通用通知接口
$notify = new Notify();

//存储微信的回调
$xml = $GLOBALS['HTTP_RAW_POST_DATA'];


$notify->saveData($xml);
$savedata = $notify->getData();
log::warn('xml：'.$xml);
//验证签名，并回应微信。
//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
//尽可能提高通知的成功率，但微信不保证通知最终能成功。
if($notify->checkSign() == FALSE){
	//log::warn('验证失败');
	$notify->setReturnParameter("return_code","FAIL");//返回状态码
	$notify->setReturnParameter("return_msg","签名失败");//返回信息
}
else
{
	//log::warn('验证成功');
	$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
	$ddid = explode('_', $savedata['out_trade_no']);

	$ddid = $ddid[1];
	if($pt==1)
	{
		$dd = new Model('micro_membercard_pay_log');

	}
	else
	{
		$dd = new Model('micro_membercard_recharge_log');
	}

	$dd->find($ddid);
	if($pt==1)
	$dd->bty = '5';


	$dd->t_no = $savedata['transaction_id'];
	$dd->status = '0';


	$dd->save();

	//修改余额
	$db  = DB::get_db();
	$price = $dd->price;
	if($pt!=1)
	{
		$sql = "update micro_membercard_member_list set balance = balance + '".$price."' where wid = $dd->wid and wxid= '".$dd->wxid."' ";
		$modebalance = $db->execute($sql);
		rechargeGive($dd->wid,$dd->wxid,2,0,$dd->price);
		changeGrade($dd->wid,$dd->wxid);
	}
	else
	{
		if($dd->ttid!=0)
		{

			$m = new Model('micro_membercard_yhq_set_list');
			$m->update($condition=array('id'=>$dd->ttid),$data=array('status'=>1));

		}
		cusgive($dd->wid,$dd->wxid,$price,$dd->spoutlet_id,$dd->ttid);
	}

}
$returnXml = $notify->returnXml();
echo $returnXml;

