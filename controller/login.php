<?php
$u = new SampleModel();
if(Request::get('un'))
{
	$un = Request::get('un');
}


function set_user_login($mu){
	
	Auth::im_user();
	Session::set('uid',$mu->id);
	Session::set('mainuid',$mu->id);
	$uid = Session::get('uid');
	user_upgrade($uid);
	$uu = new Model('user');
	$uu->find($mu->id);
	//设置客户类型
	$uu->is_wx =1;
	$uu->save();
	$mu = $uu;
	Session::set('level_id',$mu->level_id);
	Session::set('un',$mu->un);
	Session::set('userpid',$mu->userpid);
	Session::set('ue',$mu->email);
	Session::set('ut',$mu->telephone);
	Session::set('upc',$mu->ispwdcg);
		
	if($mu->isadmin=='a'){
		Auth::im_admin('admin');
	}
	
    Session::set('upath','/ups/'.date('Y/m',strtotime($mu->rtime)).'/'.$mu->id.'axlg/');
	$upath1='ups/'.date('Y/m',strtotime($mu->rtime)).'/'.$mu->id.'axlg';
	YYUC::oss_elfinder($upath1,'我的素材库','5000k',array('image','audio'));
	File::creat_dir(YYUC_FRAME_PATH.YYUC_PUB.Session::get('upath'));
	Session::set('headpic',trim($mu->headpic));
	Cookie::set('uid',$mu->id);
	Cookie::set('uuid',md5($mu->un.Request::ip().$mu->pwd));
	Redirect::to('/start');
}

$needind = true;
if($u->try_post()){
    if(!$u->vercodeok())
	{
		tusi('验证码不正确');
	}
	else
	{
	    Conf::$session_prefix = 'sdzzbabcd';
		$mu = null;
		if(Session::has('mu')){
			$mu = Session::get('mu');
		}
		Session::clear();
		Session::set('mu',$mu);
		Cookie::remove('weixinid');
		$needind = false;	
		$mu = new Model('user');
		$mu->un = $u->un;
		$mu->pwd = $u->pwd;
		$mu->email = $u->un;
		$mu->telephone = $u->un;
		if(!empty($u->un) && !empty($u->pwd) && ($mu->is_real(array('un','pwd')) || $mu->is_real(array('email','pwd')) || $mu->is_real(array('telephone','pwd')))){			
			if($mu->isstop != '0'){
				tusi('账户已经被停用或作废');				
			}else{		
				set_user_login($mu);
			}
			
		}else{
			$u->pwd = '';
			tusi('登录信息不正确');
		}
	}
}

//会员升级或降级操作
function user_upgrade($uid){ 
	$user = new Model('user');
	$user->find($uid);
	Session::remove('level_id');
	// uesr upgrade
	if((strtotime($user->mendtime) < time() || $user->level_id == 1) && $user->next_level_id != 0){
		$user->level_id = $user->next_level_id;
		Session::set('level_id',$user->next_level_id);
		$user->mendtime = $user->next_mendtime;
		if($user->save()){
			$user->next_level_id = '';
			$user->next_mendtime = '';
			$user->save();
			return true;
		}
	}
	//user donwgrading
	if(strtotime($user->mendtime)<time() && $user->next_level_id == 0){
		$user->level_id = 1;
		Session::set('level_id',1);
		$user->mendtime = '';
		$user->save();
		return true;
	}	
}

