<?php

$uid = Session::get('uid');
$wid = Session::get('wid');
$unid = Session::get('unid');


$m = new Model('z02_zmd_tx_bank');
$m->find(array('wid'=>$wid,'unid'=>$unid));


if($m->try_post()){

    $m->create_time = date('Y-m-d H:i:s',time());
	$m->wid = $wid;
	$m->unid = $unid;
	$m->save();
	
	tusi('保存成功');
}
