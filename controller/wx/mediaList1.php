<?php
$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
if (!empty($wid) && !empty($wxid)) {
	$cataId = Request::get(1);
	$cata = new Model('cata_list');
	$cata->find(array('id'=>$cataId));

	$db = DB::get_db();
	if (Request::get(2)=='dropload') {
		$cataId = Request::get(1);
		$sta = intval(Request::get('page'));
		if (empty($sta)) {
			$sta = 0;
		}
		$sta = $sta*10;

		$sql = "SELECT id,title,pic,jianjie from artice_list WHERE cateid='".$cataId."' order by id desc limit $sta,10";
		$video_array = $db->query($sql);
		echo json_encode($video_array);
		die;
	}
}else{
	die('请从微信进入');
}








