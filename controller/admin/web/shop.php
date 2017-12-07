<?php 
$wid = Session::get('wid');

$shop = new Model('web_shop');
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
		$pro = new Model('web_pro');
		$pro->delete(array('sid'=>$shop->id));
	}
}
if(Request::get(1)=='shang'){
	$id = Request::post('id');
	$shop->find(array('id'=>$id));
	if($shop->id){
		$shop->status='1';
		$shop->save();
		$pro = new Model('web_pro');
		$pro->where(array('sid'=>$shop->id))->update(array('status'=>'1'));
	}
}
if(Request::get(1)=='xia'){
	$id = Request::post('id');
	$shop->find(array('id'=>$id));
	if($shop->id){
		$shop->status='0';
		$shop->save();
		$pro = new Model('web_pro');
		$pro->where(array('sid'=>$shop->id))->update(array('status'=>'0'));
	}
}