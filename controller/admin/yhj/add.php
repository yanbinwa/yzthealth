<?php

$lbs = new Model('youhuiquan');
$wid = Session::get('wid');
if('edit'==Request::get(1))
{

	$id    = Request::get('id');

	$lbs->find(array('id'=>$id));
}

if($lbs->try_post()){
	$lbs->uid = Session::get('uid');
	$lbs->wid = Session::get('wid');
	$lbs->creattime = date('Y-m-d H:i:s',time());
 	$lbs->save();
	tusi('保存成功');
	Redirect::to('index');

}

?>