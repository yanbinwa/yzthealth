<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}

if(Session::has('wid')){
	$wid = Session::get('wid');
	$wxid= Session::get('wxid');

	$where = array('wid'=>$wid);
	$set  = new Model('micro_membercard_set');
	$set->find($where);
	$info = new Model('micro_membercard_info');
	$info->find($where);
	
	$mlistm= new Model('micro_membercard_member_list');
	$mlistmcc = $mlistm->where(array('wid'=>$wid,'wxid'=>$wxid))->count();
	
	
	$gl = new Model('micro_membercard_cardgift_list');
	$glist = $gl->where(array('wid'=>$wid,'status'=>1,'xs'=>1))->list_all();
	
}else{
	die();
}


