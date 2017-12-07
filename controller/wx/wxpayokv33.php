<?php

$wid = Request::get(1);
$hdurl =  'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$jqwzqd = substr($hdurl,46);
$did_str = substr($jqwzqd, 0, -5);


$payset = new Model('ai9me_pay_set');
$payset->find(array('id'=>1));

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
	/*$savedata返回值 {"appid":"wx0a2d8bb53d937973","bank_type":"CFT","cash_fee":"1","fee_type":"CNY","is_subscribe":"Y","mch_id":"1482835312","nonce_str":"iqouij6ig3a34yiivaac4nbxex1amr3c","openid":"ohkY51rDC1OqT6rS6cYpz15rjGa8","out_trade_no":"WXLL_1500960995","result_code":"SUCCESS","return_code":"SUCCESS","sign":"618B9CD0065E34FE5AF963416E0CE1D8","time_end":"20170725133642","total_fee":"1","trade_type":"JSAPI","transaction_id":"4000882001201707252573111956"}*/
	/*log::warn('xml：'.$xml);*/
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
	    $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
		/*$ddid = explode('_', $savedata['out_trade_no']);*/

		$orders = new Model('orders');
		$orders->find(array('trade_no@~'=>$savedata['out_trade_no']));
		/*log::warn('out_trade_no++++++++++++++++++'.$savedata['out_trade_no'].'+++++++++++$orders->id++++++++'.$orders->id);*/
		if ($orders->id) {
			$did_arr = explode('-', $did_str);
			foreach ($did_arr as $key => $v) {
				$ordersc = new Model('orders');
				$ordersc->find(array('id'=>$v));
				$proid = $ordersc->proid;
				$pronum = $ordersc->num;
				if($ordersc->id &&$ordersc->pay_status==0)
				{
					$ordersc->pay_status = 1;
					$ordersc->pay_time = date('Y-m-d H:i:s',time());
					$ordersc->save();
					
					$sproduct = new Model('z02_sproduct');
					$sproduct->find(array('id'=>$proid));
					$r = json_decode($sproduct->rowtemp);
					foreach ($r as $key=>$v)
					{
						$r[$key]->store_nums = $v->store_nums - $pronum;
						$pstore_nums   =  $sproduct->store_nums - $pronum;
						$psale_nums    =  $sproduct->sale_nums + $pronum;
					}
					$rjson = json_encode($r);
					$sproduct->update($condition=array('id'=>$proid),$data=array('rowtemp'=>$rjson,'store_nums'=>$pstore_nums,'sale_nums'=>$psale_nums));

					//如果有优惠卷id
					if($ordersc->couponid){
						$coupon = new Model('youhuiquan_list');
						$coupon->find(array('id'=>$ordersc->couponid,'state'=>0));
						if($coupon->id){
							$coupon->state = 1;
							$coupon->usetime = date('Y-m-d H:i:s',time());
							$coupon->save();
						}
						$youhuiquanset = new Model('youhuiquan');
						$youhuiquanset->find(array('id'=>$coupon->yid,'state'=>0));
						if($youhuiquanset->id){
							$youhuiquanset->is_use += 1;
							$youhuiquanset->save();
						}
					}
						$pubs = new Model('pubs');
						$pubs -> find($wid);
						
						$supplier = new Model('lgc_supplier_user');	
						$supplier->find(array('id'=>$ordersc->unid));
						if ($ordersc->llshopid) {
							$lldsupplier = new Model('lgc_supplier_user');	
							$lldsupplier->find(array('id'=>$ordersc->llshopid));
							if ($lldsupplier->lxrtel) {
								$sms_send_params = array(
									'mobile'=>trim($lldsupplier->lxrtel),
									'contents'=>'您有新理疗到店订单，请及时处理。',
									'uid'=>$pubs->uid,
									'wid'=>$wid,
									'sendfrom'=>'orders',
									'additional'=>''
								);
								send_sms($sms_send_params);
							}					
						}
						$user = new Model('user');
						$user->find(array('id'=>58484));	
						//订单短信通知
						/*log::warn('$orders->telephone'.$orders->telephone);*/
						if ($ordersc->telephone) {
								$sms_send_params = array(
								'mobile'=>trim($ordersc->telephone),
								'contents'=>'您已下单成功，祝您身体健康。',
								'uid'=>$pubs->uid,
								'wid'=>$wid,
								'sendfrom'=>'orders',
								'additional'=>''
							);
							send_sms($sms_send_params);
						}
						if ($supplier->lxrtel) {
							$sms_send_params = array(
								'mobile'=>trim($supplier->lxrtel),
								'contents'=>'您有新订单，请及时处理。',
								'uid'=>$pubs->uid,
								'wid'=>$wid,
								'sendfrom'=>'orders',
								'additional'=>''
							);
							send_sms($sms_send_params);
						}
										
						if ($user->telephone) {
							$sms_send_params = array(
								'mobile'=>trim($user->telephone),
								'contents'=>'平台有新订单，请登录平台查看。',
								'uid'=>$pubs->uid,
								'wid'=>$wid,
								'sendfrom'=>'orders',
								'additional'=>''
							);
							send_sms($sms_send_params);
						}			

				}
			}
			
		}

	}
	$returnXml = $notify->returnXml();
	echo $returnXml;
	
	