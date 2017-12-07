<?php
$wid = Session::get('wid');
$m = new Model('artice_list');
if('del'==Request::get(1))
{
	$id = Request::post('id');
	$m->find($id);
	$m->remove();
}
elseif('down'==Request::get(1))
{
	$id = Request::post('id');
	$m->find($id);
	if($m->wid != $wid){
		die();
	}else{
		$m->update(array('id'=>$id),array('status'=>'1'));
	}
}
elseif('up'==Request::get(1))
{
	$id = Request::post('id');
	$m->find($id);
	if($m->wid != $wid){
		die();
	}else{
		$m->update(array('id'=>$id),array('status'=>'0'));
	}
}
else
{	
 
    $id  = Request::get(1);
	$cid = intval(Request::get(2));
	
	//分类查询
	$cm = new Model('cata_list');
	$cmres = $cm->where()->map_array('id','cataname');
	

	$title = trim($_GET['title']);
	
	if($cid!=0)
	{
	   $where['cateid'] = $cid;
	}
	
	if($title!='')
	{
		$where['title@~'] = $title;
	}
	$p = new Pagination();
	$res = $p->model_list($m->where($where)->order('id desc'));
	
}



