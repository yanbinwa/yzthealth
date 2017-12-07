<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');

$m = new Model('z02_set');
$m->find(array('wid'=>$wid));


if ($wid==6769786) {
	$linkurl = Conf::$http_path.'wsc2_weijin/index.html?wid='.$wid;
}else{
	$linkurl = Conf::$http_path.'wsc2/index.html?wid='.$wid;
}


if($m->try_post()){
	$m->wid = $wid;
	$m->create_time = date('Y-m-d H:i:s',time());
	$m->save();
	
	$input['wid'] = $wid;
	$input['keyword'] = $m->gjz;
	$input['type'] = 'z02_set';
	$input['pid'] = $m->id;
	setKeyword($input);
	
	tusi('保存成功');
}

//积分线上测试程序
// if($wid==3723)
// {
	page::view("setkeyword2");
// }