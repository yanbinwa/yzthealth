<?php 
Conf::$session_prefix = 'sdzzbabcd';

$mid = Request::get(1);

$member = new Model('member');
$member->find(array('id'=>$mid));

$bank_arr = array('1'=>'中国工商银行','2'=>'中国农业银行','3'=>'中国建设银行','4'=>'中国银行');
if($member->try_post()){
	$member->save();
	tusi("保存成功");
}






