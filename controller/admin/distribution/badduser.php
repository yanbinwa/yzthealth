<?php
access_control();
$unid = Session::get('unid'); //代理商id
if (!$unid) {
	Redirect::to('/dls/login');
}

if(Request::get(1) == 'check'){
	$member= new Model('member');
	$member->find(array('uname'=>Request::post('name')));
	if($member->id){
		$return['status']=1;
	}else{
		$return['status']=2;
	}
	echo json_encode($return);
	die;
}
if(Request::get(1) == 'checktel'){
	$member= new Model('member');
	$tel = Request::post('tel');
	if(preg_match("/^1[34578]{1}\d{9}$/",$tel)){
		$member->find(array('telephone'=>Request::post('tel')));
		if($member->id){
			$return['status']=1;
			$return['msg'] ='此手机号码已经注册请重新输入';
		}else{
			$return['status']=2;
		}
	}else{
		$return['status'] =1;
		$return['msg'] = "手机号码格式不正确";
	}
	
	echo json_encode($return);
	die;
}

$m = new Model('member');

if(Request::get(1)){
	$m->find(Request::get(1));
	
}
$status = array('0'=>'正常','2'=>'冻结');

if($m->try_post()){
	//判断用户名是否存在
	$errorinfo = '';
	
	if(!$errorinfo){
		$m->rtime = date('Y-m-d H:i:s',time());
		$user->rip = Request::ip();
		$m->save();
		
		$model = new Model('member_distribut');
		$checkgl = $model->find(array('mid'=>$m->id));
		if($checkgl->id == ''){
			$model->mid = $m->id;
			$model->did = $unid;
			$model->creat_time = date("Y-m-d H:i:s",time());
			$model->save();
		}
		Redirect::to('blist');
	}
	
}
