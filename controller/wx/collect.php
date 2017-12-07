<?php
$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
$wechatset = new Model('web_set');
$wechatset->find(array('id'=>2));
if (!empty($wid) && !empty($wxid)) {
	$db = DB::get_db();
	
	if (Request::get(1) =='dropload') {
		$sta = intval(Request::get('page'));
		if (empty($sta)) {
			$sta = 0;
		}
		$sta = $sta*10;
		$artice_array = array();
		$collect = new Model('collect_list');
		$collect_arr = $collect->where(array('wxid'=>$wxid))->limit($sta,10)->list_all_array();
		if(count($collect_arr)){
			foreach($collect_arr as $k=>$v){
				$sql = "SELECT id,title,maketime from artice_list WHERE id='".$v['artId']."'";
				$sql_arr = $db->query($sql);
				$sql_arr[0]['title'] = mb_substr($sql_arr[0]['title'],0,12,'utf-8');
				$artice_array[] = $sql_arr[0];
			}
		}
		echo json_encode($artice_array);
		die;
	}
}else{
	die('请从微信进入');
}









