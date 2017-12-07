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
	$k = Request::get(1);
	$m = new Model('micro_membercard_inte_log');
	$where = "wid='".$wid."' and wxid='".$wxid."' and score!=0";
    if($k==2)
    {
       $where = $where." and type in(1,3,4,6,7,8)";
    }
    if ($k==3)
    {
       $where = $where." and type in(2,5)";
    }
    $rs = $m->where($where)->order('create_time desc')->list_all();
    $rsc = $m->count();
    $ta = array('1'=>'手动增加积分','2'=>'手动扣除积分','3'=>'活动赠送积分','4'=>'生日赠送积分','5'=>'兑换消费积分','6'=>'消费赠送积分','7'=>'分享奖励积分','8'=>'签到奖励积分','9'=>'转发奖励积分','10'=>'充值赠送积分'); 
}