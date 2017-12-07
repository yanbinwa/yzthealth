<?php

/**
 * wechat php test
 */
//define your token

header("Content-type:text/html;charset=utf-8");
Page::ignore_view();
include_once "wxBizMsgCrypt.php";
include_once 'auth.class.php'; 

//define("TOKEN",md5(Request::get('appid').Conf::$management_center_target));
//$wid = Request::get('appid');

$token='sdzzb110';
$encodingAesKey='sdzzb110sdzzb110sdzzb110sdzzb110sdzzb110232';
$appId='wx02560241e9071ed8';

$pc = new WXBizMsgCrypt($token, $encodingAesKey, $appId);
$url=explode('/',Request::url());
$appid=$url[1];
define("TOKEN",'sdzzb110');
$encryptMsg='';

//log::warn('===========================================appid'.$appid);


$pub = new Model('pubs');
$pub->find(array('cusid'=>$appid));
$wid = $pub->id;

if($GLOBALS["HTTP_RAW_POST_DATA"]){
	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
	//extract post data
	if (!empty($postStr)){
	
	    $msg_sign = $_REQUEST['msg_signature'];
		$timeStamp= $_REQUEST['timestamp'];
		$nonce= $_REQUEST['nonce'];
		//file_put_contents('info.txt','AA_'.$msg_sign.'___'.$timeStamp.'___'.$nonce.'--AA',FILE_APPEND);
		$errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $postStr, $msg);
		$postObj = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
		$fromUsername = $postObj->FromUserName;
		$toUsername = $postObj->ToUserName;
		$keyword = trim($postObj->Content);
		$type = $postObj->MsgType;
 
	
		if($type=='location'){
			$data_tj = new Model('data_statistics');
			$data_tj->wid = $wid;
			$data_tj->type= 4;
			$data_tj->wxid= $postObj->FromUserName;
			$data_tj->ctime= DB::raw('NOW()');
			$data_tj->save();
			//地理位置信息
			$m = new Model('lbs');
			$lres = $m->where(array('wid'=>$wid))->list_all();
			$jd = floatval($postObj->Location_Y);
			$wd = floatval($postObj->Location_X);
			$jlarr = array();
			$ja = false;
			$locnum = 0;
			foreach ($lres as $l){

				$jl = get_distance_by_lng_lat($jd,$wd,floatval($l->jd),floatval($l->wd));//Conf::$http_path.'weiweb/'.$wid.'/'
				$name = preg_replace("/\s/","",$l->name);
				$content = preg_replace("/\s/","",strip_tags($l->content));
				//	$hyurl = 'http://api.map.baidu.com/marker?location='.$l->wd.','.$l->jd.'&title='.$name.'&content='.$name.'&output=html&src=zzb|guanjia';
				//$hyurl = Conf::$http_path.'wx/lbs.html?origin_wd='.$wd.'origin_jd='.$jd.'&wd='.$l->wd.'&jd='.$l->jd.'&title='.$name.'&address='.$l->address.'&jl='.$jl.'&tel='.$l->tel;
				$hyurl = Conf::$http_path.'wx/lbs.html?id='.$l->id.'&jl='.$jl.'&jd='.$jd.'&wd='.$wd;
				$jlarr[$jl] = array('tit'=>($l->name.',距离'.$jl.'米'),'pic'=>$l->pic,'url'=>$hyurl,'ms'=>$content);
				if(!$ja){
					$ja = $jlarr[$jl];
				}
			}
			ksort($jlarr);
			$jlarrs = array();
			foreach ($jlarr as $k=>$v){//前9条
				$locnum++;
				if($locnum >9){
					break;
				}
				$jlarrs[$k] = $v;
			}

			if(count($lres)>1){
				response_more($jlarrs,$postObj);
			}else{
				response_one($ja['tit'].'。',$ja['pic'],$ja['ms'],$ja['url'],$postObj);
			}
		}
		//找到消息号码
		if($type=='event'){
			if(!empty($postObj->EventKey) && !empty($postObj->Ticket)){

				$data_ex = new Model('dingzhi_car_user');
				$data_ex->wid = $wid;
				$data_ex->wxid= $postObj->FromUserName;
				$data_ex->qrcode= $postObj->Ticket;


				//$data_ex->save();
				//扫描
				/*if('SCAN' == $postObj->Event){
					$data_ex->scene_id=$postObj->EventKey;
					Log::warn("aaaaaaaaa");
					$data_ex->find(array('wid'=>$wid,'wxid'=>$postObj->FromUserName,'qrcode'=>$postObj->Ticket));

					//$quinfo=M("Qudao")->where($quwhere)->find();
					if($data_ex->has_id())
					$data_ex->update("$condition=array('wid'=>$wid,'wxid'=>$postObj->FromUserName,'qrcode'=>$postObj->Ticket),$data=array('scan_num'=>(scan_num+1)");
					}*/
				//关注
				if ('subscribe' == $postObj->Event){
					$sceneid=str_replace("qrscene_","",$postObj->EventKey);
					$data_ex->scene_id=$sceneid;
					$data_ex->find(array('qrcode'=>$postObj->Ticket));


					$sence_add = new Model('dingzhi_car_scene');
					$sence_add->wxid= $postObj->FromUserName;
					$sence_add->sid = $data_ex->scene_id;


					//获取access_token
					if(Cache::has($wid.'access_token')){
						$access_token = Cache::get($wid.'access_token');
					}else{
						$appid = "wxd5d18d75d966e33d";//爱车去哪儿
						$appsecret = "5f5382cdf747bafd69c3cf439a7099ec";//爱车去哪儿
							
						$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
							
							
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						$output = curl_exec($ch);
						curl_close($ch);

						$jsoninfo = json_decode($output, true);
						$access_token = $jsoninfo["access_token"];
						Cache::set($wid.'access_token', $access_token,7000);
					}
					if(!$sence_add->has_id()){
						$url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$postObj->FromUserName&lang=zh_CN";
						$result  = HttpCurl::quickpost($url);
						$jsoninfo = json_decode($result,true);
						$nickname =$jsoninfo["nickname"];
						$sence_add->nickname = $nickname;
						$sence_add->save();
					}
					if($data_ex->has_id()){
						$sql = "update `dingzhi_car_user` set `sub_num`=`sub_num`+1 where `qrcode`='$postObj->Ticket'";
						$query = mysql_query($sql);
					}
				}
			}

			if('subscribe'==$postObj->Event){
				$data_tj = new Model('data_statistics');
				$data_tj->wid = $wid;
				$data_tj->type= 1;
				$data_tj->wxid= $postObj->FromUserName;
				$data_tj->ctime= DB::raw('NOW()');
				$data_tj->save();
				//关注
				$fa = new Model('first_attention');
				$fa->find(array('wid'=>$wid));
				
				
				/* $wifi = new Model('wifi_keyword');
				 $wifi->find(array('wid'=>$wid)); */
				if($fa->has_id()){
					/* if($wifi->isFirstAttention){
						$url = Conf::$http_path.'wifi/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($wifi->name,$wifi->pic,$wifi->ms,$url,$postObj);
						}else */
					if($fa->typ=='0'){
					//	Log::warn($fa->content);
						
						response_text($fa->content,$postObj);
						
						
 					}else{
 						
						$res = new Model('res');
						$res->find($fa->resource_id);
						response_arts($res,$postObj);
					}
				}else{

				}

			}if('unsubscribe'==$postObj->Event){
				$data_tj = new Model('data_statistics');
				$data_tj->wid = $wid;
				$data_tj->type= 5;
				$data_tj->wxid= $postObj->FromUserName;
				$data_tj->ctime= DB::raw('NOW()');
				$data_tj->save();
			}elseif('CLICK'==$postObj->Event){
				$data_tj = new Model('data_statistics');
				$data_tj->wid = $wid;
				$data_tj->type= 3;
				$data_tj->wxid= $postObj->FromUserName;
				$data_tj->ctime= DB::raw('NOW()');
				$data_tj->save();

				$key = $postObj->EventKey;
				//Log::error($key);
				/////////////////////////////--------------------------
				$key = explode('@', $key);
				if($key[0]=='res_wb'){
					//文本回复
					$ggk = new Model('res_text');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						response_text($ggk->txt,$postObj);
						return;
					}
				}elseif($key[0]=='res_tw' || $key[0]=='res_dtw'){
					//图文回复
					$res = new Model('res');
					$res->find($key[1]);
					if($res->has_id()){
						response_arts($res,$postObj);
					}
				}elseif($key[0]=='res_gjz'){
					//关键字
					$ggk = new Model('res_text');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						check_and_replay($ggk->txt,$postObj);
						return;
					}
					
				
					
					
				}elseif($key[0]=='res_yhq'){
					//优惠券
					$coupon = new Model('coupon');
					$coupon->find($key[1]);
					if($coupon->has_id()){
						$url = Conf::$http_path.'wx/yhq-'.$coupon->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($coupon->name,$coupon->pic,$coupon->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=='res_ggk'){
					//刮刮卡
					$ggk = new Model('ggk');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'wx/ggk-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=='res_xydzp'){
					//幸运大转盘
					$ggk = new Model('xydzp');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'wx/xydzp-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=='res_xyj'){
					//幸运机
					$ggk = new Model('xyj');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'wx/xyj-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=='res_zjd'){
					//砸金蛋
					$ggk = new Model('zjd');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'wx/zjd-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=='res_fxdr'){
					//分享达人
					$ggk = new Model('fxdr');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'wx/fxdrlist.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=='res_wtg'){
					//微团购
					$ggk = new Model('micro_group_buy');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'wx/wtg-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=='res_yzdd'){
					//一战到底
					$ggk = new Model('yzdd');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'wx/yzdd-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						if($wid=='59078')
						$ggk->ms = '';
						
						response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=='res_weiba'){
					//微吧
					$ggk = new Model('weiba');
					$ggk->find(array('wid'=>$wid));
					if($ggk->has_id()){
						$arts = array();
						$fart = array();
						$url = Conf::$http_path.'wx/weiba-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						$fart['tit'] = $ggk->ms;
						$fart['url'] = $url;
						$fart['pic'] = $ggk->pic;
						$arts[] = $fart;
						//查找话题
						$m = new Model('weiba_ht');
						$subres = $m->where(array('wid'=>$wid))->order('zm desc')->limit('7')->list_all();
						if(count($subres)>0){
							foreach ($subres as $re){
								$tart = array();
								//$tart['tit'] = '#'.$re->keywd.'#';
								$tart['tit'] = $re->keywd;
								$tart['url'] = Conf::$http_path.'wx/weiba-'.$ggk->id.'-'.$re->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
								$tart['pic'] = Conf::$http_path.'res/s.png?d';
								$arts[] = $tart;
							}
						}else{
							$tart = array();
							$tart['tit'] = '发起新话题';
							$tart['url'] = Conf::$http_path.'wx/weiba-'.$ggk->id.'-new.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
							$tart['pic'] = Conf::$http_path.'res/s.png?df';
							$arts[] = $tart;
						}
						response_more($arts,$postObj,1);
						return;
					}
				}elseif($key[0]=='res_whyk'){
					//会员卡
					$coupon = new Model('micro_member_card');
					$coupon->find($key[1]);
					if($coupon->has_id()){
						$url = Conf::$http_path.'wx/hyk-'.$coupon->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						//查询我的会员卡号
						$red = new Model('micro_member_card_record');
						$red->find(array('cid'=>$coupon->id,'wxid'=>$postObj->FromUserName));
						if($red->has_id()){
							response_one($coupon->name,$coupon->pic,'尊敬的会员卡用户，您的卡号为：'.$red->sn.'。'.$coupon->ms,$url,$postObj);
						}else{
							response_one($coupon->name,$coupon->pic,$coupon->ms,$url,$postObj);
						}

						return;
					}
				}elseif($key[0]=='res_wdy'){
					//微调研
					$ggk = new Model('micro_survey');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'wx/wdy-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=='res_wtp'){
					//微投票
					$ggk = new Model('micro_vote');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'wx/wtp-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=='res_wyy'){
					//在线预订
					$zxyd = new Model('online_booking');
					$zxyd->find($key[1]);
					if($zxyd->has_id()){
						$url = Conf::$http_path.'wx/onlineBooking-'.$zxyd->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($zxyd->name,$zxyd->pic,$zxyd->ms,$url,$postObj);
						return;
					}
				}
		
				////////////////////////////-----------------------------
			}
			//菜单待续
		}elseif($type=='image'){
		    if($wid=='78151')
			$url = 'http://www.weixinguanjia.cn/media/images/love.png';
			else
			//$url = $postObj->PicUrl;
			//$url = 'http://www.weixinguanjia.cn/res/wall/logo.jpg';//$postObj->PicUrl;
			//log::warn('===============================================传图片：'.$postObj->PicUrl);
			//$url = "http://www.weixinguanjia.cn/res/wall/touxiang/wall".mt_rand(1,5).".jpg";//$postObj->PicUrl;

			$mid = $postObj->MediaId;
			//看看是不是在墙上
			//看看是否在墙上
			$qlog = new Model('z01_wall_u');
			$qlog->where(array('wxid'=>$postObj->FromUserName,'wid'=>$wid,'ison'=>'1'))->find();
			if($qlog->has_id()){
				//上过墙
				if($qlog->ison=='1'){
					//在墙上
					// $qlog->pic = $url;
					// $qlog->save();
					//response_text('发送成功，再次发送请直接回复；取消微信墙，请回复“0”', $postObj);
					response_one_nopic('微信上墙', '您已进入微信墙，点击进入摇一摇，内容上墙请直接回复。', Conf::$http_path.'wall/myyyy-'.$qlog->qid.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid, $postObj);
					return;
				}
					
			}

			//微打印-接收图片
			if($mid){

				//判断当前公众账户是否有打印设备
				$device = new Model('micro_wprint_device');
				$deviceNum = $device->where(array('wid'=>$wid))->count();
				if($deviceNum){
					//判断是否开启微秀打印
					$print_keyword = new Model('micro_wprint_keword');
					$print_keyword->where(array('wid'=>$wid))->find();
					if($print_keyword->ifkey == '1' && Session::has('is_print')){
						$print_pic = new Model('micro_wprint_pics');
						$print_pic->where(array('mid'=>$mid,'wid'=>$wid))->find();
						if($print_pic->has_id()){
						}else{
							$print_pic->fromuser = $postObj->FromUserName;
							$print_pic->mid = $postObj->PicUrl;
							$print_pic->wid = $wid;
							$print_pic->is_print = 0;
							$print_pic->use_time = DB::raw('now()');
							$print_pic->save();
						}
						$lomo_msg ='照片已收到,<a href="http://'.$_SERVER['HTTP_HOST'].'/wprint-'.$print_pic->id.'.html?ac=make&wxid='.$postObj->FromUserName.'">点击这里</a>开始制作微秀卡！';
						response_text($lomo_msg,$postObj);
						Session::remove('is_print');
					}else{
						$print_pic = new Model('micro_wprint_pics');
						$print_pic->where(array('mid'=>$mid,'wid'=>$wid))->find();
						if($print_pic->has_id()){
						}else{
							$print_pic->fromuser = $postObj->FromUserName;
							$print_pic->mid = $postObj->PicUrl;
							$print_pic->wid = $wid;
							$print_pic->is_print = 0;
							$print_pic->use_time = DB::raw('now()');
							$print_pic->save();
						}
						$lomo_msg ='照片已收到,<a href="http://'.$_SERVER['HTTP_HOST'].'/wprint-'.$print_pic->id.'.html?ac=make&wxid='.$postObj->FromUserName.'">点击这里</a>开始制作微秀卡！';
						response_text($lomo_msg,$postObj);
						Session::remove('is_print');
					}
					if($print_keyword->ifkey == '0'){ //只要用户传照片就打印

					}
				}
			}






		}elseif($keyword!=''){
			$data_tj = new Model('data_statistics');
			$data_tj->wid = $wid;
			$data_tj->type= 2;
			$data_tj->wxid= $postObj->FromUserName;
			$data_tj->ctime= DB::raw('NOW()');
			$data_tj->save();
			if($keyword=='宝宝'){

				response_text('升级成功',$postObj);
				if ($wid==1322) {
					//warn::
				}
				//记住账户的uuid
				$pub = new Model('pubs');
				$pub->update(array('id'=>Request::get('appid')),array('uuid'=>$toUsername));
			}else{
				check_and_replay($keyword,$postObj);
			}
		}
	}
	die();
}else{
	$wechatObj = new wechatCallbackapiTest();
	if($wechatObj->valid()){//更新验证规则
		$pub = new Model('pubs');
		$pub->update(array('id'=>Request::get('appid')),array('http'=>'','token'=>'','isval'=>'1'));
	}
}
die();



