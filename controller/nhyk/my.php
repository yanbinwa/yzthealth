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
	$m->find(array('wid'=>$wid,'wxid'=>$wxid));
	
	$info = new Model('micro_membercard_info');
	$info->find(array('wid'=>$wid));
	
	$g = new Model('micro_membercard_grade');
	$grs = $g->find(array('wid'=>$wid,'lev'=>$m->grade));
	
	$m->grade = $grs->grade;
	
	$m1 = new Model('micro_membercard_member_info');
	$m1->find(array('wid'=>$wid,'wxid'=>$wxid));
	
	$m2 = new Model('micro_membercard_yhq_set_list');
	//优惠券
	$yhq = $m2->where(array('wid'=>$wid,'wxid'=>$wxid,'tty'=>1,'status'=>0))->count();
	//代金券
	$djq = $m2->where(array('wid'=>$wid,'wxid'=>$wxid,'tty'=>2,'status'=>0))->count();
	
	
}else{
	die();
}

