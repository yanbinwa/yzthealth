<?php
access_control();

$m = new Model('distribution');
if(Request::get(1)){
	$m->find(Request::get(1));
	
}


$status = array('1'=>'审核通过','2'=>'禁用');
if($m->try_post()){
	$m->sex = Request::post('sex');
	$m->pay_status = Request::post('pay_status');
	if(!$m->creat_time){
		$m->creat_time = date('Y-m-d H:i:s',time());
	}

	$db = DB::get_db();
	if($_POST["ins"]){
		if($_POST["ins"] == "insert"){
			$sql = "select id from distribution where name='".$m->name."'";
		}else{
			$sql = "select id from distribution where name='".$m->name."' and id!=".$m->id;
		}
		$rs = $db->query($sql);
		$id = $rs[0]['id'];
		if($id){
			tusi("用户名重复");
		}else{
			$m->save();
			Redirect::to('list');
		}
	}else{
		$m->save();
		Redirect::to('list');
	}

	
}
