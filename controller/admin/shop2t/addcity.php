<?php
access_control();
$wid = Session::get('wid');
$unid = Session::get('unid');

//默认省份
$defaultProvince = 1;

//获取邮费分组
$groupModel = new Model('postage_group');
$groupArray = $groupModel->where(array(wid=>$wid,'unid'=>$unid))->list_all_array();

//获取省份和城市
$provinceModel = new Model('chinaarea');
$provinceArray = $provinceModel->where(array('typ'=>1))->list_all_array();

//获取默认省份城市
$cityArray = $provinceModel->where(array('pid'=>$defaultProvince))->list_all_array();

$addCityModel = new Model('postage_add_city');
//获取已经添加的城市信息
$checkcities = $addCityModel->where(array(wid=>$wid,'unid'=>$unid))->list_all_array();
foreach ($checkcities as $value){
	$checkArray[] = $value['city_id'];
}

//获取表单数据
if(Request::post() && $wid = Session::get('wid')){
	if(isset($_POST['checkcity']) && !empty($_POST['checkcity'])){
		$addCityModel->province_id = $_POST['province'];
		$addCityModel->group_id = $_POST['group_name'];
		$addCityModel->wid = $wid;
        $addCityModel->unid = $unid;
		//删除旧数据
		foreach ($checkcities as $value) {
			if($value['province_id'] == $_POST['province']){
				$addCityModel->delete(array(city_id=>$value['city_id'],wid=>$wid,'unid'=>$unid));
			}
		}
		foreach ($_POST['checkcity'] as $value) {
			$addCityModel->id = '';
			$addCityModel->city_id = $value;
			$addCityModel->save();
		}
		Redirect::to('postageGroup.html?wid='.$wid);
	}else{
		$addCityModel->delete(array(province_id=>$defaultProvince,wid=>$wid));
		Redirect::to('postageGroup.html?wid='.$wid);
	}
}

//省市2级联动
if($province = Request::get('province')){
	$cityArray = $provinceModel->where(array('pid'=>$province,typ=>2))->list_all_array();
	$returnJson= array('success'=>true,'data'=>$cityArray,'checked'=>$checkcities);
	echo json_encode($returnJson);
	exit;
}
?>