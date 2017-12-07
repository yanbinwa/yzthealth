<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');

$m = new Model('z02_set');
$m->find(array('id'=>23));

if($m->try_post()){
	$m->save();
	tusi('保存成功');
}
