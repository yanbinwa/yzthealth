<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}

if(Session::has('wid')){
	$wid = Session::get('wid');
	$wxid = Session::get('wxid');
	$lb = new Model('youhuiquan_list');
	//待使用优惠券
	$where = array('wid'=>$wid,'openid'=>$wxid,'state'=>0);
	$rs = $lb->where($where)->order('receivetime desc')->list_all();
	foreach($rs as $k=>$v){
		$lbs = new Model('youhuiquan');
		$rs[$k]->info = $lbs->where(array('wid'=>$v->wid,'id'=>$v->yid,'endtime@>'=>date('Y-m-d H:i:s',time())))->find();
		if(!$lbs->id){
			unset($rs[$k]);
		}
		

	}
	//已使用优惠券
	$lbed = new Model('youhuiquan_list');
	$where1 = array('wid'=>$wid,'openid'=>$wxid,'state'=>1);
	$yhjed = $lbed->where($where1)->order('receivetime desc')->list_all();

	if (count($yhjed)) {
		foreach($yhjed as $k=>$v){
			$lbs = new Model('youhuiquan');
			$yhjed[$k]->info = $lbs->where(array('wid'=>$v->wid,'id'=>$v->yid))->find();

		}	
	}
	//已失效优惠券
	$lbedate = new Model('youhuiquan_list');
	$where2 = array('wid'=>$wid,'openid'=>$wxid,'state'=>0);
	$yhjdate = $lbedate->where($where2)->order('receivetime desc')->list_all();
	if (count($yhjdate)) {
		foreach($yhjdate as $k=>$v){
			$lbs = new Model('youhuiquan');
			$yhjdated[$k] = $lbs->where(array('wid'=>$v->wid,'id'=>$v->yid,'endtime@<'=>date('Y-m-d H:i:s',time())))->find();
			if (!$lbs->has_id()) {
				unset($yhjdated[$k]);
			}


		}
	}
			

	 
}else{
	die('请从微信进入');
}

if(Request::post() && 'check'==Request::get(1)){
	$wid = Session::get('wid');
	$wxid = Session::get('wxid');
	$myyhj = new Model('youhuiquan_list');
	$id = floatval($_POST['id']);
	$price = floatval($_POST['price']);
	if(!empty($id)){
		$info = $myyhj->where(array('id'=>$id,'wid'=>$wid,'openid'=>$wxid))->find();
		if($info->id){
			$yhjlist = new Model('youhuiquan');
			$list_info = $yhjlist->where(array('id'=>$info->yid))->find();
			if($list_info->id){
				if($list_info->endtime >= date('Y-m-d H:i:s',time())){
					if($price>=$list_info->maxcon){
						$errno = 200;
						$error = "您本单将减免".$list_info->redcon."元";
					}else{
						$errno = 202;
						$error = "您不满足使用此优惠券的条件";
					}
				}else{
					$errno = 202;
					$error = "此优惠劵已过期";
				}
			}else{
				$errno = 202;
				$error = "此优惠劵已失效";
			}
		}else{
			$errno = 202;
			$error = $info;
		}
	}else{
		$errno = 203;
		$error = '参数传递错误请刷新后重试';
	}
	$rs = array(
	  'errno' =>$errno,
	  'error' =>$error 
	);
	response::json($rs);
}