<?php
/**
 *   @desc 用户密码的修改
 * */
$user = new Model('lgc_supplier_user');
if($_POST){
	
	$user->find(Session::get('unid'));
	
	$opwd    = Request::post('opwd');
	$pwd    = Request::post('pwd');
	$pwd1  = Request::post('pwd1');
	
	if($opwd != $user->passwords){
		Response::write('原密码错误');
	}
   if($pwd != $pwd1){
   	    Response::write(0);
   }
   if($pwd =='' ||$pwd1 ==''){
     	Response::write(2);
   }
  
   $user->passwords = $pwd;
 
   $user->save();
   Response::write('修改完成');
}

