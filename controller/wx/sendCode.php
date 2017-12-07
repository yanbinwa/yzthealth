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
	$m = new Model('member_send_code_log');

	if($m->try_post())
	{
		$apply = new Model('apply_cooperation');
		$apply->find(array('wid'=>$wid,'telephone'=>trim($_POST['tel'])));
		if($apply->has_id() && $apply->wxid!=$wxid)
		{
			$return = array(
			  'errno' =>'2',
			  'error' =>'手机号已被占用'
			);
		}else{
			//验证每日发送次数
			$sql = "select count(1) as cc from member_send_code_log where to_days(create_time)=to_days(now()) and wid='".$wid."' and tel='".trim($_POST['tel'])."'";
			$db = DB::get_db();
			$rs = $db->query($sql);
			if($rs[0]['cc']<20)
			{
				$r6 = rand6();
				$now = date('Y-m-d H:i:s',time());

				$pubs = new Model('pubs');
				$pubs -> find($wid);

				$user = new Model('user');
				$user->find($pubs->uid);
				if($user->surplus_sms<1)
				{
					$return = array(
					  'errno' =>'4',
					  'error' =>'发送失败'
				    );
				}else{	
					$sms_send_params = array(
						'mobile'=>trim($_POST['tel']),
						'contents'=>'您的验证码是'.$r6.'（30分钟内有效），请不要将验证码泄漏给他人。',
						'uid'=>$pubs->uid,
						'wid'=>$wid,
						'wxid'=>$wxid,
						'sendfrom'=>'user',
						'additional'=>''
					);
					if(send_sms($sms_send_params))
					{
						$m->wid = $wid;
						$m->create_time = $now;
						$m->tel = trim($_POST['tel']);
						$m->code = $r6;
						$m->save();
						$return = array(
						  'errno' =>'0',
						  'error' =>'发送成功'
						);
					}else{
						$return = array(
						  'errno' =>'3',
						  'error' =>'发送失败'
						);
					}
				}
			}else{
				$return = array(
				  'errno' =>'1',
				  'error' =>'每个手机号每天只能发送20次验证码'
				  );
			}
		}

		echo json_encode($return);
	}
	die;
}else{
	die();
}

