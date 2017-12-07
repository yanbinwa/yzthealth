<?php 
$wid = Session::get('wid');

$shop = new Model('web_pro');
$where;
if(Request::get('title')){
	$title = Request::get('title');
	$where .= " name like '%".$title."%'";
}

$p = new Pagination();
$res = $p->model_list($shop->where($where)->order('sort desc,id DESC'));

if(Request::get(1)=='del'){
	$id = Request::post('id');
	$shop->find(array('id'=>$id));
	if($shop->id){
		$shop->remove();
		
	}
}
if(Request::get(1)=='shang'){
	$id = Request::post('id');
	$shop->find(array('id'=>$id));
	if($shop->id){
		$shop->status='1';
		$shop->save();
		
	}
}
if(Request::get(1)=='xia'){
	$id = Request::post('id');
	$shop->find(array('id'=>$id));
	if($shop->id){
		$shop->status='0';
		$shop->save();
		
	}
}