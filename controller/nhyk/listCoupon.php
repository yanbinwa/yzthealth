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
	$m = new Model('micro_membercard_member_list');
	$m->find(array('wid'=>$wid,'wxid'=>$wxid));
	if($m->has_id())
	{
		$setm = new Model('micro_membercard_yhq_set_list');
		
		$mm = new Model('micro_membercard_yhq_list');
		$rs = $mm->where(array('wid'=>$wid,'property'=>1,'type'=>1))->order('create_time desc')->list_all();
		foreach ($rs as $key=>$v)
		{
			if($v->maketimes!=0)
			{
				$setcc = $setm->where(array('ttid'=>$v->id,'wxid'=>$wxid))->count();
				if($setcc<$v->maketimes)
				{
					$v->hasget = 0;
				}
				else
				{
					$v->hasget = 1;
				}
			}

			$bttime = explode('-', $v->bttime);
			if (strtotime($bttime[1]) <= time() ) {
				unset($rs[$key]);
				continue;
			}
			$v->bttime = date('Y-m-d',strtotime($bttime[1]));
			if($v->sendtimes!=0)
			{
				$setc = $setm->where(array('ttid'=>$v->id))->count();
				$v->shorttimes = $v->sendtimes-$setc;
				if($v->shorttimes<0)
				$v->shorttimes = 0;
			}
			

			$yhqgrade = explode('-', $v->people);
			if ($yhqgrade[0]!=0)
			{
				if(!in_array($m->grade, $yhqgrade))
				{
					unset($rs[$key]);
				}
			}
		}
		

		$tp = intval($_GET['type']);
		$id = intval($_GET['id']);
		if($id!=0) //领券
		{
			$mm1 = $mm ->find($id);
			$grade = explode(',', $mm1->people);

			if($mm1->wid==$wid && (in_array($m->grade, $grade) || $grade[0]==0))
			{
				$setcount = $setm->where(array('ttid'=>$id,'wxid'=>$wxid))->count();
				$seted = $setm->where(array('ttid'=>$id))->count();
				if($mm1->sendtimes>$seted || $mm1->sendtimes==0)
				{
					if($mm1->maketimes==0 || $setcount<$mm1->maketimes)
					{
						$setm->ttid  = $id;
						$setm->tty   = $tp;
						$setm->wid   = $wid;
						$setm->wxid  = $wxid;
						$setm->sn = rand6();
						$setm->snstetime   = date('Y-m-d H:i:s',time());
						$setm->create_time = date('Y-m-d H:i:s',time());
						$setm->type = 2;

						$setm->save();
						$status =1;
						$msg = "领取成功";
					}
					else
					{
						$status =2;
						$msg = "已经领取过了";
					}
				}
				else
				{
					$status =3;
					$msg = "喔喔，来晚了，发没了哦";
				}
			}
			else
			{
				$status =4;
				$msg = "参数传递错误";
			}
			echo json_encode(array('status'=>$status,'msg'=>$msg));
			die;
		}
	}
	else
	{
		Redirect::to('haveno');
	}
}else{
	die();
}


