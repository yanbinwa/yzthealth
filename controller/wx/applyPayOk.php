<?php

$vid = Request::get(1);
$wxid = Request::get(2);

$payset = new Model('ai9me_pay_set');
$payset->find(array('id'=>'1'));

include_once("/home/wwwroot/wxpay/WxPayHelperv33.php");

define(APPID, $payset->appId);  //appid
define(MCHID, $payset->partner); //受理商ID，身份标识
define(KEY, $payset->key);      //商户支付密钥Key。审核通过后，在微信发送的邮件中查看
define(APPSECRET, $payset->appSecret);



  //使用通用通知接口
	$notify = new Notify();

	//存储微信的回调
	$xml = $GLOBALS['HTTP_RAW_POST_DATA'];	
	

	$notify->saveData($xml);
	$savedata = $notify->getData();
	//log::warn('xml：'.$xml);
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
		//log::warn('验证陈功');
		    $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
			//$ddid = explode('_', $savedata['out_trade_no']);
			//log::warn(json_encode($savedata));
			$apply = new Model('apply_cooperation');
			$apply->where(array('trade_no@~'=>$savedata['out_trade_no'],'id'=>$vid))->find();
			if ($apply->id && $apply->pay_status==0) {
				if ($apply->apply_type==1) {
					$apply->payment_amount = $payset->dianpu_fee;
				}else{
					$apply->payment_amount = $payset->lls_fee;
				}
				$apply->pay_status = 1;
				$apply->pay_time = date('Y-m-d H:i:s',time());
				$apply->save();
			}

			
	}
	$returnXml = $notify->returnXml();
	echo $returnXml;
	
	