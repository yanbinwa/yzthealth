<?php
access_control();
$unid = Session::get('unid'); //代理商id
$mid = Request::get(1);
$m = new Model('lgc_supplier_user');
$m->find(array('id'=>$mid,'belongs_id'=>$unid));
if(!$m->id){
	die();
	//http://www.jiuzhougo.cn/admin/distribution/select-249.html
}
//积分列表
//积分列表


$integralLog = new Model('integral_log');
// $integralArray = $integralLog->where(array('mid'=>$mid,'used'=>1))->order('id desc')->list_all_array();
$p = new Pagination();
$integralArray = $p->model_list($integralLog->where(array('unid'=>$m->id,'used'=>1))->order('id desc') );


$orders = new Model('orders');
$rs = $orders->where(array('unid'=>$m->id,'status'=>2))->list_all();	//累计消费积分
$sumTotal = 0;
foreach($rs as $v)
{
   $sumTotal += $v->total;
   $member = new Model('member');
   $member->find($v->mid);
   $v->hyname = $member->uname;
   $v->ture_name = $member->true_name;
}




$db = DB::get_db();
$sql = "select sum(total) as total ,sum(point) as point from integral_log where unid=$m->id and used=1";
$result = $db->query($sql);
$result = $result[0];
$sumPoint = $result['point'];		//累计消耗点数
$sumIntegral = $result['total'];	//累计赠送积分
$sumWithdrawals = $integralLog->where(array('unid'=>$m->id,'used'=>2))->sum('total');		//累计提现积分
$point = $m->point;					//现有点数