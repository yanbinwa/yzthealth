<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}
if(Session::has('wxid') && Session::has('wid')){
	$wid = Session::get('wid');
	$wxid = Session::get('wxid');
	$prid = Request::get(1);
	
	//上方幻灯片
	$p = new Model('z02_sproduct');
	$p->find($prid);
	//判断该产品团购是否开始
	$kai = 1;
	$kaishi = strtotime($p->tkssj);
	if($kaishi > time()){
		$kai = 0;
	}
}