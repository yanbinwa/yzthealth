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

	$m = new Model('micro_membercard_member_list');
	$m->find(array('wxid'=>$wxid));

	$grade = explode(',', $mm1->people);


	$m = new Model('micro_membercard_notice');
	$rs = $m->where(array('wid'=>$wid,'wxid'=>$wxid,'status'=>0))->order('id desc')->list_all();
	foreach ($rs as $key=>$v)
	{
		$grade = explode(',', $v->group);
		if($grade[0]!=0)
		{
			if(!in_array($m->grade, $grade))
			{
               unset($rs[$key]);
			}
		}
	}
}else{
	die();
}

