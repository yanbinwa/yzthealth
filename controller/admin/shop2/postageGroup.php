<?php

$wid = Session::get('wid');
if(!wid){
	Redirect::to('/admin/ind');
}
//获取分组
$groupModel = new Model('postage_group');
$groupArray = $groupModel->where(array(wid=>$wid))->list_all_array();

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

if($groupModel->try_post()&&$_POST['newgroupid']=="newgroupid"){
	$groupModel->wid = $wid;
 	if($groupModel->save()) {
 		Redirect::to('/admin/shop2/postageGroup.html?wid='.$wid); 
    }  
}

$addCityModel = new Model('postage_add_city');
if($addCityModel->try_post()&&$_POST['grouplocate']=="grouplocate"){
	if(!empty($_POST['locate'])){
	$group_id = $_POST['group_id'];	
	$addrarr = '';
	foreach ($_POST['locate'] as $k => $v) {
			$addrarr[] = explode(":",$v);
	}

	foreach ($addrarr as $ke => $va) {


				if (isset($addrarr[$ke]['1'])) {
					$addarr[]= $addrarr[$ke]['1'];
					$addrstr = implode(',',$addarr);
				}else{
					$addarrp[]= $addrarr[$ke]['0'];
					$addrstrp = implode(',',$addarrp);
				}
				
			}

	if (!empty($addrstr)) {
		foreach ($addarr as $val) {
		$sql = "select pid,ord from chinaarea where name like '%$val%' limit 0,1";
		$cityArr= $db->query($sql);
		foreach ($cityArr as $valu) {

			$province_id = $valu['pid'];
			$city_id = $valu['ord'];
			$addCityModel->where(array('group_id'=>$group_id,'wid'=>$wid,'city_id'=>$city_id))->find();
			if (!$addCityModel->id) {
				$sql = "INSERT INTO postage_add_city VALUES ('','$group_id','$province_id','$city_id','$wid')";
				$db->query($sql);
			}
			

		}
				
	}
		
	}
	if (!empty($addrstrp)) {
		foreach ($addarrp as $val) {
		$sql = "select pid,ord from chinaarea where pid in (select id from chinaarea where name like '%$val%') ";
		$cityArr= $db->query($sql);
		foreach ($cityArr as $valu) {
			$province_id = $valu['pid'];
			$city_id = $valu['ord'];
			$checkCity = new Model('postage_add_city');
			$checkCity->where(array('group_id'=>$group_id,'wid'=>$wid,'city_id'=>$city_id))->find();
			if (!$checkCity->id) {
				$sql = "INSERT INTO postage_add_city VALUES ('','$group_id','$province_id','$city_id','$wid')";
				$db->query($sql);
			}
			

		}
				
	}
	}
	
	tusi('保存成功');
	Redirect::to('postageGroup');
	}
		
}

?>
