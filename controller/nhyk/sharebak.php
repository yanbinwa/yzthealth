<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}

if(Session::has('wxid') && Session::has('wid')){

	$jssdk = new JSSDK("wxa9b82278b4b75bfa", "a75af2d0b3576a3d2e494dbe8832ff92");
    $signPackage = $jssdk->GetSignPackage();

	
	$wid = Session::get('wid');
	$wxid= Session::get('wxid');
	$m = new Model('micro_membercard_member_list');
	$m->find(array('wid'=>$wid,'wxid'=>$wxid));

	$mm = new Model('micro_membercard_eidtmemberrecommend');
	$rs = $mm->where(array('wid'=>$wid))->order('queue desc')->list_all();
	$path = conf::$http_path;
	
	$mem_set = new Model('micro_membercard_set');
	$mem_set->find(array('wid'=>$wid));


	$edaygive = 0;
	
	if('s'==Request::get(1))
	{
		Page::ignore_view();
		$ms = new Model('micro_membercard_share_log');
		$ms->wid = $wid;
		$ms->wxid = $wxid;
		$ms->type = trim($_REQUEST['type']);
		$ms->err = trim($_REQUEST['sta']);
		$ms->create_time = date('Y-m-d H:i:s',time());
		$ms->save();

		$mset = new Model('micro_membercard_setscores');
		$mset->find(array('wid'=>$wid));
		$edaygive = $mset->edaygive;
		//tusi('恭喜您获得{$mset->edaygive}积分');
		$i = 0;
		if($ms->type=='send_msg') //转发
		{
			$i = $mset->zfgive;
			
			if($mset->zfgivetimes!='' &&  $mset->zfgivetimes!='0')
			{
				$cc = $ms->where("to_days(create_time)=to_days(now()) and wxid='".$wxid."' and wid='".$wid."' and type='send_msg' ")->count();
				if($mset->zfgivetimes>=$cc)
				{
					changeInte($m->id,$mset->zfgive,0,'',9);
				} //当天
			}
			else
			changeInte($m->id,$mset->zfgive,0,'',9);
				
		}
		elseif($ms->type=='timeline') //分享
		{
			$i = $mset->fxgive;
			
			if($mset->fxgivetimes!='' &&  $mset->fxgivetimes!='0')
			{
				$cc = $ms->where("to_days(create_time)=to_days(now()) and wxid='".$wxid."' and wid='".$wid."' and type!='send_msg' ")->count();
				if($mset->fxgivetimes>=$cc)
				{
					changeInte($m->id,$mset->fxgive,0,'',7);
				} //当天
			}
			else
			changeInte($m->id,$mset->fxgive,0,'',7);
		}
		else  //微博
		{
			$i = $mset->fxgive;
			
			if($mset->fxgivetimes!='' &&  $mset->fxgivetimes!='0')
			{
				$cc = $ms->where("to_days(create_time)=to_days(now()) and wxid='".$wxid."' and wid='".$wid."' and type!='send_msg' ")->count();
				if($mset->fxgivetimes>=$cc)
				{
					changeInte($m->id,$mset->fxgive,0,'',7);
				} //当天
			}
			else
			changeInte($m->id,$mset->fxgive,0,'',7);
		}
		
		echo "恭喜您获得".$i."积分";
		
	}
}else{
	die();
}

