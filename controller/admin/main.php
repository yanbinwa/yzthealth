<?php
if(!Session::get('mainuid')){
	Redirect::index();
}
$userpid = Session::get('userpid');

$user = new Model('user');
$pub = new Model('pubs');

if(Request::get(1)){
	$uid = Request::get(1);
	$user->find($uid);
	if($user->id==Session::get('mainuid') || Session::get('mainuid')==$user->pid){
		$pub->find(array('uid'=>$uid));
		$user->headpic = $pub->headpic;
	}else{
		die();
	}
}else{
	$user->find(Session::get('mainuid'));
	$pub->find(array('uid'=>Session::get('mainuid')));
	$user->headpic = $pub->headpic;
}

Session::set('wid',$pub->id);

//  判断子账户 
$uid =Session::get('uid');
$usersss =$user->where(array('id'=>$uid))->find(); 
if(!empty($usersss->userpid)){			//子账户
	$pubs =$pub->where(array('uid'=>$usersss->userpid))->find();
	Session::set('wid',$pubs->id);
}


$initifurl = '';
if(Request::get('page')){
	$initifurl = '/'.str_replace('@', '/', Request::get('page')).'.html';
}
$un = $user->un;
$level = $user->level_id;
$level_name = '';
set_user_login($user);

$wid = Session::get('wid');
include another('index');

//free function menu
$free = Request::get('free');
$auth_check = new Model('auth_check');
$u = new Model('user');

$u->find(Session::get('uid'));
$haspages = false;
$hybmc = array();


if($free == 'free'){
	$auth_check->find(array('auth_id'=>1));
	$nopages = explode(',',$auth_check->controllers);
	//
	//行业版限制
	if(trim($u->mainhyb)!=''){
		$hybmc = $hybwz_arr;
		unset($hybmc[$u->mainhyb]);
	}
}elseif($free == 'all'){
	$nopages = '';
}else{
	if($u->uprice=='0'){
		$auth_check->find(array('auth_id'=>Session::get('level_id')));
		$nopages = explode(',',$auth_check->controllers);
		//行业版限制
		if(trim($u->mainhyb)!=''){
			$hybmc = $hybwz_arr;
			unset($hybmc[$u->mainhyb]);
		}
	}else{
		$haspages =  explode(',',$u->ubk);
	}

}
//print_r($hybmc);die();
$index1 = array();
$index2 = array();



for($i=0;$i<count($index);$i++){
	$j = count($index1);
	$n = count($index2);

	if($haspages !==false)
	{
		if(in_array($index[$i]['id'],$haspages))
		{
			$index1[$j] = $index[$i];
		}
	}
	else
	{
		if( in_array( $index[$i]['id'], array('weifangc','weiqic','weiyil','weijiud','weimeir','weijians','weizhengw','weiwuy','weijiub','weishangc','weicany','weiship','weilvy','weihunq','weizhuangx','weihuad','weiktv','jiaoy')))
		{
			$index2[$n] = array();
			$index2[$n]['name'] = $index[$i]['name'];
			$index2[$n]['id'] = $index[$i]['id'];

			$index2[$n]['sub'] = array();
			for ($m=0;$m<count($index[$i]['sub']);$m++){
				if(!in_array($index[$i]['sub'][$m]['file'],$nopages)){
					$index2[$n]['sub'][] = array('file'=>$index[$i]['sub'][$m]['file'],'name'=>$index[$i]['sub'][$m]['name']);
				}
			}
			if(empty($index2[$n]['sub'])){
				unset($index2[$n]);
			}
		}
		else
		{
			$index1[$j] = array();
			$index1[$j]['name'] = $index[$i]['name'];
			$index1[$j]['id'] = $index[$i]['id'];

			$index1[$j]['sub'] = array();
			for ($m=0;$m<count($index[$i]['sub']);$m++){
				if(!in_array($index[$i]['sub'][$m]['file'],$nopages)){

					$index1[$j]['sub'][] = array('file'=>$index[$i]['sub'][$m]['file'],'name'=>$index[$i]['sub'][$m]['name']);
				}
			}
			if(empty($index1[$j]['sub'])){
				unset($index1[$j]);
			}
		}
	}
}



include_once(YYUC_FRAME_PATH.'plugin/Auths2.class.php');//加载权限控制类
$auths2 =new Auths2();
//  子账户权限判断,先判断 是不是子账户
$uid =Session::get('uid');
$usersss =$user->where(array('id'=>$uid))->find(); 
if(!empty($usersss->userpid)){			//子账户
	foreach($index1 as $key=>$value){
		if(!($auths2->check($value['id'],$uid))){//  一级栏目没有权限则直接
			unset($index1[$key]);
		}else{
			foreach($value['sub'] as $k=>$v){
				if(!($auths2->check($v['file'],$uid))){
					unset($index1[$key]['sub'][$k]);
				}
			}
		}
	}
}

//$index1  = $index1;
$cataarr = catelist();
$index1  = array_merge($index1,$cataarr);


//end

function set_user_login($mu){
	Auth::im_user();
	Session::set('uid',$mu->id);
	$uid = Session::get('uid');
	user_upgrade($mu);
	if(!Session::has('level_id')){
		Session::set('level_id',$mu->level_id);
	}
	Session::set('un',$mu->un);
	Session::set('ue',$mu->email);
	Session::set('ut',$mu->telephone);

	if($mu->isadmin=='a'){
		Auth::im_admin('admin');
	}
	Session::set('upath','/ups/'.date('Y/m',strtotime($mu->rtime)).'/'.$mu->id.'/');
	File::creat_dir(YYUC_FRAME_PATH.YYUC_PUB.'/'.Session::get('upath'));
	Session::set('headpic',trim($mu->headpic));
	Cookie::set('uid',$mu->id);
	Cookie::set('uuid',md5($mu->un.Request::ip().$mu->pwd));
}
//会员升级或降级操作
function user_upgrade($user){
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

function catelist()
{
   $indexarr = array();
   $tm = new Model('cata_list');
   $lbres = $tm->where(array('pid'=>'0'))->order('sorts desc')->list_all();
   
   
   foreach($lbres as $key=>$v)
   {
      $indexarr[$key]['name'] = $v->cataname;
	  $indexarr[$key]['id'] = 'lm'.$v->id;
	  
	  $cm = new Model('cata_list');
	  $cmres = $cm->where(array('pid'=>$v->id))->order('sorts desc')->map_array('id','cataname');
	  $carray = array();
	  foreach($cmres as $k=>$vv)
	  {
		 $carray[$k]['name'] = $vv;
		 $carray[$k]['file'] = 'news/newslist-c-'.$k;
	  }
	  $indexarr[$key]['sub'] = $carray;
   }
   return $indexarr;
}
