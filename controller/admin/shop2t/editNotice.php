<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');
$m = new Model('z02_index_notice');


if(Request::get(1)){
    
	$m->find(Request::get(1));
	

	if($m->wid!=$wid){
		die('参数传递非法');
	}
}


if($m->try_post()){
	$m->wid = $wid;
	$m->create_time = date('Y-m-d H:i:s',time());
	$m->save();
	Redirect::to('noticeList');
}



