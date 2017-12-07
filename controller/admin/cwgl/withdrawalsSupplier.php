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
	$orderModel->status = 1;
	$orderModel->updated_at = time();
	$orderModel->save();
	
}elseif($type == 'refuse'){ //不通过
	fanhuanxianjin($orderModel);
	$orderModel->refuse_data = Request::get('refuse_data');
	$orderModel->status = 2;
	$orderModel->updated_at = time();
	$orderModel->save();
}

$return['success'] = true;
echo json_encode($return);exit;

//不通过提现金额返还商户
function fanhuanxianjin($orderModel){
	if ($orderModel->unid) {
		$supplier = new Model('lgc_supplier_user');
		$supplier->find(array('id'=>$orderModel->unid));
		if (!$supplier->id) return false;
		$supplier->total += $orderModel->total;
		$supplier->save();
	}
}
