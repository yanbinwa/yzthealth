<?php

$unid = intval(Request::get('uid')); 

	if (Request::get(1) =='dropload') {
		$sta = intval(Request::get('page'));
		if (empty($sta)) {
			$sta = 0;
		}
		$sta = $sta*5;

		$db = DB::get_db();
		$sql = "SELECT id,name,pic,lltime,rowtemp from z02_sproduct WHERE unid='".$unid."' and sh_status='1' and is_ll='1' order by id desc limit $sta,5";
		$artice_array = $db->query($sql);
		foreach ($artice_array as $k => $v) {
		$rowtemp = json_decode($v['rowtemp'],true);
		foreach ($rowtemp as $key => $value) {
			if ($key > 0) {
				break;
			}
			$rowtempa = $value['sell_price'];
		}
		$artice_array[$k]['rowtemp'] = $rowtempa;
	}
		echo json_encode($artice_array);
		die;
	}
$webSet = new Model('web_set');
$webSet->find(array('id'=>2));


	