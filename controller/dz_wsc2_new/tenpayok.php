<?php
page::$need_view = false;
//财付通
include YYUC_FRAME_PATH.'plugin/Tenpay/ResponseHandler.class.php';
include YYUC_FRAME_PATH.'plugin/Tenpay/WapNotifyResponseHandler.class.php';

//财付通
$wid = Request::get(1);
$payset = new Model('ai9me_pay_set');
$payset->find(array('token'=>$wid,'pc_enabled'=>'1','pc_type'=>'tenpay'));
if(!$payset->has_id()){
	die();
}

//$payset->key='1900000109';
/* 创建支付应答对象 */
$resHandler = new WapNotifyResponseHandler();
$resHandler->setKey($payset->key);

//判断签名
if($resHandler->isTenpaySign()) {
	//商户号
	$bargainor_id = $resHandler->getParameter("bargainor_id");
	 
	//商户订单号
	$sp_billno = $resHandler->getParameter("sp_billno");

	//财付通交易单号
	$transaction_id = $resHandler->getParameter("transaction_id");
	//金额,以分为单位
	$total_fee = $resHandler->getParameter("total_fee");

	//支付结果
	$pay_result = $resHandler->getParameter("pay_result");

	if( "0" == $pay_result  ) {
		//写入订单记录
		$ddid = explode('_', $sp_billno);
		$ddid = $ddid[1];
		$dd = new Model('z02_order');
		$dd->find($ddid);


		$prow = json_decode($dd->prolist);
		$pp = new Model('z02_sproduct');
		foreach ($prow as $p)
		{
			$pp->find($p->pid);
			$r = json_decode($pp->rowtemp);

			foreach ($r as $key=>$v)
			{
				if($v->goods_no == $p->gid)
				{
					$r[$key]->store_nums = $v->store_nums - $p->num;
					$pstore_nums   =  $pp->store_nums - $p->num;
					$psale_nums    =  $pp->sale_nums + $p->num;
				}
			}
			$rjson = json_encode($r);
			$pp->update($condition=array('id'=>$p->pid),$data=array('rowtemp'=>$rjson,'store_nums'=>$pstore_nums,'sale_nums'=>$psale_nums));
		}

		$dd->status = '1';
		$dd->save();
		$set =  new Model("z02_set");
		$set->find(array('wid'=>$wid));
		if( $set->email && $set->openemail == 1){
			sent_email($dd,$set);
		}
		if( $set->sms && $set->opensms == 1 ){
			sent_sms($dd,$set);
		}
		echo 'success';
	}
	else
	{
		echo 'fail';
	}
} else {
	//回调签名错误
	echo "fail";
}