function onthewall($qlog,$keyword,$postObj,$isfirst=false){
	global $wid;
	if('0'==$keyword){
		$qlog->ison='0';
		$qlog->save();
		response_text('你已经成功退出微信墙！', $postObj);
		return;
	}
	if(trim($qlog->nc)==''){
		if($isfirst){
			response_text('请输入显示的“昵称”进入微信墙；昵称长度少于6；如需退出回复“0”', $postObj);
		}else{
		
		    $qlog1 = new Model('z01_wall_u');
		    $qlog1->find(array('nc'=>$keyword,'qid'=>$qlog->qid));
			if($qlog1->has_id())
			{
			   response_text('昵称有人叫了，换个吧！', $postObj);
			}
			else
			{
			    $qlog->nc = $keyword;
				$qlog->pic ="http://www.weixinguanjia.cn/res/wall/touxiang/wall".mt_rand(1,19).".jpg";
				$qlog->save();
				response_one_nopic('微信上墙', '您已进入微信墙，点击进入摇一摇，内容上墙请直接回复。', 
				Conf::$http_path.'wall/myyyy-'.$qlog->qid.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid, $postObj);
				//response_text('请上传图片作为您的头像；如需退出回复“0”', $postObj);
			}
		}
		return;
	}
	if(trim($qlog->pic)==''){
		$qlog->pic ="http://www.weixinguanjia.cn/res/wall/touxiang/wall".mt_rand(1,19).".jpg";
		$qlog->save();

		return;
		//response_text('请上传图片作为您的头像；如需退出回复“0”', $postObj);
	}else{
		//上墙
		$qll = new Model('z01_wall_l');

		$qll->wxid = $postObj->FromUserName;
		$qll->wid = $wid;
		$qll->nr = $keyword;
		$qll->qid = $qlog->qid;
		$qll->nc = $qlog->nc;
		$qll->pic = $qlog->pic;
		$qll->ctime = DB::raw('now()');

		$qll->save();
		response_text('发送成功，再次发送请直接回复；取消微信墙，请回复“0”', $postObj);
	}
}

