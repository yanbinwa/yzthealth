<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');
$m = new Model('z02_index_notice');



//delete
if('del'==Request::get(1)){
	$m->find(Request::get(2));
	if($m->wid==Session::get('wid')){
		$m->remove();
		tusi('操作成功');
		Redirect::delay_to("noticeList",1);
	}
	else
	tusi('操作失败');
}



$where = array('wid'=>$wid);
$p = new Pagination();
$r = $p->model_list($m->where($where)->order('id desc'));


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

