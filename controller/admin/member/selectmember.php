<?php
access_control();
$mid = Request::get(1);
$m = new Model('member');
$m->find(array('id'=>$mid));
$brokerage_sum = 0;
$withdrawals_sum = 0;
//累计佣金
$brokerage_record = new Model('brokerage_record');
$brokerage_arr = $brokerage_record->where(array('member_id'=>$mid))->list_all_array();
foreach ($brokerage_arr as $key => $value) {
	$brokerage_sum+=$value['brokerage'];
}
//累计提现
$withdrawals = new Model('withdrawals_order');
$withdrawals_arr = $withdrawals->where(array('mid'=>$mid,'status'=>1))->list_all_array();
foreach ($withdrawals_arr as $key => $value) {
	$withdrawals_sum+=$value['total'];
}
//下级分销人数
$ren_count = $m->where(array('status'=>2,'fx_wxid'=>$m->wxid))->count();