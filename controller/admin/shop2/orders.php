<?php
$u = new Model('z01_set');
$u->find(1);
include_once("/mnt/sdzzb/wxpay/WxPayHelper.php");
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');

$m = new Model('z02_order');

$where = array('wid'=>$wid,  'or' => array('status@<>'=>'0','pay_type'=>'0') );

if('w'==Request::get(1))
{
	$where['wlstatus'] = 0;
}

$keyword = trim($_GET['keyword']);
$sstatus = intval($_GET['sstatus']);

if(!empty($keyword))
{
	$where['id@~'] = $keyword;
}
$searchtime = trim($_GET['searchtime']);
$timearr = explode('-',$searchtime); 
$start_at = trim($timearr[0]);
$end_at = trim($timearr[1]);

if ($searchtime) {
	$where['create_time@>='] = $start_at;
}
if ($searchtime) {

	$where['create_time@<='] = $end_at;
}

if($sstatus!=0)
{
	if($sstatus==1)
	{
		$where['wlstatus'] = 1;
	}
	else
	{
		$where['wlstatus'] = 0;
	}
}

$where['is_del'] = 0;
$p = new Pagination();



$rs = $p->model_list($m->where($where)->order('id desc'));


$dzm = new Model('micro_membercard_dz_list');
foreach($rs as $v)
{
	$dzm->find($v->addid);
	$v->addid = $dzm->shr;
	$v->kdtype= $kuaidi_arr[$v->kdtype];
}

$wcount = $m->where(array('wid'=>$wid,'wlstatus'=>0,'status@<>'=>'0','pay_type@<>'=>'1','is_del'=>0))->count();



$pay_status = array('0'=>'未支付','1'=>'已支付','2'=>'已完成','3'=>'申请退款','4'=>'已退款','5'=>'退款失败');
// 0:未支付,1：已支付 2：已完成 3退款中 4 已退款 5 退款拒绝
$pay_type = array('0'=>'货到付款','1'=>'支付宝','2'=>'财付通','3'=>'微支付','4'=>'积分支付' ,'5'=>'统一付支付','8'=>'银联支付');
$distribution_status = array('-1'=>'无需物流(已处理)','0'=>'未发货','1'=>'已发货');



if(Request::post() && 's'==Request::get(1))
{
	$oid    = intval($_POST['oid']);
	$kdtype = trim($_POST['kdtype']);
	$kd_no  = trim($_POST['kd_no']);
	if(!empty($oid) && !empty($kdtype))
	{
		$m->find($oid);
		if($m->wid==$wid)
		{
			if($m->pay_type==3)
			{
				$psetm = new Model('ai9me_pay_set');
				$psetm->find(array('token'=>$wid,'pc_type'=>'wxpay','pc_enabled'=>1));
				if(!empty($psetm->paySignKey))
				{
					if($kdtype=='-1')
					{
						$m->update($condition=array('id'=>$oid),$data=array('kdtype'=>$kdtype,'kd_no'=>$kd_no,'wlstatus'=>1,'send_time'=>date('Y-m-d H:i:s',time())));
						if($m)
						{
							$errno = 200;
							$error = '发货成功';
						}
						else
						{
							$errno = 201;
							$error = '系统错误请刷新后重试';
						}
					}
					else
					{
						$access_token = getaccess_token($psetm->appId,$psetm->appSecret);
						if(!empty($access_token))
						{
							$timestamp = time();
							$d = array();
							$d['appid']          = $psetm->appId;
							$d['appkey']         = $psetm->paySignKey;
							$d['openid']         = $m->wxid;
							$d['transid']        = $m->trade_no;
							$d['out_trade_no']   = 'WXSC_'.$oid;
							$d['deliver_timestamp'] = $timestamp;
							$d['deliver_status'] = "1";
							$d['deliver_msg']    = "ok";
							ksort($d);
							$paras = array();

							foreach ($d as $k=>$v){
								$paras[]= $k."=".$v;
							}
							$paras_str = implode('&', $paras);
							$sign = strtolower(sha1($paras_str));

							$url = "https://api.weixin.qq.com/pay/delivernotify?access_token=".$access_token;
							$txt = '{
						     "appid" : "'.$d['appid'].'",
						     "openid" : "'.$d['openid'].'",
						     "transid" : "'.$d['transid'].'",
						     "out_trade_no" : "'.$d['out_trade_no'].'",
						     "deliver_timestamp" : "'.$d['deliver_timestamp'].'",
						     "deliver_status" : "'.$d['deliver_status'].'",
						     "deliver_msg" : "'.$d['deliver_msg'].'",
						     "app_signature" : "'.$sign.'",
						     "sign_method" : "sha1"
						 }';

							$status = https_post($url,$txt);
							$status = json_decode($status);

							if($status->errmsg=="ok"){

								$m->update($condition=array('id'=>$oid),$data=array('kdtype'=>$kdtype,'kd_no'=>$kd_no,'wlstatus'=>1,'send_time'=>date('Y-m-d H:i:s',time())));
								if($m)
								{
									$errno = 200;
									$error = '发货成功';
								}
								else
								{
									$errno = 201;
									$error = '系统错误请刷新后重试';
								}
							}
							else
							{
								$errno = 206;
								$error = $status->errmsg;
							}
						}
						else
						{
							$errno = 205;
							$error = '获取access_token失败';
						}
					}
				}
				else
				{
					$m->update($condition=array('id'=>$oid),$data=array('kdtype'=>$kdtype,'kd_no'=>$kd_no,'wlstatus'=>1,'send_time'=>date('Y-m-d H:i:s',time())));
					if($m)
					{
						$errno = 200;
						$error = '发货成功';
					}
					else
					{
						$errno = 206;
						$error = '系统错误请刷新后重试';
					}
				}
			}
			else
			{
				$m->update($condition=array('id'=>$oid),$data=array('kdtype'=>$kdtype,'kd_no'=>$kd_no,'wlstatus'=>1,'send_time'=>date('Y-m-d H:i:s',time())));
				if($m)
				{
					$errno = 200;
					$error = '发货成功';
				}
				else
				{
					$errno = 206;
					$error = '系统错误请刷新后重试';
				}
			}
		}
		else
		{
			$errno = 202;
			$error = '参数传递非法';
		}
	}
	else
	{
		$errno = 203;
		$error = '参数传递错误请刷新后重试';
	}
	$rs = array(
	  'errno' =>$errno,
	  'error' =>$error 
	);
	response::json($rs);
}
if(Request::post() && 'd'==Request::get(1)){
	$id    = intval($_POST['id']);
	if(!empty($id)){
		$m->find($id);
		if($m->wid==$wid){
			$m->update($condition=array('id'=>$id),$data=array('is_del'=>1));
			if($m){
				$errno = 200;
				$error = '删除成功';
			}else{
				$errno = 201;
				$error = '系统错误请刷新后重试';
			}
		}else{
			$errno = 202;
			$error = '参数传递非法';
		}
	}else{
		$errno = 203;
		$error = '参数传递错误请刷新后重试';
	}
	$rs = array(
	  'errno' =>$errno,
	  'error' =>$error 
	);
	response::json($rs);
}
