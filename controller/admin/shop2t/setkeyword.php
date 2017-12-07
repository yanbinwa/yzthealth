<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');
if(Session::has('unid'))
{
   Session::clear();
}

$m = new Model('z02_set');
$m->find(array('wid'=>$wid));
$linkurl = Conf::$http_path.'wsc2t/index.html?wid='.$wid;

if($m->try_post()){
	$m->wid = $wid;
	$m->create_time = date('Y-m-d H:i:s',time());
	$m->save();
	tusi('保存成功');
}