//智能回复
function check_and_replay($keyword,$postObj){
	global $wid;
	//匹配各项活动

	if($keyword=='qpqpqpp'){
		response_one_nopic('123', '456', 'http://www.baidu.com', $postObj);
		return;
	}
	//看看是否在墙上
	$qlog = new Model('z01_wall_u');
	$qlog->where(array('wxid'=>$postObj->FromUserName,'wid'=>$wid,'ison'=>'1'))->find();
	if($qlog->has_id()){
		//上过墙
		if($qlog->ison=='1'){
			//在墙上
			onthewall($qlog,$keyword,$postObj);
			return;
		}
	}
	

	
	
	
	//看看是否想上墙
	//微网站
	$booking = new Model('z01_wall');
	$booking->find(array('wid'=>$wid,'kw'=>$keyword));
	if($booking->has_id()){
		$qlog = new Model('z01_wall_u');
		$qlog->where(array('wxid'=>$postObj->FromUserName,'wid'=>$wid,'qid'=>$booking->id))->find();
		if(!$qlog->has_id()){
			$qlog = new Model('z01_wall_u');
			$qlog->wxid = $postObj->FromUserName;
			$qlog->wid = $wid;
			$qlog->ison = '1';
			$qlog->qid = $booking->id;
			$qlog->save();
			onthewall($qlog,$keyword,$postObj,true);
		}else{
			$qlog->ison = '1';
			$qlog->save();
			response_one_nopic('微信上墙', '您已进入微信墙，点击进入摇一摇，内容上墙请直接回复。', Conf::$http_path.'wall/myyyy-'.$qlog->qid.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid, $postObj);
			return;
			//onthewall($qlog,$keyword,$postObj);
		}

		return;
	}

	// 魔盒wifi 一键上网
	if($keyword == '我要上网'){

		$pubs = new Model('pubs');
		$pubs->find(array('id'=>$wid,'isval'=>1));
		$authenset = new Model('wifimohe_authenset');
		$authenset->find(array('uid'=>$pubs->uid));
		if($authenset->has_id()){
			$lomo_msg ='<a href="http://www.mohewifi.com/wifidog/login.html?type=weixin&wxid='.$postObj->FromUserName.'&wid='.$wid.'"'.'>点击这里</a>开始上网！';
			response_text($lomo_msg,$postObj);
			return ;
		}


	}

	//全民经纪人密码找回 
	if($keyword == '找回密码' || $keyword == '重置密码' || $keyword == '重置账号密码'){
		
		$get_pass = new Model('qmjjr_member_list');
		$wxid = $postObj->FromUserName;
		$get_pass->find(array('wid'=>$wid,'wxid'=>$wxid));
		if($get_pass->has_id()){
			$get_pass->pwd = '';
			$get_pass->save();
			response_text('您的密码已清空,再次登录时，重新输入新密码！',$postObj);
			return ;
		}
	}

	// 新版魔盒wifi 开始上网
	if($keyword == '开始上网'){
		$pubs = new Model('pubs');
		$pubs->find(array('id'=>$wid,'isval'=>1));
		$authenset = new Model('mohe_authenset');
		$authenset->find(array('uid'=>$pubs->uid));
		if($authenset->has_id()){
			$lomo_msg ='<a href="http://www.mohewifi.com/wifi/login.html?type=weixin&wxid='.$postObj->FromUserName.'&wid='.$wid.'"'.'>点击这里</a>开始上网！';
			response_text($lomo_msg,$postObj);
			return ;
		}
	}




	//微网站
	$booking = new Model('wwz_keyword');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'weiweb/'.$wid.'/?&wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	//error_log(print_r($keyword,1),3,__FILE__.'.log');
	//优惠券
	$coupon = new Model('coupon');
	$coupon->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($coupon->has_id()){
		$url = Conf::$http_path.'wx/yhq-'.$coupon->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($coupon->name,$coupon->pic,$coupon->ms,$url,$postObj);
		return;
	}
		//一元夺宝

	$unitary = new Model('imicms_unitary');
	$unitary->find(array('wid'=>$wid,'keyword'=>$keyword));
	if($unitary->has_id()){
		$url = Conf::$http_path.'unitary/goods.html?wxid='.$postObj->FromUserName.'&wid='.$unitary->wid.'&unitaryid='.$unitary->id;
		response_one($unitary->name,$unitary->wxpic,$unitary->wxinfo,$url,$postObj);
		return;
	}	
	
	//优惠券2
	$coupon2 = new Model('coupon2');
	$coupon2->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($coupon2->has_id()){
		//如果活动还未开始
		if($coupon2->status ==2){
			response_text('活动还未开始，敬请关注!',$postObj);
		}
		$url = Conf::$http_path.'wx/yhj-'.$coupon2->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		//活动是否结束
		if($coupon2->status ==0){			//已经结束
			response_one($coupon2->endtitle,$coupon2->endpic,$coupon2->enddesc,$url,$postObj);
		}else{
			response_one($coupon2->name,$coupon2->startpic,$coupon2->ms,$url,$postObj);
		}
		return;
	}	
	//刮刮卡
	$ggk = new Model('ggk');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/ggk-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
		//刮刮卡
	$ggk2 = new Model('ggk2');
	$ggk2->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk2->has_id()){
		$url = Conf::$http_path.'wx/ggk2-'.$ggk2->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk2->name,$ggk2->pic,$ggk2->ms,$url,$postObj);
		return;
	}
	//圣诞
	$sdhd = new Model('sdhd');
	$sdhd->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($sdhd->has_id()){
		$url = Conf::$http_path.'wx/sdhd-'.$sdhd->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($sdhd->name,$sdhd->sdhd_pic,$sdhd->sdhd_ms,$url,$postObj);
		return;
	}
	//幸运大转盘
	$ggk = new Model('xydzp');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/xydzp-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	//新版幸运大转盘
	$ggk = new Model('newxydzp');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$param = '';
		if($ggk->type){
			$param = '&type=draw';
		}
		$url = Conf::$http_path.'xinxydzp/dzpindex-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid.$param;
		response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	

	
	
	$ggk = new Model('micro_jfxv1_active_list');
	$ggk->find(array('wid'=>$wid,'keyword'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'jfxv1/activev1-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->title,$ggk->pic,$ggk->jianjie,$url,$postObj);
		return;
	}

	//幸运机
	$ggk = new Model('xyj');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/xyj-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}

	//抢红包
	$ggk = new Model('hongbao_red');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/free.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}

	//抢免费券
	$ggk = new Model('hongbao_free');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/free.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	//抢票
	$ggk = new Model('ticket_free');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/ticket-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}

	//砸金蛋
	$ggk = new Model('zjd');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/zjd-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	//砸金蛋2.0
	$ggk = new Model('zjd_new');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'xinzjd/zjd-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->hdmc,$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	//拆礼盒
	$ggk = new Model('clh');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'chailihe/clh-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->hdmc,$ggk->pic,$ggk->twjj,$url,$postObj);
		return;
	}
	
	//分享达人
	$ggk = new Model('fxdr');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/fxdrlist.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}

	//微团购
	$ggk = new Model('micro_group_buy');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/wtg-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}

	//一战到底
	$ggk = new Model('yzdd');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/yzdd-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
			if($wid=='59078')
						$ggk->ms = '';
		
		response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	
		//一战到底2.0
	$ggk2 = new Model('yzdd2');
	$ggk2->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk2->has_id()){
		$url = Conf::$http_path.'wx/yzdd2-'.$ggk2->id.'.html?wid='.$wid;		
		$time =explode('-',$yzdd->time);
		$starttime =strtotime($time[0]);

		// $inputArray['wid'] 	   = $wid;
		// $inputArray['keyword'] = $keyword;
		// $inputArray['type']    = 'yzdd2';
		// $inputArray['pid']     = $ggk2->id;
		// $inputArray['title']   = $ggk2->name;
		// $inputArray['content'] = $ggk2->ms;
		// $inputArray['url']     = $url;
		// $inputArray['img']     = $ggk2->pic;
		// $returned = setKeyword($inputArray);
		$url .= "&wxid=".$postObj->FromUserName;
		if($starttime >= time() || $ggk2->tk ==0){			//还未开始
			response_text('活动还未开始，敬请关注!',$postObj);
		}else{
			response_one($ggk2->name,$ggk2->pic,$ggk2->ms,$url,$postObj);
		}
		return;
	}
	
	//微贺卡
	$booking = new Model('z01_hk');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'hk/hk.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	//贺卡大全
	$booking = new Model('z01_hkdq');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'heka/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	//微请帖
	$booking = new Model('w_qingtie');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'qingtie/qt-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,$booking->ms,$url,$postObj);
		return;
	}

	//微表白 @haojie
	$booking = new Model('biaobai_keyword');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'biaobai/biaobai.html?wxid='.$postObj->FromUserName.'&wid='.$wid.'&hid='.$booking->id;
		response_one($booking->name,$booking->pic,$booking->ms,$url,$postObj);
		return;
	}

	//微东海鲜 @haojie
	$booking = new Model('donghaixian_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'donghaixian/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,$booking->jianjie,$url,$postObj);
		return;
	}

	//全民经纪人
	$booking = new Model('qmjjr_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wkdz/mw.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	
	//特价房
	$booking = new Model('qmjjr_tjhouse_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'tjhouse/mw.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	
	
	
	//微驾校
	$booking = new Model('jiaxiao_keyword');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		//response_text('Hi~ 欢迎使微秀！现在您上传一张照片即可制作LOMO卡！',$postObj);
		$url = Conf::$http_path.'jiaxiao/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,$booking->ms,$url,$postObj);
		return;
	}

	//微吧
	$ggk = new Model('weiba');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$arts = array();
		$fart = array();
		$url = Conf::$http_path.'wx/weiba-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		$fart['tit'] = $ggk->ms;
		$fart['url'] = $url;
		$fart['pic'] = $ggk->pic;
		$arts[] = $fart;
		//查找话题
		$m = new Model('weiba_ht');
		$where['wid'] = $wid;
		$where['wxid'] = null;
		$subres = $m->where($where)->order('zm desc')->limit('7')->list_all();
		if(count($subres)>0){
			foreach ($subres as $re){
				$tart = array();
				//$tart['tit'] = '#'.$re->keywd.'#';
				$tart['tit'] = $re->keywd;
				$tart['url'] = Conf::$http_path.'wx/weiba-'.$ggk->id.'-'.$re->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
				$tart['pic'] = Conf::$http_path.'res/s.png?d';
				$arts[] = $tart;
			}
		}else{
			$tart = array();
			$tart['tit'] = '发起新话题';
			$tart['url'] = Conf::$http_path.'wx/weiba-'.$ggk->id.'-new.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
			$tart['pic'] = Conf::$http_path.'res/s.png?2';
			$arts[] = $tart;
		}
		response_more($arts,$postObj,1);
		return;
	}

	//会员卡
	$coupon = new Model('micro_member_card');
	$coupon->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($coupon->has_id()){
		//error_log(print_r($postObj,1),3,__FILE__.'.log');
		$url = Conf::$http_path.'wx/hyk-'.$coupon->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		//查询我的会员卡号
		$red = new Model('micro_member_card_record');
		$red->find(array('cid'=>$coupon->id,'wxid'=>$postObj->FromUserName));
		if($red->has_id()){
			response_one($coupon->name,$coupon->pic,'尊敬的会员卡用户，您的卡号为：'.$red->sn.'。'.$coupon->ms,$url,$postObj);
		}else{
			response_one($coupon->name,$coupon->pic,$coupon->ms,$url,$postObj);
		}
		return;
	}
	//新版会员卡
	$nhyk = new Model('micro_membercard_set');
	$nhyk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($nhyk->has_id()){
		if($wid=='30705')
		$url = Conf::$http_path.'nhyk1/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		
		else
		{
			$nhyklm = new Model('micro_membercard_member_list');
			$nhyklm->find(array('wid'=>$wid,'wxid'=>$postObj->FromUserName));
			
			if($nhyk->tolink==1 && $nhyklm->has_id())
			{
			     $url = Conf::$http_path.'nhyk/my.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
			}
			else
			{
			     $url = Conf::$http_path.'nhyk/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
			}
		    
		}
	

		response_one($nhyk->name,$nhyk->pic,$nhyk->jianjie,$url,$postObj);
		return;
	}

	//新版微商城 - 无锡微商城 @haojie
	$sub_sellers = is_sub_seller();
	
	$wsc = new Model('z02_set');
	$wsc->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($wsc->has_id()){
		if($wid=='38263'){
			$url = Conf::$http_path.'wsc1/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
			/* elseif($wid == '9014')
			 $url = Conf::$http_path.'wsc/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid; */
		}
		elseif($wid == '41860'){
			$url = Conf::$http_path.'wsc3/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		}elseif($wid == '3723' || $wid == '65780'){
			$url = Conf::$http_path.'wsc4/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		}elseif($wid == '3723' || $wid == '75573'){
			$url = Conf::$http_path.'wsc75573/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		}elseif($wid == '6769786'){
			$url = Conf::$http_path.'wsc_weijin/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		}
		/*elseif($wid == '9014' or $wid='17' or $wid=='53982' or in_array($wid,$sub_sellers['sub_wids'])){
			$url = Conf::$http_path.'wsc_wuxi/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
			//$url = 'http://cibyl.com.cn/wsc_wuxi/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		}*/
		else{
			$url = Conf::$http_path.'wsc2/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		}

		response_one($wsc->name,$wsc->pic,$wsc->jianjie,$url,$postObj);
		return;
	}
	//新版微团购
	$wtg = new Model('z02_tuanset');
	$wtg->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($wtg->has_id()){
		$url = Conf::$http_path.'wsc2/tuan.html?mm=1&wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($wtg->name,$wtg->pic,$wtg->jianjie,$url,$postObj);
		return;
	}
	//新版微餐饮
	$wcy = new Model('micro_wcy_set');
	$wcy->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($wcy->has_id()){
		$url = Conf::$http_path.'wcy/index1.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($wcy->name,$wcy->pic,$wcy->jianjie,$url,$postObj);
		return;
	}

	//分销
	$lkh = new Model('laikehu_dist_shop');
	$lkh->find(array('wid'=>$wid,'keywords'=>$keyword));
	// if($wid=='6765368'){6765368
	// 	log::warn('laikehu_dist_shop_____'.$keyword.'--'.$wid);
	// }
	if($lkh->has_id()){
		$url = $lkh->url.'&fromuser='.$postObj->FromUserName;
		response_one($lkh->title,$lkh->thumb,$lkh->description,$url,$postObj);
		return;
	}

	//微调研
	$ggk = new Model('micro_survey');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/wdy-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	//微投票
	$ggk = new Model('micro_vote');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/wtp-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	//微投票2.0
	$ggk = new Model('micro_vote_up');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/wtp_up-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	//微投票3.0
	
	$ggk = new Model('micro_wtpv3_active_list');
	$ggk->find(array('wid'=>$wid,'keyword'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wtp/index-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		if($wid==6487){
			$url = Conf::$http_path.'wtp1/index-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		}
		response_one($ggk->title,$ggk->pic,$ggk->jianjie,$url,$postObj);
		return;
	}

	
	
	
	//在线预订
	$booking = new Model('online_booking');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wx/onlineBooking-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	//新版预约
// 	if($wid=='3723'){

		$booking = new Model('newyy');
		$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
		if($booking->has_id()){
			$url = Conf::$http_path.'yuyue2/yy-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
			response_one($booking->tit,$booking->pic,$booking->des,$url,$postObj);
			return;
		}
// 	}else{
// 		$booking = new Model('newyy');
// 		$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
// 		if($booking->has_id()){
// 			$url = Conf::$http_path.'yuyue/yy-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
// 			response_one($booking->tit,$booking->pic,$booking->des,$url,$postObj);
// 			return;
// 		}
// 	}

	//微相册
	$booking = new Model('micro_photo_list');
	$booking->find(array('wid'=>$wid,'keyword'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wx/wxclist-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->artpic,$booking->ms,$url,$postObj);
		return;
	}
	//微房产
	$booking = new Model('micro_estate_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wfc/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,'',$url,$postObj);
		return;
	}
	//微健身
	$booking = new Model('micro_jianshen_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wjs/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,'',$url,$postObj);
		return;
	}
	//微地图
	$booking = new Model('map_keyword');
	$booking->find(array('wid'=>$wid,'keyword'=>$keyword));
	if($booking->has_id()){

		$url = Conf::$http_path.'ditu/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,$booking->desc,$url,$postObj);
		return;
	}

	
				//订制 微龙变频帮
	$weilong = new Model('weilong_set');
	$weilong->find(array('wid'=>$wid,'keyword'=>$keyword));
	
	if($weilong->has_id()){
		$weilong_user = new Model('weilong_user_list');
		$weilong_user->where(array('wxid'=>$postObj->FromUserName))->find();
			//获取access_token
		if(Cache::has($wid.'access_token')){
			$access_token = Cache::get($wid.'access_token');
		}else{
			$appid = "wxd682e340aec08524";//
			$appsecret = "201ee7d868fd20f7dead35cd5b7ca414";//
				
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
				
				
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);

			$jsoninfo = json_decode($output, true);
			$access_token = $jsoninfo["access_token"];
			Cache::set($wid.'access_token', $access_token,7000);
		}
		
		$url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$postObj->FromUserName&lang=zh_CN";
		$result  = HttpCurl::quickpost($url);
		$jsoninfo = json_decode($result,true);
		if(!$weilong_user->has_id()){
			$data['wxid'] =$postObj->FromUserName;
			$data['wid'] =$wid;
			$data['uickname'] =$jsoninfo["nickname"];
			$data['uheadimgurl'] =$jsoninfo["headimgurl"];
			$data['utime'] =time();
			
			$weilong_user->save($data);

		}
		
		$url = Conf::$http_path.'weilong/index.html?hid='.$weilong->id."&wxid=".$postObj->FromUserName;
		response_one($weilong->title,$weilong->pic,$weilong->desc,$url,$postObj);
		return;
	}
	//微旅游
	$booking = new Model('micro_lvyou_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wlvy/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,'',$url,$postObj);
		return;
	}
	//微美容
	$booking = new Model('micro_meirong_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wmr/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,'',$url,$postObj);
		return;
	}
	//微政务
	$booking = new Model('micro_zhengwu_set');
	$booking->find(array('wid'=>$wid,'or' => array('gjz'=>$keyword,'zixun_gjz' => $keyword,"tousu_gjz" => $keyword,"jianyi_gjz" =>$keyword)));
	if($booking->has_id()){
		if(($booking->gjz)==$keyword){
			$url = Conf::$http_path.'wzw/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
			$name = $booking->name;
			response_one($name,$booking->pic,'',$url,$postObj);
		}elseif(($booking->zixun_gjz)==$keyword){
			$url = Conf::$http_path.'wzw/zxqzadd.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
			$name = $booking->name."_咨询求助";
			response_one($name,$booking->pic,'',$url,$postObj);
		}elseif(($booking->tousu_gjz)==$keyword){
			$url = Conf::$http_path.'wzw/tousuadd.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
			$name = $booking->name."_投诉举报";
			response_one($name,$booking->pic,'',$url,$postObj);
		}elseif(($booking->jianyi_gjz)==$keyword){
			$url = Conf::$http_path.'wzw/remarkadd.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
			$name = $booking->name."_建议献策";
			response_one($name,$booking->pic,'',$url,$postObj);
		}else{
			$url = Conf::$http_path.'wzw/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
			$name = $booking->name;
			response_one($name,$booking->pic,'',$url,$postObj);
		}
	}
	//中秋节活动添加 start
	if(strval($wid)=='3702'&&strval($keyword)=='815'){
		$g_wid=new Model('pubs');
		$g_wid->where ( array ('id' => $wid) )->find();
		if($g_wid->cusid&&$g_wid->cussec){
			if(Cache::get($wid.$g_wid->cusid)){
					
				$token=Cache::get($wid.$g_wid->cusid);
				$user_info=file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$token."&openid=".$postObj->FromUserName."&lang=zh_CN");
				$user_info=json_decode($user_info);
				//获取用户信息
				$game_user=new Model('js_game_user');
				$game_user->where ( array ('wxid' =>$postObj->FromUserName,'type'=>"zhongqiu") )->find();
				$headimg="input-file";
				if(!$game_user->$headimg){
					$game_user->$headimg=$user_info->headimgurl;
					$game_user->name=$user_info->nickname;
					$game_user->wxid=$postObj->FromUserName;
					$typ="type";
					$game_user->$typ="zhongqiu";
					$game_user->wid=$wid;
					$game_user->save();
				}
			}else{
				$acc_token=file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$g_wid->cusid."&secret=".$g_wid->cussec);
				$acc_token=json_decode($acc_token);
				if($acc_token->access_token){
					Cache::set($wid.$g_wid->cusid,$acc_token->access_token,3600);
					$token=Cache::get($wid.$g_wid->cusid);
					$user_info=file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$token."&openid=".$postObj->FromUserName."&lang=zh_CN");
					$user_info=json_decode($user_info);
					//获取用户信息
					$game_user=new Model('js_game_user');
					$game_user->where ( array ('wxid' =>$postObj->FromUserName,'type'=>"zhongqiu") )->find();
					$headimg="input-file";
					if(!$game_user->$headimg){
						$game_user->name=$user_info->nickname;
						$game_user->$headimg=$user_info->headimgurl;
						$game_user->wxid=$postObj->FromUserName;
						$typ="type";
						$game_user->$typ="zhongqiu";
						$game_user->wid=$wid;
						$game_user->save();
					}
				}
			}


		}
	}
	//中秋节活动添 end////////
	//微物业
	$booking = new Model('micro_wuye_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wwy/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,'',$url,$postObj);
		return;
	}
	//微社区
	$booking = new Model('micro_bbs_set');
	$booking->find(array('wid'=>$wid,'keyword'=>$keyword));
	if($booking->has_id()){
	    /*if($wid==3702)
		{
		  $url = Conf::$http_path.'wsqv2/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		}
		else
		*/
		$url = Conf::$http_path.'wsqv2/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->title,$booking->fm_pic,$booking->fm_info,$url,$postObj);
		return;
	}
	//微分享
	$booking = new Model('micro_fenxiang_set');
	$booking->find(array('wid'=>$wid,'or'=>array('gjz'=>$keyword,'keyword'=>$keyword)));
	if($booking->has_id()){
		if(($booking->gjz)==$keyword){
			$url = Conf::$http_path.'wx/fenxiang.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
			response_one($booking->name,$booking->pic,$booking->jianjie,$url,$postObj);
		}elseif(($booking->keyword)==$keyword){
			$url = Conf::$http_path.'wx/fx_list.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
			response_one($booking->name,$booking->pic,$booking->jianjie,$url,$postObj);
		}
	}
	//微ktv
	$booking = new Model('micro_ktv_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'ktv/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,'',$url,$postObj);
		return;
	}
	
	
	//微教育
	$booking = new Model('micro_jiaoyu_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'jiaoyu/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,'',$url,$postObj);
		return;
	}
	//微酒吧
	$booking = new Model('micro_jiuba_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'jiuba/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,'',$url,$postObj);
		return;
	}
	//微花店
	$booking = new Model('micro_huadian_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'huadian/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,'',$url,$postObj);
		return;
	}
	//微婚庆
	$booking = new Model('micro_hunqing_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'hunqing/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,'',$url,$postObj);
		return;
	}
	//微装修
	$booking = new Model('micro_zhuangxiu_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'zhuangxiu/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,'',$url,$postObj);
		return;
	}
	//微汽修
	$booking = new Model('micro_qixiu_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'qixiu/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,'',$url,$postObj);
		return;
	}
	//微食品
	$booking = new Model('micro_shipin_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'shipin/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,'',$url,$postObj);
		return;
	}
	//360全景
	$qunjing = new Model('360_full_view');
	$qunjing->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($qunjing->has_id()){
		$url = Conf::$http_path.'qj/qj-'.$qunjing->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($qunjing->name,$qunjing->pic,'',$url,$postObj);
		return;
	}
	//微生活
	$ggk = new Model('micro_life_set');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'life/life.html?wid='.$wid;
		response_one($ggk->name,$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	//微生活中的商户管理
	$ggk = new Model('micro_life_shop');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		if($ggk->url != ''){
			$url = $ggk->url;
		}else{
			$url = Conf::$http_path.'life/story-'.$ggk->id.'.html';
		}
		response_one($ggk->title,$ggk->pic,$ggk->jianjie,$url,$postObj);
		return;
	}

	//微餐饮
	$booking = new Model('micro_canyin_keyword');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	$shopstyle = new Model('ai9me_shop_style');
	$shopstyle->find(array('token'=>$wid));

	if($booking->has_id()){
		$theme = intval($shopstyle->s_show); //皮肤
		$show = intval($shopstyle->s_style); //显示格式
		/*if($show==1)
		 {
		 $action = 'cats';
		 }
		 elseif($show==2)
		 {
		 $action = 'products';
		 }
		 else
		 {*/
		$action = 'index';
		//}

		$url = Conf::$http_path.'admin2/index.php?g=Wap&m=Product&a='.$action.'&wxid='.$postObj->FromUserName.'&wid='.$wid.'&dining=1&theme='.$theme.'&show='.$show;
		response_one($booking->name,$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	//微商城
	$booking = new Model('micro_shop_keyword');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	$shopstyle = new Model('ai9me_shop_style');
	$shopstyle->find(array('token'=>$wid));

	if($booking->has_id()){
		$theme = intval($shopstyle->s_show); //皮肤
		$show = intval($shopstyle->s_style); //显示格式
		if($show==1)
		{
			$action = 'cats';
		}
		elseif($show==2)
		{
			$action = 'products';
		}
		else
		{
			$action = 'index';
		}
		$url = Conf::$http_path.'admin2/index.php?g=Wap&m=Product&a='.$action.'&wxid='.$postObj->FromUserName.'&wid='.$wid.'&theme='.$theme.'&show='.$show;
		response_one($booking->name,$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	//微喜帖
	$booking = new Model('micro_xitie_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wx/wXiTie-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->art_pic,'',$url,$postObj);
		return;
	}
	

	//新微投票
	$booking = new Model('life_votes_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'votes/index-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic1,'',$url,$postObj);
		return;
	}
	
	
    //
	$booking = new Model('game_dabai_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'dabai/index-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,'',$url,$postObj);
		return;
	}
	
	//微场景
	$booking = new Model('micro_chang');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'chang/index-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,$booking->dsc,$url,$postObj);
		return;
	}
	//微酒店
	$booking = new Model('micro_hotel');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wjd1/index-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		//$url = Conf::$http_path.'wjd/index-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		//response_one($booking->tit,$booking->pic,$booking->des,$url,$postObj);
		response_one($booking->tit,$booking->pic,'',$url,$postObj);
		return;
	}
	//天目微酒店
	$booking = new Model('tian_hotel');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wjd2/index-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		//$url = Conf::$http_path.'wjd/index-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		//response_one($booking->tit,$booking->pic,$booking->des,$url,$postObj);
		response_one($booking->tit,$booking->pic,'',$url,$postObj);
		return;
	}
	
	
	//微留言
	$booking = new Model('liuyan_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wly/ly-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->tit,$booking->pic,'',$url,$postObj);
		return;
	}
	
	//微医疗
	$booking = new Model('micro_yiliao_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'weiweb/'.$wid.'/'.$booking->xwid.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,$booking->jianjie,$url,$postObj);
		return;
	}

	//微汽车
	$booking = new Model('micro_car_keyword');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){

		$url = Conf::$http_path.'weiweb/'.$wid.'/'.$booking->xwid.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	//车主关怀
	$booking = new Model('micro_car_guanhuai');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wqc/guanhuai.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	//预约试驾
	$booking = new Model('micro_car_yysj');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wqc/yysj.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->tit,$booking->pic,$booking->des,$url,$postObj);
		return;
	}
	//预约保养
	$booking = new Model('micro_car_yyby');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wqc/yyby.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->tit,$booking->pic,$booking->des,$url,$postObj);
		return;
	}
	
	//定制 必胜客红酒预约
	$booking = new Model('dz_mdset6768305');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'bsk/bsk.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,$booking->jianjie,$url,$postObj);
		return;
	}
	
	//wifi
	$booking = new Model('wifi_keyword');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wifi/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,$booking->pic,$booking->ms,$url,$postObj);
		return;
	}



	//微打印-设置启动命令
	$booking = new Model('micro_wprint_keword');
	$booking->find(array('wid'=>$wid,'keyword'=>$keyword));
	if($booking->has_id()){
		Session::set('is_print',1);
		response_text('Hi~ 欢迎使微秀！现在您上传一张照片即可制作LOMO卡！',$postObj);
		return;
	}

    //魔盒微信登陆api
    
	$mh_wx_login_api = new Model('mh_wx_login_api');
	$mh_wx_login_api->find(array('wid'=>$wid,'word'=>$keyword));
	if($mh_wx_login_api->has_id()){
		$lomo_msg ='<a href="http://www.mohewifi.com/wifi/login.html?type=weixin&wxid='.$postObj->FromUserName.'&wid='.$wid.'"'.'>点击这里</a>开始上网！';
		response_text($lomo_msg,$postObj);
		return ;
	}




	//微游戏
	$booking = new Model('js_game_keyword');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		//cusid cussec
		$g_wid=new Model('pubs');
		$g_wid->where ( array ('id' => $wid) )->find();
		if($g_wid->cusid&&$g_wid->cussec){
			if(Cache::get($wid.$g_wid->cusid)){
				$token=Cache::get($wid.$g_wid->cusid);
				$user_info=file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$token."&openid=".$postObj->FromUserName."&lang=zh_CN");
				$user_info=json_decode($user_info);
				//获取用户信息
				$game_user=new Model('js_game_user');
				$game_user->where ( array ('wxid' =>$postObj->FromUserName) )->find();
				$headimg="input-file";
				if(!$game_user->$headimg && $booking->typ!='nuannan'){
					$game_user->$headimg=$user_info->headimgurl;
					$game_user->wxid=$postObj->FromUserName;
					$typ="type";
					$game_user->$typ=$booking->typ;
					$game_user->wid=$wid;
					$game_user->save();
				}
			}else{
				$acc_token=file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$g_wid->cusid."&secret=".$g_wid->cussec);
				$acc_token=json_decode($acc_token);
				if($acc_token->access_token){
					Cache::set($wid.$g_wid->cusid,$acc_token->access_token,3600);
					$token=Cache::get($wid.$g_wid->cusid);
					$user_info=file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$token."&openid=".$postObj->FromUserName."&lang=zh_CN");
					$user_info=json_decode($user_info);
					//获取用户信息
					$game_user=new Model('js_game_user');
					$game_user->where ( array ('wxid' =>$postObj->FromUserName) )->find();
					$headimg="input-file";
					if(!$game_user->$headimg && $booking->typ!='nuannan'){
						$game_user->$headimg=$user_info->headimgurl;
						$game_user->wxid=$postObj->FromUserName;
						$typ="type";
						$game_user->$typ=$booking->typ;
						$game_user->wid=$wid;
						$game_user->save();
					}
				}
			}


		}
		if($booking->typ=='queqiao'||$booking->typ=='shenjingmao'||$booking->typ=='nuannan'){
			$url = Conf::$http_path.$booking->typ.'/index.html?&wxid='.$postObj->FromUserName.'&wid='.$wid;
		}else{
			$url = Conf::$http_path.'jsgame/'.$booking->typ.'/index.html?&wxid='.$postObj->FromUserName.'&wid='.$wid;
		}
		response_one($booking->name,$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	
	//dongran  首先完全匹配网站设置的关键字
	$key_word = new Model('key_word');
	$kkres1 = $key_word->where(array('pubsId'=>$wid ,'keyWord'=>$keyword))->find();
		if ($kkres1->id)
		{
			check_and_replay_keyword($kkres1,$postObj);
			return;
		}
	

	$widmodel = new Model("pubs");
	$widmodel->find(array('id'=>$wid));
	if ($widmodel->othertype==1 && !empty($widmodel->otherurl))
	{
		if ($postObj->Event=="CLICK")
		{
			$postObj->Content=$keyword;
			$postObj->MsgType='text';
		}
		$post="ToUserName=".$postObj->ToUserName."&FromUserName=".$postObj->FromUserName."&CreateTime=".$postObj->CreateTime."&MsgType=".$postObj->MsgType."&Content=".$postObj->Content."&MsgId=".$postObj->MsgId;
		log::warn('===============================data2:'.$post);
		$data = curlpost($post, $widmodel->otherurl);
		if ($data)
		{
            //log::warn('===============================data1:'.$data);
			global $pc,$timeStamp, $nonce, $encryptMsg;
			$pc->encryptMsg($data, $timeStamp, $nonce, $encryptMsg);
		}
	}

	//匹配关键词
	$kkres = $key_word->where(array('pubsId'=>$wid))->list_all();
	foreach ($kkres as $kk){
		$kkarr = $kk->keyWord.'';
		$kkarr = str_replace('，', ',', $kkarr);
		$kkarr = explode(',', $kkarr);

		if(in_array($keyword, $kkarr,true)){
			check_and_replay_keyword($kk,$postObj);
			return;
		}
	}

	foreach ($kkres as $kk){
		$kkarr = $kk->keyWord.'';
		$keytype = $kk->matchingType;
		if($keytype=='1'){
			$kkarr = str_replace('，', ',', $kkarr);
			$kkarr = explode(',', $kkarr);
			foreach ($kkarr as $tkw){
				if(strpos($keyword, $tkw)!==false){
					check_and_replay_keyword($kk,$postObj);
					return;
				}
				if(strpos($tkw, $keyword)!==false){
					check_and_replay_keyword($kk,$postObj);
					return;
				}
			}
		}

	}
	//匹配智能客服
	//智能客服(调教)
	$myans = new Model('my_answer');
	$myans->find(array('question'=>$keyword,'wid'=>$wid));
	if($myans->has_id()){
		response_text($myans->answer,$postObj);
		return;
	}

	$cs = new Model('customer_service');
	$cs->find(array('wid'=>$wid));
	$nc = trim($cs->name);
	$nc = $nc==''?'小宝':$nc;
	if($cs->tianqi=='1' && strpos($keyword,'天气')!==false){
		$week = array('0'=>'星期天','1'=>'星期一','2'=>'星期二','3'=>'星期三','4'=>'星期四','5'=>'星期五','6'=>'星期六');
		$w    = date('w');
		$week1 = $week[$w];
		$date  = date("Y年m月d日").' '.$week1."\n";
			
		$keyword1 = str_replace('天气', '',$keyword );
		$keyword1 = str_replace('市', '',$keyword1 );
		include another('city');
		$city1 = $city[$keyword1];
		if(!$city1){
			response_text('您要查询的地区不存在',$postObj);
			return;
		}
			
		$result1 = file_get_contents("http://www.weather.com.cn/data/cityinfo/$city1.html");
		$result2 = file_get_contents("http://www.weather.com.cn/data/sk/$city1.html");
		$res1    = json_decode($result1);
		$res2    = json_decode($result2);
			
		$weather = $res2->weatherinfo->WD.$res2->weatherinfo->WS.'湿度'.$res2->weatherinfo->SD.$res1->weatherinfo->weather.$res1->weatherinfo->temp1.'~'.$res1->weatherinfo->temp2."\n".'当前温度'.$res2->weatherinfo->temp.'℃'."\n".'预报时间:'.$res2->weatherinfo->time;
		response_text($keyword."\n\r".$date.$weather,$postObj);
		return;
	}
	if($cs->translate=='1' && strpos($keyword,'@')===0){
		$keyword = str_replace('@', '翻译', $keyword);
		//翻译
		$res = HttpClient::quickGet('http://api.ajaxsns.com/api.php?key=free&appid=0&msg='.urlencode($keyword),3);
		if(!empty($res)){
			$res = json_decode($res);
			$res = str_replace('{br}', "\n\r", $res->content);
			response_text($res,$postObj);
			return;
		}
	}
	if($cs->cangts=='1' && strpos($keyword,'藏头诗')!==false){
		$res = HttpClient::quickGet('http://api.ajaxsns.com/api.php?key=free&appid=0&msg='.urlencode($keyword),3);
		if(!empty($res)){
			$res = json_decode($res);
			$res = str_replace('{br}', "\n\r", $res->content);
			response_text($res,$postObj);
			return;
		}
	}

	if($cs->xiaohua=='1' && $keyword=='笑话'){
		$res = HttpClient::quickGet('http://api.ajaxsns.com/api.php?key=free&appid=0&msg='.urlencode($keyword),3);
		if(!empty($res)){
			$res = json_decode($res);
			$res = str_replace('{br}', "\n\r", $res->content);
			response_text($res,$postObj);
			return;
		}
	}

	//彩票
	if($cs->caipiao=='1' && strpos($keyword,'彩票')===0){
		$keyword = str_replace('彩票', '', $keyword);
		$res = HttpClient::quickGet('http://api2.sinaapp.com/search/lottery/?appkey=0020130430&appsecert=fa6095e113cd28fd&reqtype=text&keyword='.urlencode($keyword),3);
		if(!empty($res)){
			$res = json_decode($res);
			$res = str_replace('{br}', "\n\r", $res->text->content);
			response_text($res,$postObj);
			return;
		}
	}

	//计算
	if($cs->jisuan=='1' && strpos($keyword,'计算')===0){
		if($keyword !='计算'){
			$keyword = str_replace('计算', '', $keyword);
		}
		$res = HttpClient::quickGet('http://api.ajaxsns.com/api.php?key=free&appid=0&msg='.urlencode($keyword),3);
		if(!empty($res)){
			$res = json_decode($res);
			$res = str_replace('{br}', "\n\r", $res->content);
			response_text($res,$postObj);
			return;
		}
	}

	//股票
	if($cs->gupiao=='1' && strpos($keyword,'股票')===0){
		$keyword = str_replace('股票', '', $keyword);
		$res = HttpClient::quickGet('http://api2.sinaapp.com/search/stock/?appkey=0020130430&appsecert=fa6095e113cd28fd&reqtype=text&keyword='.urlencode($keyword),3);
		if(!empty($res)){
			$res = json_decode($res);
			$res = str_replace('{br}', "\n\r", $res->text->content);
			response_text($res,$postObj);
			return;
		}
	}

	//人品
	if($cs->renpin=='1' && strpos($keyword,'人品')===0){
		$keyword = str_replace('人品', '', $keyword);
		$rp = renpin($keyword,$fs);
		response_text('【'.$keyword.'的人品:'.$fs.'】'."\r\n".renpin($keyword)."\n\r人品内容纯属虚构，仅供娱乐之用，切勿当真！",$postObj);
		return;
	}
	//火车
	if($cs->huoche=='1' && strpos($keyword,'火车')===0){
		$keyword = str_replace('火车', '', $keyword);
		$keyword = explode('到', $keyword);
		if(count($keyword)!=2){
			response_text('输入有误',$postObj);
			return;
		}
		$res = HttpClient::quickGet("http://www.twototwo.cn/train/Service.aspx?format=json&action=QueryTrainScheduleByTwoStation&key=5da453b2-b154-4ef1-8f36-806ee58580f6&startStation=" . urlencode($keyword[0]) . "&arriveStation=" . urlencode($keyword[1]) . "&startDate=" . date('Y') . "&ignoreStartDate=0&like=1&more=0",3);
		$str = '';
		if ($res) {
			try{
				$data = json_decode($res);
				$main = $data->Response->Main->Item;
				if (count($main) > 10) {
					$conunt = 10;
				} else {
					$conunt = count($main);
				}
				for ($i = 0; $i < $conunt; $i++) {
					$str .= "\n【车次】" . $main[$i]->CheCiMingCheng . "\n【类型】" . $main[$i]->CheXingMingCheng . "\n【发车时间】:　" . $main[$i]->FaShi . "\n【耗时】" . $main[$i]->LiShi . ' 小时';
					$str .= "\n----------------------";
				}
			}catch(Exception $e){
				response_text('输入有误',$postObj);
				return;
			}
		} else {
			$str = '没有找到 ' . $keyword[0] . ' 至 ' . $keyword[1] . ' 的列车';
		}
		response_text($str,$postObj);
		return;
	}



	//快递
	if($cs->kuaidi=='1' && strpos($keyword,'快递')===0){
		$keyword = str_replace('快递', '', $keyword);
		$keyword = explode('@', $keyword);
		$res = HttpClient::quickGet('http://www.weinxinma.com/api/index.php?m=Express&a=index&name='.urlencode($keyword[0]).'&number='.$keyword[1],3);
		if(!empty($res)){
			$res = str_replace('{br}', "\n\r", $res);
			response_text($res,$postObj);
			return;
		}
	}
	//百科
	if($cs->baike=='1' && strpos($keyword,'百科')===0){
		$keyword = mb_substr($keyword, 2);
		$name_gbk         = iconv('utf-8', 'gbk', $keyword);
		$encode           = urlencode($name_gbk);
		$url              = 'http://baike.baidu.com/list-php/dispose/searchword.php?word=' . $encode . '&pic=1';
		$get_contents_gbk     = HttpClient::quickGet($url);
		preg_match("/URL=(\S+)'>/s", $get_contents_gbk, $out);
		$real_link     = 'http://baike.baidu.com' . $out[1];
		$get_contents2 = HttpClient::quickGet($real_link);
		preg_match('#"Description"\scontent="(.+?)"\s\/\>#is', $get_contents2, $matchresult);
		if (isset($matchresult[1]) && $matchresult[1] != "") {
			response_text(htmlspecialchars_decode($matchresult[1]),$postObj);
		} else {
			response_text("抱歉，没有找到与“" . $keyword . "”相关的百科结果。",$postObj);
		}
		return;
	}
	//新闻
	if($cs->xinwen=='1' && $keyword=='新闻'){
		$rss = RSS::Parse('http://news.qq.com/newsgn/rss_newsgn.xml');
		$num = 0;
		$res = array();
		foreach ($rss['items'] as $it){
			if($num>8){
				break;
			}
			$res[] = array('tit'=>$it['title'],'pic'=>Conf::$http_path.'res/s.png?df','url'=>$it['link'],'ms'=>$it['description']);
			$num++;
		}
		response_more($res,$postObj);
		return;
	}
	//huangli
	if($cs->huangli=='1' && ($keyword=='日历' || $keyword=='黄历' || $keyword=='万年历' || $keyword=='几号')){
		response_text(Lunar::today(),$postObj);
		return;
	}



	//$key_word = new Model('key_word');
	//$key_word->find(array('keyWord@~'=>$keyword,'pubsId'=>$wid));
	//智能聊天
	if($cs->autoans == '1'){
		$key_word = new Model('key_word');
		$key_word->find(array('keyWord'=>$keyword,'pubsId'=>$wid));
		if(!$key_word->has_id()){
			$from ='123';
			$to ='123';
			$xiaojiu = xiaojiu($keyword,$from,$to);
			response_text($xiaojiu,$postObj);
			return;
		}
	}



	//匹配"*"关键词
	$key_word = new Model('key_word');
	$key_word->find(array('keyWord'=>'*','pubsId'=>$wid));
	if($key_word->has_id()){
		check_and_replay_keyword($key_word,$postObj);
		return;
	}
	//都没有匹配上则不回答

}


