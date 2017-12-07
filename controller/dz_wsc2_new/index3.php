<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}

if(Session::has('wxid') && Session::has('wid')){
	
	
	$wid = Session::get('wid');
	$wxid = Session::get('wxid');
	$key = Request::get('key',Session::get('wsckey'));
	Session::set('wsckey',$key);
		

	if(Request::post('ord')){
	
		$ordarr = array('iddesc'=>'sort desc,id desc','zk'=>'zk','sale_nums'=>'sale_nums');

		$typ = Request::post('typ');
		$start = Request::post('limit','0');
		$pro = new Model('z02_sproduct');
		$wherearr = array('wid'=>$wid,'status'=>0,'sh_status'=>1,'is_ll'=>0);
		if($typ!='0'){
			$wherearr['typ'] = $typ;
		}
		//$wherearr['OR'] = array('name@~'=>$key,'ms@~'=>$key);
		$wherearr['OR'] = array('name@~'=>$key,'ms@~'=>$key);

		$res = $pro->where($wherearr)->limit($start,10)->order($ordarr[Request::post('ord')])->list_all_array();
		Response::json($res);
	}else{
		
	}
	
}else{
	die('请从微信进入');
}