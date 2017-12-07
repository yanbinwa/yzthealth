<?php
$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
if (!empty($wid) && !empty($wxid)) {
    $supplier = new Model('lgc_supplier_user');
    $supplier->find(array('wxid'=>$wxid,'wid'=>$wid));
    if($supplier->has_id()){
        $fans = new Model('fans');
        $fans->find(array('wid'=>$wid,'wxid'=>$wxid));
        $orders = new Model('orders');
        //已完成订单
		$orders_arr_num = $orders->where(array('unid'=>$supplier->id,'status'=>1,'pay_status'=>1,'unid'=>$supplier->id))->count();
        //已支付订单
	    $orders_array_num = $orders->where(array('unid'=>$supplier->id,'status'=>0,'pay_status'=>1,'unid'=>$supplier->id))->count();        
        //未支付订单
        $orders_pay_num = $orders->where(array('unid'=>$supplier->id,'status'=>0,'pay_status'=>0,'unid'=>$supplier->id))->count();          
    }else{
        Redirect::to('userLogin');
    }
}else{
    die('请从微信进入');
}

