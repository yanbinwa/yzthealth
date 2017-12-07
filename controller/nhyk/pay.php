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


	function checkyhq($qid,$wxid)
	{
		$m = new Model('micro_membercard_yhq_set_list');
		$rs = $m->where(array('wxid'=>$wxid,'ttid'=>$qid,'status'=>0))->order('id asc ')->limit(1)->list_all();
		
		if(!empty($rs[0]->id))
		{
			return $rs[0]->id;
			
		}
		else
		{
			return false;
			
			
		}
		
	}
	function updateqsta($id,$sta)
	{
		$m = new Model('micro_membercard_yhq_set_list');
		return $m->update($condition=array('id'=>$id),$data=array('status'=>$sta));
	}

	function updatebalance($id,$price)
	{
		$db  = DB::get_db();
		$sql = "update micro_membercard_member_list set balance = balance - $price where id=$id ";
		return	 $db->execute($sql);
	}

	if($_POST)
	{
		$store     = intval($_POST['store']);
		$paytype   = intval($_POST['paytype']);
		$sub       = trim($_POST['sub']);
		$money     = trim($_POST['money']);
		$validCode = trim($_POST['validCode']);
		$password  = trim($_POST['password']);
		$qid       = intval($_POST['qid']);
		$pay_pwd   = trim($_POST['pay_pwd']);

		if($money!='' && $money!=0)
		{
			if($store!=0)
			{
				if($paytype!=0 && in_array($paytype, array(1,2,3,4,5)))
				{
					switch ($paytype)
					{
						
						case 1:  //现金付款
							$xffs =  '现金付款';
							$m1 = new Model('micro_membercard_info');
							$m1->find(array('wid'=>$wid));
							
							if($password==$m1->pwd)
							{
								$m2 = new Model('micro_membercard_member_list');
								$m2->find(array('wxid'=>$wxid));
								
								
								if($qid!=0)
								{
									$checkyhq = checkyhq($qid,$wxid);
									
									if($checkyhq)
									{
										$m3 = new Model('micro_membercard_yhq_list');
										$m3->find($qid);
										
										if($m3->type==2) //代金券
										{
											if($money>=$m3->xemoney)
											{
												
												$shortm = $money-$m3->dmoney;

											}
											else
											{
												
												
												$errno = 7;
												$error = '您的消费金额小于'.$m3->xemoney.',不能使用此代金券，请换用其他消费券';
												
											}
										}
									}
									else
									{
										$errno = 8;
										$error = '优惠券参数传递错误';
									}
								}
							}
							else
							{
								$errno = 5;
								$error = '商家确认密码不正确';
								
							}
							break;
						case 2:  //余额支付
							$xffs =  '余额支付';
							$m2 = new Model('micro_membercard_member_list');
							$m2->find(array('wxid'=>$wxid));
							$m_1 = new Model('micro_membercard_info');
							$m_1->find(array('wid'=>$wid));
							$mc = new Model('micro_membercard_send_code_log');
							$rmc = $mc ->where(array('wid'=>$wid,'tel'=>$m2->phone))->order('id desc')->limit(1)->list_all();
							$lastsendtime = strtotime($rmc[0]->create_time);
							$sttime = time()-$lastsendtime;
							if($m_1->yu_e_pay==0){
								if($sttime>600)
								{
									$errno=5;
									$error='验证码已过期';
								}
							}

							if($rmc[0]->code!=$validCode&&$m_1->yu_e_pay==0)
							{
								$errno=1;
								$error='验证码不正确';
							}else if($m_1->yu_e_pay==1&&$m2->yu_e_pay==""){
								$errno=16;
								$error='您还没设置密码，到个人中心设置';
							}else if($m_1->yu_e_pay==1&&$pay_pwd==""){
								$errno=15;
								$error='支付密码不能为空';
							}else if($m_1->yu_e_pay==1&&$pay_pwd!=$m2->yu_e_pay){
								$errno=14;
								$error='支付密码不正确';
							}
							else
							{
								$shortm = $money;
								if($qid!=0)
								{
									$checkyhq = checkyhq($qid,$wxid);
									if($checkyhq)
									{

										$m3 = new Model('micro_membercard_yhq_list');
										$m3->find($qid);
										if($m3->type==2) //代金券
										{
											if($money>=$m3->xemoney)
											{
												$shortm = $money-$m3->dmoney;
											}
											else
											{
												$errno = 7;
												$error = '您的消费金额小于'.$m3->xemoney.',不能使用此优惠券，请换用其他消费券';
											}
										}
									}
									else
									{
										$errno = 8;
										$error = '优惠券参数传递错误';
									}
								}
								if($m2->balance < $shortm)
								{
									$errno = 6;
									$error = '您的余额不足，请先充值或换用其他支付方式';
								}
							}
							break;
						case 4:  //财付通
							$xffs =  '财付通支付';
							die('平台暂不支持财付通支付');
							break;
						case 5:  //微支付


							$xffs =  '微信支付';
							$m2 = new Model('micro_membercard_member_list');
							$m2->find(array('wxid'=>$wxid));
							$shortm = $money;
							if($qid!=0)
							{
								$checkyhq = checkyhq($qid,$wxid);
								if($checkyhq)
								{

									$m3 = new Model('micro_membercard_yhq_list');
									$m3->find($qid);
									if($m3->type==2) //代金券
									{
										if($money>=$m3->xemoney)
										{
											$shortm = $money-$m3->dmoney;
										}
										else
										{
											$errno = 7;
											$error = '您的消费金额小于'.$m3->xemoney.',不能使用此优惠券，请换用其他消费券';
										}
									}
								}
								else
								{
									$errno = 8;
									$error = '优惠券参数传递错误';
								}
							}
							break;



							/*case 4:  //支付宝支付
							 	
							$xffs =  '支付宝支付';
							$m2 = new Model('micro_membercard_member_list');
							$m2->find(array('wxid'=>$wxid));
							$shortm = $money;
							if($qid!=0)
							{
							$checkyhq = checkyhq($qid,$wxid);
							if($checkyhq)
							{

							$m3 = new Model('micro_membercard_yhq_list');
							$m3->find($qid);
							if($m3->type==2) //代金券
							{
							if($money>=$m3->xemoney)
							{
							$shortm = $money-$m3->dmoney;
							}
							else
							{
							$errno = 7;
							$error = '您的消费金额小于'.$m3->xemoney.',不能使用此优惠券，请换用其他消费券';
							}
							}
							}
							else
							{
							$errno = 8;
							$error = '优惠券参数传递错误';
							}
							}
							break;
							*/
					}
					//消费
					if(empty($errno))
					{
						$m = new Model('micro_membercard_pay_log');
						if(empty($sub))
						{
							if ($checkyhq) {
								$yhq = new Model('micro_membercard_yhq_set_list');
								$yhq->find($checkyhq);
								if ($yhq->has_id()) {
									$m->sn = $yhq->sn;
								}
							}
							$m->wid = $wid;
							$m->wxid = $wxid;
							$m->price = $money;
							$m->spoutlet_id = $store;
							$m->uname= $m2->name;
							$m->ttid = $qid;
							$m->tty = $m3->type;
							$m->ttje  = $m3->dmoney;
							$m->bty  = $paytype;
							$m->cardno  = $m2->cardno;

							$m->phone  = $m2->phone;
							$m->create_time = date('Y-m-d H:i:s',time());
							if($paytype==5)
							{
								$m->status = 1;
								$m->trade_no = order_no();
							}
							$save = $m->save();
						}
						if($save || !empty($sub))
						{
								
								
							if($paytype==5)  //微支付
							{

								if(!empty($sub))
								{
									$m->find(array('trade_no'=>$sub));
									
									$payset = new Model('ai9me_pay_set');
									$payset->find(array('token'=>$wid,'pc_enabled'=>1,'pc_type'=>'wxpay'));

									if($payset->paySignKey=='')
									Redirect::to('http://www.weixinguanjia.'.$payset->payml.'/wxpay/nhyktopayv33-'.$m->id.'-'.$payset->id.'.html?showwxpaytitle=1&wid='.$wid.'&wxid='.$wxid.'&pt=1');
									else
									Redirect::to('http://www.weixinguanjia.'.$payset->payml.'/wxpay/nhyktopay-'.$m->id.'-'.$payset->id.'.html?showwxpaytitle=1&wid='.$wid.'&wxid='.$wxid.'&pt=1');

								}
								else
								{
									$errno = 0;
									$error = $m->trade_no;
								}

								//die;
									
								//支付
								/*$ms = new Model('ai9me_pay_set');
								 $ms->find(array('token'=>$wid,'pc_enabled'=>1));
								 if($ms->has_id())
								 {
									$data = array
									(
									'partner' =>$ms->partner,
									'key' =>$ms->key,
									'seller_email' =>$ms->seller_email,
									'wid' =>$wid,
									'WIDout_trade_no'=>$sub,
									'WIDsubject' =>"付款，金额：".$shortm,
									'WIDtotal_fee' =>$shortm
									);
									if(!empty($sub))
									doalipay($data);
									else
									{
									$errno = 0;
									$error = $m->trade_no;
									}
									}
									else
									{
									$errno = 2;
									$error = '商家未配置支付宝账户信息';
									}
									*/
							}
							else
							{
								$updatebalance = 1;
								if($paytype==2)
								{
									$updatebalance = updatebalance($m2->id,$shortm);
								}
								if($updatebalance)
								{
									if($qid!=0)
									{
										$u = updateqsta($checkyhq,1);
										if(!$u)
										{
											$errno = 10;
											$error = '优惠券状态更新失败';
										}
									}

									if(empty($errno))
									{
										cusgive($wid,$wxid,$money,$store,$qid);
										$errno = 0;
										$error = $money;
									}
								}
								else
								{
									$errno = 11;
									$error = '更改余额失败';
								}
							}
						}
						else
						{
							$errno = 9;
							$error = '订单保存失败';
						}
					}
				}
				else
				{
					$errno = 2;
					$error = '请选择支付方式';
				}
			}
			else
			{
				$errno = 3;
				$error = '请选择消费门店';
			}
		}
		else
		{
			$errno = 4;
			$error = '请输入消费金额';
		}
		echo json_encode(array('errno'=>$errno,'error'=>$error));
		die;
	}

	$momey = trim($_GET['m']);
	$m = new Model('micro_membercard_member_list');
	$m->find(array('wxid'=>$wxid));
}else{
	die();
}

