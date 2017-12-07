<?php
/**
 *    @desc角色
 * */
$uid = Session::get(mainuid);			//用户ID
$group = new Model('think_auth_group');


//delete
if('del'==Request::get(1)){
	$gp =$group->find(Request::get(2));
	$group->id= Request::get(2);
	$group->remove();
	$rule = new Model('think_auth_rule');
	$rule->id =$gp->rules;
	if($rule->remove()){
		tusi('删除成功');
	}
	else
	tusi('操作失败');
}


$result =$group->where(array('uid'=>$uid))->list_all();

