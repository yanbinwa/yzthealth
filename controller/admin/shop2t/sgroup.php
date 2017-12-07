<?php
access_control();
$wid = Session::get('wid');
$unid = Session::get('unid');

$m = new Model('z02_sgroup');
if('del'==Request::get(1)){
	$id = Request::post('id');
	$m->find($id);
	if($m->wid == $wid && $m->unid == $unid){
		$m->delete();
	}else{
		die();
	}
}else{
	$m->where(array('wid'=>$wid,'unid'=>$unid));
	$p = new Pagination();
	$res = $p->model_list($m);
}