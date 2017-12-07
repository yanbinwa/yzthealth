<?php
header("Content-type:text/html;charset=utf-8");
Page::ignore_view();
include_once "wxBizMsgCrypt.php";
include_once 'auth.class.php'; 

$optionsaaa = array(
        'component_appid' => 'wx02560241e9071ed8',
        'component_appsecret' => '0c79e1fa963cd80cc0be99b20a18faeb',
        'component_verify_ticket'=>file_get_contents('ComponentVerifyTicket.json')  //component_verify_ticket�ɹ���ƽ̨ÿ��10���ӣ��������͸�������ƽ̨�����ڴ������ںŵ�����ƽ̨���ͨ���󣬲ŻῪʼ���ͣ���
);

$weObj = new Auth($optionsaaa);
$token='sdzzb110';
$encodingAesKey='sdzzb110sdzzb110sdzzb110sdzzb110sdzzb110232';

$appId='wx02560241e9071ed8';
$pc = new WXBizMsgCrypt($token, $encodingAesKey, $appId);
$url=explode('/',Request::url());
$appid=$url[1];

log::warn('appid============:'.$appid);

define("TOKEN",'sdzzb110');
$encryptMsg='';
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
		$pub = new Model('pubs');
		$pub->find(array('cusid'=>$appid));
		if($pub->has_id())
		{
		   $wid=$pub->id;
		}
		if($pub->isfw == 1){
			$userinfo = getUserInfoFromUrl($fromUsername);
		}
		
		if($type =='event')
		{
			$event=$postObj->Event;
			response_text($event.'from_callback',$postObj);exit;
		}
		else
		{
		    if($keyword == 'TESTCOMPONENT_MSG_TYPE_TEXT')
			{
			  response_text($keyword.'_callback',$postObj);exit;
			}
			
			
			if(strpos($keyword,'QUERY_AUTH_CODE')!==false)
			{
				response_textnull(' ',$postObj);
				$auth_code=str_replace('QUERY_AUTH_CODE:','',$keyword);
				
				log::warn('auth_code============:'.$auth_code);
				
			    $authorizer_access_token=$weObj->get_authorizer_access_token('wx570bc396a51b8ff8');
				
				log::warn('authorizer_access_token============:'.$authorizer_access_token);
				
				$kefuurl='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$authorizer_access_token;
				$kffucontent='{
								"touser":"'.$fromUsername.'",
								"msgtype":"text",
								"text":
								{
									 "content":"'.$auth_code.'_from_api'.'"
								}
							  }';
				$res=json_decode(https_request($kefuurl,$kffucontent));
				
                //file_put_contents('info.txt',print_R($kffucontent,true).'--'.print_r($res,true),FILE_APPEND);
				exit;

			}
			
			
			/*
			
			if(strpos($keyword,'QUERY_AUTH_CODE')!==false)
			{
				//����ʾ������Ȩ�ص����ȡ���ں���Ϣ
				$auth_code=str_replace('QUERY_AUTH_CODE:','',$keyword);
				$wechats_info=$weObj->get_authorization_info($auth_code);//��ȡ��Ȩ����Ϣ
		
				$appid=$wechats_info['AuthorizerAppid'];
				$authorizer_access_token=$wechats_info['authorizer_access_token'];
				$authorizer_refresh_token=$wechats_info['authorizer_refresh_token'];
				//��ȡ��ϸ��Ϣ
				$gzhinfo=$weObj->get_authorizer_info($appid);
				
				//file_put_contents('info.txt',print_R($wechats_info,true)); 
                file_put_contents('info.txt',print_r($wechats_info,true),FILE_APPEND);
			

				$pubs= new Model("pubs");
				$pubs->where(array('cusid'=>$appid))->find();
				
				$datas['sqappid']=$appid;
				$datas['cusid']=$appid;
				$datas['authorizer_access_token']=$authorizer_access_token;
				$datas['authorizer_refresh_token']=$authorizer_refresh_token;
				$datas['isbind']=1;
				$datas['authcode']=$auth_code;
				$datas['nexttime']=date('Y-m-d H:i:s',time()+7100);
				$datas['nickname']=$gzhinfo['authorizer_info']['nick_name'];
				$datas['head_img']=$gzhinfo['authorizer_info']['head_img'];
				$datas['service_type_info']=$gzhinfo['authorizer_info']['service_type_info']['id']; //��Ȩ�����ں����ͣ�0�����ĺţ�1��������ʷ���ʺ�������Ķ��ĺţ�2��������
				$datas['verify_type_info']=$gzhinfo['authorizer_info']['verify_type_info']['id'];//��Ȩ����֤���ͣ�-1����δ��֤��0����΢����֤��1��������΢����֤��2������Ѷ΢����֤��3������������֤ͨ������δͨ��������֤��4������������֤ͨ������δͨ��������֤����ͨ��������΢����֤��5������������֤ͨ������δͨ��������֤����ͨ������Ѷ΢����֤
				$datas['uuid']=$gzhinfo['authorizer_info']['user_name'];
				$datas['alias']=$gzhinfo['authorizer_info']['alias'];//΢�ź�
				$datas['qrcode_url']=$gzhinfo['authorizer_info']['qrcode_url'];//΢�Ŷ�ά��
				if($pubs->has_id())
				{
					
				  // $pubs->update(array('cusid'=>$appid),$datas);
				}
				else
				{
					
						$pubs->sqappid=$appid;
						$pubs->cusid=$appid;
						$pubs->uid=Session::get('mainuid');
						$pubs->authorizer_access_token=$authorizer_access_token;
						$pubs->authorizer_refresh_token=$authorizer_refresh_token;
						$pubs->isbind=1;
						$pubs->authcode=$auth_code;
						$pubs->nexttime=date('Y-m-d H:i:s',time()+7100);
						$pubs->nickname=$gzhinfo['authorizer_info']['nick_name'];
						$pubs->head_img=$gzhinfo['authorizer_info']['head_img'];
						$pubs->service_type_info=$gzhinfo['authorizer_info']['service_type_info']['id']; //��Ȩ�����ں����ͣ�0�����ĺţ�1��������ʷ���ʺ�������Ķ��ĺţ�2��������
						$pubs->verify_type_info=$gzhinfo['authorizer_info']['verify_type_info']['id'];//��Ȩ����֤���ͣ�-1����δ��֤��0����΢����֤��1��������΢����֤��2������Ѷ΢����֤��3������������֤ͨ������δͨ��������֤��4������������֤ͨ������δͨ��������֤����ͨ��������΢����֤��5������������֤ͨ������δͨ��������֤����ͨ������Ѷ΢����֤
						$pubs->uuid=$gzhinfo['authorizer_info']['user_name'];
						$pubs->alias=$gzhinfo['authorizer_info']['alias'];//΢�ź�
						$pubs->qrcode_url=$gzhinfo['authorizer_info']['qrcode_url'];//΢�Ŷ�ά��
						$pubs->save();
				}
				//response_text(' ',$postObj); //esc post 
				//file_put_contents('kefu.json',$pub->sqappid.":".$postObj->FromUserName);
				exit;

			}
			*/
			
			
			
			
		}
		$userinfo = '';

		$wxidbiao = new Model('wxid');
		$wxidbiao->where(array('wid'=>$wid,'wxid'=>$postObj->FromUserName))->find();
		if(!$wxidbiao->has_id()){
			$wxidbiao->wid = $wid;
			$wxidbiao->wxid = $postObj->FromUserName;

			if($postObj->MsgType == 'event'){
				$wxidbiao->type = $postObj->Event;
				if($postObj->Event == 'LOCATION'){
					$wxidbiao->jd = $postObj->Longitude;
					$wxidbiao->wd = $postObj->Latitude;
				}
			}else{
				$wxidbiao->type = $postObj->MsgType;
			}
			$wxidbiao->time = time();
			$wxidbiao->date = date('Y-m-d H:i:s');
			$wxidbiao->save();
		}elseif($postObj->MsgType == 'event' && $postObj->Event == 'LOCATION'){
			$wxidbiao->jd = $postObj->Longitude;
			$wxidbiao->wd = $postObj->Latitude;
			$wxidbiao->date = date('Y-m-d H:i:s');
			$wxidbiao->type = 'LOCATION';
			$wxidbiao->save();
		}

		
		if($type=='location'){
			
			$data_tj = new Model('data_statistics');
			$data_tj->wid = $wid;
			$data_tj->type= 4;
			$data_tj->ctime= DB::raw('NOW()');
			$data_tj->save();
			//����λ����Ϣ
			$m = new Model('lbs');
			$lres = $m->where(array('wid'=>$wid))->list_all();
			$jd = floatval($postObj->Location_Y);
			$wd = floatval($postObj->Location_X);
			$jlarr = array();
			$ja = false;
			foreach ($lres as $l){
				$jl = get_distance_by_lng_lat($jd,$wd,floatval($l->jd),floatval($l->wd));//Conf::$http_path.'weiweb/'.$wid.'/'
				$name = preg_replace("/\s/","",$l->name);
				$content = preg_replace("/\s/","",strip_tags($l->content));
				//$hyurl = 'http://api.map.baidu.com/marker?location='.$l->wd.','.$l->jd.'&title='.$name.'&content='.$name.'&output=html&src=weiba|weiweb';
				$hyurl = Conf::$http_path.'wx/shop.html?wxid='.$postObj->FromUserName.'&wid='.$wid.'&jd='.$jd.'&wd='.$wd;

				////Log::error($hyurl);
				$jlarr[$jl] = array('tit'=>($l->name.',����'.$jl.'��'),'pic'=>Conf::$http_path.$l->pic,'url'=>$hyurl,'ms'=>$content);
				if(!$ja){
					$ja = $jlarr[$jl];
				}
			}
			/*if($wid == 531)
			{
			file_put_contents('log.txt',print_r($jlarr,true).print_r($postObj,true));
			}*/
			ksort($jlarr);
			$ss=array();
			$iy=0;
			foreach($jlarr as $key => $value)
			{ 
			   $iy=$iy+1;
			   if($iy <= 10)
				{
			   $ss[$key]=$value;
				}
			}
			if(count($lres)>1){
				response_more($ss,$postObj);
			}else{
				response_one($ja['tit'].'��',$ja['pic'],$ja['ms'],$ja['url'],$postObj);
			}
			
			
		}
		//�ҵ���Ϣ����
		if($type=='event'){
			
			if('subscribe'==$postObj->Event){
				$data_tj = new Model('data_statistics');
				$data_tj->wid = $wid;
				$data_tj->type= 1;
				$data_tj->ctime= DB::raw('NOW()');
				$data_tj->save();
				//��ע
				$fansinfo = $userinfo;
				if($fansinfo->nickname)
					$postObj->nickname = $fansinfo->nickname;
				$fa = new Model('first_attention');
				$fa->find(array('wid'=>$wid));
				if($fa->has_id()){
					if($fa->typ=='0'){
						if($fansinfo){
							//if($wid==347) file_put_contents('../controller/4444.txt',$fansinfo->nickname);
							$fa->content = str_replace('{name}',$fansinfo->nickname,$fa->content);
							response_text($fa->content,$postObj);
						}else{
							$fa->content = str_replace('{name}','��',$fa->content);
							response_text($fa->content,$postObj);
						}
					}elseif($fa->typ=='3'){//�ؼ���
						check_and_replay($fa->content,$postObj);
					}else{//ͼ��
						$res = new Model('res');
						$res->find($fa->resource_id);
						response_arts($res,$postObj);
					}
				}else{
					
				}

				
				//file_put_contents('../controller/111111111.txt',$fansinfo);
				if($fansinfo){
					//�����˿�б�
					if($fansinfo->sex == 1){
						$fansinfo->sex = '��';
					}elseif($fansinfo->sex == 2){
						$fansinfo->sex = 'Ů';
					}else{
						$fansinfo->sex = 'δ֪';
					}
					$f = new Model('fans');
					$f->wid = $wid;
					$f->subscribe = $fansinfo->subscribe;
					$f->openid = $fansinfo->openid;
					$f->nickname = $fansinfo->nickname;
					$f->sex = $fansinfo->sex;
					$f->city = $fansinfo->city;
					$f->country = $fansinfo->country;
					$f->province = $fansinfo->province;
					$f->language = $fansinfo->language;
					$f->headimgurl = $fansinfo->headimgurl;
					$f->subscribe_time = $fansinfo->subscribe_time;
					$f->save();
				}else{
					$f = new Model('fans');
					$f->wid = $wid;
					$f->subscribe = '1';
					$f->openid = $postObj->FromUserName;
					$f->subscribe_time = time();
					$f->save();
				}
				
			}elseif('unsubscribe'==$postObj->Event){
				$data_tj = new Model('data_statistics');
				$data_tj->wid = $wid;
				$data_tj->type= 5;
				$data_tj->ctime= DB::raw('NOW()');
				$data_tj->save();

				//ɾ����˿
				$f = new Model('fans');
				$f->delete(array('openid'=>$postObj->FromUserName,'wid'=>$wid));

			}elseif('CLICK'==$postObj->Event){
				$data_tj = new Model('data_statistics');
				$data_tj->wid = $wid;
				$data_tj->type= 3;
				$data_tj->ctime= DB::raw('NOW()');
				$data_tj->save();
				
				$key = $postObj->EventKey;

				
				//Log::error($key);
				/////////////////////////////--------------------------
				$key = explode('@', $key);
				if($key[0]=='res_wb'){
					//�ı��ظ�
					$ggk = new Model('res_text');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						response_text($ggk->txt,$postObj);
						return;
					}
				}elseif($key[0]=='res_tw' || $key[0]=='res_dtw'){
					//ͼ�Ļظ�
					$res = new Model('res');
					$res->find($key[1]);
					if($res->has_id()){
						response_arts($res,$postObj);
					}					
				}elseif($key[0]=='res_gjz'){


					//file_put_contents('../controller/1111.txt',$key[1]);
					if($key[1]==md5('��ϵ�ͷ�') || $key[1]==md5('��ͷ�')){
						response_kefu($postObj);
						return;
					}
					//�ؼ���
					$ggk = new Model('res_text');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						check_and_replay($ggk->txt,$postObj);
						return;
					}
				}elseif($key[0]=='res_yhq'){
					//�Ż�ȯ
					$coupon = new Model('coupon');
					$coupon->find($key[1]);
					if($coupon->has_id()){
						$url = Conf::$http_path.'wx/yhq-'.$coupon->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($coupon->name,Conf::$http_path.$coupon->pic,$coupon->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=='res_ggk'){
					//�ιο�
					$ggk = new Model('ggk');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'wx/ggk-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=='res_xydzp'){
					//���˴�ת��
					$ggk = new Model('xydzp');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'wx/xydzp-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
					}elseif($key[0]=='res_wgw'){
					$ggk = new Model('wwz_keyword');
					$ggk->find(array('wid'=>$wid));
					if($ggk->has_id()){
						$url = Conf::$http_path.'weiweb/'.$wid;
						response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=='res_xyj'){
					//���˻�
					$ggk = new Model('xyj');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'wx/xyj-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}
				elseif($key[0]=='res_qhb'){
					//�����
					$ggk = new Model('qhb');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'wx/game.html?wxid='.$postObj->FromUserName.'&hdid='.$ggk->id.'&wid='.$wid;
						response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}
				elseif($key[0]=='res_baomingset'){
					//����ϵͳ
					$ggk = new Model('baomingset');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'huiyi/baoming-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->description,$url,$postObj);
						return;
					}
				}
				elseif($key[0]=='res_yyl'){
					//ҡҡ��
					$ggk = new Model('yyl');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'wx/yyl_new-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}
				elseif($key[0]=='res_wtg'){
					//΢�Ź�
					$ggk = new Model('micro_group_buy');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'wx/wtg-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=='res_wtg_index'){
					//΢�Ź�
					$ggk = new Model('wtg_index');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'wx/wtg_index-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=='res_wms_index'){
					//�ֳ���ɱ
					$ggk = new Model('wms_index');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'seckill/wms_index-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}
				elseif($key[0]=='res_yzdd'){
					//һվ����
					$ggk = new Model('yzdd');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'wx/yzdd-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=='res_weiba'){
					//΢��
					$ggk = new Model('weiba');
					$ggk->find(array('wid'=>$wid));
					if($ggk->has_id()){
						$arts = array();
						$fart = array();
						$url = Conf::$http_path.'wx/weiba-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						$fart['tit'] = $ggk->ms;
						$fart['url'] = $url;
						$fart['pic'] = Conf::$http_path.$ggk->pic;
						$arts[] = $fart;
						//���һ���
						$m = new Model('weiba_ht');
						$subres = $m->where(array('wid'=>$wid))->order('zm desc')->limit('7')->list_all();
						if(count($subres)>0){
							foreach ($subres as $re){
								$tart = array();
								$tart['tit'] = '#'.$re->keywd.'#';
								$tart['url'] = Conf::$http_path.'wx/weiba-'.$ggk->id.'-'.$re->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
								$fart['pic'] = Conf::$http_path.'res/s.png';
								$arts[] = $tart;
							}
						}else{
							$tart = array();
							$tart['tit'] = '�����»���';
							$tart['url'] = Conf::$http_path.'wx/weiba-'.$ggk->id.'-new.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
							$fart['pic'] = Conf::$http_path.'res/s.png';
							$arts[] = $tart;
						}
						response_more($arts,$postObj);
						return;
					}
				}elseif($key[0]=='res_whyk'){
					//��Ա��
					$coupon = new Model('micro_member_card');
					$coupon->find($key[1]);
					if($coupon->has_id()){
						$url = Conf::$http_path.'wx/hyk-'.$coupon->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						//��ѯ�ҵĻ�Ա����
						$red = new Model('micro_member_card_record');
						$red->find(array('cid'=>$coupon->id,'wxid'=>$postObj->FromUserName));
						if($red->has_id()){
							response_one($coupon->name,Conf::$http_path.$coupon->pic,'�𾴵Ļ�Ա���û������Ŀ���Ϊ��'.$red->sn.'��'.$coupon->ms,$url,$postObj);
						}else{
							response_one($coupon->name,Conf::$http_path.$coupon->pic,$coupon->ms,$url,$postObj);
						}
						
						return;
					}
				}elseif($key[0]=='res_wdy'){
					//΢����
					$ggk = new Model('micro_survey');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						$url = Conf::$http_path.'wx/wdy-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=='res_wtp'){
					//΢ͶƱ
					$ggk = new Model('micro_vote');
					$ggk->find($key[1]);
					if($ggk->has_id()){
						if($wid == 186){
							$url = Conf::$http_path.'wx/wtp186-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						}else{
							$url = Conf::$http_path.'wx/wtp-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						}
						response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=="res_wktv"){
					  //΢KTV
					$booking = new Model('micro_ktv_set');
					$booking->find(array('wid'=>$wid));
					if($booking->has_id()){
						$url = Conf::$http_path.'ktv/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
						return;
					}
				}elseif($key[0]=="res_wjd"){
					  //΢�Ƶ�
					$booking = new Model('micro_hotel');
					$booking->find(array('wid'=>$wid));
					if($booking->has_id()){
						$url = Conf::$http_path.'wjd/index-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($booking->tit,Conf::$http_path.$booking->pic,'',$url,$postObj);
						return;
					}
				}elseif($key[0]=="res_wfc"){
					  //΢����
					$booking = new Model('micro_estate_set');
					$booking->find(array('wid'=>$wid));
					if($booking->has_id()){
						$url = Conf::$http_path.'wfc/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
						return;
					}
				}elseif($key[0]=="res_yiliao"){
					  //΢ҽ��
					$booking = new Model('micro_yiliao_set');
					$booking->find(array('wid'=>$wid));
					if($booking->has_id()){
						$url = Conf::$http_path.'yiliao/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
						return;
					}
				}elseif($key[0]=="res_wmr"){
					  //΢����
					$booking = new Model('micro_meirong_set');
					$booking->find(array('wid'=>$wid));
					if($booking->has_id()){
						$url = Conf::$http_path.'wmr/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
						return;
					}
				}elseif($key[0]=="res_wwy"){
					  //΢��ҵ
					$booking = new Model('micro_wuye_set');
					$booking->find(array('wid'=>$wid));
					if($booking->has_id()){
						$url = Conf::$http_path.'wwy/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
						return;
					}
				}elseif($key[0]=="res_wzw"){
					  //΢����
					$booking = new Model('micro_zhengwu_set');
					$booking->find(array('wid'=>$wid));
					if($booking->has_id()){
						$url = Conf::$http_path.'wzw/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
						return;
					}
				}elseif($key[0]=="res_wsp"){
					  //΢ʳƷ
					$booking = new Model('micro_shipin_set');
					$booking->find(array('wid'=>$wid));
					if($booking->has_id()){
						$url = Conf::$http_path.'shipin/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
						return;
					}
				}elseif($key[0]=="res_wly"){
					  //΢����
					$booking = new Model('micro_lvyou_set');
					$booking->find(array('wid'=>$wid));
					if($booking->has_id()){
						$url = Conf::$http_path.'wlvy/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
						return;
					}
				}elseif($key[0]=="res_wjs"){
					  //΢����
					$booking = new Model('micro_jianshen_set');
					$booking->find(array('wid'=>$wid));
					if($booking->has_id()){
						$url = Conf::$http_path.'wjs/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
						return;
					}
				}elseif($key[0]=="res_wjy"){
					  //΢����
					$booking = new Model('micro_jiaoyu_set');
					$booking->find(array('wid'=>$wid));
					if($booking->has_id()){
						$url = Conf::$http_path.'jiaoyu/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
						return;
					}
				}elseif($key[0]=="res_wjb"){
					  //΢�ư�
					$booking = new Model('micro_jiuba_set');
					$booking->find(array('wid'=>$wid));
					if($booking->has_id()){
						$url = Conf::$http_path.'jiuba/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
						return;
					}
				}elseif($key[0]=="res_whq"){
					  //΢����
					$booking = new Model('micro_hunqing_set');
					$booking->find(array('wid'=>$wid));
					if($booking->has_id()){
						$url = Conf::$http_path.'hunqing/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
						return;
					}
				}elseif($key[0]=="res_wzx"){
					  //΢װ��
					$booking = new Model('micro_zhuangxiu_set');
					$booking->find(array('wid'=>$wid));
					if($booking->has_id()){
						$url = Conf::$http_path.'zhuangxiu/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
						return;
					}
				}elseif($key[0]=="res_whd"){
					  //΢����
					$booking = new Model('micro_huadian_set');
					$booking->find(array('wid'=>$wid));
					if($booking->has_id()){
						$url = Conf::$http_path.'huadian/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
						return;
					}
				}elseif($key[0]=="res_wqc"){
					  //΢����
					$booking = new Model('micro_car_keyword');
					$booking->find(array('wid'=>$wid));
					if($booking->has_id()){
						$url = Conf::$http_path.'weiweb/'.$wid.'/'.$booking->xwid.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($booking->name,Conf::$http_path.$booking->pic,$booking->ms,$url,$postObj);
						return;
					}
				}elseif($key[0]=="res_wsc"){
					  //΢�̳�
					$booking = new Model('micro_shop_keyword');
					$booking->find(array('wid'=>$wid));
					if($booking->has_id()){
						$url = Conf::$http_path.'admin2/index.php?g=Wap&m=Product&a=index&wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
						return;
					}
				}elseif($key[0]=="res_wcy"){
					  //΢����
					$booking = new Model('micro_canyin_keyword');
					$booking->find(array('wid'=>$wid));
					if($booking->has_id()){
						$url = Conf::$http_path.'admin2/index.php?g=Wap&m=Product&a=index&wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
						return;
					}
				}elseif($key[0]=='res_wyy'){
					//����Ԥ��
					$zxyd = new Model('online_booking');
					$zxyd->find($key[1]);
					if($zxyd->has_id()){
						$url = Conf::$http_path.'wx/onlineBooking-'.$zxyd->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
						response_one($zxyd->name,Conf::$http_path.$zxyd->pic,$zxyd->ms,$url,$postObj);
						return;
					}
		        }
				////////////////////////////-----------------------------
			}
			//�˵�����
		}elseif(!empty( $keyword )){
			$data_tj = new Model('data_statistics');
			$data_tj->wid = $wid;
			$data_tj->type= 2;
			$data_tj->ctime= DB::raw('NOW()');
			$data_tj->save();
			
			if($keyword=='΢��ͨ'){
				
				response_text('�����ɹ�,΢��ͨ�ŶӸ�л��ʹ��΢��ͨ΢��ϵͳ��ʹ�ù��������κ�������Լ�ʱ��������ϵ��',$postObj);
				//��ס�˻���uuid
				$pub = new Model('pubs');
				$pub->update(array('id'=>Request::get('appid')),array('uuid'=>$toUsername));
			}else{
				
				check_and_replay($keyword,$postObj);
			}
		}elseif(!empty( $postObj->PicUrl )){//ͼƬ��ǽ
			$booking = new Model('micro_wall_config');
			$booking->find(array('wid'=>$wid));
			$d = new Model('micro_wall_user_name');
			$d->find(array('wid'=>$wid,'wxid'=>$postObj->FromUserName));
			
			if($booking->tpsq==1 && $booking->has_id()){
			
				$key = '<img style="max-width:350px;max-height:350px" src="'.$postObj->PicUrl.'"/>';
				$m = new Model('micro_wall_content');
				$m->wid=$wid;
				$m->wxid=$postObj->FromUserName;
				$m->content=$key;
				$m->check=$booking->need_check?0:1;
				$m->time=DB::raw('NOW()');
				$m->save();
				$re = $booking->res_word;
				if(empty($d->name)){
					
					if(!$userinfo){
						$re .= "\r\n����δ�����ǳ�! ��ظ� �ҽ�+��������! �����޷���ǽ!";
					}else{
						$d->name = $userinfo->nickname;
						$d->headimgurl = $userinfo->headimgurl;
						$d->wid=$wid;
						$d->wxid=trim($postObj->FromUserName);
						$d->save();
						response_text($re,$postObj);
						return;
					}
				}else{
					$d->name = $userinfo->nickname;
					$d->headimgurl = $userinfo->headimgurl;
					$d->save();
				}
				response_text($re,$postObj);
				return;
			
			}else{
				//ƥ��[ͼƬ]�ؼ���
				$superkw = new Model('key_word');
				$superkw->where(array('pubsId'=>$wid,'keyWord'=>'[ͼƬ]'))->find();

				if($superkw->has_id()){
					response_text($superkw->replyContent,$postObj);
					return;
				}
			}
		}elseif($type=='voice'){
			$keyword = $postObj->Recognition;
			if($keyword !=''){
				check_and_replay($keyword,$postObj);
			}
		}
	}
	die();
}else{
	$wechatObj = new wechatCallbackapiTest();	
	if($wechatObj->valid()){//������֤����
		$pub = new Model('pubs');
		$pub->update(array('id'=>Request::get('appid')),array('http'=>'','token'=>'','isval'=>'1'));
	}
}
die();





//���ܻظ�
function check_and_replay($keyword,$postObj){
	global $wid;
	global $userinfo;

	//ƥ��[*]�ؼ���
	$superkw = new Model('key_word');
	$superkw->where(array('pubsId'=>$wid,'keyWord'=>'[*]'))->find();
	
	if($superkw->has_id()){
		
		check_and_replay_keyword($superkw,$postObj);
		return;
	}


	if($keyword=='��ϵ�ͷ�' || $keyword=='��ͷ�'){
		response_kefu($postObj);
		return;
	}

	//ƥ�����
	//�Ż�ȯ
	$coupon = new Model('coupon');
	$coupon->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($coupon->has_id()){
		$url = Conf::$http_path.'wx/yhq-'.$coupon->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($coupon->name,Conf::$http_path.$coupon->pic,$coupon->ms,$url,$postObj);
		return;
	}
	//�ιο�
	$ggk = new Model('ggk');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/ggk-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	
	//���˴�ת��
	$ggk = new Model('xydzp');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/xydzp-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	
	//���˻�
	$ggk = new Model('xyj');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/xyj-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	//�����
	$ggk = new Model('qhb');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/game.html?wxid='.$postObj->FromUserName.'&hdid='.$ggk->id.'&wid='.$wid;
		response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	//����ϵͳ����
	$ggk = new Model('baomingset');
	$ggk->find(array('wid'=>$wid,'key'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'huiyi/baoming-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->description,$url,$postObj);
		return;
	}
	//΢�Ź��б�
	$ggk = new Model('wtg_keyword');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/wtg_index-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	//�ֳ���ɱ�б�
	$ggk = new Model('wms_keyword');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'seckill/wms_index-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	//ҡҡ��
	$ggk = new Model('yyl');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		if($wid==210||$wid==165){
			$url = Conf::$http_path.'wx/yyl_new-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		}else{
			$url = Conf::$http_path.'wx/yyl_new-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		}
		response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	//΢�Ź�
	$ggk = new Model('micro_group_buy');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/wtg-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	//�ֳ���ɱ
	$ggk = new Model('micro_seckill_buy');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'seckill/wms-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	
	//һվ����
	$ggk = new Model('yzdd');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/yzdd-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	
	//΢��
	$ggk = new Model('weiba');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$arts = array();
		$fart = array();
		$url = Conf::$http_path.'wx/weiba-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		$fart['tit'] = $ggk->ms;
		$fart['url'] = $url;
		$fart['pic'] = Conf::$http_path.$ggk->pic;
		$arts[] = $fart;
		//���һ���
		$m = new Model('weiba_ht');
		$subres = $m->where(array('wid'=>$wid))->order('zm desc')->limit('7')->list_all();
		if(count($subres)>0){
			foreach ($subres as $re){
				$tart = array();
				$tart['tit'] = '#'.$re->keywd.'#';
				$tart['url'] = Conf::$http_path.'wx/weiba-'.$ggk->id.'-'.$re->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
				$fart['pic'] = Conf::$http_path.'res/s.png';
				$arts[] = $tart;
			}			
		}else{
			$tart = array();
			$tart['tit'] = '�����»���';
			$tart['url'] = Conf::$http_path.'wx/weiba-'.$ggk->id.'-new.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
			$fart['pic'] = Conf::$http_path.'res/s.png';
			$arts[] = $tart;
		}
		response_more($arts,$postObj);
		return;
	}
	
	//��Ա��	
	$coupon = new Model('micro_member_card');
	$coupon->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($coupon->has_id()){
		$url = Conf::$http_path.'wx/hyk-'.$coupon->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;		
		//��ѯ�ҵĻ�Ա����
		$red = new Model('micro_member_card_record');
		$red->find(array('cid'=>$coupon->id,'wxid'=>$postObj->FromUserName));
		if($red->has_id()){
			response_one($coupon->name,Conf::$http_path.$coupon->pic,'�𾴵Ļ�Ա���û������Ŀ���Ϊ��'.$red->sn.'��'.$coupon->ms,$url,$postObj);
		}else{
			response_one($coupon->name,Conf::$http_path.$coupon->pic,$coupon->ms,$url,$postObj);
		}
		return;
	}

	//�°��Ա��	
	$coupon = new Model('micro_membercard_set');
	$coupon->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($coupon->has_id()){
		if($userinfo && $userinfo->unionid){
			$url = Conf::$http_path.'nhyk/index.html?wxid='.$userinfo->unionid.'&wid='.$wid;
		}else{
			$url = Conf::$http_path.'nhyk/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;	
		}
		//��ѯ�ҵĻ�Ա����
		$red = new Model('micro_membercard_record');
		$red->find(array('wid'=>$wid,'wxid'=>$postObj->FromUserName));
		if($red->has_id()){
			response_one($coupon->name,Conf::$http_path.$coupon->pic,'�𾴵Ļ�Ա���û������Ŀ���Ϊ��'.$red->sn.'��'.$coupon->jianjie,$url,$postObj);
		}else{
			response_one($coupon->name,Conf::$http_path.$coupon->pic,$coupon->jianjie,$url,$postObj);
		}
		return;
	}
	
	//΢����
	$ggk = new Model('micro_survey');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		$url = Conf::$http_path.'wx/wdy-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	//΢ͶƱ
	$ggk = new Model('micro_vote');
	$ggk->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($ggk->has_id()){
		//$url = Conf::$http_path.'wx/wtp-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		if($wid == 186){
			$url = Conf::$http_path.'wx/wtp186-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		}else{
			$url = Conf::$http_path.'wx/wtp-'.$ggk->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		}
		response_one($ggk->name,Conf::$http_path.$ggk->pic,$ggk->ms,$url,$postObj);
		return;
	}
	//����Ԥ��
	$booking = new Model('online_booking');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wx/onlineBooking-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	
	//΢���
	$booking = new Model('micro_photo_list');
	$booking->find(array('wid'=>$wid,'keyword'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wx/wxclist-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->artpic,$booking->ms,$url,$postObj);
		return;
	}
	//΢����
	$booking = new Model('micro_estate_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wfc/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢ҽ��
	$booking = new Model('micro_yiliao_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'yiliao/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢ʳƷ
	$booking = new Model('micro_shipin_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'shipin/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢����
	$booking = new Model('micro_lvyou_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wlvy/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢����
	$booking = new Model('micro_jianshen_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wjs/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢����
	$booking = new Model('micro_meirong_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wmr/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢����
	$booking = new Model('micro_zhengwu_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wzw/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢���
	$booking = new Model('micro_diancai_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	//�鿴�Ƿ��зֵ�
	$user = new Model('user');
	$user->where(array('diancaiid'=>$wid))->find();
	if($user->has_id() && $booking->has_id()){
		$url = Conf::$http_path.'dc/repastStore-'.$wid.'.html?wxid='.$postObj->FromUserName;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}else if($booking->has_id()){
		$url = Conf::$http_path.'dc/index-'.$wid.'.html?wxid='.$postObj->FromUserName;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢��ҵ
	$booking = new Model('micro_wuye_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wwy/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢ktv
	$booking = new Model('micro_ktv_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'ktv/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢����
	$booking = new Model('micro_jiaoyu_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'jiaoyu/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢�ư�
	$booking = new Model('micro_jiuba_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'jiuba/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢����
	$booking = new Model('micro_huadian_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'huadian/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢����
	$booking = new Model('micro_hunqing_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'hunqing/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢װ��
	$booking = new Model('micro_zhuangxiu_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'zhuangxiu/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢����
	$booking = new Model('micro_qixiu_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'qixiu/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢����
	$booking = new Model('micro_canyin_keyword');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'admin2/index.php?g=Wap&m=Product&a=index&wxid='.$postObj->FromUserName.'&wid='.$wid.'&token='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢�̳�
	$booking = new Model('micro_shop_keyword');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'admin2/index.php?g=Wap&m=Product&a=index&wxid='.$postObj->FromUserName.'&wid='.$wid.'&token='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//�°�΢�̳�
	$booking = new Model('z02_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		if($wid==165){
			$url = Conf::$http_path.'newwsc/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid.'&token='.$wid;
		}else{
			$url = Conf::$http_path.'newwsc/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid.'&token='.$wid;
		}
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}

	//�ڳ��̳�
	$booking = new Model('z02_zhongchouset');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		if($wid==165){
			$url = Conf::$http_path.'zhongchou/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid.'&token='.$wid;
		}else{
			$url = Conf::$http_path.'zhongchou/index.html?wxid='.$postObj->FromUserName.'&wid='.$wid.'&token='.$wid;
		}
		response_one($booking->name,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢ϲ��
	$booking = new Model('micro_xitie_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wx/wXiTie-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->art_pic,'',$url,$postObj);
		return;
	}
	//΢����
	$booking = new Model('micro_yaoqing_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'yaoqing/wXiTie-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->art_pic,'',$url,$postObj);
		return;
	}
	//΢�Ƶ�
	$booking = new Model('micro_hotel');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wjd/index-'.$booking->id.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->tit,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢����
	$booking = new Model('liuyan_set');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		if($wid==306){
			$url = Conf::$http_path.'wly/ly306.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		}else{
			$url = Conf::$http_path.'wly/ly.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		}
		response_one($booking->tit,Conf::$http_path.$booking->pic,'',$url,$postObj);
		return;
	}
	//΢����
	$booking = new Model('micro_car_keyword');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		
		$url = Conf::$http_path.'weiweb/'.$wid.'/'.$booking->xwid.'.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	//�����ػ�
	$booking = new Model('micro_car_guanhuai');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wqc/guanhuai.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	//ԤԼ�Լ�
$booking = new Model('micro_car_yysj');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wqc/yyby.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->tit,Conf::$http_path.$booking->pic,$booking->des,$url,$postObj);
		return;
	}
	//ԤԼ����
	$booking = new Model('micro_car_yyby');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'wqc/yysj.html?wxid='.$postObj->FromUserName.'&wid='.$wid;
		response_one($booking->tit,Conf::$http_path.$booking->pic,$booking->des,$url,$postObj);
		return;
	}
	//����

	$booking = new Model('wwz_keyword');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'weiweb/'.$wid."/?wxid=".$postObj->FromUserName;
		response_one($booking->name,Conf::$http_path.$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	//�ؿ�
	$booking = new Model('z01_hk');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'hk/hk.html?wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	//�ؿ�
	$booking = new Model('z01_hkdq');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'heka/index.html?wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	//360
	$booking = new Model('360_full_view');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'qj/quanjing-'.$booking->id.'.html?wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	//���ֺ�
	$booking = new Model('micro_music_keyword');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		$url = Conf::$http_path.'box/box.html?wid='.$wid;
		response_one($booking->name,Conf::$http_path.$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	
	//���籭
	$shijiebei = new Model('shijiebei');
	$shijiebei->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($shijiebei->has_id()){
		$url = Conf::$http_path.'shijiebei/index.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		response_one($shijiebei->name,Conf::$http_path.$shijiebei->pic,$shijiebei->ms,$url,$postObj);
		return;
	}
	//�������Ž�ʦͶƱ
	$linyitoupiao = new Model('linyitoupiao');
	$linyitoupiao->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($linyitoupiao->has_id()){
		$url = Conf::$http_path.'linyitoupiao/index.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		if($wid == 199){
			$url = Conf::$http_path.'linyitoupiao1/index.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		}elseif($wid == 478){
			$url = Conf::$http_path.'linyitoupiao2/index.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		}elseif($wid == 510){
			$url = Conf::$http_path.'linyitoupiao3/index.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		}
		response_one($linyitoupiao->name,Conf::$http_path.$linyitoupiao->pic1,$linyitoupiao->ms,$url,$postObj);
		return;
	}

	$linyitoupiao = new Model('linyitoupiaonew');
	$linyitoupiao->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($linyitoupiao->has_id()){
		$url = Conf::$http_path.'linyitoupiaonew/index.html?rid='.$linyitoupiao->id.'&wid='.$wid.'&wxid='.$postObj->FromUserName;
		if($wid == 199){
			$url = Conf::$http_path.'linyitoupiao4/index.html?rid='.$linyitoupiao->id.'&wid='.$wid.'&wxid='.$postObj->FromUserName;
		}
		response_one($linyitoupiao->name,Conf::$http_path.$linyitoupiao->pic1,$linyitoupiao->ms,$url,$postObj);
		return;
	}

	//һ����������
	$bns_set = new Model('bns_set');
	$bns_set->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($bns_set->has_id()){
		$url = Conf::$http_path.'bunengsi/index.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		response_one($bns_set->name,Conf::$http_path.$bns_set->pic,$bns_set->jianjie,$url,$postObj);
		return;
	}

	//�ٻ�����
	$bns_set = new Model('baihua_set');
	$bns_set->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($bns_set->has_id()){
		$url = Conf::$http_path.'baihua/xuanze.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		response_one($bns_set->name,Conf::$http_path.$bns_set->pic,$bns_set->jianjie,$url,$postObj);
		return;
	}

	//�����������ӮiPhone6�
	$glyq_set = new Model('zglyq_set');
	$glyq_set->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($glyq_set->has_id()){
		if($wid==308 || $wid==228){
			$url = Conf::$http_path.'zjyq/index.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		}elseif($wid==186){
			$url = Conf::$http_path.'glyq/index1.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		}else{
			$url = Conf::$http_path.'glyq/index.html?vf=2&wid='.$wid.'&wxid='.$postObj->FromUserName;
		}
		response_one($glyq_set->name,Conf::$http_path.$glyq_set->pic,$glyq_set->jianjie,$url,$postObj);
		return;
	}

	//������������ͱ�ֽ
	$glyq2set = new Model('glyq2set');
	$glyq2set->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($glyq2set->has_id()){
		$url = Conf::$http_path.'glyq2/index.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		response_one($glyq2set->name,Conf::$http_path.$glyq2set->pic,$glyq2set->jianjie,$url,$postObj);
		return;
	}

	//2048��Ϸ
	$bns_set = new Model('z2048_set');
	$bns_set->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($bns_set->has_id()){
		if($wid == 242 || $wid == 176){
			$url = Conf::$http_path.'2048gl/index.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		}else{
			$url = Conf::$http_path.'2048/index.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		}
		response_one($bns_set->name,Conf::$http_path.$bns_set->pic,$bns_set->jianjie,$url,$postObj);
		return;
	}
	//�¹ιο�
	$zggk_set = new Model('zggk_set');
	$zggk_set->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($zggk_set->has_id()){

		if($wid == 242){
			$url = Conf::$http_path.'youxi/ggk1.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		}elseif($wid == 312){
			$url = Conf::$http_path.'youxi/ggk2.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		}else{
			$url = Conf::$http_path.'youxi/ggk.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		}
		response_one($zggk_set->name,Conf::$http_path.$zggk_set->pic,$zggk_set->jianjie,$url,$postObj);
		return;
	}
	//Χס��è��Ϸ
	$bns_set = new Model('zsjm_set');
	$bns_set->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($bns_set->has_id()){
		$url = Conf::$http_path.'shenjingmao/index.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		response_one($bns_set->name,Conf::$http_path.$bns_set->pic,$bns_set->jianjie,$url,$postObj);
		return;
	}
	//�����ָ��Ϸ
	$bns_set = new Model('zfksz_set');
	$bns_set->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($bns_set->has_id()){
		$url = Conf::$http_path.'fksz/index.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		response_one($bns_set->name,Conf::$http_path.$bns_set->pic,$bns_set->jianjie,$url,$postObj);
		return;
	}
	//���ʻ�����
	$bns_set = new Model('zlinyify_set');
	$bns_set->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($bns_set->has_id()){
		$url = Conf::$http_path.'linyify/linyi.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		response_one($bns_set->name,Conf::$http_path.$bns_set->pic,$bns_set->jianjie,$url,$postObj);
		return;
	}
	//̩��������
	$bns_set = new Model('zlinyify_set');
	$bns_set->find(array('wid'=>$wid,'gjz2'=>$keyword));
	if($bns_set->has_id()){
		$url = Conf::$http_path.'linyify/taian.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		response_one($bns_set->name,Conf::$http_path.$bns_set->pic,$bns_set->jianjie,$url,$postObj);
		return;
	}
	//���ݻ�����
	$bns_set = new Model('zlinyify_set');
	$bns_set->find(array('wid'=>$wid,'gjz3'=>$keyword));
	if($bns_set->has_id()){
		$url = Conf::$http_path.'linyify/hangzhou.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		response_one($bns_set->name,Conf::$http_path.$bns_set->pic,$bns_set->jianjie,$url,$postObj);
		return;
	}
	//�Ĵ�������
	$bns_set = new Model('zlinyify_set');
	$bns_set->find(array('wid'=>$wid,'gjz4'=>$keyword));
	if($bns_set->has_id()){
		$url = Conf::$http_path.'linyify/sichuan.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		response_one($bns_set->name,Conf::$http_path.$bns_set->pic,$bns_set->jianjie,$url,$postObj);
		return;
	}

	//Χס��è��Ϸ
	$tyyqset = new Model('tyyqset');
	$tyyqset->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($tyyqset->has_id()){
		if($wid !=296 && $wid!=230){
			$url = Conf::$http_path.'yq/index.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		}else{
			$url = Conf::$http_path.'tyyq/index.html?wid='.$wid.'&wxid='.$postObj->FromUserName;
		}
		response_one($tyyqset->name,Conf::$http_path.$tyyqset->pic,$tyyqset->jianjie,$url,$postObj);
		return;
	}

	//�°�ԤԼ
	$booking = new Model('newyy');
	$booking->find(array('wid'=>$wid,'gjz'=>$keyword));
	if($booking->has_id()){
		if($wid ==176 && $booking->id==129){
			$url = Conf::$http_path.'yuyue/bank-'.$booking->id.'.html?wid='.$wid."&wxid=".$postObj->FromUserName;
		}
		else if($wid ==531  && $booking->id==131){
			$url = Conf::$http_path.'yuyue/bank-'.$booking->id.'.html?wid='.$wid."&wxid=".$postObj->FromUserName;
		}
		else if($wid ==552  && $booking->id==146){
			$url = Conf::$http_path.'yuyue/bank-'.$booking->id.'.html?wid='.$wid."&wxid=".$postObj->FromUserName;
		}		
		else{
			$url = Conf::$http_path.'yuyue/yy-'.$booking->id.'.html?wid='.$wid."&wxid=".$postObj->FromUserName;
		}
		response_one($booking->tit,Conf::$http_path.$booking->pic,$booking->ms,$url,$postObj);
		return;
	}
	//ҡһҡ
	$cs = new Model('micro_yyy_keyword');
	$cs->find(array('wid'=>$wid));
	if(preg_match("|^".$cs->gjz."[0-9]{11}$|",$keyword,$re)){
		
		preg_match("|[0-9]{11}$|",$keyword,$re);
		$url = Conf::$http_path."/admin2/index.php?g=Wap&m=Toshake&a=index&token=".$wid."&phone=".$re[0]."&wecha_id=".$postObj->FromUserName;
		response_one($cs->name,Conf::$http_path.$cs->pic,$cs->ms,$url,$postObj);
		return;
	}
	//ҡҡ��
	$yyl = new Model('micro_yyl_keyword');
	$yyl->find(array('wid'=>$wid));
	if(preg_match("|^".$yyl->gjz."[0-9]{11}$|",$keyword,$re)){
		
		preg_match("|[0-9]{11}$|",$keyword,$re);
		$url = Conf::$http_path."/admin2/index.php?g=Wap&m=Toshakes&a=index&token=".$wid."&phone=".$re[0]."&wecha_id=".$postObj->FromUserName;
		response_one($yyl->name,Conf::$http_path.$yyl->pic,$yyl->ms,$url,$postObj);
		return;
	}
	$wifi = new Model('micro_wifi_keyword');
	$wifi->find(array('wid'=>$wid,'gjz'=>$keyword));
	//����wifi����
	if($wifi->has_id()){
		$fromUsername = trim($postObj->FromUserName);
		$info = curl_file_get_contents('http://service.rippletek.com/Portal/Wx/wxFunLogin?appId='.$wifi->appId.'&appKey='.$wifi->appKey.'&nodeId='.$wifi->nodeId.'&openId='.$fromUsername);
		$info=json_decode($info,true);
		if($info['result']=="ok"){
			response_text("�� �� �� �� ��<a href=\"".$info['url']."\">ֱ�ӵ��</a>,�������豸�����������豸��½����������֤��:".$info["token"]."(��֤����Ч��10���ӡ�)",$postObj);
		}else{
			response_text("��¼ʧ�ܣ������ԡ�",$postObj);
		}
  		//$a = curl_file_get_contents("http://web.rippletek.com/api/portal/login?appId=8554xDPMCuLTkwVn&appKey=otF9HKubMQHOEKKbgwLyMnb30oXjMbvJ&nodeId=jhms&openId=".$fromUsername);
		//$b = json_decode($a,true);
	
		//response_text("<a href='".$b['url']."' > �����������</a>",$postObj);
		return;
	}
	//΢��ǽ
	$booking = new Model('micro_wall_config');
	$booking->find(array('wid'=>$wid));
	$d = new Model('micro_wall_user_name');
	$d->find(array('wid'=>$wid,'wxid'=>$postObj->FromUserName));
	if($booking->has_id() && substr($keyword,0,6)=='�ҽ�'){
		$d->name = substr($keyword,6);
		$d->wid=$wid;
		$d->wxid=trim($postObj->FromUserName);
		$d->save();
		$res = "�ǳ����óɹ�!";
			response_text($res,$postObj);
			return;
	}
	if($booking->has_id()){
	//��ǽ
	if($booking->sqgjz=='' || strpos($keyword,$booking->sqgjz)!==false)
	{
		
		$key = substr($keyword,strlen($booking->sqgjz),strlen($keyword));
		//$key = str_replace($booking->sqgjz,"",$keyword);
		
		$m = new Model('micro_wall_content');
		$m->wid=$wid;
		$m->wxid=$postObj->FromUserName;
		$m->content=$key;
		$m->check=$booking->need_check?0:1;
		$m->time=DB::raw('NOW()');
		$m->save();
		$re = $booking->res_word;
		if(empty($d->name)){
			
			if(!$userinfo){
				$re .= "\r\n����δ�����ǳ�! ��ظ� �ҽ�+��������! �����޷���ǽ!";
			}else{
				$d->name = $userinfo->nickname;
				$d->headimgurl = $userinfo->headimgurl;
				$d->wid=$wid;
				$d->wxid=trim($postObj->FromUserName);
				$d->save();
				response_text($re,$postObj);
				return;
			}
		}elseif(empty($key)){
			if($wid == 210){
				$r = "˵�㻰�ɣ���������˵�Ļ�������ǽ��";
			}else{
				$r = "˵�㻰�ɣ����� ".$booking->sqgjz."������˵�Ļ�������ǽ��";
			}
			response_text($r,$postObj);
			return;
		}else{
			$d->name = $userinfo->nickname;
			$d->headimgurl = $userinfo->headimgurl;
			$d->save();
		}
		response_text($re,$postObj);
		return;
	}elseif($booking->tpgjz!='' && strpos($keyword,$booking->tpgjz)!==false){	

	if($booking->open_luck){
	$m = new Model('micro_wall_user_name');
	$m->find(array('wid'=>$wid,'wxid'=>$postObj->FromUserName));
	if($m->has_id() && $booking->re_luck === false)
	{}else{
	$m->wid=$wid;
	$m->wxid=$postObj->FromUserName;
	$m->last_time=time();
	$m->time=DB::raw('NOW()');
	$m->save();
	$key = str_replace($booking->tpgjz,"",$keyword);
	$s = new Model('micro_wall_vote');
	$s->find(array('wid'=>$wid,'id'=>$key));
	$s->count++;
	$s->save();
	$re = $booking->res_word;
	if(empty($d->name))
	{
		
		if(!$userinfo){
			$re .= "\r\n����δ�����ǳ�! ��ظ� �ҽ�+��������! �����޷���ǽ!";
		}else{
			$d->name = $userinfo->nickname;
			$d->headimgurl = $userinfo->headimgurl;
			$d->wid=$wid;
			$d->wxid=trim($postObj->FromUserName);
			$d->save();
			response_text($re,$postObj);
			return;
		}
	}
	response_text($re,$postObj);
	}
	}return;}}

	//ƥ��ؼ���
	$key_word = new Model('key_word');
	$kkres = $key_word->where(array('pubsId'=>$wid))->list_all();
	foreach ($kkres as $kk){
		$kkarr = $kk->keyWord.'';
		$kkarr = str_replace('��', ',', $kkarr);
		$kkarr = explode(',', $kkarr);
		if(in_array($keyword, $kkarr)){
			
			check_and_replay_keyword($kk,$postObj);
			return;
		}
	}
	
	foreach ($kkres as $kk){
		$kkarr = $kk->keyWord.'';
		$keytype = $kk->matchingType;
		if($keytype=='1'){
			$kkarr = str_replace('��', ',', $kkarr);
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
	/**
	$key_word->find(array('keyWord@~'=>$keyword,'pubsId'=>$wid));
	if($key_word->has_id()){
		check_and_replay_keyword($key_word,$postObj);
		return;
	}
	*/
	//ƥ�����ܿͷ�
	//���ܿͷ�(����)
	$myans = new Model('my_answer');
	$myans->find(array('question'=>$keyword,'wid'=>$wid));
	if($myans->has_id()){
		response_text($myans->answer,$postObj);
		return;
	}
	/**
	$myans->find(array('question@~'=>$keyword,'wid'=>$wid));
	if($myans->has_id()){
		response_text($myans->answer,$postObj);
		return;
	}
	*/

	//ƥ��"*"�ؼ���
	$key_word = new Model('key_word');
	$key_word->find(array('keyWord'=>'*','pubsId'=>$wid));
	if($key_word->has_id()){
		check_and_replay_keyword($key_word,$postObj);
		return;
	}


	//ƥ��"*"�ؼ���
	$key_word = new Model('key_word');
	$key_word->find(array('keyWord'=>'*','pubsId'=>$wid));
	if($key_word->has_id()){
		check_and_replay_keyword($key_word,$postObj);
		return;
	}
	//��û��ƥ�����򲻻ش�
	
}
function check_and_replay_keyword($key_word,$postObj){
	global $wid;
    file_put_contents('info.txt',$key_word.'++++++++++1111',FILE_APPEND);
	$rtyp = $key_word->replyType;
	if($rtyp=='1'){
		response_text($key_word->replyContent,$postObj);
	}else{
		$res = new Model('res');
		$res->find($key_word->resId);
		response_arts($res,$postObj);
	}
}


//�ظ��ı�
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
//�ظ���
function response_textnull($txt,$postObj){
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
	$pc->encryptMsg('', $timeStamp, $nonce, $encryptMsg);
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
//�ظ���ͼ��
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
	$item=sprintf($subitem, str_replace('{name}',$postObj->nickname,$r->tit), str_replace('{name}',$postObj->nickname,$r->des), Conf::$http_path.$r->pic, $r->url);
	$resstr = str_replace('ITEM', $item, $resstr);
	global $pc,$timeStamp, $nonce, $encryptMsg;
	$ss=$pc->encryptMsg($resstr, $timeStamp, $nonce, $encryptMsg);
	
}
//�ظ���ͼ��
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
		$item.=sprintf($subitem, str_replace('{name}',$postObj->nickname,$r->tit), Conf::$http_path.$r->pic, $r->url);
	}
	$resstr = str_replace('ITEM', $item, $resstr);
	global $pc,$timeStamp, $nonce, $encryptMsg;
	$pc->encryptMsg($resstr, $timeStamp, $nonce, $encryptMsg);
}



//�ظ���ͼ������
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


//�ظ���ͼ��
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
 function curl_file_get_contents($durl){
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $durl);
   curl_setopt($ch, CURLOPT_TIMEOUT, 30);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
   curl_setopt($ch, CURLOPT_REFERER,_REFERER_);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   $r = curl_exec($ch);
    curl_close($ch);
   return $r;
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
		sort($tmpArr);
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
 *  @desc ���������ľ�γ�ȼ������
 *  @param float $lat γ��ֵ
 *  @param float $lng ����ֵ
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

// �����ļ����ݶ�ȡ�ͱ��� ��Լ��������� �ַ���������
function F($name, $value='', $path='cache/') {
    static $_cache = array();
    $filename = $path . $name . '.php';
    if ('' !== $value) {
        if (is_null($value)) {
            // ɾ������
            return unlink($filename);
        } else {
            // ��������
            $dir = dirname($filename);
            // Ŀ¼�������򴴽�
            if (!is_dir($dir))
                mkdir($dir);
            $_cache[$name] =   $value;
            return file_put_contents($filename, "<?php\nreturn " . var_export($value, true) . ";\n?>");
        }
    }
    if (isset($_cache[$name]))
        return $_cache[$name];
    // ��ȡ��������
    if (is_file($filename)) {
        $value = include $filename;
        $_cache[$name] = $value;
    } else {
        $value = false;
    }
    return $value;
}

function getUrlResult($url){
	//var_dump(function_exists('curl_init'));
	//$url = 'http://www.baidu.com';
	//$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx32d4f7aed59fdd4f&secret=a953780a79a025ebf603be93a3c5128e';
	$file_contents		= "";
	if(false && function_exists('file_get_contents'))
	{
		$file_contents = file_get_contents($url);
	}
	else if(function_exists('curl_init'))
	{
		$ch = curl_init();
		//$timeout = 5;
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  
		$file_contents = curl_exec($ch); 
		curl_close($ch);
	}
	return $file_contents;
}

function getUserInfoFromUrl($openid){
	

	$pub = new Model('pubs');
	$pub->find(array('id'=>Request::get('appid')));

	$appid = $pub->cusid;
	$secret = $pub->cussec;

	$jssdk = new JSSDK($pub->cusid,$pub->cussec);
	$access_token = $jssdk->getAccessToken();
	//if($_GET['appid'] == 377){
		//file_put_contents('../controller/1111111.txt',$access_token);
	//}

	$get_info_url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
	$user_info_json = getUrlResult($get_info_url);
	//file_put_contents('../controller/111.txt',$user_info_json);
	$user_info_obj = json_decode($user_info_json);
	//echo 8;
	//var_dump($user_info_obj);
	/*if($user_info_obj->errcode== 42001){
		//echo 11111111;
		$get_token_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
		$access_token_json = getUrlResult($get_token_url);
		$access_token_obj = json_decode($access_token_json);
		$access_token = $access_token_obj->access_token;
		F('access_token_'.$_GET['appid'],$access_token);
		//getUserInfoFromUrl($openid);
		$get_info_url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
		$user_info_json = getUrlResult($get_info_url);
		//file_put_contents('../controller/111.txt',$access_token_json);
		$user_info_obj = json_decode($user_info_json);
	}*/
	if($user_info_obj->errcode){
		return false;
	}else{
		return $user_info_obj;
	}
}


function portalLogin($postObj){

	$m = new Model('micro_wifi_keyword');
	$m->where(array('wid'=>$wid))->find();
	$info = HttpClient::quickGet('http://service.rippletek.com/Portal/Wx/wxFunLogin?appId='.$m->appId.'&appKey='.$m->appKey.'&nodeId='.$m->nodeId.'&openId='.$postObj->FromUserName,3);
	$query=$server."?".http_build_query($param);
	$info=json_decode($info,true);
	if($info['result']=="ok"){
		response_text("�� �� �� �� ��<a href=\"".$info['url']."\">ֱ�ӵ��</a>,�������豸�����������豸��½����������֤��:".$info["token"]."(��֤����Ч��10���ӡ�)",$postObj);
	}else{
		response_text("��¼ʧ�ܣ������ԡ�",$postObj);
	}
}

//�����ͷ�
function response_kefu($postObj){
	global $wid;
	$fromUsername = $postObj->FromUserName;
	$toUsername = $postObj->ToUserName;

	$textTpl = "<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[transfer_customer_service]]></MsgType>
	</xml>";
	$resstr =  sprintf($textTpl, $fromUsername, $toUsername, time());
	echo $resstr;
}

function https_request($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
	log::warn('output==============================='.$output);
    return $output;
}

?>