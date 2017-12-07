<?php 
Conf::$session_prefix = 'sdzzbabcd';
$unid = Session::get('unid');

$orderModel = new Model('orders');
$integralLog = new Model('integral_log');
$member = new Model('member');
$supplier = new Model('lgc_supplier_user');

//昨日统计
$timeEnd = strtotime(date('Y-m-d'));
$timeEnd = $timeEnd+10;
$timeBegin = $timeEnd - 24*3600-10;
$db = DB::get_db();

$yesterdayCount = $orderModel->where(array('pay_status@>='=>1,'unid'=>$unid,'created_at@>'=>$timeBegin,'created_at@<'=>$timeEnd))->count();
$yesterdaytotal = $orderModel->where(array('status'=>1,'unid'=>$unid,'created_at@>'=>$timeBegin,'created_at@<'=>$timeEnd))->sum('total');

//昨日新增商户
$yesterdaysupplierCount = $supplier->where(array('status@>='=>1,'unid'=>$unid,'created_at@>'=>$timeBegin,'created_at@<'=>$timeEnd))->count();
//昨日新增会员
$yesterdaymemberCount = $member->where(array('status@>='=>2,'unid'=>$unid,'created_at@>'=>date('Y-m-d',time()-(3600*24)),'created_at@<'=>date('Y-m-d',time())))->count();

//7天数据统计
$timeEnd = $timeEnd + 24*3600;
$integralTotal = 0;
$pointTotal = 0;
$turnover = 0;
$turnoverArray = array();

for ($i=7; $i >0 ; $i--) { 
	$tBegin = $timeEnd - 24*3600*$i;
	$tEnd   = $timeEnd - 24*3600*($i-1);
	$tBegin = $tBegin +10;
	$tEnd = $tEnd -10;

	$total = $orderModel->where(array('status'=>1,'unid'=>$unid,'created_at@>'=>$tBegin,'created_at@<'=>$tEnd))->sum('total');

	$sql = "select ifnull(sum(total),0) as integral,ifnull(sum(point),0) as point from integral_log ";
	$sql.= "where (used=1 or used =3) and created_at between $tBegin and $tEnd  ";
	
	
	$result1 = $db->query($sql); 
	$result1 = $result1[0];

	$turnoverArray[] = $total;
	$turnover += $total;
	
	$datesArray[] = date('Y-m-d',$tBegin);
}

$turnover1_arr = $orderModel->where(array('status'=>1,'unid'=>$unid))->list_all_array();
$turnover1 = 0;
foreach ($turnover1_arr as $key => $value) {
	$turnover1+= $value['total'];
}