/**以下智能聊天方法--开始**/
function media($content) //多媒体转换
{
	if(strstr($content,'murl')){//音乐
		$a=array();
		foreach (explode('#',$content) as $content)
		{
			list($k,$v)=explode('|',$content);
			$a[$k]=$v;
		}
		$content = $a;
	}
	elseif(strstr($content,'pic'))//多图文回复
	{
		$a=array();
		$b=array();
		$c=array();
		$n=0;
		$contents = $content;
		foreach (explode('@t',$content) as $b[$n])
		{
			if(strstr($contents,'@t'))
			{
				$b[$n] = str_replace("itle","title",$b[$n]);
				$b[$n] = str_replace("ttitle","title",$b[$n]);
			}

			foreach (explode('#',$b[$n]) as $content)
			{
				list($k,$v)=explode('|',$content);
				$a[$k]=$v;
				$d.= $k;
			}
			$c[$n] = $a;
			$n++;

		}
		$content = $c ;
	}
	return $content;
}


function setKeyword($Array){
	$keywordModel = new Model('micro_keyword');
	$keywordModel->find(array('wid'=>$Array['wid'],'keyword'=>$Array['keyword'],'type'=>$Array['type'],'pid'=>$Array['pid']));
	if (!$keywordModel->has_id()) {
		$keywordModel->wid = $Array['wid'];
		$keywordModel->keyword = $Array['keyword'];
		$keywordModel->type = $Array['type'];
		$keywordModel->img = $Array['img'];
		$keywordModel->title = $Array['title'];
		$keywordModel->content = $Array['content'];
		$keywordModel->url = $Array['url'];
		$keywordModel->pid = $Array['pid'];
		$keywordModel->created_at = time();
		if ($keywordModel->save()) {
			return 'success';
		}return '关键词插入失败';
	}return '关键词已存在';
}

