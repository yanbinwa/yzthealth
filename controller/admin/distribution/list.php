<?php
access_control();
$title = Request::post('title');
if($title){
	$where['or'] = array('name@~'=>$title,'contact@~'=>$title);
}

$m = new Model('distribution');
if('del'==Request::get(1)){
	$id = Request::get(2);
	$m->find($id);
	$m->delete();
}
$linkurl = Conf::$http_path.'dls/login.html';
$sex = array('1'=>'男','2'=>'女');
$p = new Pagination();
$r = $p->model_list($m->where($where)->order('id desc'));

// //所有商户下上个月1号到上个月的最后一天

// $start_ymd = date('Y-m-01 00:00:00', strtotime('-1 month')); //上个月的开始日期1号
// $start_time=strtotime($start_ymd);
// $end_ymd = date('Y-m-t 23:59:59', strtotime('-1 month')); //上个月的最后一天
// $end_time = strtotime($end_ymd);
// 

$db = DB::get_db();
foreach ($r as $key => $value) {
	//代理商下的商户
	$sql_shid = "SELECT id FROM lgc_supplier_user WHERE belongs_id=".$value->id;
	$sharr = $db->query($sql_shid);
	$value->shnum = count($sharr);
	$dlscreat_time = strtotime($value->creat_time);
	$value->total = 0;
	foreach ($sharr as $sk => $sv) {
		//商户下的所有订单
		$sql_order = "SELECT id,total FROM orders ";
		$sql_order .= "WHERE status=2 AND unid=".$sharr[$sk]["id"]." AND updated_at>$dlscreat_time";
		$ordersArr = $db->query($sql_order);
		foreach ($ordersArr as $ok => $ov) {
			$value->total = $value->total+$ordersArr[$ok]["total"];
		}
	}

}

foreach ($r as $key => $value) {
	//代理商的所有订单
	$dlscreat_time = strtotime($value->creat_time);
	$sql_order2 = "SELECT id,total FROM orders ";
	$sql_order2 .= "WHERE status=2 AND belong_dlsid=".$value->id." AND updated_at>$dlscreat_time";
	$ordersArr2 = $db->query($sql_order2);

	foreach ($ordersArr2 as $ok => $ov) {
		$value->total = $value->total+$ordersArr2[$ok]["total"];
	}
}

