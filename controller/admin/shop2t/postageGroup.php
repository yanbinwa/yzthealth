<?php
access_control();
$wid = Session::get('wid');
$unid = Session::get('unid');

//获取分组
$groupModel = new Model('postage_group');
$groupArray = $groupModel->where(array(wid=>$wid,'unid'=>$unid))->list_all_array();

$addCityModel = new Model('postage_add_city');

foreach ($groupArray as $key => $value) {
	$k = $value['id'];
	$db = DB::get_db();
	//普通SQL
	$sql = "select pac.id,c.name,c.ord,pgp.group_name,pgp.id as gid from postage_add_city as pac 
	left join postage_group as pgp on pac.group_id = pgp.id 
	left join chinaarea as c on pac.city_id = c.ord
	where pac.group_id =".$value['id']." and pac.wid=".$wid;
	$cityArray[$k] = $db->query($sql);
}

$m = new Model('z02_set');
$m->find(array('wid'=>$wid));

if($groupModel->try_post()){
    $groupModel->unid = $unid;
	$groupModel->wid = $wid;
 	if($groupModel->save()) {
 		Redirect::to('postageGroup.html?wid='.$wid);
    }  
}
?>
