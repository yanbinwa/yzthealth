<?php
access_control();
$mid = Request::get(1);
$m = new Model('member');
$m->find(array('id'=>$mid));
//积分列表
$integralLog = new Model('lg_integral_log'); 
// $integralArray = $integralLog->where(array('mid'=>$mid,'used'=>1))->order('id desc')->list_all_array();
$p = new Pagination();
$integralArray = $p->model_list($integralLog->where(array('uid'=>$mid))->order('id desc'));
$integralLog2 = new Model('lg_integral_log'); 
$rs = $integralLog2->where(array('uid'=>$mid))->list_all();
$c = 0;
foreach ($rs as $k => $v) {
	$c = $c+$v->lg_integral_num;
}
