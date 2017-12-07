<?php
$uid = Session::get('uid');
$wid = Session::get('wid');

$m = new Model('z02_stag');
if('del'==Request::get(1)){
	$id = Request::post('id');
	$m->find($id);
	if($m->wid != Session::get('wid')){
		die();
	}else{
		$m->delete();
	}
}elseif('add'==Request::get(1)){
	$tag = Request::post('tag');
	$m->wid = Session::get('wid');
	$m->name = $tag;
	$m->save();
}else{
	$where = array('wid'=>$wid);
	$m->where($where);
	$res = $m->list_all();
}