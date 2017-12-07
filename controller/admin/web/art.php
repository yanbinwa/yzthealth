<?php 
$wid = Session::get('wid');

$art = new Model('web_art');
$where;
if(Request::get('title')){
	$title = Request::get('title');
	$where .= " title like '%".$title."%'";
}
$type = new Model('web_type');
$typearr = $type->map_array('id','name');
// dump($where);
// $list = $art->list_all();
// dump($list);
$p = new Pagination();
$res = $p->model_list($art->where($where)->order('sort desc,id DESC'));

if(Request::get(1)=='del'){
	$id = Request::post('id');
	$art->find(array('id'=>$id));
	if($art->id){
		$art->remove();
	}
}