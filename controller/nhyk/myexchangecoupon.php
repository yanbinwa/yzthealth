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


	$sta = 0;
	if('n'==Request::get(1))
	$sta = 0;
	if('y'==Request::get(1))
	$sta = 1;

	$m = new Model('micro_membercard_yhq_set_list');
	$rs = $m->where(array('wxid'=>$wxid,'tty'=>3,'status'=>$sta))->order('create_time desc')->list_all();

	$mm = new Model('micro_membercard_yhq_list');
	if(!empty($rs))
	{
		foreach ($rs as $v)
		{
			$mm->find($v->ttid);
			$v->name = $mm->name;
			$v->pic = $mm->pic;
			$bttime = explode('-', $mm->bttime);
			$v->bttime = date('Y-m-d',strtotime($bttime[1]));
			$v->des = $mm->des;
		}
	}
}else{
	die();
}

