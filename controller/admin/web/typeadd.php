<?php
$wid = Session::get('wid');

$m = new Model('web_type');
$id = Request::get(1);
$m->find(array('id'=>$id));
if($m->try_post()){
	$m->save();
	tusi('保存成功');
	Redirect::delay_to("type.html",1);
}

