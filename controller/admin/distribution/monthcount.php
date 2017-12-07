<?php
access_control();
$unid = Session::get('unid'); //代理商id
$mc = new Model('month_count');
$db = DB::get_db();

//所有商户下上个月1号到上月最后一天23:59:59

$nowmonthday_ten = date('Y-m-10 00:00:00',time()); //这个月10号
$nowmonth_tenday = strtotime($nowmonthday_ten);

$start_ymd = date('Y-m-01 00:00:00', strtotime('-1 month')); //上个月的开始日期1号
$start_time=strtotime($start_ymd);

$thisMfirst =  strtotime(date('Y-m-01',time()));
$end_ymd = date('Y-m-d 23:59:59',strtotime('-1 day',$thisMfirst)); //上个月的最后一天
$end_time = strtotime($end_ymd);

$dlsarrs = new Model('distribution');
$id = $unid;
$dlsarr = $dlsarrs->find($id);
$dlscreat_time = strtotime($dlsarr->creat_time);

$supplier = new Model('lgc_supplier_user');
$sh_count = $supplier->where(array('belongs_id'=>$unid,'status'=>1,'is_lls'=>0,'create_time@>='=>$start_time,'create_time@<='=>$end_time))->count();
$lls_count = $supplier->where(array('belongs_id'=>$unid,'status'=>1,'is_lls'=>1,'create_time@>='=>$start_time,'create_time@<='=>$end_time))->count();

$pay_set = new Model('ai9me_pay_set');
$pay_set->find(array('id'=>1));
$money_count =(($sh_count*$pay_set->dianpu_fee*$pay_set->dp_dl)/100) + (($lls_count*$pay_set->lls_fee*$pay_set->lls_dl)/100);

$time_name = date("YmdHis",$start_time)."-".date("YmdHis",$end_time);


$mc->time_name = $time_name;
$mc->unid = $unid;
$mc->sh_num = $sh_count;
$mc->lls_num = $lls_count;
$mc->money_count = $money_count;
$mc->create_time = time();
$mc->start_time = $start_time;
$mc->end_time = $end_time;
$mc2 = new Model('month_count');
$arr = $mc2->find(array('time_name@~'=>$time_name,'unid'=>$unid));

$now_time = time();
//每月十号$end_time
if($now_time > $nowmonth_tenday){
	if($arr->id){
		tusi("上月总业绩已生成");
	}else{
		$mc->save();
	}
}


$sql = "select * from month_count where unid=".$unid." order by id desc";
$p = new Pagination();
$list = $p->sql_list($sql);
foreach ($list as $lk => $lv) {
	$list[$lk]['time_name'] = date("Y-m-d",$list[$lk]['start_time'])."至".date("Y-m-d",$list[$lk]['end_time']);
}
//print_r($list);
