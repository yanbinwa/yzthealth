<?php

/**
 * wechat php test
 */
//define your token

header("Content-type:text/html;charset=utf-8");
Page::ignore_view();
include_once "wxBizMsgCrypt.php";
//include_once 'auth.class.php';

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
$pub = new Model('pubs');
$pub->find(array('cusid'=>$appid));
$wid = $pub->id;
if($wid == 6765368){
	log::warn('----cececegetapi='.$wid);
}
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
		
		//语音消息
		/*if($type=='voice'){
			$fileInfo = getMediaByMediaId($wid,$postObj->MediaId);
			$path = "/mnt/sdzzb/sdzzb/wx/pub/wxmedia/voice/".$wid;
			if (!file_exists($path))
			{
				mkdir($path, 0777);
			}
			$name = '/'.time().rand(1000,9999).'.mp3';
			$path = $path.$name;
			if (saveMedia($path,$fileInfo['body']))
				response_text('上传语音成功',$postObj);
			else{
				response_text('上传语音失败',$postObj);
			}
			return;
				
		}*/
		// 公众号配对聊天功能
		/*if($wid == 6765368){
			$pd = new Model('fans');
			$pd->find(array('wid'=>$wid,'wxid'=>$fromUsername));
			if($pd->pdstatus == 1){
				if($keyword=='配对'){
					$ppd = new Model('fans');
					$ppd = $ppd->where(array('wid'=>$wid,'pdstatus'=>2))->order('id')->limit(1)->list_all();
					if(count($ppd) == 1){
						$pppd = $ppd[0];
						$pd->pdstatus = 3;
						$pd->pdwxid = $pppd->wxid;
						$pd->save();
						$pppd->pdstatus = 3;
						$pppd->pdwxid = $fromUsername;
						$pppd->save();
						$access_token = getAccessTokenByWid($wid);
						$qrcode = '{"touser":"'.$pppd->wxid.'","msgtype":"text","text":{"content":"您与['.$pd->nickname.']匹配成功，向他/她打个招呼吧^_^"}}';
						$access_token_url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
						https_post($access_token_url,$qrcode);
						response_text('您与['.$pppd->nickname.']匹配成功，向他/她打个招呼吧^_^',$postObj);
					}elseif(count($ppd) == 0){
						$pd->pdstatus = 2;
						$pd->save();
						response_text('开始匹配，请稍后。。。',$postObj);
					}
				}
			}elseif($pd->pdstatus == 2){
				if($keyword == '退'){
					$pd->pdstatus = 1;
					$pd->save();
					response_text('您已成功退出配对聊天，如需参与，请回复“配对”',$postObj);
				}else{
					response_text('系统正在匹配，请稍后。。。',$postObj);
				}
			}elseif($pd->pdstatus == 3){
				if($keyword=='退'){
					$pdnc = new Model('fans');
					$pdnc = $pdnc->find(array('wid'=>$wid,'wxid'=>$pd->pdwxid));
					$pd->pdstatus = 1;
					$pd->pdwxid = '';
					$pd->save();
					$pdnc->pdstatus = 1;
					$pdnc->pdwxid = '';
					$pdnc->save();
					$access_token = getAccessTokenByWid($wid);
					$qrcode = '{"touser":"'.$pdnc->wxid.'","msgtype":"text","text":{"content":"对方已退出配对聊天，如需参与，请回复“配对”"}}';
					$access_token_url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
					https_post($access_token_url,$qrcode);
					response_text('您已成功退出配对聊天，如需参与，请回复“配对”',$postObj);
					exit;
				}else{
					if($type == 'text'){
						$access_token = getAccessTokenByWid($wid);
						$qrcode = '{"touser":"'.$pd->pdwxid.'","msgtype":"text","text":{"content":"'.'['.$pd->nickname.':]'.$keyword.'"}}';
						$access_token_url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
						https_post($access_token_url,$qrcode);
					}elseif($type == 'voice'){
						$access_token = getAccessTokenByWid($wid);
						$qrcode = '{"touser":"'.$pd->pdwxid.'","msgtype":"voice","voice":{"media_id":"'.$postObj->MediaId.'"}}';
						$access_token_url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
						https_post($access_token_url,$qrcode);
					}elseif($type == 'image'){
						$access_token = getAccessTokenByWid($wid);
						$qrcode = '{"touser":"'.$pd->pdwxid.'","msgtype":"image","image":{"media_id":"'.$postObj->MediaId.'"}}';
						$access_token_url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
						https_post($access_token_url,$qrcode);
					}elseif($type == 'video'){
						$access_token = getAccessTokenByWid($wid);
						$qrcode = '{"touser":"'.$pd->pdwxid.'","msgtype":"video","video":{"media_id":"'.$postObj->MediaId.'","thumb_media_id":"'.$postObj->ThumbMediaId.'","title":"title","description":"description"}}';
						$access_token_url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
						https_post($access_token_url,$qrcode);
				}else{
						response_text('抱歉，目前仅支持文字、图片、语音消息',$postObj);
					}

				}
			}
		}*/
		//是否关注
		$data_tj = new Model('data_statistics');
		$data_tj->find(array('wid'=>$wid,'wxid'=>$postObj->FromUserName,'type'=>1));
		if (!$data_tj->id) {
			$data_tj->wid = $wid;
			$data_tj->type= 1;
			$data_tj->wxid= $postObj->FromUserName;
			$data_tj->ctime= DB::raw('NOW()');
			$data_tj->save();
		}

		if($type=='location'){
			//地理位置信息
			$m = new Model('lbs');

			$jd = floatval($postObj->Location_Y);
			$wd = floatval($postObj->Location_X);
			

			if($wid == '75829'){
				/*log::warn($jd.'----$wid=75829---wid='.$wd);*/

				$url = "http://api.map.baidu.com/geocoder/v2/?callback=renderReverse&location=".$wd.",".$jd."&output=json&pois=0&ak=ZPblcFBL7nfkCSWUe31m6vNliXvHm236";
				$dataResult = http($url);
				$dataResult = str_replace('renderReverse&&renderReverse(','',$dataResult);
				$dataResult = str_replace(')','',$dataResult);
				$addressComponent = json_decode($dataResult,true);
				$dataCity = $addressComponent['result']['addressComponent']['district'];
				/*log::warn('----$wid=75829---wid='.$dataCity);*/
				$chinaarea = new Model('chinaarea');
				$chinaarea->find(array('name'=>$dataCity));
				$lres = $m->where(array('wid'=>$wid,'l_xianqu'=>$chinaarea->id))->limit('9')->list_all();
				if (empty($lres)) {
					/*$lres = $m->where(array('wid'=>$wid))->order('id desc')->limit('0,9')->list_all();*/
					$lres = $m->where(array('wid'=>$wid,'l_shi'=>$chinaarea->pid))->limit('9')->list_all();
					if (empty($lres)) {
						$chinaarea->find(array('id'=>$chinaarea->pid));
						$lres = $m->where(array('wid'=>$wid,'l_sheng'=>$chinaarea->pid))->limit('9')->list_all();
					}
				}

			}else{
				$lres = $m->where(array('wid'=>$wid))->list_all();
			}

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
			if('SCAN'==$postObj->Event)
			{
				  $sceneid1=str_replace("qrcode@","",$postObj->EventKey); 
				  
				  if($wid=='6780859')  //必胜客
				  {
					      $om = new Model('dz_md6768305_gz_log');
						  $om->find(array('mdid'=>$sceneid1,'option_wxid'=>$postObj->FromUserName,'type'=>1));
						  if(!$om->has_id())
						  {
							  //记录关注
							  $om->wid = $wid;
							  $om->mdid = $sceneid1;
							  $om->option_wxid = $postObj->FromUserName;
							  $om->type = '1';
							  $om->create_time = date('Y-m-d H:i:s',time());
							  $om->save();
						  }
				  }
				  
				  if($wid=='6780859' && $sceneid1=='3911')
				  {
					 $res = new Model('res');
					 $res->find('313304');
					 response_arts($res,$postObj); 
				  }
				  
				  if($wid=='6768305' && $sceneid1=='3924')
				  {
					 $res = new Model('res');
					 $res->find('322565');
					 response_arts($res,$postObj); 
				  }
				  
			}
			
			//关注
			if('subscribe'==$postObj->Event){
		        //判断场景ID 进行处理
				if(strpos($postObj->EventKey,"qrcode@")){
					$sceneid=str_replace("qrscene_qrcode@","",$postObj->EventKey);
					if($sceneid!='')
					{
					  if($wid=='6780859')  //必胜客
					  {
					    //log::warn('-------------------------关注2'.$sceneid1);
					      $om = new Model('dz_md6768305_gz_log');
						  $om->find(array('mdid'=>$sceneid,'option_wxid'=>$postObj->FromUserName,'type'=>1));
						  if(!$om->has_id())
						  {
							  //记录关注
							  $om->wid = $wid;
							  $om->mdid = $sceneid;
							  $om->option_wxid = $postObj->FromUserName;
							  $om->type = '1';
							  $om->create_time = date('Y-m-d H:i:s',time());
							  $om->save();
						  }
					   }
					   
					   if($wid=='6780859' && $sceneid=='3911')
					   {
						 $res = new Model('res');
						 $res->find('313304');
						 response_arts($res,$postObj); 
					   }
					   
					   if($wid=='6768305' && $sceneid=='3924')
					   {
						 $res = new Model('res');
						 $res->find('322565');
						 response_arts($res,$postObj); 
					   }
				  
				  
					}
				}
			}
			
			

			if('subscribe'==$postObj->Event)
			{
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
						response_text($fa->content,$postObj);
 					}else{
						$res = new Model('res');
						$res->find($fa->resource_id);
						response_arts($res,$postObj);
					}
				}

			}
			if('unsubscribe'==$postObj->Event)
			{
				$data_tj = new Model('data_statistics');
				$data_tj->delete(array('wxid'=>$postObj->FromUserName,'type'=>1));
			}
			elseif('CLICK'==$postObj->Event)
			{
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
				$mid = $postObj->MediaId;
			//看看是不是在墙上
			$qlog = new Model('z01_wall_u');
			$qlog->where(array('wxid'=>$postObj->FromUserName,'wid'=>$wid,'ison'=>'1'))->find();
			if($qlog->has_id()){
				//上过墙
				if($qlog->ison=='1'){
					if ($pubs->wtyp==1 || $pubs->wtyp==3) {
						response_text('暂不支持图片', $postObj);
						return;
					}
					if (!$qlog->pic) {
						$path = "/mnt/sdzzb/sdzzb/wx/pub/wxmedia/images/".$wid;
						$name = "/".time().rand(1000,9999).'.jpg';
						if (!file_exists($path))
						{
							mkdir($path, 0777);
						}
						$path = $path.$name;
						$fileInfo = getMediaByMediaId($wid,$postObj->MediaId);
						log::warn('$fileInfo='.$fileInfo['body'].'wid='.$wid);
						if (saveMedia($path,$fileInfo['body'])) {
							$qlog->mediaId = $postObj->MediaId;
							$qlog->pic = Conf::$http_path."wxmedia/images/".$wid.$name;
							$qlog->save();
//							response_text('头像上传成功', $postObj);
							response_one_nopic('微信上墙', '您已进入微信墙，点击进入摇一摇，内容上墙请直接回复。如需退出回复“0”', Conf::$http_path.'wall/myyyy-'.$qlog->qid.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid, $postObj);

						}else{
							response_text('头像上传失败，请稍后重试', $postObj);
						}
						return;
					}
					//在墙上
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
			//}



		}elseif($keyword!=''){

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
	$pubs = new Model('pubs');
	$pubs->find($wid);
	if('0'==$keyword){
		
			$qlog->ison='0';
			$qlog->save();
			response_text('你已经成功退出微信墙！', $postObj);
			return;
		
	}
	if(trim($qlog->nc)==''){
		if($isfirst){
			if($wid == '59396'){
				response_text('请输入您的11位真实手机号做为活动昵称,昵称是您唯一有效领奖依据,留言直接回复文字,退出回复“0”', $postObj);
			}else{	
				response_text('请输入显示的“昵称”进入微信墙；昵称长度少于6；如需退出回复“0”', $postObj);
			}
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
				if(trim($qlog->pic)==''){
			    	if ($pubs->wtyp==1 || $pubs->wtyp==3) {
						$qlog->pic = "http://www.weixinguanjia.cn/res/wall/touxiang/wall".rand(0,15).".jpg";
					}
			    }
				$qlog->save();
				response_text('昵称设置成功', $postObj);
			}
		}
		return;
	}
	if(trim($qlog->pic)==''){
		response_text('请上传图片作为您的头像；如需退出回复“0”', $postObj);
		return;
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
		/*response_text('发送成功，再次发送请直接回复；取消微信墙，请回复“0”', $postObj);*/
		response_one_nopic('微信上墙', '您已进入微信墙，点击进入摇一摇，内容上墙请直接回复。如需退出回复“0”', Conf::$http_path.'wall/myyyy-'.$qlog->qid.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid, $postObj);
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
	if($wid == 6791011){
		log::warn('+++++++++微信墙啊asd++++++++wid='.$wid);
	}
	$booking = new Model('z01_wall');
	$booking->find(array('wid'=>$wid,'kw'=>$keyword));
//	if($booking->has_id()){
//		$qlog = new Model('z01_wall_u');
//		$qlog->where(array('wxid'=>$postObj->FromUserName,'wid'=>$wid,'qid'=>$booking->id))->find();
//		if(!$qlog->has_id()){
//			$qlog = new Model('z01_wall_u');
//			$qlog->wxid = $postObj->FromUserName;
//			$qlog->wid = $wid;
//			$qlog->ison = '1';
//			$qlog->qid = $booking->id;
//			$qlog->save();
//			onthewall($qlog,$keyword,$postObj,true);
//		}else{
//			$qlog->ison = '1';
//			$qlog->save();
//			response_one_nopic('微信上墙', '您已进入微信墙，点击进入摇一摇，内容上墙请直接回复。', Conf::$http_path.'wall/myyyy-'.$qlog->qid.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid, $postObj);
//			return;
//		}
//		return;
//	}
//	微信墙
	$pub=new Model('pubs');
	$fuck=$pub->find(array('id'=>$wid));
	if($booking->has_id()){
		$qlog = new Model('z01_wall_u');
		$qlog->where(array('wxid'=>$postObj->FromUserName,'wid'=>$wid,'qid'=>$booking->id))->find();
		if(!$qlog->has_id()&&$fuck->service_type_info<2){ //不是服务号的
			$qlog = new Model('z01_wall_u');
			$qlog->wxid = $postObj->FromUserName;
			$qlog->wid = $wid;
			$qlog->ison = '1';
			$qlog->qid = $booking->id;
			$qlog->save();
			onthewall($qlog,$keyword,$postObj,true);
		}elseif(!$qlog->has_id()&&$fuck->service_type_info>=2){//是服务号的
			$ACCESS_TOKEN = getAccessTokenByWid($wid);
			$user_inf=file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$ACCESS_TOKEN."&openid=".$postObj->FromUserName."&lang=zh_CN");
			$user_inf=json_decode($user_inf);
			$qlog = new Model('z01_wall_u');
			$qlog->nc = $user_inf->nickname;

			$qlog->pic=$user_inf->headimgurl;
			$qlog->wxid = $postObj->FromUserName;
			$qlog->wid = $wid;
			$qlog->ison = '1';
			$qlog->qid = $booking->id;
			$qlog->save();
			onthewall($qlog,$keyword,$postObj,true);
		} else{
			$qlog->ison = '1';
			$qlog->save();
			response_one_nopic('微信上墙', '您已进入微信墙，点击进入摇一摇，内容上墙请直接回复。如需退出回复“0”', Conf::$http_path.'wall/myyyy-'.$qlog->qid.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid, $postObj);
			return;
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

	$keywordModel = new Model('micro_keyword');
	$keywordModel->find(array('wid'=>$wid,'keyword'=>$keyword));
	if ($keywordModel->has_id()) {
	 	//微网站
	 	if($keywordModel->type =="wwz_keyword"){
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
			if($model->has_id()){
				$url = Conf::$http_path.'weiweb/'.$wid.'/?&wxid='.$postObj->FromUserName.'&wid='.$wid;
				response_one($model->name,$model->pic,$model->ms,$url,$postObj);
				return;
			}
	 	}elseif($keywordModel->type =="coupon"){  //优惠券
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wx/yhq-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="coupon2"){  //优惠券2
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					//如果活动还未开始
					if($model->status ==2){
						response_text('活动还未开始，敬请关注!',$postObj);
					}
					$url = Conf::$http_path.'wx/yhj-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					//活动是否结束
					if($model->status ==0){			//已经结束
						response_one($model->endtitle,$model->endpic,$model->enddesc,$url,$postObj);
					}else{
						response_one($model->name,$model->startpic,$model->ms,$url,$postObj);
					}
					return;
				}
	 	}elseif($keywordModel->type =="ggk"){  //刮刮卡
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wx/ggk-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="ggk2"){  //刮刮卡2
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wx/ggk2-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="sdhd"){  //圣诞
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wx/sdhd-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->sdhd_pic,$model->sdhd_ms,$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="xydzp"){  //幸运大转盘
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wx/xydzp-'.$model->id.'.html?wid='.$wid;
					$url .= '&wxid='.$postObj->FromUserName;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;				
		
				}
	 	}elseif($keywordModel->type =="newxydzp"){  //新版幸运大转盘
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$param = '';
					if($model->type){
						$param = '&type=draw';
					}
					$url = Conf::$http_path.'xinxydzp/dzpindex-'.$model->id.'.html?wid='.$wid.$param;
	
					
					$url .='&wxid='.$postObj->FromUserName;
					response_one($model->name,$model->pic,$model->hdsm,$url,$postObj);
					return;		
				}
	 	}elseif($keywordModel->type =="micro_jfxv1_active_list"){  // 
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'jfxv1/activev1-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->title,$model->pic,$model->jianjie,$url,$postObj);
					return;
					
				}
	 	}elseif($keywordModel->type =="xyj"){  // 幸运机
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wx/xyj-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
					
				}
	 	}elseif($keywordModel->type =="hongbao_red"){  // 抢红包
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wx/free.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
					
				}
	 	}elseif($keywordModel->type =="zjd"){  // 砸金蛋
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
						$url = Conf::$http_path.'wx/zjd-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($model->name,$model->pic,$model->ms,$url,$postObj);
						return;
					
				}
	 	}elseif($keywordModel->type =="hongbao_free"){  // 抢免费券
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wx/free.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
					
				}
	 	}elseif($keywordModel->type =="ticket_free"){  // 抢票
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wx/ticket-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
					
				}
	 	}elseif($keywordModel->type =="zjd_new"){  // 砸金蛋2.0
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
						$url = Conf::$http_path.'xinzjd/zjd-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($model->hdmc,$model->pic,$model->ms,$url,$postObj);
						return;
					
				}
	 	}elseif($keywordModel->type =="clh"){  // 拆礼盒
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'chailihe/clh-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->hdmc,$model->pic,$model->twjj,$url,$postObj);
					return;
					
				}
	 	}elseif($keywordModel->type =="fxdr"){  // 分享达人
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wx/fxdrlist.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
					
				}
	 	}elseif($keywordModel->type =="micro_group_buy"){  // 微团购
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wx/wtg-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
					
				}
	 	}elseif($keywordModel->type =="yzdd"){  // 一战到底
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wx/yzdd-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						if($wid=='59078')
									$model->ms = '';
					
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
					
				}
	 	}elseif($keywordModel->type =="yzdd2"){  // 一战到底2.0
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					if($wid=='6769607')
					{
					  $url = Conf::$http_path.'yzdd6769607/index-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;	
					}
					else
					$url = Conf::$http_path.'wx/yzdd2-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;		

					$time =explode('-',$yzdd->time);
					$starttime =strtotime($time[0]);
					
					if($starttime >= time() || $model->tk ==0){			//还未开始
						response_text('活动还未开始，敬请关注!',$postObj);
					}else{
						response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					}
					return;
					
				}
	 	}elseif($keywordModel->type =="yzdd_dz"){  // 一战到底定制
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					
					$url = Conf::$http_path.'yzdd/index-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;		

					$time =explode('-',$yzdd->time);
					$starttime =strtotime($time[0]);
					
					if($starttime >= time() || $model->tk ==0){			//还未开始
						response_text('活动还未开始，敬请关注!',$postObj);
					}else{
						response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					}
					return;
					
				}
	 	}elseif($keywordModel->type =="z01_hk"){  // 微贺卡
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'hk/hk.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
					
				}
	 	}elseif($keywordModel->type =="z01_hkdq"){  // 贺卡大全
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'hk/hk.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
					
				}
	 	}elseif($keywordModel->type =="w_qingtie"){  // 微请帖
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'qingtie/qt-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
					
				}
	 	}elseif($keywordModel->type =="biaobai_keyword"){  // 微表白 @haojie
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'biaobai/biaobai.html?wxid='.$postObj->FromUserName.'&wid='.$wid.'&hid='.$booking->id;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
					
				}
	 	}elseif($keywordModel->type =="donghaixian_set"){  // 微东海鲜 @haojie
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'donghaixian/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->jianjie,$url,$postObj);
					return;
					
				}
	 	}elseif($keywordModel->type =="qmjjr_set"){  // 全民经纪人
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					if($wid == 6765368 || $wid == 6780484 || $wid == 6788906){
						$url = Conf::$http_path.'wkdz_yypk/mw.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
	 				}else{
	 					$url = Conf::$http_path.'wkdz/mw.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
	 				}
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
					
				}
	 	}elseif($keywordModel->type =="qmjjr_tjhouse_set"){  // 特价房
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'tjhouse/mw.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
					
				}
	 	}elseif($keywordModel->type =="jiaxiao_keyword"){  // 微驾校
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					//response_text('Hi~ 欢迎使微秀！现在您上传一张照片即可制作LOMO卡！',$postObj);
					$url = Conf::$http_path.'jiaxiao/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
					
				}
	 	}elseif($keywordModel->type =="weiba"){  // 微吧
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$arts = array();
					$fart = array();
					$url = Conf::$http_path.'wx/weiba-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					$fart['tit'] = $model->ms;
					$fart['url'] = $url;
					$fart['pic'] = $model->pic;
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
							$tart['url'] = Conf::$http_path.'wx/weiba-'.$model->id.'-'.$re->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
							$tart['pic'] = Conf::$http_path.'res/s.png?d';
							$arts[] = $tart;
						}
					}else{
						$tart = array();
						$tart['tit'] = '发起新话题';
						$tart['url'] = Conf::$http_path.'wx/weiba-'.$model->id.'-new.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						$tart['pic'] = Conf::$http_path.'res/s.png?2';
						$arts[] = $tart;
					}
					response_more($arts,$postObj,1);
					return;
					
				}
	 	}elseif($keywordModel->type =="micro_member_card"){  // 会员卡
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
				//error_log(print_r($postObj,1),3,__FILE__.'.log');
					$url = Conf::$http_path.'wx/hyk-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					//查询我的会员卡号
					$red = new Model('micro_member_card_record');
					$red->find(array('cid'=>$model->id,'wxid'=>$postObj->FromUserName));
					if($red->has_id()){
						response_one($model->name,$model->pic,'尊敬的会员卡用户，您的卡号为：'.$red->sn.'。'.$model->ms,$url,$postObj);
					}else{
						response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					}
					return;					
				}
	 	}elseif($keywordModel->type =="micro_membercard_set"){  // 新版会员卡
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					if($wid=='30705')
						$url = Conf::$http_path.'nhyk1/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					
					else
					{
						$nhyklm = new Model('micro_membercard_member_list');
						$nhyklm->find(array('wid'=>$wid,'wxid'=>$postObj->FromUserName));
						
						if($model->tolink==1 && $nhyklm->has_id())
						{
						     $url = Conf::$http_path.'nhyk/my.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						}
						else
						{
						     $url = Conf::$http_path.'nhyk/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						}
					    
					}
				

					response_one($model->name,$model->pic,$model->jianjie,$url,$postObj);
					return;				
				}
	 	}elseif($keywordModel->type =="z02_tuanset"){  // 新版微团购
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wsc2/tuan.html?mm=1&wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->jianjie,$url,$postObj);
					return;
			
				}
	 	}elseif($keywordModel->type =="micro_wcy_set"){  // 新版微餐饮
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
	 				if($wid == 6765368 || $wid == 6780484 || $wid == 6788906){
						$url = Conf::$http_path.'wcy_yypk/index2.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
	 				}else{
	 					$url = Conf::$http_path.'wcy/index1.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
	 				}
					// $url = Conf::$http_path.'wcy/index1.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->jianjie,$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="imicms_unitary"){  // 一元夺宝
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'unitary/goods.html?wxid='.$postObj->FromUserName.'&wid='.$wid.'&unitaryid='.$model->id;
					response_one($model->name,$model->wxpic,$model->wxinfo,$url,$postObj);
					return;
			
				}
	 	}elseif($keywordModel->type =="laikehu_dist_shop"){  // 分销
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = $model->url.'&fromuser='.$postObj->FromUserName;
					response_one($model->title,$model->thumb,$model->description,$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_survey"){  // 微调研
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wx/wdy-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_vote"){  // 微投票
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
						$url = Conf::$http_path.'wx/wtp-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($model->name,$model->pic,$model->ms,$url,$postObj);
						return;
				}
	 	}elseif($keywordModel->type =="micro_vote_up"){  // 微投票2.0
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wx/wtp_up-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
						
				}
	 	}elseif($keywordModel->type =="micro_wtpv3_active_list"){  // 微投票3.0
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
						$url = Conf::$http_path.'wtp/index-'.$model->id.'.html?wid='.$wid;
						if($wid==6487){
							$url = Conf::$http_path.'wtp1/index-'.$model->id.'.html?wid='.$wid;
						}
 
						$url .= "&wxid=".$postObj->FromUserName;

						response_one($model->title,$model->pic,$model->jianjie,$url,$postObj);
						return;						
				}
	 	}elseif($keywordModel->type =="micro_wtpv4_active_list"){  // 微投票4.0
			$model = new Model($keywordModel->type);
			$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
			if($model->has_id()){
				$url = Conf::$http_path.'wtp4/index-'.$model->id.'.html?wid='.$wid;
				if($wid==6487){
					$url = Conf::$http_path.'wtp4/index-'.$model->id.'.html?wid='.$wid;
				}

				$url .= "&wxid=".$postObj->FromUserName;

				response_one($model->title,$model->pic,$model->jianjie,$url,$postObj);
				return;
			}
		}elseif($keywordModel->type =="online_booking"){  // 在线预订
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wx/onlineBooking-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="newyy"){  // 新版预约
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
	 				if($wid == 6765368 || $wid == 6780484 || $wid == 6788906){
						$url = Conf::$http_path.'yuyue_yypk/yy-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
	 				}else{
	 					$url = Conf::$http_path.'yuyue2/yy-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
	 				}
					
					response_one($model->tit,$model->pic,$model->des,$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_photo_list"){  // 微相册
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wx/wxclist-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->artpic,$model->ms,$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_estate_set"){  // 微房产
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wfc/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,'',$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_jianshen_set"){  // 微健身
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wjs/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,'',$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="map_keyword"){  // 微地图
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'ditu/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->desc,$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_lvyou_set"){  // 微旅游
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wlvy/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,'',$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_meirong_set"){  // 微美容
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wmr/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,'',$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_zhengwu_set"){  // 微政务
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'or' => array('gjz'=>$keyword,'zixun_gjz' => $keyword,"tousu_gjz" => $keyword,"jianyi_gjz" =>$keyword)));
 			if($model->has_id()){
				if(($model->gjz)==$keyword){
						$url = Conf::$http_path.'wzw/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						$name = $model->name;
						response_one($name,$model->pic,'',$url,$postObj);
					}elseif(($model->zixun_gjz)==$keyword){
						$url = Conf::$http_path.'wzw/zxqzadd.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						$name = $model->name."_咨询求助";
						response_one($name,$model->pic,'',$url,$postObj);
					}elseif(($model->tousu_gjz)==$keyword){
						$url = Conf::$http_path.'wzw/tousuadd.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						$name = $model->name."_投诉举报";
						response_one($name,$model->pic,'',$url,$postObj);
					}elseif(($model->jianyi_gjz)==$keyword){
						$url = Conf::$http_path.'wzw/remarkadd.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						$name = $model->name."_建议献策";
						response_one($name,$model->pic,'',$url,$postObj);
					}else{
						$url = Conf::$http_path.'wzw/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						$name = $model->name;
						response_one($name,$model->pic,'',$url,$postObj);
					}				
					return;
			}
	 	}elseif($keywordModel->type =="micro_wuye_set"){  // 微物业
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wwy/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,'',$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_bbs_set"){  // 微社区
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wsqv2/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->title,$model->fm_pic,$model->fm_info,$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_ktv_set"){  // 微ktv
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'ktv/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,'',$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_jiaoyu_set"){  // 微教育
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'jiaoyu/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,'',$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_jiuba_set"){  // 微酒吧
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'jiuba/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,'',$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_huadian_set"){  // 微花店
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'huadian/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,'',$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_hunqing_set"){  // 微婚庆
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'hunqing/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,'',$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_zhuangxiu_set"){  // 微装修
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'zhuangxiu/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,'',$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_qixiu_set"){  // 微汽修
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'qixiu/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,'',$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_shipin_set"){  // 微食品
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'shipin/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,'',$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="360_full_view"){  // 360全景
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'qj/qj-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,'',$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_life_set"){  // 微生活
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'life/life.html?wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="micro_life_shop"){  // 微生活中的商户管理
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					if($model->url != ''){
							$url = $model->url;
						}else{
							$url = Conf::$http_path.'life/story-'.$model->id.'.html';
						}
						response_one($model->title,$model->pic,$model->jianjie,$url,$postObj);
						return;
				}
	 	}elseif($keywordModel->type =="micro_canyin_keyword"){  // 微餐饮
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
				 	$shopstyle = new Model('ai9me_shop_style');
					$shopstyle->find(array('token'=>$wid));
					$theme = intval($shopstyle->s_show); //皮肤
					$show = intval($shopstyle->s_style); //显示格式
					$action = 'index';
			 		$url = Conf::$http_path.'admin2/index.php?g=Wap&m=Product&a='.$action.'&wxid='.$postObj->FromUserName.'&wid='.$wid.'&dining=1&theme='.$theme.'&show='.$show;
				response_one($model->name,$model->pic,$model->ms,$url,$postObj);
				return;

				 
				}
	 	}elseif($keywordModel->type =="micro_shop_keyword"){  // 微商城
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
				 	$shopstyle = new Model('ai9me_shop_style');
					$shopstyle->find(array('token'=>$wid));
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
						response_one($model->name,$model->pic,$model->ms,$url,$postObj);
						return;
					 
				}
	 	}elseif($keywordModel->type =="micro_xitie_set"){  // 微喜帖
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
			 		$url = Conf::$http_path.'wx/wXiTie-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->art_pic,'',$url,$postObj);
					return;

				}
	 	}elseif($keywordModel->type =="life_votes_set"){  // 新微投票
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'votes/index-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic1,'',$url,$postObj);
					return;

				}
	 	}elseif($keywordModel->type =="micro_chang"){  // 微场景
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'chang/index-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->dsc,$url,$postObj);
					return;

				}
	 	}elseif($keywordModel->type =="micro_hotel"){  // 微酒店
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wjd1/index-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					//$url = Conf::$http_path.'wjd/index-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					//response_one($booking->tit,$booking->pic,$booking->des,$url,$postObj);
					response_one($model->tit,$model->pic,'',$url,$postObj);
					return;

				}
	 	}elseif($keywordModel->type =="yypk_hotel"){  // 优优品客微酒店
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wjd_yypk/index-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					//$url = Conf::$http_path.'wjd/index-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					//response_one($booking->tit,$booking->pic,$booking->des,$url,$postObj);
					response_one($model->tit,$model->pic,'',$url,$postObj);
					return;

				}
	 	}elseif($keywordModel->type =="tian_hotel"){  // 天目微酒店
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wjd2/index-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					//$url = Conf::$http_path.'wjd/index-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					//response_one($booking->tit,$booking->pic,$booking->des,$url,$postObj);
					response_one($model->tit,$model->pic,'',$url,$postObj);
					return;

				}
	 	}elseif($keywordModel->type =="liuyan_set"){  // 微留言
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wly/ly-'.$model->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->tit,$model->pic,'',$url,$postObj);
					return;

				}
	 	}elseif($keywordModel->type =="micro_ds_set"){  // 微打赏
			$model = new Model($keywordModel->type);
			$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
			if($model->has_id()){
				$url = Conf::$http_path.'ds/register.html?wid='.$wid;
				response_text($url,$postObj);
				return;

			}
		}elseif($keywordModel->type =="micro_yiliao_set"){  // 微医疗
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'weiweb/'.$wid.'/'.$model->xwid.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->jianjie,$url,$postObj);
					return;

				}
	 	}elseif($keywordModel->type =="micro_car_keyword"){  // 微汽车
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'weiweb/'.$wid.'/'.$model->xwid.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;

				}
	 	}elseif($keywordModel->type =="micro_car_guanhuai"){  // 车主关怀
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wqc/guanhuai.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;

				}
	 	}elseif($keywordModel->type =="micro_car_yysj"){  // 预约试驾
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wqc/yysj.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->tit,$model->pic,$model->des,$url,$postObj);
					return;

				}
	 	}elseif($keywordModel->type =="micro_car_yyby"){  // 预约保养
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'wqc/yyby.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->tit,$model->pic,$model->des,$url,$postObj);
					return;

				}
	 	}
		elseif($keywordModel->type =="z02_set"){  // 微商城
			$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
			if($model->has_id()){
				if($wid=='38263'){
					$url = Conf::$http_path.'wsc1/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
				}elseif($wid == '41860'){
					$url = Conf::$http_path.'wsc3/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
				}elseif($wid == '3723' || $wid == '65780'){
					$url = Conf::$http_path.'wsc4/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
				}elseif($wid == '3723' || $wid == '75573'){
					$url = Conf::$http_path.'wsc75573/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
				}elseif($wid == '6769786'){
					$url = Conf::$http_path.'wsc_weijin/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
				}else{
					$url = Conf::$http_path.'wsc2/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
				}
				response_one($model->name,$model->pic,$model->jianjie,$url,$postObj);
				return;
			}
		}
		elseif($keywordModel->type =="dz_mdset6768305"){  // 定制 必胜客红酒预约
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$url = Conf::$http_path.'bsk/bsk.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
					response_one($model->name,$model->pic,$model->jianjie,$url,$postObj);
					return;

				}
	 	}elseif($keywordModel->type =="weilong_set"){  //微龙
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
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
					

					$url = Conf::$http_path.'weilong/index.html?hid='.$model->id."&wxid=".$postObj->FromUserName;
					response_one($model->title,$model->pic,$model->desc,$url,$postObj);
					return;
				}
	 	}elseif($keywordModel->type =="js_game_keyword"){  // 微游戏
	 		$model = new Model($keywordModel->type);
	 		$model->find(array('wid'=>$wid,'id'=>$keywordModel->pid));
	 			if($model->has_id()){
					$g_wid=new Model('pubs');
					$g_wid->where ( array ('id' => $wid) )->find();
					if($g_wid->cusid&&$g_wid->cussec){
						$access_token = getAccessTokenByWid($wid);
//						$access_token = Cache::get('access_token_'.$g_wid->cusid);
						if ($access_token) 
						{
							$user_info=file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$postObj->FromUserName."&lang=zh_CN");
							$user_info=json_decode($user_info);
							
							//获取用户信息
							$game_user=new Model('js_game_user');
							$game_user->where ( array ('wxid' =>$postObj->FromUserName,'wid'=>$wid ) )->find();
							$headimg="inputfile";
							//if(!$game_user->$headimg && $booking->typ!='nuannan' && $booking->typ!='yetaitu'){
							if(!$game_user->$headimg && $model->typ!='yetaitu'){
								
								$game_user->name = $user_info->nickname;
								$game_user->$headimg=$user_info->headimgurl;
								$game_user->wxid=$postObj->FromUserName;
								$typ="type";
								$game_user->$typ=$model->typ;
								$game_user->wid=$wid;
								$game_user->save();
							}
						}
						else
						{
							$access_token = getAccessTokenByWid($wid);
							/*$acc_token=file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$g_wid->cusid."&secret=".$g_wid->cussec);
							$acc_token=json_decode($acc_token);*/
							
							if($access_token){
//								Cache::set('access_token_'.$g_wid->cusid,$acc_token->access_token,7000);
								
								$token = $access_token;
								$user_info=file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$token."&openid=".$postObj->FromUserName."&lang=zh_CN");
								$user_info=json_decode($user_info);
								//获取用户信息
								$game_user=new Model('js_game_user');
								$game_user->where ( array ('wxid' =>$postObj->FromUserName,'wid'=>$wid) )->find();
								$headimg="inputfile";
								//if(!$game_user->$headimg && $booking->typ!='nuannan' && $booking->typ!='yetaitu'){
								
								if(!$game_user->$headimg  && $model->typ!='yetaitu'){
									
									$game_user->name=$user_info->nickname;
									$game_user->$headimg=$user_info->headimgurl;
									$game_user->wxid=$postObj->FromUserName;
									$typ="type";
									$game_user->$typ=$model->typ;
									$game_user->wid=$wid;
									$game_user->save();
								}
							}
						}
					}
					
					if($model->typ=='queqiao' || $model->typ=='shenjingmao' || $model->typ=='nuannan' || $model->typ=='yetaitu')
					{
					    if($wid=='6768305' && $model->typ=='nuannan')
						{
						    $model->typ = 'nuannanbsk';
						}
						$url = Conf::$http_path.$model->typ.'/index.html?&wxid='.$postObj->FromUserName.'&wid='.$wid;
					}
					else
					{
						$url = Conf::$http_path.'jsgame/'.$model->typ.'/index.html?&wxid='.$postObj->FromUserName.'&wid='.$wid;
					}
					response_one($model->name,$model->pic,$model->ms,$url,$postObj);
					return;

				}
	 	}
		 
		
		
		 
 	}

	//微打印-设置启动命令
	$booking = new Model('micro_wprint_keword');
	$booking->find(array('wid'=>$wid,'keyword'=>$keyword));
	if($booking->has_id()){
		Session::set('is_print',1);
		response_text('Hi~ 欢迎使微秀！现在您上传一张照片即可制作LOMO卡！',$postObj);
		return;
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
				$headimg="inputfile";
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
					$headimg="inputfile";
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
		}

		if($postObj->MsgId){
			$post="ToUserName=".$postObj->ToUserName."&FromUserName=".$postObj->FromUserName."&CreateTime=".$postObj->CreateTime."&MsgType=".$postObj->MsgType."&Content=".$postObj->Content."&MsgId=".$postObj->MsgId;
		}elseif($postObj->EventKey){
			$post="ToUserName=".$postObj->ToUserName."&FromUserName=".$postObj->FromUserName."&CreateTime=".$postObj->CreateTime."&MsgType=".$postObj->MsgType."&Content=".$postObj->Content."&EventKey=".$postObj->Content;
		}
		$data = curlpost($post  , $widmodel->otherurl);
		if ($data)
		{
			global $pc,$timeStamp, $nonce, $encryptMsg;
			$pc->encryptMsg($data, $timeStamp, $nonce, $encryptMsg);
			return ;
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
	// if($cs->autoans == '1'){
	// 	$key_word = new Model('key_word');
	// 	$key_word->find(array('keyWord'=>$keyword,'pubsId'=>$wid));
	// 	if(!$key_word->has_id()){
	// 		$from ='123';
	// 		$to ='123';
	// 		$xiaojiu = xiaojiu($keyword,$from,$to);
	// 		response_text($xiaojiu,$postObj);
	// 		return;
	// 	}
	// }



	//匹配"*"关键词
	$key_word = new Model('key_word');
	$key_word->find(array('keyWord'=>'*','pubsId'=>$wid));
	if($key_word->has_id()){
		check_and_replay_keyword($key_word,$postObj);
		//warm::log('check_and_replay_keyword($key_word,$postObj);check_and_replay_keyword($key_word,$postObj);check_and_replay_keyword($key_word,$postObj);');
		return;
	}else{
		//warm::log('check_and_replay_keyword($key_word,$postObj);check_and_replay_keyword($key_word,$postObj);check_and_replay_keyword($key_word,$postObj);');
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

function getMediaByMediaId($wid,$mediaId){
	$ACCESS_TOKEN = getAccessTokenByWid($wid);
		log::warn('ACCESS_TOKEN='.$ACCESS_TOKEN.'wid='.$wid.'$mediaId='.$mediaId);

	$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$ACCESS_TOKEN&media_id=$mediaId";
	//发送请求
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_NOBODY, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$package  = curl_exec($ch);
	$httpinfo = curl_getinfo($ch);
	curl_close($ch);
	$imageAll = array_merge(array('header'=>$httpinfo),array('body'=>$package));
	return $imageAll;

}

function saveMedia($filename,$filecontent){
	$local_file = fopen($filename, 'w');
	if (false !== $local_file) {
		if (false !== fwrite($local_file, $filecontent)) {
			fclose($local_file);
			return true;
		}
		return false;
	}
	return false;
}

function getATByWid($wid){
	$pubs = new Model('pubs');
	$pubs->find($wid);
	$Wxapi = new Wxapi($pubs->cusid,$pubs->cussec);
	if(Cache::has('access_token_'.$pubs->cusid) ){
		return '有cache';	
		$access_token = Cache::get('access_token_'.$pubs->cusid);
	}else{
		
		$access_token = $Wxapi->getAccessToken($pubs->cusid,$pubs->cussec);
		return $access_token;
		Cache::set('access_token_'.$pubs->cusid,$access_token,7000);

	}
	return $access_token;	
}

