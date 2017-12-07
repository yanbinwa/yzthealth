<?php
	access_control();
	$uid = Session::get('uid');
	$wid = Session::get('wid');
	$lbs = new Model('youhuiquan_list');
	$yid = Request::get(1);
	$p = new Pagination();
	$where = array('yid'=>$yid,'wid'=>$wid);
	$rs = $p->model_list($lbs->where($where)->order('id desc'));
	foreach ($rs as $key => $value) {
		
		$rs[$key]->info = get_wx_info($value->openid,$wid);
	}




