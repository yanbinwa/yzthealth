<?php
/**
 *    @desc 添加账户
 * */
$uid = Session::get(mainuid);			//用户ID
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
	$user->un=$_REQUEST['identity'].'@'.$uid;
	$oluser =$_REQUEST['oluser'];
	if(empty($oluser)){
		$user->pwd=$_REQUEST['password'];
		$user->name=$_REQUEST['name'];
	}else{
		$user->id =$oluser;
	}
	$user->userpid =$uid;
	$uid =$user->save();
	
	
	$group = new Model('think_auth_group_access');
	
	$value ='';
	foreach($_POST['role_id'] as $v){
		$value.=$v.',';
	}
	if(empty($oluser)){
		$group->uid=$uid->id;
	}else{
		$groupol =$group->where(array('uid'=>$oluser))->find();
		$group->id=$groupol->id;
	}
	$group->group_id=$value;
	
	if($group->save()){
		echo "<script>alert('提交成功');history.go(-1)</script>";
	}else{
		echo "<script>alert('提交失败');history.go(-1)</script>";
	}
  
}
// 
 