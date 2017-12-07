<?php
$u = new SampleModel();
$m = new Model('lgc_supplier_user');
$needind = true;
if($m->try_post()){
	Conf::$session_prefix = 'sdzzbabcd';

	$floor = null;
	if(Session::has('mu')){
		$floor = Session::get('mu');
	}
	Session::clear();
	Session::set('floor_mu',$floor);
	$m->find(array('uns'=>$m->uns,'passwords'=>$m->passwords));
	if($m->has_id())
	{
	   
		if($m->status == '2'){
			Page::view('login');
			tusi('账户未审核通过');
		}
		elseif($m->status == '0'){
			Page::view('login');
			tusi('审核中');
		}
		else{
			$wid = $m->wid;
			$pub = new Model('pubs');
			$pub->find($wid);
			$uid = $pub->uid;
            $mu = new Model("user");
            $mu->find($uid);
			//$mu->find('63013');
			//$mu->find('58493');

			Session::set('uns',$m->uns);
			Session::set('unid',$m->id);
			Session::set('unpid',$m->pid);

			
			Session::set('floor_mu',$mu);
			
			set_floor_login($m,$mu);
		}
	}
	else{
		$m->passwords = '';

		echo '<script type="text/javascript" charset="utf8">alert("登录信息不正确");window.history.go(-1);</script>';
		exit;
	}
}

function set_floor_login($m,$mu){
	
	
	Conf::$session_prefix = 'sdzzbabcd';
	Session::set('uid',$mu->id);
	Session::set('mainuid',$mu->id);

	$pub = new Model('pubs');
	$pub->find(array('uid'=>$mu->id));
	Session::set('wid',$pub->id);
	Session::set('upath_floor','/ups/'.date('Y/m',strtotime($mu->rtime)).'/'.$mu->id.'/'.$m->id.'axlg/');
	$upath1='ups/'.date('Y/m',strtotime($mu->rtime)).'/'.$mu->id.'/'.$m->id.'axlg';
	//YYUC::set_elfinder(YYUC_FRAME_PATH.YYUC_PUB.'/'.$upath1,Conf::$http_path.$upath1,'我的图库','500k',array('image'));
	YYUC::oss_elfinder($upath1,'我的素材库','5000k',array('image','audio'));
	File::creat_dir(YYUC_FRAME_PATH.YYUC_PUB.Session::get('upath_floor'));
	Redirect::to('/business/floor_index');
}



