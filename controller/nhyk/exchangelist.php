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
	$mm = new Model('micro_membercard_member_list');
	$mem = new Model('micro_membercard_yhq_list');
	$mm->find(array('wid'=>$wid,'wxid'=>$wxid));

	$m = new Model('micro_membercard_exchange');
	$where = array('wid'=>$wid,'status'=>1);

	if('lp'==Request::get(1))
	$where['type'] = 1;
	if('cj'==Request::get(1))
	$where['type'] = 2;

	$rs = $m->where($where)->order('sort desc')->list_all();
    if(count($rs)>0)
	{
	
	   	foreach ($rs as $key=>$v)
		{
	
			$g = explode(',', $v->peopel);
			if ($g[0]!=0)
			{
				if(!in_array($mm->grade, $g))
				{
					unset($rs[$key]);
				}
			}
			$time = explode('-', $v->time);
			$v->time = date('Y-m-d',strtotime($time[1]));
			
				
			if($v->type==1) //yhq
			{
				$mem->find($v->ttid);
				$v->pic = $mem->pic;
				$v->des = $mem->des;
			}
			else //hd
			{
			
				if($v->tty==4)//刮刮卡
				{
					$mhd = new Model('ggk');
				}
				if($v->tty==5)//大转盘
				{
					$mhd = new Model('xydzp');
				}
				if($v->tty)
				{
				    $mhd->find($v->ttid);
					$v->pic = $mhd->pic;
					$v->des = $mhd->ms;
				}

				
			}
		}

	}


	//兑换
	if('exc'==Request::get(1))
	{
		$m->find(Request::get(2));
		if($m->wid==$wid)
		{
			$yhqme = new Model('micro_membercard_yhq_set_list');
			//验证发送数量总限制
			$mem->find($m->ttid);
			
			$allsend = $yhqme->where(array('ttid'=>$m->ttid,'wid'=>$wid))->count();
			if($mem->sendtimes==0 || $mem->sendtimes > $allsend)
			{
				$g = explode(',', $m->peopel);
				if ($g[0]!=0)
				{
					if(!in_array($mm->grade, $g))
					{
						$status =3;
						$msg    = '参数传递错误';
					}
				}
				if(empty($status))
				{
					if($mm->integral>=$m->score)
					{
						$yhqcc = $yhqme->where(array('ttid'=>$m->ttid,'wxid'=>$wxid))->count();
						if($yhqcc<$m->times)
						{
							$errno = changeInte($mm->id,-$m->score,0,'',5);
							if($errno==0)
							{
								//兑换
								$yhqme->hdid  = $m->ttid;
								$yhqme->ttid  = $m->ttid;
								$yhqme->tty   = $m->tty;
								$yhqme->wid   = $wid;
								$yhqme->wxid  = $wxid;
								$yhqme->sn = rand6();
								$yhqme->snstetime   = date('Y-m-d H:i:s',time());
								$yhqme->create_time = date('Y-m-d H:i:s',time());
								$yhqme->type = 3;
								$yhqme->hdid = $m->id;
								$yhqme->save();

								$status =1;
								$msg    = '兑换成功';
							}
							else
							{
								$status =6;
								$msg    = '兑换失败';
							}
						}
						else
						{
							$status =5;
							$msg    = '您已经兑换过了';
						}
					}
					else
					{
						$status =4;
						$msg    = '对不起您的积分不足，您当前剩余积分'.$mm->integral;
					}
				}
			}
			else
			{
				$status =2;
				$msg    = '来晚了，没有啦~';
			}
		}
		else
		{
			$status =2;
			$msg    = '参数传递错误';
		}

		echo json_encode(array('status'=>$status,'msg'=>$msg));
		die;
	}
}else{
	die('dd');
}

