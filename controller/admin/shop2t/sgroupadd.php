<?php
access_control();
$wid = Session::get('wid');
$unid = Session::get('unid');

$m = new Model('z02_sgroup');
if(Request::get(1)){
	$m->find(Request::get(1));
	if($m->wid != $wid || $m->unid != $unid){
		die();
	}
}

if($m->try_post()){
	$m->wid = $wid;
	$m->unid = $unid;
	$m->save();
	tusi('保存成功');
	Redirect::delay_to('sgroup.html',1);
}