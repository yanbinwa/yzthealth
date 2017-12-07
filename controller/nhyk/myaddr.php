<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}

if(Session::has('wxid') && Session::has('wid')){
	$wid = Session::get('wid');
	$wxid= Session::get('wxid');

	$m = new Model('micro_membercard_dz_list');
	$rs = $m->where(array('wxid'=>$wxid))->list_all();
	$cc = count($rs);
	if($cc==0)
	{
		$mm = new Model('micro_membercard_member_info');
		$mm->find(array('wxid'=>$wxid));
		$rs['-1'] = $mm;
	}

	if(Request::get('del') == 'del' && Request::get('id')){
		$del = $m->find(Request::get('id'));
		$del->remove();
		Redirect::to('myaddr');
	}
}else{
	die();
}
