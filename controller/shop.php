<?php
$index = "shop";
$set = new Model('web_set');
$set->find();

// 
$art = new Model('web_shop');
$p = new Pagination();

$list = $p->model_list($art->where(array('status'=>1))->order('sort desc,id desc'));

foreach ($list as $k => $v) {
	$introduction = strip_tags($v->introduction);// 去除 HTML、XML 以及 PHP 的标签。
	$list[$k]->introduction = mb_substr($introduction,0,66,'utf-8');

}

