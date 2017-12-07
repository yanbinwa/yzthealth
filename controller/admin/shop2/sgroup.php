<?php
$m = new Model('z02_sgroup');
if('del'==Request::get(1)){
	$id = Request::post('id');
	$m->find($id);
	if($m->wid != Session::get('wid')){
		die();
	}else{
		$m->delete();
	}
}else{
	$m->where(array('wid'=>Session::get('wid')));
	$p = new Pagination();
	$res = $p->model_list($m);
}