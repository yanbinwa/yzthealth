<?php
/**
 *    @desc 添加角色
 * */
 
Page::ignore_view() ;
$uid = Session::get(mainuid);			//用户ID
$db = DB::get_db();  
 
//del
if(Request::get(1)){
 	//删除账户
 	$uidol =Request::get(1);
 	$user =new Model('user');
 	$user->id =$uidol;
 	$user->remove();
 	//删除group_access 
 	$groupa =new Model('think_auth_group_access');
 	$group =$groupa->where(array('uid'=>$uidol))->find();
 	$groupa->id =$group->id;
	if($groupa->remove()){
		echo "<script >alert('删除成功!');history.go(-1)</script>";
	
 	}
}
 