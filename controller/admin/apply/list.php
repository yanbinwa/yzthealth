<?php
access_control();
$title = Request::post('title');
$shenhe = Request::post('shenhe');
if($title){
	$where = array('telephone@~'=>$title,'status'=>$shenhe);
}

$apply = new Model('apply_cooperation');
if('del'==Request::get(1)){
	$id = Request::get(2);
	$apply->find($id);
	$apply->delete();
	
}
$p = new Pagination();
$r = $p->model_list($apply->where($where)->order('id desc'));





