<?php

$set = new Model('ai9me_pay_set');
$set->find(array('id'=>1));

if($set->try_post()){
	$set->save();
	tusi('保存成功');

}