<?php
/**
 *    @desc 添加角色
 * */
$uid = Session::get(mainuid);			//用户ID
$db = DB::get_db();  
if(Request::get(1)){
	$ouid=Request::get(1);
}



//rest
if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
 
	if(!empty($_REQUEST['password']) && !empty($_REQUEST['affirm_password']) && ($_REQUEST['password']==$_REQUEST['affirm_password'])){
		$user =new Model('user');
		$user->id =$_REQUEST['id'];
		$user->pwd =$_REQUEST['password'];
		$user->save();
		tusi('保存成功');
		Redirect::delay_to('user.html',1);
		
	}
}
 