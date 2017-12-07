<?php
access_control();
$wid = Session::get('wid');
$unid = Session::get('unid');

$db = DB::get_db();
$m = new Model('z02_sprop');

if(Request::get(1)){
	$spec_id = Request::get(1);
	
	//根据规格编号 获取规格详细信息
	//$specData = $db->query("select * from z02_sprop where wid='{$wid}' and unid='{$unid}' and is_del='0' and id='{$spec_id}'"); 
	
	$specData = $m->where(array('wid'=>$wid,'unid'=>$unid,'is_del'=>0,'id'=>$spec_id))->list_all_array();
	
	
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
    $res = $m->where(array('wid'=>$wid,'unid'=>$unid,'is_del'=>0))->list_all_array();
	//$res = $db->query("select * from z02_sprop where wid='{$wid}' and unid='{$unid}' and is_del='0'"); 
}