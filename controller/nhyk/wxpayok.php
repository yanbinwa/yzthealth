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

$config = array(
		'appId' => $payset->appId, // 公众号身份标识
		'appSecret' => $payset->appSecret, // 权限获取所需密钥 Key
		'paySignKey' => $payset->paySignKey, // 加密密钥 Key，也即appKey
		'partnerId' =>  $payset->partner, // 财付通商户身份标识
		'partnerKey' => $payset->key, // 财付通商户权限密钥 Key
		'notifyUrl' => "http://www.weixinguanjia.cn/nhyk/wxpayok-".$wid."-".$sid.".html", // 微信支付完成服务器通知页面地址
		'successUrl' => 'http://www.weixinguanjia.cn/nhyk/my.html' // 微信支付完成跳转到的页面，也可以在传递之前自定义
);
file_put_contents('/mnt/wp1.log', var_export($_GET,true));
file_put_contents('/mnt/wp2.log', var_export($_POST,true));

$osign = $_GET['sign'];
unset($_GET['sign']);
$nst_arr = array();
foreach ($_GET as $k=>$v){
	$nst_arr[] = $k.'='.$v;
	//log::warn($k.':'.$v);
}

//log::warn('微信支付');

$string1 = implode('&', $nst_arr);
$string1 .= '&key='.$payset->key;
$sign = strtoupper(md5($string1));
if($osign==$sign){

	//log::warn('微信支付验证通过');
	$ddid = explode('_', $_GET['out_trade_no']);

	//foreach ($ddid  as $k=>$v){
	// log::warn($k.':'.$v);
	// }

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


	echo 'success';
}else{
	die('滚犊子');
}


function updateqsta($id,$sta)
{
	$m = new Model('micro_membercard_yhq_set_list');
	return $m->update($condition=array('id'=>$id),$data=array('status'=>$sta));
}