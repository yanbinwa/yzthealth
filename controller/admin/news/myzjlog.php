<?php
$wid = Session::get('wid');

$m = new Model('myzj');

if('del'==Request::get(1))
{
	$id = Request::post('id');
	$sta = Request::post('val');
	$m->find($id);
	$m->update(array('id'=>$id),array('is_tj'=>$sta));
	
}
$is_show = array('0'=>'显示','1'=>'不显示');


$p = new Pagination();
$res = $p->model_list($m->where(array('aid'=>Request::get(1)))->order('id desc'));


