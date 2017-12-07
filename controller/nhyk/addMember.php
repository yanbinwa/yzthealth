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

	$info = new Model('micro_membercard_info');
	$info->find(array('wid'=>$wid));

	$min = new Model('micro_membercard_member_info');
	$m2 = new Model('micro_membercard_option_set');
	$optionrs = $m2->where(array('wid'=>$wid,'option_name@<>'=>''))->list_all();
	foreach ($optionrs as $key=>$voption)
	{
		if($voption->input_type==3)
		{
			$voption->option_value = explode('|', $voption->option_value);
		}
	}



	$m = new Model('micro_membercard_member_list');
	$m->find(array('wxid'=>$wxid));
	//判断是否已提交
	if (!empty($m->id)) {
		Redirect::to('index');
	}
	
	if($m->try_post())
	{
		if('bd'==Request::get(1))  //绑定实体卡
		{
			$phone     = trim($_POST['entry_telephone']);
			$validCode = intval($_POST['entry_checkcode']);

			$mc = new Model('micro_membercard_send_code_log');
			$rmc = $mc ->where(array('wid'=>$wid,'tel'=>$phone))->order('id desc')->limit(1)->list_all();
			$lastsendtime = strtotime($rmc[0]->create_time);
			$sttime = time()-$lastsendtime;
			if($sttime>1800)
			{
				$errno=5;
				$error='验证码已过期';
			}
			if($rmc[0]->code!=$validCode)
			{
				$errno=1;
				$error='验证码不正确';
			}
			if(empty($error))
			{
				$u = $m->update($condition=array('wid'=>$wid,'phone'=>$phone),$data=array('ver'=>1,'wxid'=>$wxid));
				if($u)
				{
					$errno=0;
					$error='绑定成功';
				}
				else
				{
					$errno=2;
					$error='绑定失败';
				}
			}
		}
		else
		{
			$phone     = $m->phone;
			if($info->msg_check==0)
			{
				$validCode = trim($_POST['validCode']);
				$mc = new Model('micro_membercard_send_code_log');
				$rmc = $mc ->where(array('wid'=>$wid,'tel'=>$phone))->order('id desc')->limit(1)->list_all();
				$lastsendtime = strtotime($rmc[0]->create_time);
				$sttime = time()-$lastsendtime;
				if($sttime>1800)
				{
					$errno=5;
					$error='验证码已过期';
				}
				if($rmc[0]->code!=$validCode)
				{
					$errno=1;
					$error='验证码不正确';
				}
			}


			$m->find(array('wid'=>$wid,'phone'=>$phone));
			if($m->has_id())
			{
				$errno=2;
				$error='手机号已被占用';
			}
			if(empty($error))
			{
				if($info->msg_check==0) $m->ver = 1;
					
				$temp = new Model('micro_membercard_cardno_temp');
				//卡号规则
				if($info->cno_set==1)//手机号
				{
					$m->cardno = $phone;
					$m->cno_set = 1;
				}
				elseif($info->cno_set==2)//自定义
				{
					$rm = $m->where(array('wid'=>$wid,'cno_set'=>2))->order('cno_my_m desc')->limit(1)->list_all();
					$r = $temp->where(array('wid'=>$wid,'type'=>2))->count();
					if($r==0)
					{
						$cno_my_m = $rm[0]->cno_my_m==''?10000:$rm[0]->cno_my_m+1;
						$temp->no = $cno_my_m;
						$temp->tp = 2;
						$temp->save();
						if($temp)
						{
							$m->cno_my_m = $cno_my_m;
							$m->cardno = $info->cno_my_s.$cno_my_m.$info->cno_my_e;
							$m->cno_set = 2;
						}
						else
						{
							$errno=4;
							$error='系统繁忙请稍后再试';
						}
					}
					else
					{
						$errno=3;
						$error='系统繁忙请稍后再试';
					}
				}
				else //默认卡号 1000000
				{
					$rm = $m->where(array('wid'=>$wid,'cno_set'=>0))->order('cardno desc')->limit(1)->list_all();
					$r = $temp->where(array('wid'=>$wid,'type'=>0))->count();
					if($r==0)
					{
						$cardno = $rm[0]->cardno==''?1000000:$rm[0]->cardno+1;
						$temp->no = $cno_my_m;
						$temp->tp = 0;
						$temp->save();
						if($temp)
						{
							$m->cardno = $cardno;
							$m->cno_set = 0;
						}
						else
						{
							$errno=4;
							$error='系统繁忙请稍后再试';
						}
					}
					else
					{
						$errno=3;
						$error='系统繁忙请稍后再试';
					}
				}
				if($errno=='')
				{
					$m->wxid = $wxid;
					$m->wid = $wid;

					$m->wxh = trim($_POST['micro_membercard_member_infoT08d9e827ffbba2efe4413cb064bbf847wxh']);
					$m->sex = trim($_POST['micro_membercard_member_infoT08d9e827ffbba2efe4413cb064bbf847sex']);
					$m->sf = trim($_POST['micro_membercard_member_infoT08d9e827ffbba2efe4413cb064bbf847sfcheck']);

					$m->hascardtime = date('Y-m-d H:i:s',time());
					$m->create_time = date('Y-m-d H:i:s',time());
					$save = $m->save();

					$min->wid = $wid;
					$min->wxid= $wxid;
					$min->b_y = trim($_POST['birth_year']);
					$min->b_m = trim($_POST['birth_month']);
					$min->b_d = trim($_POST['birth_date']);
					$min->s_p = trim($_POST['addr_prov']);
					$min->s_s = trim($_POST['addr_city']);
					$min->s_x = trim($_POST['addr_area']);
					$min->sex = trim($_POST['micro_membercard_member_infoT08d9e827ffbba2efe4413cb064bbf847sex']);
					$min->s_addr = trim($_POST['micro_membercard_member_infoT08d9e827ffbba2efe4413cb064bbf847s_addr']);
					$min->f1 = trim($_POST['micro_membercard_member_infoT08d9e827ffbba2efe4413cb064bbf847f1']);
					$min->f2 = trim($_POST['micro_membercard_member_infoT08d9e827ffbba2efe4413cb064bbf847f2']);
					$min->f3 = trim($_POST['micro_membercard_member_infoT08d9e827ffbba2efe4413cb064bbf847f3']);
					$min->f4 = trim($_POST['micro_membercard_member_infoT08d9e827ffbba2efe4413cb064bbf847f4']);
					$min->f5 = trim($_POST['micro_membercard_member_infoT08d9e827ffbba2efe4413cb064bbf847f5']);

					$min->s1 = trim($_POST['micro_membercard_member_infoT08d9e827ffbba2efe4413cb064bbf847s1']);
					$min->s2 = trim($_POST['micro_membercard_member_infoT08d9e827ffbba2efe4413cb064bbf847s2']);
					$min->s3 = trim($_POST['micro_membercard_member_infoT08d9e827ffbba2efe4413cb064bbf847s3']);
					$min->s4 = trim($_POST['micro_membercard_member_infoT08d9e827ffbba2efe4413cb064bbf847s4']);
					$min->s5 = trim($_POST['micro_membercard_member_infoT08d9e827ffbba2efe4413cb064bbf847s5']);
					$min->create_time = date('Y-m-d H:i:s',time());
					$min->save();
						

					$insert_id = $save->id;
					if($info->cno_set!=1)
					{
						$temp->find(array('wid'=>$wid,'tp'=>$info->cno_set));
						$temp->remove();
					}
					$errno=0;
					$error='保存成功';
				}
			}
		}
		if($errno==0) //开卡送 营销券发放无限制
		{
			$gl = new Model('micro_membercard_cardgift_list');
			$rgl = $gl->where(array('wid'=>$wid,'status'=>1))->list_all();
			if(!empty($rgl))
			{
				foreach ($rgl as $v)
				{
					if(!empty($v->time))
					{
						$timearr = explode('-', $v->time);
						if(time()>strtotime($timearr[0]) && time()<strtotime($timearr[1]))
						{
							if($v->type==0) //送积分
							{
								$errno = changeInte($insert_id,$v->score,0,'',3);

								$data = array(
									'wid' =>$wid,
									'wxid' =>$wxid,
									'title' =>'开卡送积分',
									'content' =>'感谢您使用会员卡，送您：'.$v->score.'积分',
									'type' =>2
								);
								systemNotice($data);

							}
							if($v->type==1) //送优惠
							{
								$yhqset = new Model('micro_membercard_yhq_set_list');
								$tty = explode(',', $v->strtty); //类别
								$ttid = explode(',', $v->strttid); //券id

								$yhqlist = new Model('micro_membercard_yhq_list');
								foreach ($tty as $key=>$vid)
								{
									if($vid!='' && $ttid[$key]!='' )
									{
										unset($yhqset->id);
										$yhqset->ttid  = $ttid[$key];
										$yhqset->tty   = $vid;
										$yhqset->wid   = $wid;
										$yhqset->wxid  = $wxid;
										$yhqset->sn = rand6();
										$yhqset->snstetime   = date('Y-m-d H:i:s',time());
										$yhqset->create_time = date('Y-m-d H:i:s',time());
										$yhqset->type = 1;
										$yhqset->hdid = $v->id;
										$yhqset->save();

										$yhqlist->find($ttid[$key]);
										$data = array(
												'wid' =>$wid,
												'wxid' =>$wxid,
												'title' =>'开卡送券',
											    'content' =>'感谢您使用会员卡，送您：'.$yhqlist->name.'一张',
												'type' =>3
										);
										systemNotice($data);
									}
								}
							}
						}
					}
				}
			}
		}
		$return = array(
			  'errno' =>$errno,
			  'error' =>$error
		);
		echo json_encode($return);
		die;
	}
}
else
{
	die();
}


