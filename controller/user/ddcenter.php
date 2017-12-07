<?php
 $wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
if (!empty($wid) && !empty($wxid)) {
    $orders = new Model('orders');
    //全部订单
    $orders_arr = $orders->where(array('wxid'=>$wxid,'status@<>'=>2))->order('id desc')->list_all_array();
    //待付款
    $Pending_payment = $orders->where(array('wxid'=>$wxid,'pay_status'=>0,'status'=>0))->order('id desc')->list_all_array();
    //待消费
    $Pending_consumption = $orders->where(array('wxid'=>$wxid,'pay_status'=>1,'status'=>0))->order('id desc')->list_all_array();
    //已完成
    $Completed = $orders->where(array('wxid'=>$wxid,'pay_status'=>1,'status'=>1))->order('id desc')->list_all_array();
}

