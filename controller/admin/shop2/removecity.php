<?php

$wid = Session::get('wid');
if(!(Request::get('wid')) && !$wid){
	Redirect::to('/admin/shop2/postageGroup');
}

$addCityModel = new Model('postage_add_city');

if($addID = Request::get('id')){
	$addCityModel->delete(array(id=>$addID,wid=>$wid));
	Redirect::to('/admin/shop2/postageGroup.html?wid='.$wid);
}
?>