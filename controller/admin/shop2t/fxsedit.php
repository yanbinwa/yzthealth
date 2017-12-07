<?php
$uid = Session::get('uid');
$wid = Session::get('wid');

$m = new Model('fx_wd_list');

if(Request::get(1)){
	$m->find(Request::get(1));
	if($m->wid != $wid){
		die();
	}

	$wm = new Model('fx_wd_list');
    //一级代理
	$lev1 = $wm->where(array('wid'=>$wid,'pid'=>$m->id))->map_array('id','name'); 
	$lev1count = count($lev1);
	//二级
	$lev2count = 0;
	$lev3count = 0;
	foreach($lev1 as $k=>$v)
	{
	    $lev2 = $wm->where(array('wid'=>$wid,'pid'=>$k))->map_array('id','name'); 
		$lev2c = count($lev2);
		$lev2count += $lev2c;
	}
	//三级
	foreach($lev2 as $kk=>$vv)
	{
	    $lev3 = $wm->where(array('wid'=>$wid,'pid'=>$kk))->map_array('id','name'); 
		$lev3c = count($lev3);
		$lev3count += $lev3c;
	}
	
	
	$wm->find($m->pid);
    $m->pwdname = $wm->name;
}


$levarr = array('0'=>'未代理','1'=>'普通代理','2'=>'高级代理','3'=>'至尊代理');


if($m->try_post()){
	$m->save();
	Redirect::to('fxslist');
}