function curlpost($curlPost,$url) //curl post 函数
{
	$this_header = array("content-type: application/x-www-form-urlencoded; charset=UTF-8");
	$ch = curl_init();//初始化curl
	curl_setopt($ch, CURLOPT_HTTPHEADER, $this_header);
	curl_setopt($ch,CURLOPT_URL,$url);//抓取指定网页
	curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
	curl_setopt($ch, CURLOPT_NOSIGNAL,1);    //注意，毫秒超时一定要设置这个
	curl_setopt($ch, CURLOPT_TIMEOUT_MS,200);  //超时毫秒，cURL 7.16.2中被加入。从PHP 5.2.3起可使用

	curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
	curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
	$data = curl_exec($ch);//运行curl
	$curl_errno = curl_errno($ch);
	$curl_error = curl_error($ch);
	curl_close($ch);
	if($curl_errno >0){
		return null;
	}else{
		return $data;
	}
	//return $data;
}







function xiaojiu($key,$from,$to) //小九接口函数，该函数可通用于其他程序
{
	global $yourdb,$yourpw;
	$key=urlencode($key);
	$yourdb=urlencode($yourdb);
	$from=urlencode($from);
	$to=urlencode($to);
	$post="chat=".$key."&db=".$yourdb."&pw=".$yourpw."&from=".$from."&to=".$to;
	$api = "http://www.xiaojo.com/bot/chata.php";
	$replys = curlpost($post,$api);
	$reply  = media(urldecode($replys));//多媒体转换
	$mongo = MongoConn::collection('wxgj','data');
	$rows = $mongo->find(array("ask" =>$key));
	//$arr = array();
	foreach ($rows as $k => $v) {
		//$arr[]=$v;
		return $v['reply'];
		break;
	}
	if (!$reply){
		if($re ==''){
			$content = array(
			     'ask'=>$key,
			     'reply'=>$reply,
			);
			$mongo->insert($content);

		}else{
			$reply = $re;
		}
	}else{
		$reply = $re;
	}


	//收集聊天数据
	/*    if(!$reply==''){
	 $mongo = MongoConn::collection('wxgj','data');
		$rows = $mongo->find(array("ask" =>$key));
		$arr = array();
		foreach ($rows as $k => $v) {
		$arr[]=$v;
		}
		if(empty($arr)){
		$content = array(
		'ask'=>$key,
		'reply'=>$reply,
		);
		$mongo->insert($content);
		}else{
		$reply = $arr[0]['reply'];
		}
		}*/

	return $reply;
}
/**以上智能聊天方法--结束**/

