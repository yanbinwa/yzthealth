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
	$m = new Model('micro_membercard_send_code_log');

	if($m->try_post())
	{
		//验证手机号唯一
		$ml = new Model('micro_membercard_member_list');
		if('s'==Request::get(1))  //绑定实体卡
		{
			$ml->where(" phone = '".trim($_POST['tel'])."' and cno_set=4 and wxid is null ")->list_all();
			$mlc = $ml->count();
			if($mlc>0)
			{
				//验证每日发送次数
				$sql = "select count(1) as cc from micro_membercard_send_code_log where to_days(create_time)=to_days(now()) and wid='".$wid."' and  tel='".trim($_POST['tel'])."'";
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
					}
					else
					{
						//if(!empty($user->sms_signature))
						//{
						//	$sms_signature ='【'.$user->sms_signature.'】';
						//}
						//else
						//{
						$sms_signature = '官网：weixinguanjia.com';
						//}

						$sms_send_params = array(
						'mobile'=>trim($_POST['tel']),
						'contents'=>'【微信管家】您申请（绑定实体卡会员卡）验证码是'.$r6.'，请在30分钟内输入'.$sms_signature,
						'uid'=>$pubs ->uid,
						'wid'=>$wid,
						'wxid'=>$wxid,
						'sendfrom'=>'nhyk',
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
						}
						else
						{
							$return = array(
							  'errno' =>'3',
							  'error' =>'发送失败'
							  );
						}
					}
				}
				else
				{
					$return = array(
					  'errno' =>'1',
					  'error' =>'每个手机号每天只能发送20次验证码'
					  );
				}
			}
			else
			{
				$return = array(
				  'errno' =>'2',
				  'error' =>'手机号不存在或商家未导入实体卡会员信息'
				  );
			}
		}
		else
		{
			$ml->find(array('wid'=>$wid,'phone'=>trim($_POST['tel'])));
			if($ml->has_id() && $ml->wxid!=$wxid)
			{
				$return = array(
				  'errno' =>'2',
				  'error' =>'手机号已被占用'
				  );
			}
			else
			{
				//验证每日发送次数
				$sql = "select count(1) as cc from micro_membercard_send_code_log where to_days(create_time)=to_days(now()) and wid='".$wid."' and tel='".trim($_POST['tel'])."'";
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
					}
					else
					{
						/**if(!empty($user->sms_signature))
						{
							$sms_signature ='【'.$user->sms_signature.'】';
						}
						else
						{
							$sms_signature = '官网：weixinguanjia.com【微信管家】';
						}
						*/
						
						$sms_signature = '官网：weixinguanjia.com';

						if('bs'==Request::get(1))
						{
							$s = '绑定手机';
						}
						elseif('bo'==Request::get(1))
						{
							$s = '解绑手机';
						}
						elseif('bn'==Request::get(1))
						{
							$s = '绑定新手机';
						}
						else
						{
							$s = '订单付款';
						}
							
						$sms_send_params = array(
						'mobile'=>trim($_POST['tel']),
						'contents'=>'【微信管家】您申请（'.$s.'会员卡）验证码是'.$r6.'，请在30分钟内输入'.$sms_signature,
						'uid'=>$pubs->uid,
						'wid'=>$wid,
						'wxid'=>$wxid,
						'sendfrom'=>'nhyk',
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
						}
						else
						{
							$return = array(
							  'errno' =>'3',
							  'error' =>'发送失败'
							  );
						}
					}
				}
				else
				{
					$return = array(
					  'errno' =>'1',
					  'error' =>'每个手机号每天只能发送20次验证码'
					  );
				}
			}
		}
		echo json_encode($return);
	}
	die;
}else{
	die();
}

