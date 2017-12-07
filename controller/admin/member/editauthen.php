<?php
	access_control();
	$wid = Session::get('wid');
	if(Request::get(1) == 'show'){
		$uid = Request::get(2);
		$m = new Model('member');
		$info = $m->where(array('id'=>$uid))->list_all_array();
		echo json_encode($info[0]);
		exit;
	}
	if(Request::get(1)=='edit'){
		$id = Request::post('id');
		$status = Request::post('status');
		$bz = Request::post('bz');
		$m= new Model('member');
		if($m->find($id)){
			$m->status = $status;
			$m->bz = $bz;
			if($m->save()){
				maketj_reg($id);
				$return['info'] = '审核成功';
				$return['status'] = '1';
			}else{
				$return['info'] = '审核失败';
				$return['status'] = '2';
			}
		}else{
			$return['info'] = '系统错误';
			$return['status'] = '2';
		}
		echo json_encode($return);
		exit;
	}
	