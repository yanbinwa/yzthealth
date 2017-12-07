<?php
$uid = Session::get('uid');
$wid = Session::get('wid');

$m = new Model('fx_txsq_list');

if(Request::get(1)){
	$m->find(Request::get(1));
	if($m->wid != $wid){
		die();
	}
	$bankm = new Model('fx_user_bank');
	$bankm->find(array('wd_id'=>$m->wd_id));

	$wm = new Model('fx_wd_list');
	$wm->find($m->wd_id);
    if($wm->pid!=0)
    {
      $pwm = new Model('fx_wd_list');
      $pwm->find($m->pid);
      $wm->pwdname = $pwm->name;
    }
	
	if($m->shr=='')
	{
		$uns = Session::get('un');
		if(empty($uns)){
			$uns = 'admin';
		}
		$m->shr = $uns;
	}

}

$type = array(
   '-1'=>'请选择提现方式',
	'1111'=>'支付宝',
	'1112'=>'财付通',
	'1113'=>'中国工商银行',
	'1114'=>'中国建设银行',
	'1115'=>'中国银行',
	'1116'=>'中国农业银行',
	'1117'=>'交通银行',
	'1118'=>'招商银行',
	'1119'=>'民生银行',
	'1120'=>'平安银行'
);

$status = array('1'=>'提现成功','2'=>'提现失败');


if($m->try_post()){
	//$m->wid = $wid;
	if($m->sh_tm=='')
	$m->sh_tm = date('Y-m-d H:i:s',time());
	
	if($m->status==2)
	{
	   $wdm = new Model('fx_wd_list');
	   $wdm->find($m->wd_id);
	   
	   $wdm->flje = $wdm->flje + $m->amount;
	   $wdm->save();
	}
	
	$m->save();
	
	
	Redirect::to('txlist');
}
