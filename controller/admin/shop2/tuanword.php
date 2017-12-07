<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');

$m = new Model('z02_tuanset');
$m->find(array('wid'=>$wid));
$linkurl = Conf::$http_path.'wsc2/tuan.html?wid='.$wid;

if($m->try_post()){
	$m->wid = $wid;
	$m->create_time = date('Y-m-d H:i:s',time());
	$m->save();
	
	$input['wid'] = $wid;
	$input['keyword'] = $m->gjz;
	$input['type'] = 'z02_tuanset';
	$input['pid'] = $m->id;
	setKeyword($input);
	
	tusi('保存成功');
}
