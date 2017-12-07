<?php
$uid = Session::get('uid');
$wid = Session::get('wid');

$m = new Model('z02_return_log');

if(Request::get(1)){
	$m->find(Request::get(1));
	if($m->wid != $wid){
		die();
	}
}


$rtype = array('1'=>'退货','2'=>'换货');
$rsta = array('1'=>'退款申请中','2'=>'退款中','3'=>'已退款');
$rreplysta = array('0'=>'未处理','1'=>'同意','2'=>'不同意');



if($m->try_post()){
    $m->rrtime = date('Y-m-d H:i:s',time());
	$m->save();
	Redirect::to('thhorders');
}
