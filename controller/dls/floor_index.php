<?php
Conf::$session_prefix = 'sdzzbabcd';
if(!Session::get('mainuid')){
	Redirect::to('login');
}
//设置颜色
$wid = Session::get('wid');
$cur_user = new Model('user');
$cur_user->find(Session::get('uid'));
$theme = trim($cur_user->theme);
if($theme==''){
	$theme = 'theme-blue';
}


Session::set('maintheme',$theme);
$user = new Model('user');
$pub = new Model('pubs');

$sysset = new Model('z01_set');
$sysset->find(1);

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

$initifurl = '';
if(Request::get('page')){
	$initifurl = '/'.str_replace('@', '/', Request::get('page')).'.html';
}
$un = $user->un;
$level = $user->level_id;



include another('/config');
//free function menu
$free = Request::get('free');
$auth_check = new Model('auth_check');
$u = new Model('user');

$u->find(Session::get('uid'));
$haspages = false;
$hybmc = array();


//print_r($hybmc);die();
$index1 = array();
for($i=0;$i<count($index);$i++)
{
	$j = count($index1);

	if($haspages !==false){
		if(in_array($index[$i]['id'],$haspages)){
			$index1[$j] = $index[$i];
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
		/*if($index[$i]['id']=="lgc")
		{
			unset($index1[$j]['sub'][1]);
			unset($index1[$j]['sub'][2]);
			unset($index1[$j]['sub'][4]);
		}
		*/
		if(empty($index1[$j]['sub'])){
			unset($index1[$j]);
		}

	}
}

