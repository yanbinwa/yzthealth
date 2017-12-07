<?php
access_control();
$wid = Session::get('wid');
if(Session::has('unid'))
{
   Session::clear();
}


$m = new Model('z02_stype');
if('del'==Request::get(1)){
	$id = Request::post('id');
	$m->find($id);
	if($m->wid == $wid){
		$m->is_show = 0;
		$m->save();
	}else{
		die();
	}
}else{
	$m->where(array('wid'=>$wid,'is_show'=>'1'))->order('sort desc');
	$p = new Pagination();
	$res = $p->model_list($m);
}