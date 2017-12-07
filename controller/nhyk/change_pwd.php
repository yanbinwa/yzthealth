<?php
if (Request::get ( 'wxid' )) {
	Session::set ( 'wxid', Request::get ( 'wxid' ) );
}
if (Request::get ( 'wid' )) {
	Session::set ( 'wid', Request::get ( 'wid' ) );
}

if (Session::has ( 'wxid' ) && Session::has ( 'wid' )) {
	$wid = Session::get ( 'wid' );
	$wxid = Session::get ( 'wxid' );
	
	$m = new Model ( 'micro_membercard_member_list' );
	$m->find ( array ('wid' => $wid, 'wxid' => $wxid ) );
	if ($m->try_post ()) {
		$old = Request::post ( 'oldpassword' );
		$pwd = Request::post ( 'password' );
		$re_pwd = Request::post ( 'repassword' );
		if ($pwd != $re_pwd) {
			tusi ( '两次的密码不一致' );
		}else{
		if ($m->yu_e_pay != "" && $old != $m->yu_e_pay) {
			tusi ( '密码不正确' );
		} else {
			$m->yu_e_pay = $pwd;
			$re = $m->save ();
			if ($re) {
				tusi ( '修改成功，修改为:'.$m->yu_e_pay );
				Redirect::delay_to('index',2);
			} else {
				tusi ( '保存失败' );
			}
		}
		}
		
		
	}

} else {
	die ();
}
