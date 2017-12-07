<?php
$wid = Session::get('wid');

$m = new Model('z02_comm');

if('del'==Request::get(1))
{
	$id = Request::post('id');
	$sta = Request::post('val');
	$m->find($id);
	if($m->wid != $wid){
		die();
	}else{
		$m->update(array('id'=>$id),array('is_show'=>$sta));
	}
}
$is_show = array('0'=>'显示','1'=>'不显示');


$p = new Pagination();
$res = $p->model_list($m->where(array('wid'=>$wid,'pid'=>Request::get(1)))->order('id desc'));


