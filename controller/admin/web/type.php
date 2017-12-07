<?php
$wid = Session::get('wid');

$m = new Model('web_type');

// if('del'==Request::get(1))
// {
// 	$id = Request::post('id');
// 	$sta = Request::post('val');
// 	$m->find($id);
// 	$m->update(array('id'=>$id),array('is_tj'=>$sta));
	
// }
// $is_show = array('0'=>'显示','1'=>'不显示');


$p = new Pagination();
$res = $p->model_list($m->order('id desc'));

