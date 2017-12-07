<?php
//提现类型
$fx_user = Session::get('news');
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
$m = new Model('fx_txsq_list');
$bank = new Model('fx_user_bank');
if(1==Request::get(1))
{
	$id = Request::post('id');
	$status = 1;
	$m->update($condition=array('id'=>$id),$data=array('status'=>$status));
	exit;
}
elseif(2==Request::get(1))
{
	$id = Request::post('id');
	$status = 2;
	$m->update($condition=array('id'=>$id),$data=array('status'=>$status));
	exit;
}



if('del'==Request::get(1)){
	$id = Request::post('id');
	$status = 127;
	$m->update($condition=array('id'=>$id),$data=array('status'=>$status));
	exit;
}
else
{
	$p = new Pagination();
	$where['wid'] = Session::get('wid');
	$res = $p->model_list($m->where($where)->order("id desc"));
	
	foreach($res as $v){
		$bank->find(array('wd_id'=>$v->wd_id));
		$v->typ_name = $type[$bank->type]; 
		$v->tx_name = $bank->uname; 
		$v->user_name = $fx_user->name;
	}
}

//待处理
$wcount = $m->where(array('status'=>0,'wid'=>Session::get('wid')))->count();

