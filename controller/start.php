<?php
Conf::$session_prefix = 'sdzzbabcd';
if(!Session::get('uid')){
	Session::clear();
	Cookie::clear();
	Redirect::index();
}
$pub = new Model('pubs');
$pub->find(array('uid'=>Session::get('uid')));
//并且当前的会员不是子管理员
$user =new Model('user');
$users =$user->find(array('id'=>Session::get('uid')));



if(!$pub->has_id()){
	$pub->uid = Session::get('uid');
	$pub->isval = '0';
	if(empty($users->userpid)){
		$pub->save();
	}
	Session::set('wid',$pub->id);
	init_data($pub->id,$pub->uid);
	
	if(empty($users->userpid)){
		Redirect::to('/admin/main');
	}else{
		Redirect::to('/admin/main');
	}
}else if($pub->isval !='1'){
	Session::set('wid',$pub->id);
	
	Redirect::to('/admin/main');
}else{	
	Session::set('wid',$pub->id);
	
	Redirect::to('/admin/main');
}


function init_data($wid,$uid){

	$begin_time = date('Y-m-d H:i:s',time());
	$end_time   = date('Y-m-d H:i:s',strtotime("+2 year"));
	$sl = 10000;
}

function init_data1($wid,$uid){

	$begin_time = date('Y-m-d H:i:s',time());
	$end_time   = date('Y-m-d H:i:s',time()+604800*4);
	$db= DB::get_db();

	


}