function check_and_replay_keyword($key_word,$postObj){
	global $wid;
	$rtyp = $key_word->replyType;
	if($rtyp=='1'){
		if($wid=="17")
		{
			error_log(print_r("3333" ,1) ,3 ,"upload/res.log");
				
		}
		$con = $key_word->replyContent;
		$str = str_replace("&gt;", ">",$con);
		$str = str_replace("&lt;", "<",$str);
		//	$str = str_replace("\n\r", " ; ", $str);
		response_text($str,$postObj);
		

	}else{
		$res = new Model('res');
		$res->find($key_word->resId);
		response_arts($res,$postObj);
	}
}
function checkUrl($url)
{
	if (preg_match('/^(http):/i', $url))
	{
		return $url;
	}
	else
	{
		return Conf::$http_path.$url;
	}
}

/*======================================新版start==========================================*/


//回复文本
function response_text($txt,$postObj){
	$fromUsername = $postObj->FromUserName;
	$toUsername = $postObj->ToUserName;
	$textTpl = "<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[%s]]></MsgType>
	<Content><![CDATA[%s]]></Content>
	<FuncFlag>0</FuncFlag>
	</xml>";
	$res = sprintf($textTpl, $fromUsername, $toUsername, time(), "text", htmlspecialchars_decode(trim($txt)));
	//Log::error($res);
	global $pc,$timeStamp, $nonce, $encryptMsg;
	$pc->encryptMsg($res, $timeStamp, $nonce, $encryptMsg);
	//echo $res;
}

