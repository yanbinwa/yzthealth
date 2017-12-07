<?php
access_control();

$apply = new Model('apply_cooperation');
if(Request::get(1)){
	$apply->find(Request::get(1));
	
}
$dengji = $dltype;

$status = array('1'=>'审核通过','2'=>'拒绝');
$applyType = array('1'=>'理疗店','2'=>'个人理疗师');

if($apply->try_post()){
	$apply->sh_time = date('Y-m-d H:i:s',time());
	$apply->save();
	tusi("保存成功");
	Redirect::to('list');

	
}
