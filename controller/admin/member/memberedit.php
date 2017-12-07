<?php
access_control();
$id = Request::get(1);
if(!empty($id)){
	$m = new Model('member');
	$m->find(array('id'=>$id));
}
$status = array('0'=>'未进行实名认证','1'=>'等待审核','2'=>'实名认证成功','3'=>'审核失败');
$is_freez = array('0'=>'正常','1'=>'冻结');
if($m->try_post()){

	$m->save();
	tusi('保存成功');
	Redirect::delay_to("list",1);
}