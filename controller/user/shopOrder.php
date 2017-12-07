<?php
 $wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
if (!empty($wid) && !empty($wxid)) {
	$supplier = new Model('lgc_supplier_user');
	$supplier->where(array('wxid'=>$wxid))->find();
	if ($supplier->id) {
		$orders = new Model('orders');
	    //全部订单
	    $orders_arr = $orders->where(array('or'=>array('unid'=>$supplier->id,'llshopid'=>$supplier->id),'status@<>'=>2))->order('id desc')->list_all_array();
	    //待付款
	    $Pending_payment = $orders->where(array('or'=>array('unid'=>$supplier->id,'llshopid'=>$supplier->id),'pay_status'=>0,'status'=>0))->order('id desc')->list_all_array();
	    //待消费
	    $Pending_consumption = $orders->where(array('or'=>array('unid'=>$supplier->id,'llshopid'=>$supplier->id),'pay_status'=>1,'status'=>0))->order('id desc')->list_all_array();
	    //已完成
	    $Completed = $orders->where(array('or'=>array('unid'=>$supplier->id,'llshopid'=>$supplier->id),'pay_status'=>1,'status'=>1))->order('id desc')->list_all_array();
	}
    
}