function response_arts($res,$postObj){
	$r = json_decode($res->con);
	if(is_array($r)){
		response_morearts($r,$res->id,$postObj);
	}else{
		response_oneart($r,$res->id,$postObj);
	}
}
//回复单图文
function response_oneart($r,$rid,$postObj){
	global $wid;
	$fromUsername = $postObj->FromUserName;
	$toUsername = $postObj->ToUserName;
	$textTpl = "<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[%s]]></MsgType>
	<ArticleCount>%s</ArticleCount>
	<Articles>
	ITEM
	</Articles>
	</xml>";
	$resstr =  sprintf($textTpl, $fromUsername, $toUsername, time(), "news", 1);				
	$subitem = "<item>
	<Title><![CDATA[%s]]></Title>
	<Description><![CDATA[%s]]></Description>
	<PicUrl><![CDATA[%s]]></PicUrl>
	<Url><![CDATA[%s]]></Url>
	</item>";
	$addpos = '?';
	if(!$r->url){
		$r->url = $r->ourl;
	}
	if(strpos($r->url, '?')!==false){
		$addpos = '&';
	}
	$r->url = $r->url.$addpos.'wxid='.$fromUsername.'&wid='.$wid.'&rid='.$rid;
	$r->url = $r->url.'#mp.weixin.qq.com';
	$item=sprintf($subitem, str_replace('{name}',$postObj->nickname,$r->tit), str_replace('{name}',$postObj->nickname,$r->des), $r->pic, $r->url);
	$resstr = str_replace('ITEM', $item, $resstr);
	global $pc,$timeStamp, $nonce, $encryptMsg;
	$ss=$pc->encryptMsg($resstr, $timeStamp, $nonce, $encryptMsg);
	
}
//回复多图文
function response_morearts($res,$rid,$postObj){
	global $wid;
	$fromUsername = $postObj->FromUserName;
	$toUsername = $postObj->ToUserName;
	$textTpl = "<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[%s]]></MsgType>
	<ArticleCount>%s</ArticleCount>
	<Articles>
	ITEM
	</Articles>
	</xml>";
	$resstr =  sprintf($textTpl, $fromUsername, $toUsername, time(), "news", count($res));
	$item = '';
	$subitem = "<item>
	<Title><![CDATA[%s]]></Title>
	<PicUrl><![CDATA[%s]]></PicUrl>
	<Url><![CDATA[%s]]></Url>
	</item>";
	foreach ($res as $r){

		if(!$r->url){
			$r->url = $r->ourl;
		}
		$addpos = '?';
		if(strpos($r->url, '?')!==false){
			$addpos = '&';
		}
		$r->url = $r->url.$addpos.'wxid='.$fromUsername.'&wid='.$wid.'&rid='.$rid;
		$r->url = $r->url.'#mp.weixin.qq.com';
		$item.=sprintf($subitem, str_replace('{name}',$postObj->nickname,$r->tit), $r->pic, $r->url);
	}
	

	
	
	$resstr = str_replace('ITEM', $item, $resstr);
	global $pc,$timeStamp, $nonce, $encryptMsg;
	$pc->encryptMsg($resstr, $timeStamp, $nonce, $encryptMsg);
}



