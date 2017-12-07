<?php
access_control();
$wid = Session::get('wid');
if(Session::has('unid'))
{
   Session::clear();
}

$m = new Model('z02_index_ad');
$m->find(array('wid'=>$wid));


if($m->try_post()){
	$m->wid = $wid;
	$m->create_time = date('Y-m-d H:i:s',time());
	$m->save();
	tusi('保存成功');
}

if($wid==9)
Page::view('adset9');

