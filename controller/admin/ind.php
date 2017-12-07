<?php
if(!Session::get('mainuid')){
	Redirect::index();
}
//
if(!auth_check_fun(Session::get('mainuid'))){
	Redirect::to('/admin/main');
}

$u = new Model('user');
$u->find(Session::get('mainuid'));

$pubs = new Model('pubs');
$pub1 = $pubs->where(array('uid'=>$u->id))->field('headpic')->find();
$u->headpic = $pub1->headpic;

$us = new Model('user');
$subusers = $us->where(array('pid'=>Session::get('mainuid')))->order('id')->list_all();

$wid = Session::get('wid');


//设置颜色

if('setcolor'==Request::get(1)){
	$u->theme = Request::post('col');
	$u->save();
	Session::set('maintheme',null);
	Session::set('maintheme',$u->theme);

	
	Response::write($u->theme);
}
$theme = trim($u->theme);
if($theme==''){
	$theme = 'theme-blue';
}
Session::set('maintheme',$theme);



if(Redirect::last_data()){
	tusi(Redirect::last_data());
}