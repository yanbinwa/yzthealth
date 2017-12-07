<?php
access_control();
$unid = Session::get('unid'); //代理商id
$mc = new Model('month_count');
$m = new Model('distribution');
$db = DB::get_db();

//所有商户下上个月1号到上月最后一天23:59:59

$nowmonthday_ten = date('Y-m-02 00:00:00',time()); //这个月10号
$nowmonth_tenday = strtotime($nowmonthday_ten);

$start_ymd = date('Y-m-01 00:00:00', strtotime('-1 month')); //上个月的开始日期1号
$start_time=strtotime($start_ymd);
$thisMfirst =  strtotime(date('Y-m-01',time()));
$end_ymd = date('Y-m-d 23:59:59',strtotime('-1 day',$thisMfirst)); //上个月的最后一天
$end_time = strtotime($end_ymd);

// echo $nowmonthday_ten."<br />";
// echo $start_ymd."<br />";
// echo $end_ymd."<br />";
// exit;
//上月最后一天$end_time

$where = " 1 ";
$find = new Model('month_count');
if($find->try_post()){
	$status = Request::post('status');
	$dlsname = Request::post('dlsname');
	if($status == 1){
		$where = " deal_status=1 ";
	}elseif($status == 2){
		$where = " deal_status=0 ";
	}
	if($dlsname){
		$dls_sql = "SELECT id FROM distribution WHERE name='".$dlsname."'";
		$arr_dls = $db->query($dls_sql);
		$where = " unid='".$arr_dls[0]["id"]."'";
	}
	if($status!=0 && $dlsname){
		if($status == 1){
			$where = " deal_status=1 AND unid='".$arr_dls[0]["id"]."'";
		}elseif($status == 2){
			$where = " deal_status=0 AND unid='".$arr_dls[0]["id"]."'";
		}
	}
}




$sql = "select * from month_count where $where order by id desc";
$p = new Pagination();
$list = $p->sql_list($sql);
foreach ($list as $lk => $lv) {
	$sql_dis = "SELECT id,name,status FROM distribution WHERE id=".$list[$lk]['unid'];
	$disr = $db->query($sql_dis);
	$list[$lk]['dis_name'] = $disr[0]["name"];
	$list[$lk]['dis_status'] = $disr[0]["status"];
	if($list[$lk]['dis_status'] == 1){
		$list[$lk]['dis_status'] = "账号正常";
	}elseif($list[$lk]['dis_status'] == 2){
		$list[$lk]['dis_status'] = "封号";
	}
	$list[$lk]['create_time'] = date("Y-m-d H:i:s",$list[$lk]['create_time']);
	if($list[$lk]['update_time']){
		$list[$lk]['update_time'] = date("Y-m-d H:i:s",$list[$lk]['update_time']);
	}else{
		$list[$lk]['update_time'] = "待处理";
	}
	if($list[$lk]['deal_status'] == 1){
		$list[$lk]['deal_status'] = "已处理";
	}else{
		$list[$lk]['deal_status'] = "待处理";
	}
	$list[$lk]['time_name'] = date("Y-m-d",$list[$lk]['start_time'])."至".date("Y-m-d",$list[$lk]['end_time']);
}


//代理商业绩汇总多选处理
if('dlsmonthcount'==Request::post('types')){

	$db = DB::get_db();
	$ids = Request::post('ids');
	$deal_type = Request::post('deal_type');
	if($ids){
		foreach ($ids as $id){
			$times = time();
			$sql_update = "UPDATE month_count SET deal_status=$deal_type,update_time=$times WHERE id=".$id;
			$rs = $db->query($sql_update);
		}
		$return["success"] = true;
		echo json_encode($return);exit;
	}else{
		$return["success"] = false;
		echo json_encode($return);exit;
	}
}

