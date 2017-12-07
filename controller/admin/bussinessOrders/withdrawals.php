<?php

$uid = Session::get('uid');
$unid = Session::get('unid');

$supplier = new Model('lgc_supplier_user');
$supplier->find($unid);

$orderModel = new Model('withdrawals_order');
$bank = new Model('z02_zmd_tx_bank');
$bank->find(array('unid'=>$unid));
if (!$bank->id) {
	redirect::to('setbank');
}

$rebate = new Model('rebate');
$rebate->find(1);

if ($orderModel->try_post()) {
	$date = date('Y-m-d');
	$w = date('w');
	$err = '';

	if ($w != $rebate->weeks) {
		$err = '仅周一可以提现';
		goto error;
	}
	if ($orderModel->total % $rebate->min_withdrawals) {
		$err = "提现金额必须是".$rebate->min_withdrawals."的倍数";
		goto error;
	}

	if ($orderModel->total > $supplier->total) {
		$err = "提现金额大于现有积分";
		goto error;
	}

	$timeBegin = strtotime("$date -".($w ? $w - 1 : 6).' days');
	$timeEnd   = $timeBegin + 24*3600*7;
	$ordersModel = new Model('withdrawals_order');
	$orderCount = $ordersModel->where(array('unid'=>$unid,'created_at@>'=>$timeBegin,'created_at@<'=>$timeEnd))->count(1);
	if ($orderCount) {
		$err = '每周仅可提现一次';
		goto error;
	}

	goto success;

	success:
	$db = DB::get_db();
	$bankArr_sql = "select * from z02_zmd_tx_bank where unid=$unid and id=".$bank->id;
	$bankArr = $db->query($bankArr_sql);

	$orderModel->bank_id = $bankArr[0]['type'];
	$orderModel->card_num = $bankArr[0]['card_no'];
	$orderModel->rename = $bankArr[0]['uname'];

	$orderModel->created_at = time();
	$orderModel->unid = $unid;
	$orderModel->bank_card_id = $bank->id;
	$orderModel->status = 0;

	//print_r($orderModel);exit;
	//TODO 验证手机验证码
	
	$orderModel->save();
	//TODO 冻结积分
	freezingIntegral($orderModel);
	redirect::to('mytxlist');

	error:
	//TODO	
	//print_r($orderModel);exit;
}