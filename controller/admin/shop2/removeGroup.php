<?php
$wid = Session::get('wid');
if(!$wid){
	Redirect::to('/admin/ind');
}

$groupModel = new Model('postage_group');
$addCityModel = new Model('postage_add_city');

if(Request::get('group_id')){
	$gid = Request::get('group_id');
	$groupModel->delete(array('wid'=>$wid,'id'=>$gid));
	$addCityModel->delete(array('wid'=>$wid,'group_id'=>$gid));
	Redirect::to('/admin/shop2/postageGroup');
	//$addCityModel->delete(array(id=>$addID,wid=>$wid));
}

?>