<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}

if(Session::has('wxid') && Session::has('wid')){
	$wid = Session::get('wid');
	$wxid= Session::get('wxid');

	$year = intval($_GET['y'])==0?date('Y',time()):intval($_GET['y']);
	$month = intval($_GET['m'])==0?date('m',time()):intval($_GET['m']);

	$nyear = $year;
	$pyear = $year;

	$nextm = $month+1;
	$prem  = $month-1;
	if($nextm>12)
	{
		$nextm = 1;
		$nyear = $year+1;
	}
	elseif($prem==0)
	{
		$prem = 12;
		$pyear = $year-1;
	}


	$m = new Model('micro_membercard_setscores');
	$m->find(array('wid'=>$wid));

	$mm = new Model('micro_membercard_member_list');
	$mm->find(array('wid'=>$wid,'wxid'=>$wxid));

	$sm = new Model('micro_membercard_sign_log');

	$cc = $sm->where("to_days(create_time)=to_days(now()) and wxid='".$wxid."' and type=0")->count(); //当天

	$allcc = $sm->where("wid ='".$wid."' and wxid='".$wxid."'")->count(); //所有

	$evem = $sm->where("year(create_time)='".$year."' and month(create_time)='".$month."' and  wid ='".$wid."'  and wxid='".$wxid."' and type=0 ")->list_all(); //所有
	if(!empty($evem))
	{
		foreach ($evem as $v)
		{
			if($verstr!='')
			$verstr = $verstr.','.date('d',strtotime($v->create_time));
			else
			$verstr = date('d',strtotime($v->create_time));
		}
	}
	/****************** 签到 送积分 推荐商品  lzy****************/
	if($wid==54718){
	//if($wid==147){
		$pro = new Model('z02_sproduct');
		$tj7 = $pro->where(array('wid'=>$wid,'rm'=>'1','status'=>0))->limit(6)->order('sort desc')->list_all();
	}
	/****************** ********* *****************/
	if('qd'==Request::get(1) && $cc==0)
	{

		$sm->wid = $wid;
		$sm->wxid= $wxid;
		$sm->inte= $m->edaygive;
		$sm->title= '签到得积分';
		$sm->create_time = date('Y-m-d H:i:s',time());
		if($sm->save())
		{
			changeInte($mm->id,$m->edaygive,0,'',8);
				
			$lx = $sm->where("DATE_SUB(CURDATE(), INTERVAL ".$m->nday." DAY) <= date(create_time) and  wid ='".$wid."' and wxid='".$wxid."' and type=0 and iskey=0")->count(); //当天
			$nday = intval($m->nday);
			if($lx==$m->nday && $nday!=0) //送
			{   
				$db = DB::get_db();
			    $sql = "update  micro_membercard_sign_log  set iskey= 1 where wxid=$wxid and DATE_SUB(CURDATE(), INTERVAL $m->nday  DAY) <= date(create_time)";
				$db->query($sql);
				unset($sm->id);
				$sm->wid = $wid;
				$sm->wxid= $wxid;
				$sm->inte= $m->ndaygive;
				$sm->title= '连续签到得积分';
				$sm->type= 1;
				$sm->create_time = date('Y-m-d H:i:s',time());
				$sm->save();
				changeInte($mm->id,$m->ndaygive,0,'',8);
			}
			$return = array('status'=>1,'msg'=>'恭喜您签到成功，获得'.$m->edaygive.'积分');
		}
		else
		{
			$return = array('status'=>0,'msg'=>'签到失败，请刷新后重试');
		}
		echo json_encode($return);die;
	}

}else{
	die();
}

