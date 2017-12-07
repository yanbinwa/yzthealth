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
	$momey = trim($_GET['m']);
	$m = new Model('micro_membercard_member_list');
	$m->find(array('wxid'=>$wxid));

	if($_POST)
	{
		$money     = trim($_POST['money']);
		$paytype   = intval($_POST['paytype']);
		if($money!=0)
		{
			//写充值记录
			$mr = new Model('micro_membercard_recharge_log');
			$mr->wid = $wid;
			$mr->wxid = $wxid;
			$mr->price = $money;
			$mr->uname = $m->name;
			$mr->cardno = $m->cardno;
			$mr->phone = $m->phone;
			$mr->create_time = date('Y-m-d H:i:s',time());
			$mr->type = $paytype;
			$mr->status = 1;
			$mr->trade_no = order_no();
			if($mr->save())
			{
				if($paytype==4)  //4.财付通
				{
					$payset = new Model('ai9me_pay_set');
					$payset->find(array('token'=>$wid,'pc_enabled'=>1,'pc_type'=>'tenpay'));
						
					die('暂不支持财付通支付，敬请期待');


				}
				if($paytype==5) //微信支付
				{
					$payset = new Model('ai9me_pay_set');
					$payset->find(array('token'=>$wid,'pc_enabled'=>1,'pc_type'=>'wxpay'));
						
					if($payset->paySignKey=='')
					Redirect::to('http://www.weixinguanjia.'.$payset->payml.'/wxpay/nhyktopayv33-'.$mr->id.'-'.$payset->id.'.html?showwxpaytitle=1&wid='.$wid.'&wxid='.$wxid);
					else
					Redirect::to('http://www.weixinguanjia.'.$payset->payml.'/wxpay/nhyktopay-'.$mr->id.'-'.$payset->id.'.html?showwxpaytitle=1&wid='.$wid.'&wxid='.$wxid);
				}
			}
		}
		else
		{
			die('请输入正确的充值金额');
		}
		die;
	}
}else{
	die();
}

