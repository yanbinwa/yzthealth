<?php
access_control();
$type = request::get('type');
if (!$type) {
	die();
}

$return['success'] = false;
$oid = (int)request::get('oid');

$orderModel = new Model('withdrawals_order');
$orderModel->find(array('id'=>$oid,'status'=>0));
if (!$oid || !$orderModel->id) {
	$return['msg'] = '订单信息不存在';
	echo json_encode($return);exit;
}

if ($type == 'review') {  //通过
	deductionIntegral($orderModel);
	$orderModel->status = 1;
	$orderModel->updated_at = time();
	$orderModel->save();
	
}elseif($type == 'refuse'){ //不通过

	$orderModel->refuse_data = Request::get('refuse_data');
	$orderModel->status = 2;
	$orderModel->updated_at = time();
	$orderModel->save();
}

$return['success'] = true;
echo json_encode($return);exit;

//扣除提现金额
function deductionIntegral($orderModel){
	if ($orderModel->mid) {
		$member = new Model('member');
		$member->find($orderModel->mid);
		if (!$member->id) return false;
		$member->total -= $orderModel->total;
		$member->save();
	}
}