//回复单图文内容
function response_one($tit,$pic,$des,$url,$postObj){
	global $wid;
	$fromUsername = $postObj->FromUserName;
	$toUsername = $postObj->ToUserName;
	$textTpl = "<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[%s]]></MsgType>
	<ArticleCount>%s</ArticleCount>
	<Articles>
	ITEM
	</Articles>
	</xml>";
	$resstr =  sprintf($textTpl, $fromUsername, $toUsername, time(), "news", 1);
	$subitem = "<item>
	<Title><![CDATA[%s]]></Title>
	<Description><![CDATA[%s]]></Description>
	<PicUrl><![CDATA[%s]]></PicUrl>
	<Url><![CDATA[%s]]></Url>
	</item>";
	$url = $url.'#mp.weixin.qq.com';
	$item=sprintf($subitem, $tit, $des, $pic, $url);
	$resstr = str_replace('ITEM', $item, $resstr);
	global $pc,$timeStamp, $nonce, $encryptMsg;
	$pc->encryptMsg($resstr, $timeStamp, $nonce, $encryptMsg);
}


//回复多图文
function response_more($res,$postObj){
	global $wid;
	$fromUsername = $postObj->FromUserName;
	$toUsername = $postObj->ToUserName;
	$textTpl = "<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[%s]]></MsgType>
	<ArticleCount>%s</ArticleCount>
	<Articles>
	ITEM
	</Articles>
	</xml>";
	$resstr =  sprintf($textTpl, $fromUsername, $toUsername, time(), "news", count($res));
	$item = '';
	$subitem = "<item>
	<Title><![CDATA[%s]]></Title>
	<PicUrl><![CDATA[%s]]></PicUrl>
	<Url><![CDATA[%s]]></Url>
	</item>";
	foreach ($res as $r){
		$r['url'] = $r['url'].'#mp.weixin.qq.com';
		$item.=sprintf($subitem, $r['tit'],$r['pic'], $r['url']);
	}
	$resstr = str_replace('ITEM', $item, $resstr);
	global $pc,$timeStamp, $nonce, $encryptMsg;
	$pc->encryptMsg($resstr, $timeStamp, $nonce, $encryptMsg);
}
/*======================================新版end=============================================*/





//回复单图文内容
function response_one_nopic($tit,$des,$url,$postObj){
	global $wid;
	$fromUsername = $postObj->FromUserName;
	$toUsername = $postObj->ToUserName;
	$textTpl = "<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[%s]]></MsgType>
	<ArticleCount>%s</ArticleCount>
	<Articles>
	ITEM
	</Articles>
	</xml>";
	$resstr =  sprintf($textTpl, $fromUsername, $toUsername, time(), "news", 1);
	$subitem = "<item>
	<Title><![CDATA[%s]]></Title>
	<Description><![CDATA[%s]]></Description>
	<Url><![CDATA[%s]]></Url>
	</item>";
	if(strpos($url,'mp.weixin.qq.com')== false){
		$url = $url.'#mp.weixin.qq.com';
	}

	
	$item=sprintf($subitem, $tit, $des, $url);
	$resstr = str_replace('ITEM', $item, $resstr);
	global $pc,$timeStamp, $nonce, $encryptMsg;
	$pc->encryptMsg($resstr, $timeStamp, $nonce, $encryptMsg);
}




class wechatCallbackapiTest
{
	public function valid()
	{
		$echoStr = $_GET["echostr"];

		//valid signature , option
		if($this->checkSignature()){
			echo $echoStr;
			return true;
		}
		return false;
	}
	private function checkSignature()
	{
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];

		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr,SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );

		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

/**
 *  @desc 根据两点间的经纬度计算距离
 *  @param float $lat 纬度值
 *  @param float $lng 经度值
 */
function get_distance_by_lng_lat($lng1,$lat1,$lng2,$lat2)
{
	$earthRadius = 6367000; //approximate radius of earth in meters

	/*
	 Convert these degrees to radians
	 to work with the formula
	 */

	$lat1 = ($lat1 * pi() ) / 180;
	$lng1 = ($lng1 * pi() ) / 180;

	$lat2 = ($lat2 * pi() ) / 180;
	$lng2 = ($lng2 * pi() ) / 180;

	/*
	 Using the
	 Haversine formula

	 http://en.wikipedia.org/wiki/Haversine_formula

	 calculate the distance
	 */

	$calcLongitude = $lng2 - $lng1;
	$calcLatitude = $lat2 - $lat1;
	$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
	$calculatedDistance = $earthRadius * $stepTwo;

	return round($calculatedDistance);
}
