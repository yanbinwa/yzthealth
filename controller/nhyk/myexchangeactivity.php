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

	$sta =0;

	$m = new Model('micro_membercard_yhq_set_list');
	if('y'==Request::get(1))
	{
		$sta = 1;
		$rs = $m->where(" wxid='".$wxid."' and (tty = 4 or tty = 5) and status != 0")->list_all();
	}
	else
	{
		$sta = 0;
		$rs = $m->where(" wxid='".$wxid."' and (tty = 4 or tty = 5) and status = 0")->list_all();
	}

	if(!empty($rs))
	{
		$ggk = new Model('ggk');
		$xydzp = new Model('xydzp');
		$me = new Model('micro_membercard_exchange');
		foreach ($rs as $v)
		{
			if($v->tty==4) //刮刮卡
			{
				$p = $ggk->find($v->ttid);
				$url = "/wx/ggk-".$v->ttid.".html?&wid=$wid&wxid=$wxid";
			}
			if($v->tty==5) //大转盘
			{
				$p = $xydzp->find($v->ttid);
				$url = "/wx/xydzp-".$v->ttid.".html?&wid=$wid&wxid=$wxid";
			}
			$me ->find($v->hdid);
			$v->title = $me->title;
			$bttime = explode('-', $me->time);
			$v->time = date('Y-m-d',strtotime($bttime[1]));
			$v->pic = $p->pic;
				
			$v->url = $url;
		}
	}


}else{
	die();
}

