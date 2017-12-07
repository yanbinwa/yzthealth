<?php
access_control();
$uid = Session::get('uid');

if(Request::post('id')){
	$id = Request::post('id');
	$upmc = new Model('month_count');
	$upmc->find(array('id'=>$id));
	$upmc->deal_status = 1;
	$upmc->update_time = time();
	$upmc->save();
	$return["success"] = true;
	echo json_encode($return);exit;
}

$mc = new Model("month_count");

if(Request::get(1)){
	$id = Request::get(1);
	$where = " id=".Request::get(1);
}
$db = DB::get_db();
$sql = "select * from month_count where $where";
$list = $db->query($sql);

$sql_dis = "SELECT id,name,status FROM distribution WHERE id=".$list[0]['unid'];
$disr = $db->query($sql_dis);
$list[0]['dis_name'] = $disr[0]["name"];
$list[0]['dis_status'] = $disr[0]["status"];

$list[0]['create_time'] = date("Y-m-d H:i:s",$list[0]['create_time']);
if($list[0]['update_time']){
	$list[0]['update_time'] = date("Y-m-d H:i:s",$list[0]['update_time']);
}else{
	$list[0]['update_time'] = "待处理";
}


$rs = $list[0];








