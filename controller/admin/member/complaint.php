<?php
access_control();

$complaint = new Model('complaint');


$p = new Pagination();
$res = $p->model_list($complaint->where($where)->order('id desc'));

$getcount = $complaint->where(array('is_flag'=>'1'))->count();
$tcount = $complaint->where(array('is_flag'=>'0'))->count();

if('del'==Request::get(1)){
	$ids = explode(',', Request::post('id'));
	foreach ($ids as $id){
		$complaint->find(array('id'=>$id));
		$complaint->remove();		
	}
	Response::write('ok');
}


