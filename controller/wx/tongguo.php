<?php

$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
$opid = Session::get('opid');
if (!empty($wid) && !empty($wxid)) {
    $web_set = new Model('web_set');
    $web_set->find(array('id'=>2));
	$apply = new Model('apply_cooperation');
    $apply->find(array('wxid'=>$wxid,'wid'=>$wid));     
    
}