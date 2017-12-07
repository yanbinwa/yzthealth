<?php

$wid = Session::get('wid');
if(!(Request::get('wid')) && !$wid){
	Redirect::to('postageGroup');
}

$addCityModel = new Model('postage_add_city');

if($addID = Request::get('id')){
	$addCityModel->delete(array(id=>$addID,wid=>$wid));
	Redirect::to('postageGroup.html?wid='.$wid);
}
?>