<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}

if(Session::has('wxid') && Session::has('wid'))
{
    $wid = Session::get('wid');
	$wxid = Session::get('wxid');
	
    $gwcm = new Model('z02_sgwc');
	$gwccc = $gwcm->where(array('wid'=>$wid,'wxid'=>$wxid))->count();
	$fu=new Model('micro_membercard_member_list');
	$fu->find(array('wxid'=>$wxid,'wid'=>$wid));

	$fuck=$fu->member_level;
	if($gwccc>0)
	{
	    $db = DB::get_db();
		$db->begin_transaction();
		
		$data = json_decode(Request::post('alldata'));
		
		if($wid == 6765368){
			$jianshaoqianshu = "";
		}else{
			$jianshaoqianshu = Request::post('jianshaojine') ? Request::post('jianshaojine') : 0;
		}
		$dd = new Model('z02_order');
		$dd->wid = $wid;
		$dd->wxid = $wxid;
		$dd->pay_type = $data->typ;
		$dd->status = '0';
		//$dd->trade_no = $orderid;
		$dd->wlstatus = '0';
		// $dd->yf = $data->yf;
		$dd->yf = $data->yf;

		$dd->addid = $data->addid;
		$dd->create_time = DB::raw('now()');
		$dd->songjifen =  Request::post('songjifen') ? Request::post('songjifen'):  0 ;
		$jifendianshu = Request::post('dianshu') ? Request::post('dianshu') : 0;
		$yhj = Request::post('yhjhidden') ? Request::post('yhjhidden') :0;
		
		$dd->jfsf = $jifendianshu;
		$sparr = array();
		//价格计算
		$zjg = 0;

		$prolist = array();
		foreach ($data->xm as $dds){
			$tsp = array();
			$num = intval($dds->num);
			$gwc = new Model('z02_sgwc');
			$gwc->find($dds->id);
			$p = new Model('z02_sproduct');			//商品信息表
			$p->find($gwc->pid);
			$tsp['pid'] = $gwc->pid;
			$tsp['name'] = $p->name;
			$ordername .= $p->name."|";
			$ordername = rtrim($ordername,'|');
			$tsp['gid'] = $gwc->gid;
			$tsp['num'] = $num;
			$tsp['tuan'] = $gwc->istuan;
			$tsp['bz'] = $dds->bz;

			if($dd->pay_type==0)
			{
				$p->store_nums = intval($p->store_nums)-$num;
				$p->sale_nums = intval($p->sale_nums)+$num;
			}
			if($gwc->gid=='0'){
				if($fuck==0){
					if($fuck==1){
						$tsp['dj'] = $p->lowest;
						$zjg += floatval($p->lowest) * $num;
					}	if($fuck==2){
						$tsp['dj'] = $p->er_price;
						$zjg += floatval($p->er_price) * $num;
					}	if($fuck==3){
						$tsp['dj'] = $p->san_price;
						$zjg += floatval($p->san_price) * $num;
					}
				}

			}
			else
			{
				$datares = json_decode($p->rowtemp);
				$dataresnew = array();
				foreach ($datares as $dr){
					if($gwc->gid == $dr->goods_no){
						if($fuck==0){
							$tsp['dj'] = $dr->market_price;
							$zjg += floatval($dr->market_price)*$num;
						}	if($fuck==1){
							$tsp['dj'] = $dr->sell_price;
							$zjg += floatval($dr->sell_price)*$num;
						}	if($fuck==2){
							$tsp['dj'] = $dr->cost_price;
							$zjg += floatval($dr->cost_price)*$num;
						}	if($fuck==3){
							$tsp['dj'] = $dr->san_price;
							$zjg += floatval($dr->san_price)*$num;
						}


							
						if($dd->pay_type==0)
						$dr->store_nums = intval($dr->store_nums)-$num;
					}
					$dataresnew[] = $dr;
				}
				$p->rowtemp = json_encode($dataresnew);
			}
			$gwc->remove();
			$p->save();
			$prolist[] = $tsp;
		}
		//优惠卷减免
			if($yhj){

				$myyhj = new Model('youhuijuan_list');
				$info = $myyhj->where(array('id'=>$yhj,'wid'=>$wid,'openid'=>$wxid))->find();
				if($info->id){
					$yhjlist = new Model('youhuijuan');
					$list_info = $yhjlist->where(array('id'=>$info->yid))->find();
					if($list_info->id){
						if($list_info->endtime >= date('Y-m-d H:i:s',time())){
							if($zjg>=$list_info->maxcon){
								$zjg = ($zjg*100 -$list_info->redcon*100)/100;
								var_dump($list_info->redcon);
								$dd->jg =$zjg;

								$dd->yhjid = $yhj;
							}else{
								tusi('您不满足使用条件');
							}
						}else{
							tusi("优惠券已过期");
						}
					}else{
						tusi("此优惠劵已失效");
					}
				}else{
					tusi("您还未拥有此优惠券");
				}
			
			}else{
				$zjg = $zjg;
			}
		//总金+运费
		$dd->prolist = json_encode($prolist);
		
			$memberdetail = new Model("micro_membercard_member_list");
			$memberdetail->find(array("wxid"=>$wxid ,"wid"=>$wid));
			$memberdetail->integral;
			if($jifendianshu > $memberdetail->integral){
				tusi('您的积分不足，请重新填写');
				die;
			}else{
				 //一百个积分抵用一元钱
				 // round($number ,2)
    			$yuan = round(($jifendianshu/100),2);
			}
			$dd->jg = $zjg - floatval($yuan);
			//运费
			//计算包邮价格
			$set = new Model('z02_set');
			$set->find(array('wid'=>$wid));
			if($set->consume > $zjg || $set->consume == 0){
				//列出所有收获地址
				$dzlist = new Model('micro_membercard_dz_list');
				$dzres = $dzlist->where(array('wid'=>$wid,'wxid'=>$wxid))->order('is_mr desc,id desc')->list_all();
				
				$chinaares = new Model('chinaarea');
				$areamap = $chinaares->map_array('id', 'name');

				if(empty($dzres)){
					//获取客户端ip
					$user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
					$user_IP = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"];
					// 获取ip所在地
					$userAddrArray =YYUC::local_ip($user_IP);
					$db = DB::get_db();
					$sql = "select * from chinaarea where name like '%".$userAddrArray[2]."%'";
					$areaInfo = $db->query($sql);
					$ord = $areaInfo[0]['ord'];
				}else{
					$ord = $dzres[0]->s_s;
				}
				//默认地址邮费
				$db = DB::get_db();
				//普通SQL
				$sql = "select * from postage_add_city as pac
						left join postage_group as pg on pac.group_id = pg.id
						where pac.city_id=".$ord." and pac.wid=".$wid." limit 1";
				$postageInfo = $db->query($sql);

				//以上都没有  采用默认邮费
				if(empty($postageInfo)){
					$m = new Model('z02_set');
					$m->find(array('wid'=>$wid));
					$postageInfo[0]['postages']=$m->default_postage;
					$postageInfo[0]['addone']=$m->addone;
				}
				$yf = $postageInfo[0]['postages'];
				$zjg = $zjg + floatval($yf) - $yuan;
			}else{
				$zjg = $zjg  - floatval($yuan);
			}
			
		
		

		$dd->use_balance = Request::post('if_balance',0);
		$dd->balance_expense = Request::post('balance',0);
		//判断是否使用了余额支付
		if($dd->use_balance==1 && $dd->balance_expense>=0 ){
			if($dd->balance_expense >= $zjg){
				//print_r($dd);exit;
				$dd->balance_expense = $zjg;
				$dd->status =1;
				$dd->pay_type = 3;
				//去除会员余额支付部分
				$MemberID=checkMember($dd->wid,$dd->wxid);
				changeje($MemberID,$dd->balance_expense,0,'',3);
				$dd->save();
				$db->commit();
				
				$set =  new Model("z02_set");
				$set->find(array('wid'=>$wid));
				if( $set->sms && $set->opensms == 1 ){    
					sent_sms($dd,$set);
				}
				// if($wid == 6765368){
				// 	Redirect::to('/wsc2_new/blancePaySuccess.html');
				// }
				
				//减少用户积分
				$jfff = (int)$memberdetail->integral - $jifendianshu;	
				$sql =  "update micro_membercard_member_list  set integral = " .$jfff. " where wxid =  '" . $wxid . "'";
				$db->query($sql);
				// uploadYhj($dd->id);
				// 
				// 
				// 
				// 
				$prow = json_decode($dd->prolist);
				$pp = new Model('z02_sproduct');
				foreach ($prow as $p)
				{
					$pp->find($p->pid);
					$r = json_decode($pp->rowtemp);
					
					foreach ($r as $key=>$v)
					{
						if($v->goods_no == $p->gid)
						{
							$r[$key]->store_nums = $v->store_nums - $p->num;
							$pstore_nums   =  $pp->store_nums - $p->num;
							$psale_nums    =  $pp->sale_nums + $p->num;
						}
					}
					$rjson = json_encode($r);
					$pp->update($condition=array('id'=>$p->pid),$data=array('rowtemp'=>$rjson,'store_nums'=>$pstore_nums,'sale_nums'=>$psale_nums));
				
					
				}
				//如果有优惠卷id
				if($dd->yhjid){
					$lbs = new Model('youhuijuan_list');
					$lbs->id = $dd->yhjid;
					$lbs->controller = 'WXSC';
					$lbs->usetime = date('Y-m-d H:i:s',time());
					$lbs->state = 1;
					$lbs->save();
				}
				// 
				// 
				// 
				// 
				Redirect::to('/dz_wsc2_new/me.html');
				exit;
			}else{
				$zjg = $zjg - $dd->balance_expense;
			}
		}
		
		if($zjg==0)
		{
		   $dd->status =1;
		}
		if($jifendianshu>0)
		{
			//减少用户积分	
			$jfff = (int)$memberdetail->integral - $jifendianshu;	
			$sql =  "update micro_membercard_member_list  set integral = " .$jfff. " where wxid =  '" . $wxid . "'";
			$db->query($sql);
		}
		
		if(!$dd->save()){
			$db->rollback();
			die();
		}
		
		if($zjg==0)
		{
			//减少用户积分	
			$jfff = (int)$memberdetail->integral - $jifendianshu;	
			$sql =  "update micro_membercard_member_list  set integral =  " .$jfff. " where wxid =  '" . $wxid . "'";
			
			$db->query($sql);
			$set =  new Model("z02_set");
			$set->find(array('wid'=>$wid));

			if( $set->sms && $set->opensms == 1 ){    
				sent_sms($dd,$set);
			}
		}
		$db->commit();

		
		if($zjg>0)
		{
			if($dd->pay_type==0){
				$set =  new Model("z02_set");
				$set->find(array('wid'=>$wid));
				if( $set->email && $set->openemail == 1){
					sent_email($dd,$set);
				}

				if( $set->sms && $set->opensms == 1 ){    
					sent_sms($dd,$set);
				}
				$prow = json_decode($dd->prolist);
				$pp = new Model('z02_sproduct');
				foreach ($prow as $p)
				{
					$pp->find($p->pid);
					$r = json_decode($pp->rowtemp);
					
					foreach ($r as $key=>$v)
					{
						if($v->goods_no == $p->gid)
						{
							$r[$key]->store_nums = $v->store_nums - $p->num;
							$pstore_nums   =  $pp->store_nums - $p->num;
							$psale_nums    =  $pp->sale_nums + $p->num;
						}
					}
					$rjson = json_encode($r);
					$pp->update($condition=array('id'=>$p->pid),$data=array('rowtemp'=>$rjson,'store_nums'=>$pstore_nums,'sale_nums'=>$psale_nums));
				
					
				}
				//如果有优惠卷id
				if($dd->yhjid){
					$lbs = new Model('youhuijuan_list');
					$lbs->id = $dd->yhjid;
					$lbs->controller = 'WXSC';
					$lbs->usetime = date('Y-m-d H:i:s',time());
					$lbs->state = 1;
					$lbs->save();
				}



				Redirect::to('/dz_wsc2_new/me.html');
				exit;
			}
			elseif($data->typ=='8') //银联
			{ 
				include_once '/mnt/sdzzb/sdzzb/wx/plugin/unionpayn/common.php';
				include_once '/mnt/sdzzb/sdzzb/wx/plugin/unionpayn/SDKConfig.php';
				include_once '/mnt/sdzzb/sdzzb/wx/plugin/unionpayn/secureUtil.php';
				include_once '/mnt/sdzzb/sdzzb/wx/plugin/unionpayn/log.class.php';
				
				$total_fee = 100*($zjg);
				
				// 初始化日志
				$params = array(
						'version' => '5.0.0',				//版本号
						'encoding' => 'utf-8',				//编码方式
						'certId' => getSignCertId (),			//证书ID
						'txnType' => '01',				//交易类型	
						'txnSubType' => '01',				//交易子类
						'bizType' => '000201',				//业务类型
						'frontUrl' =>  conf::$http_path."/dz_wsc2_new/me-$wid-$wxid.html",  		//前台通知地址
						'backUrl' => SDK_BACK_NOTIFY_URL,		//后台通知地址	
						'signMethod' => '01',		//签名方法
						'channelType' => '08',		//渠道类型，07-PC，08-手机
						'accessType' => '0',		//接入类型
						'merId' => '898111448160720',		        //商户代码，请改自己的测试商户号
						'orderId' => date('YmdHis').$dd->id,	//商户订单号
						'txnTime' => date('YmdHis'),	//订单发送时间
						'txnAmt' => "$total_fee",		//交易金额，单位分
						'currencyCode' => '156',	//交易币种
						'defaultPayType' => '0001',	//默认支付方式	
						//'orderDesc' => '订单描述',  //订单描述，网关支付和wap支付暂时不起作用
						'reqReserved' =>' 透传信息', //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现
				);
				// 签名
				
				//log::warn('frontUrlaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa:'.$params['frontUrl']);
				sign ( $params );


				// 前台请求地址
				$front_uri = SDK_FRONT_TRANS_URL;

				// 构造 自动提交的表单
				$html_form = create_html ( $params, $front_uri );
				echo $html_form; 
				die;

			}
			elseif($data->typ=='3'){//微信支付
			
				$payset = new Model('ai9me_pay_set');
				$payset->where(array('token'=>$wid,'pc_enabled'=>'1','pc_type'=>'wxpay'))->find();
				if(!$payset->has_id()){
					die('789');
				}
				if ($wid == 6765368)
					Redirect::to(conf::$http_path.'wxpay/wxpay-'.$dd->id.'-'.$payset->id.'-WXSC.html?showwxpaytitle=1&wid='.$wid.'&wxid='.$wxid);
				elseif($payset->paySignKey=='')
					Redirect::to(conf::$http_path.'wxpay/topayv33-'.$dd->id.'-'.$payset->id.'-WSC2NEW.html?showwxpaytitle=1&wid='.$wid.'&wxid='.$wxid);
				else
					Redirect::to(conf::$http_path.'wxpay/topay-'.$dd->id.'-'.$payset->id.'-WSC2NEW.html?showwxpaytitle=1&wid='.$wid.'&wxid='.$wxid);
				die;
			}elseif($data->typ=='2'){
				include YYUC_FRAME_PATH.'plugin/Tenpay/RequestHandler.class.php';
				include YYUC_FRAME_PATH.'plugin/Tenpay/client/ClientResponseHandler.class.php';
				include YYUC_FRAME_PATH.'plugin/Tenpay/client/TenpayHttpClient.class.php';

				$payset = new Model('ai9me_pay_set');
				$payset->where(array('token'=>$wid,'pc_enabled'=>'1','pc_type'=>'tenpay'))->find();

				$notify_url = conf::$http_path.'dz_wsc2_new/tenpayok-'.$wid.'.html';
				$call_back_url = conf::$http_path.'dz_wsc2_new/me.html';
				$merchant_url = conf::$http_path.'dz_wsc2_new/me.html';

				//---------------------------------------------------------
				//财付通即时到帐支付请求示例，商户按照此文档进行开发即可
				//---------------------------------------------------------

				//订单号，此处用时间加随机数生成，商户根据自己情况调整，只要保持全局唯一就行
				$out_trade_no = 'WXSC_'.$dd->id;

				//订单名称
				$subject = $ordername;
				//必填

				//付款金额
				$total_fee = $zjg;
				//必填
				//应答对象
				$resHandler = new ClientResponseHandler();
				//echo $payset->key.$payset->partner;
				//$payset->key = "8934e7d15453e97507ef794cf7b0519d";
				//$payset->partner = "1900000109";
				/* 创建支付请求对象 */
				$reqHandler = new RequestHandler();
				$reqHandler->init();
				$reqHandler->setKey($payset->key);
				
				
				//$reqHandler->setGateUrl("https://gw.tenpay.com/gateway/pay.htm");
				//设置初始化请求接口，以获得token_id
				$reqHandler->setGateUrl("http://wap.tenpay.com/cgi-bin/wappayv2.0/wappay_init.cgi");


				$httpClient = new TenpayHttpClient();

				//----------------------------------------
				//设置支付参数
				//----------------------------------------
				$total_fee = 100*($total_fee);
				$reqHandler->setParameter("total_fee", $total_fee);  //总金额
				//用户ip
				$reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);//客户端IP
				$reqHandler->setParameter("ver", "2.0");//版本类型
				$reqHandler->setParameter("bank_type", "0"); //银行类型，财付通填写0
				$reqHandler->setParameter("callback_url", $call_back_url);//交易完成后跳转的URL
				$reqHandler->setParameter("notify_url", $notify_url);//接收财付通通知的URL，需绝对路径
				$reqHandler->setParameter("bargainor_id", $payset->partner); //商户号
				$reqHandler->setParameter("sp_billno", $out_trade_no); //商户订单号
				$reqHandler->setParameter("desc", $subject);

				$httpClient->setReqContent($reqHandler->getRequestURL());
				//后台调用

				if($httpClient->call()) {
					$resHandler->setContent($httpClient->getResContent());
					//获得的token_id，用于支付请求
					$token_id = $resHandler->getParameter('token_id');
					$reqHandler->setParameter("token_id", $token_id);
					//请求的URL
					//$reqHandler->setGateUrl("https://wap.tenpay.com/cgi-bin/wappayv2.0/wappay_gate.cgi");
					//此次请求只需带上参数token_id就可以了，$reqUrl和$reqUrl2效果是一样的
					//$reqUrl = $reqHandler->getRequestURL();
					//$reqUrl = $reqHandler->getRequestURL();
					$reqUrl = "http://wap.tenpay.com/cgi-bin/wappayv2.0/wappay_gate.cgi?token_id=".$token_id;
					//$reqUrl = $reqHandler->getGateURL();
					Redirect::to($reqUrl);
					exit;
				}
			}
		}
		else
		{
			
			Redirect::to('/dz_wsc2_new/me.html');
			exit;
		}
	
	}
	else
	{

	   tusi('购物车为空,请选择商品');
	   //Redirect::delay_to("index.html",1);
	   Redirect::to('index.html');
	   exit;
	}

}else{

}

