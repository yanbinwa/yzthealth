<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}

if(Session::has('wxid') && Session::has('wid')){
	$wid = Session::get('wid');
	$wxid= Session::get('wxid');

	$m = new Model('micro_membercard_member_list');
	$m->find(array('wid'=>$wid,'wxid'=>$wxid));

	$m1 = new Model('micro_membercard_member_info');
	$m1->find(array('wid'=>$wid,'wxid'=>$wxid));

	$m2 = new Model('micro_membercard_option_set');
	$rs = $m2->where(array('wid'=>$wid,'option_name@<>'=>''))->list_all();
	foreach ($rs as $key=>$v)
	{
		/*if($key<5)
		 {
			unset($rs[$key]);
			}
			else
			{*/
		if($v->input_type==3)
		{
			$v->option_value = explode('|', $v->option_value);
		}
		//}
	}

	$code = trim($_GET['validCode']);
	$phone = trim($_GET['telephone']);
	if(!empty($code) && !empty($phone))
	{
		$validCode = validCode($wid,$phone,$code);
		if($validCode['errno']==200)
		{
			//绑定
			$u = $m->update($condition=array('wid'=>$wid,'wxid'=>$wxid),$data=array('ver'=>1,'phone'=>$phone));
			if($u)
			{
				$validCode = array('errno'=>0,'error'=>'绑定手机号码成功');
			}
		}
		echo json_encode($validCode);
		die;
	}

	$old_telephone = trim($_GET['old_telephone']);
	$old_telephone_code = trim($_GET['old_telephone_code']);
	$new_telephone = trim($_GET['new_telephone']);
	$new_telephone_code = trim($_GET['new_telephone_code']);

	if(!empty($old_telephone) && !empty($old_telephone_code) && !empty($new_telephone) && !empty($new_telephone_code))
	{
		$old = validCode($wid,$old_telephone,$old_telephone_code);
		$new = validCode($wid,$new_telephone,$new_telephone_code);
		if($old['errno']!='200')
		{
			$return = array('errno'=>1,'error'=>'原手机验证不通过');
		}
		if($new['errno']!='200')
		{
			$return = array('errno'=>2,'error'=>'新手机验证不通过');
		}
		if(empty($return))
		{
			//绑定
			$u = $m->update($condition=array('wid'=>$wid,'wxid'=>$wxid),$data=array('phone'=>$new_telephone));
			if($u)
			{
				$return = array('errno'=>0,'error'=>'更新手机号码绑定成功');
			}
		}
		echo json_encode($return);
		die;
	}



	if($m1->try_post()){
		//$rs
		if($rs[0]->is_edit==1)
		{
			$username  = trim($_POST['username']);
			if($m->name != $username)
			{
				$u = $m->update($condition=array('wid'=>$wid,'wxid'=>$wxid),$data=array('name'=>$username));
			}
		}
		if($rs[1]->is_edit==1)
		{
			$telephone = trim($_POST['telephone']);
			if($m->phone != $telephone)
			{
				$u = $m->update($condition=array('wid'=>$wid,'wxid'=>$wxid),$data=array('phone'=>$telephone));
			}
		}



		$m1->wid = $wid;
		$m1->wxid= $wxid;
		$m1->b_y = trim($_POST['birth_year']);
		$m1->b_m = trim($_POST['birth_month']);
		$m1->b_d = trim($_POST['birth_date']);
		$m1->s_p = trim($_POST['addr_prov']);
		$m1->s_s = trim($_POST['addr_city']);
		$m1->s_x = trim($_POST['addr_area']);
		$m1->create_time = date('Y-m-d H:i:s',time());
		$m1->save();

		Redirect::to('index');
	}




}else{
	die();
}

