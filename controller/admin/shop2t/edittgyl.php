<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');
$m = new Model('z02_tgyl_list');


if(Request::get(1)){
	$m->find(Request::get(1));
	if($m->wid != $wid){
		die();
	}
  if($m->type!=0)
    $m->des = json_decode(urldecode($m->des));
	
	
	$linkurl = Conf::$http_path.'tgyl/list-'.$m->id.'.html?wid='.$wid;
	
}

$status = array('0'=>'暂不发布','1'=>'发布');
if($m->try_post()){
	$m->wid = $wid;	
	$m->create_time = date('Y-m-d H:i:s',time());
	$m->save();
	tusi('保存成功');
	Redirect::to('listtgyl');
}





