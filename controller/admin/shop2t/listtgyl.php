<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');
$m = new Model('z02_tgyl_list');

$kw = trim($_GET['name']);

$where = array('wid'=>$wid);
if('0'==Request::get(1)) //系统
{
  $where['type'] = 0;
}
if('3'==Request::get(1)) //系统
{
  $where['type@<>'] = 0;
}

if($kw!='')
{
	$where['name@~'] = $kw;
}

$p = new Pagination();
$r = $p->model_list($m->where($where)->order('id desc'));

//delete
if('del'==Request::get(1)){
	$m->find(Request::get(2));
	if($m->wid==Session::get('wid')){
		$m->remove();
		tusi('操作成功');
		Redirect::delay_to("listtgyl",1);
	}
	else
	tusi('操作失败');
}


$ids = trim($_POST['id']);
if(!empty($ids))
{
	$cid=explode(',', $ids);
	foreach($cid as $v){
		$m ->find($v);
		if($m->wid==$wid){
			$m->remove();
		}
		else
		$error = '操作失败';
	}
	$error = '操作成功';
	
	$com = array(
	  'errno' =>0,
	  'error' =>$error 
	);
	echo json_encode($com);
	die;
}
