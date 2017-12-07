<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}
//判断是否已经有wxid
/*if(Request::get('wid') == 6790553 || Request::get('wid') == 6765368){
	if(!Session::has('wxid')&&Session::has('wid')){//如果没有去授权获取
		$tourl = 'http://'.$_SERVER['HTTP_HOST']."/".$_SERVER['REQUEST_URI'];
		do_get_wxid2(Session::get('wid'),$tourl);
	}
}*/
$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
Session::set('wid',$wid);
if(!$wid){
	die('请从微信进入');
}
$pubs = new Model('pubs');
$pubs->find($wid);
$signPackage = GetWxSignPackage($wid);
$Wxapi = new Wxapi($pubs->cusid, $pubs->cussec);

if ($code = Request::get('code')) {
	$token = $Wxapi->get_access_token($code);
	$userinfo = $Wxapi->get_user_info($token['access_token'], $token['openid']);
	$openid = $userinfo['openid'];

	$fans = new Model('fans');
	$fans->find(array('wid' => $wid, 'wxid' => $openid));
	if (!$fans->id) {
		$fans->headimg = $userinfo['headimgurl'];
		$fans->nickname = $userinfo['nickname'];
		$fans->wxid = $userinfo['openid'];
		$fans->wid = $wid;
		$fans->save();
	}
	if($fans->id){
		Session::set('fansid',$fans->id);
		Session::set('wxid',$openid);
	}
}

$f = new Model('fans');
$f = $f->find(Session::get('fansid'));
if (!Session::get('fansid') ||$f->wid != $wid){
	//高级接口获取用户信息
	$Wxapi->get_authorize_url('http://www.weixinguanjia.cn/nhyk/index.html');
}else {
	$wxid = Request::get('wxid')?Request::get('wxid'):Session::get('wxid');
	Session::set('wxid',$wxid);
	if($wid && $wxid){
		$where = array('wid'=>$wid);
		if($wid == 6765368 || $wid == 6790553){
			$fans= new Model('fans');
			$fans->find(array('wid'=>$wid,'wxid'=>$wxid));
			Session::set('fansid',$fans->id);
		}

		$set  = new Model('micro_membercard_set');
		$set->find($where);
		$info = new Model('micro_membercard_info');
		$info->find($where);

		$paym  = new Model('ai9me_pay_set');
		$payrs = $paym->where(array('token'=>$wid))->map_array('id','pc_type');

		$optionstr = '';
		foreach ($payrs as $v)
		{

			if($v=='wxpay')
			{
				$optionstr = "$optionstr<option value='5'>微信支付</option>";
			}
			/* if($v=='tenpay')
             {
                $optionstr = "$optionstr<option value='4'>财付通</option>";
             }
             */
		}

		$m2 = new Model('micro_membercard_yhq_set_list');
		//优惠券
		$yhq = $m2->where(array('wid'=>$wid,'wxid'=>$wxid,'tty'=>1,'status'=>0))->count();
		//代金券
		$djq = $m2->where(array('wid'=>$wid,'wxid'=>$wxid,'tty'=>2,'status'=>0))->count();


		$m = new Model('micro_membercard_member_list');
		//error_log(print_r($wxid.'___'.$wid,1),3,__FILE__.'.log');
		$m->find(array('wxid'=>$wxid,'wid'=>$wid));
		if($m->has_id())
		{

			if($m->status==2)
			{
				die('您的账号已冻结，请联系商家');
			}
			else
			{
				gh($wid,$wxid);
				$mp = new Model('micro_membercard_privilege');
				$mprs = $mp->where(array('wid'=>$wid,'status'=>1))->list_all();
				foreach ($mprs as $key=>$v)
				{
					if($v->pstr!='')
					{
						$g = explode(',', $v->pstr);
						if($g[0]!=0 && !in_array($m->grade, $g))
						{
							unset($mprs[$key]);
						}
					}
				}

				$m2 = new Model('micro_membercard_notice');
				$rs = $m2->where(array('wid'=>$wid,'status'=>1))->order('id desc')->limit('2')->list_all();
				foreach ($rs as $key=>$v)
				{

					$grade = explode(',', $v->group);
					if($grade[0]!=0)
					{
						if(!in_array($m->grade, $grade))
						{
							unset($rs[$key]);
						}
					}
				}
				$m1 = new Model('micro_membercard_plugmenulist');
				$rs1 = $m1->where(array('wid'=>$wid))->order('queue desc')->list_all();


				$lbs = lbs($wid);
				$str ='';
				foreach ($lbs as $key=>$val)
				{
					$str = $str.'<option value="'.$key.'"> '.$val.'</option>';
				}

				$m2 = new Model('micro_membercard_yhq_set_list');
				$rs2 = $m2->where("wid='".$wid."' and wxid='".$wxid."' and (tty=1 or tty=2) and status=0")->list_all();

				$stryhq ='';
				$myhq = new Model('micro_membercard_yhq_list');
				foreach ($rs2 as $va)
				{
					$myhq->find($va->ttid);
					$stryhq = $stryhq.'<option value="'.$va->ttid.'"> '.$myhq->name.'</option>';
				}
				//关怀
				$ghm  = new Model('micro_membercard_gh_list');
				$rrgh = $ghm->where("wid='".$wid."' and status=1 and des_show=1 and des!='' and  (people=0 or people='".$m->grade."')")->list_all();
			}
		}
		else
		{
			Redirect::to('haveno');
		}
	}else{
		Redirect::to('haveno');
	}
}





