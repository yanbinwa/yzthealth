<?php
if(	Request::get('areaid') && Request::get('wid')){
	$changeid = Request::get('areaid');
	$wid = Request::get('wid');
	$memberDzListModel = new Model('micro_membercard_dz_list');
	$memberDzListInfo  = $memberDzListModel->where(array('id'=>$changeid))->list_all_array();
	if(isset($memberDzListInfo[0]['id'])){
		$db = DB::get_db();
		//普通SQL
		$sql = "select pg.postages,pg.addone from postage_add_city as pac
				left join postage_group as pg on pac.group_id = pg.id
				where pac.city_id=".$memberDzListInfo[0]['s_s']." and pac.wid=".$wid;
		$postageInfo = $db->query($sql);
		if(empty($postageInfo)){
			// echo '{"success":false,"msg":"暂不支持该地区"}';
			// exit;
			$m = new Model('z02_set');
			$m->find(array('wid'=>$wid));
			$postageInfo[0]['postages']=$m->default_postage;
			$postageInfo[0]['addone']=$m->addone;
		}
		$data = array('success'=>true,'data'=>$postageInfo);
		echo json_encode($data);
		exit;
	}else{
		echo '{"success":false,"msg":"错误"}';
		exit;
	}
}
?>