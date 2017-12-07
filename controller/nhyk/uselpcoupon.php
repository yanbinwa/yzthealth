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



	$id = intval(Request::get(1));
	if($id!=0)
	{
		$m = new Model('micro_membercard_yhq_set_list');
		$m->find($id);
		if($m->wid == $wid)
		{
			$mm = new Model('micro_membercard_yhq_list');
			$mm->find($m->ttid);
			$bttime = explode('-', $mm->bttime);
			$mm->bttime = date('Y-m-d',strtotime($bttime[1]));
		}
		else
		die();
	}
}else{
	die();
}

