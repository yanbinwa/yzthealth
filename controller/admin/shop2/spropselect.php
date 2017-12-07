<?php
$db = DB::get_db();
$wid = Session::get('wid');
$m = new Model('z02_sprop');




if(Request::get(1)){
	$spec_id = Request::get(1);
	
	//根据规格编号 获取规格详细信息
	$specData = $db->query("select * from z02_sprop where wid='{$wid}' and is_del='0' and id='{$spec_id}'"); 
	if($specData)
	{
		echo json_encode($specData[0]);
	}
	else
	{
		//返回失败标志
		echo '';
	}
	exit('');
}else{
	$res = $db->query("select * from z02_sprop where wid='{$wid}' and is_del='0'"); 
}