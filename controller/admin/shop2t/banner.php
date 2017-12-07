<?php
access_control();
$wid = Session::get('wid');
if(Session::has('unid'))
{
   Session::clear();
}


$m = new Model('z02_index_banner');
if('del'==Request::get(1)){
	$id = Request::post('id');
	$m->find($id);
	if($m->wid == $wid){
		$m->delete();
	}else{
		die();
	}
}else{
	$m->where(array('wid'=>$wid));
	$p = new Pagination();
	$res = $p->model_list($m);
}