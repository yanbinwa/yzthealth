<?php 
$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
if (!empty($wid) && !empty($wxid)) {
	$supplier = new Model('lgc_supplier_user');
    $supplier->find(array('wxid'=>$wxid,'wid'=>$wid));
    if($supplier->has_id()){
		$start_ymd = date('Y-m-01 00:00:00', strtotime('-1 month')); //上个月的开始日期1号
		$thisMfirst =  strtotime(date('Y-m-01',time()));
		$end_ymd = date('Y-m-d 23:59:59',strtotime('-1 day',$thisMfirst)); //上个月的最后一天
		$todaystart = date('Y-m-d 00:00:00',time());
		$todayend = date('Y-m-d 23:59:59',time());

	    $orders = new Model('orders');
	    //今日订单量
	    $today_all_nums = $orders->where(array('unid'=>$supplier->id,'created_at@>='=>$todaystart,'created_at@<='=>$todayend))->count();
	    //今日成交量
	    $today_nums = $orders->where(array('unid'=>$supplier->id,'pay_status'=>1,'status'=>1,'created_at@>='=>$todaystart,'created_at@<='=>$todayend))->count();
	    //今日营业额
	    $today_moneyarr = $orders->where(array('unid'=>$supplier->id,'pay_status'=>1,'status'=>1,'created_at@>='=>$todaystart,'created_at@<='=>$todayend))->list_all_array();
	    $today_money = 0;
	    foreach ($today_moneyarr as $key => $value) {
	    	$today_money+= $value['amount'];
	    }
	    //本月订单量
	    $month_all_nums = $orders->where(array('unid'=>$supplier->id,'created_at@>='=>$start_ymd,'created_at@<='=>$end_ymd))->count();
	    //本月成交量
	    $month_nums = $orders->where(array('unid'=>$supplier->id,'pay_status'=>1,'status'=>1,'created_at@>='=>$start_ymd,'created_at@<='=>$end_ymd))->count();
	    //本月营业额
	    $month_moneyarr = $orders->where(array('unid'=>$supplier->id,'pay_status'=>1,'status'=>1,'created_at@>='=>$start_ymd,'created_at@<='=>$end_ymd))->list_all_array();  
		$month_money = 0;
	    foreach ($month_moneyarr as $key => $value) {
	    	$month_money+= $value['amount'];
	    }	    	
    }else{
        Redirect::to('userLogin');
    }
	
}
