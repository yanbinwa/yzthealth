<?php
 $wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
if (!empty($wid) && !empty($wxid)) {
	$mid = Request::get(1);
	$member = new Model('member');
    $member->where(array('wxid'=>$wxid,'wid'=>$wid,'status'=>2,'id'=>$mid))->find();	
    if ($member->has_id()) {
    	$withdrawals = new Model('withdrawals_order');
	    //全部订单
	    $orders_arr = $withdrawals->where(array('mid'=>$member->id))->list_all_array();
	    //待审核
	    $Pending_payment = $withdrawals->where(array('mid'=>$member->id,'status'=>0))->list_all_array();
	    //已提现
	    $Pending_consumption = $withdrawals->where(array('mid'=>$member->id,'status'=>1))->list_all_array();

    }

}

