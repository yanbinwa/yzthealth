<?php
/**
 *    @desc 我的账户信息
 * */
$uid = Session::get(mainuid);			//用户ID
$wid = Session::get('wid');

$role =new Model('think_auth_group');
$roles=$role->where(array('uid'=>$uid))->list_all();


$db = DB::get_db(); 
$where =" userpid={$uid}";
if(isset($_REQUEST['sbutton'])){
	if(!empty($_REQUEST['role_id'])){
		$where.=" and ".$_REQUEST['role_id'];
	}
	if(!empty($_REQUEST['search_key'])){
		$where.=" and un=".$_REQUEST['search_key'];
	}

}
$users = $db->query("select * from user where ".$where);  
foreach($users as $key=>$value){
	$users[$key]['rolename']=getRole($value['id']);
}


function getRole($uid){
	$group = DB::get_db();  
	
	$role =new Model('think_auth_group_access');
	$roles=$role->where(array('uid'=>$uid))->find();
	$roles1 ='(';
	$roles1 .=rtrim($roles->group_id,',');
	$roles1 .=')';
	$roles1 = $group->query("select * from think_auth_group where id in".$roles1);  
	$rolesname='';
	foreach($roles1 as $r){
		$rolename.=$r['title'].',';
	}
	$rolename =rtrim($rolename,',');
	return $rolename;
}