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


	$sta = Request::get(1);
	$sta = 0;
	if('n'==Request::get(1))
	$sta = 0;
	if('y'==Request::get(1))
	$sta = 1;

	$id = intval(Request::get(2));
	if($id!=0)
	{
		$ml = new Model('micro_membercard_member_list');
		$ml->find(array('wxid'=>$wxid));

		$m = new Model('micro_membercard_yhq_set_list');
		$m->find($id);

		$mm = new Model('micro_membercard_yhq_list');
		$mm->find($m->ttid);
		$sarr = explode(',', $mm->store);
		$lbsarr = lbs($wid);

		if($sarr[0]!=0)
		{
			$nlbs = array();
			foreach ($sarr as $v)
			{
				$nlbs[$v] = $lbsarr[$v];
			}
			$lbsarr = $nlbs;
		}
	}
}else{
	die();
}

