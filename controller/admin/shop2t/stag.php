<?php
$m = new Model('z02_stag');
$unid = Session::get('unid');

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
	$m->unid = $unid;
	$m->name = $tag;
	$m->save();
}else{
	$m->where(array('wid'=>Session::get('wid'),'unid'=>$unid));
	$res = $m->list_all();
}