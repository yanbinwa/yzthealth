<?php
/**
 *    @desc 我的账户信息
 * */
$uid = Session::get(mainuid);			//用户ID
$wid = Session::get('wid');

$role =new Model('think_auth_group');



//delete
if('del'==Request::get(1)){
	//删除账户
 	$uidol =Request::get(2);
 	$user =new Model('user');
 	$user->id =$uidol;
 	$user->remove();
 	//删除group_access 
 	$groupa =new Model('think_auth_group_access');
 	$group =$groupa->where(array('uid'=>$uidol))->find();
 	$groupa->id =$group->id;
	if($groupa->remove()){
		tusi('操作成功');
 	}
	else
	tusi('操作失败');
}



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