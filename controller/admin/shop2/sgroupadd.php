<?php
$m = new Model('z02_sgroup');
if(Request::get(1)){
	$m->find(Request::get(1));
	if($m->wid != Session::get('wid')){
		die();
	}
}

if($m->try_post()){
	$m->wid = Session::get('wid');
	$m->save();
	tusi('保存成功');
	Redirect::delay_to('sgroup.html',1);
}