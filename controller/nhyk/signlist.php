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

	$m = new Model('micro_membercard_sign_log');
	$rs = $m->where(" wid ='".$wid."' and wxid='".$wxid."'")->list_all(); //所有

}else{
	die();
}

