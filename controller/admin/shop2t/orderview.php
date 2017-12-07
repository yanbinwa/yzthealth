<?php
access_control();
$wid = Session::get('wid');
$unid = Session::get('unid');
$orid = Request::get(1);
$pay_type = array('0'=>'货到付款','1'=>'支付宝','2'=>'财付通','3'=>'微支付','8'=>'银联支付');
$pay_status = array('0'=>'未支付','1'=>'已支付','2'=>'已完成','3'=>'退款');
$orderModel = new Model('orders');

$orderModel->find(array('id'=>$orid));
$sproduct = new Model('z02_sproduct');
$sproduct->find(array('id'=>$orderModel->proid));

$yhq_list = new Model('youhuiquan_list');
$yhq_list->find(array('id'=>$orderModel->couponid));
if ($yhq_list->id) {
	$youhuiquan = new Model('youhuiquan');
	$youhuiquan->find(array('id'=>$yhq_list->yid));
}
$supplier = new Model('lgc_supplier_user');
if ($orderModel->llshopid) {
	$supplier->find(array('id'=>$orderModel->llshopid));
}else{
	$supplier->find(array('id'=>$orderModel->unid));
}

if (Request::post()&& Request::get(1)=='ti') {
	$id    = intval($_POST['id']);
	$bz    = trim($_POST['bz']);
	if(!empty($id)){
		$orderModel->find($id);
		if ($orderModel->id) {
			$orderModel->bz = $bz;
			$orderModel->save();
			echo 'ok';
			exit;		
		}
	}
}

