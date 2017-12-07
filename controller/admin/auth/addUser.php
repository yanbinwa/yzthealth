<?php
/**
 *    @desc 添加账户
 * */
$uid = Session::get('mainuid');			//用户ID
$wid = Session::get('wid');
$user =new Model('user');
$onuser =$user ->where(array('id'=>$uid))->find();
$role =new Model('think_auth_group');
$roles =$role ->where(array('uid'=>$uid))->list_all();

//编辑
if(Request::get(1)){
	
	$ouid =Request::get(1);
	
	
	$groupa = new Model('think_auth_group_access');
	$groups =$groupa->find(array('uid'=>$ouid));
	$group_id =explode(',',rtrim($groups->group_id,','));

	$user = new Model('user');
	$users =$user->find(array('id'=>$ouid));
	$subuser =substr($users->un,0,strpos($users->un,'@'));
	
} 



//处理提交 
if(isset($_POST['tijiao'])){

 	$user = new Model('user');
 	$user2 = new Model('user');
	$user_online =$user->find($uid);
	
	$user2->un=$_REQUEST['identity'].'@axg';
	$oluser =$_REQUEST['oluser'];
	if(empty($oluser)){
		$user2->pwd=$_REQUEST['password'];
		$user2->name=$_REQUEST['name'];
		$user2->level_id =$user_online->level_id;
		$user2->uprice =$user_online->uprice;

		$user2->mendtime =$user_online->mendtime;
		$user2->next_mendtime =$user_online->next_mendtime;
		$user2->rtime =$user_online->rtime;
		$user2->kfid =$user_online->kfid;
		$user2->agid =$user_online->agid;
		$user2->isdirect =$user_online->isdirect;
		
	}else{
	
	    $user2->name=$_REQUEST['name'];
		$user2->id =$oluser;
	}

	$user2->userpid =$uid;
	$user2->save();
	
	
	
	$group = new Model('think_auth_group_access');
	
	$value ='';
	foreach($_POST['role_id'] as $v){
		$value.=$v.',';
	}
	if(empty($oluser)){
		$group->uid=$user2->id;
	}else{
		$groupol =$group->where(array('uid'=>$oluser))->find();
		$group->id=$groupol->id;
	}
	$group->group_id=$value;
	$group->save();
	tusi('提交成功');
	Redirect::to('user');
	
	
  
}
// 
 