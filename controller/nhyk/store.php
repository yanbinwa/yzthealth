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


	$lbs = new Model('lbs');
	$lbsarr = $lbs->where(array('wid'=>$wid,'istag'=>1))->list_all();


}else{
	die();
}

