<?php
access_control();
$unid = Session::get('unid');
if (!$unid) {
	Redirect::to('/dls/login');
}
$m = new Model('distribution');
$m->find(array('id'=>$unid));

$dengji = dengji();

$status = array('1'=>'审核通过','2'=>'禁用');
if($m->try_post()){
	$m->sex = Request::post('sex');
	if(!$m->creat_time){
		$m->creat_time = date('Y-m-d H:i:s',time());
	}
	$m->save();
	Redirect::to('bupdate');
}
