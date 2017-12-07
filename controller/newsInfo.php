<?php
$index = "news";
$id = Request::get(1);
$set = new Model('web_set');
$set->find();
$art = new Model('web_art');
$art->find(array('id'=>$id));
$type = new Model('web_type');
$type->find(array('id'=>$art->tid));
//热门排行
$i = 1;
$list = $art->where(array('is_hot'=>1))->limit('4')->list_all();
//竞猜视频
$video = new Model('web_art');
$video->where(array('is_tj'=>1,'tid'=>9))->order('sort desc,id desc')->find();

$p = new Model('web_pro');
$listp = $p->where(array('status'=>1))->limit(2)->order("rand()")->list_all();