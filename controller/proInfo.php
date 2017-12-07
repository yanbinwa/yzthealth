<?php
$index = "pro";
$set = new Model('web_set');
$set->find();
$id = Request::get(1);
$pro = new Model('web_pro');
$pro->find(array('id'=>$id));
$shop = new Model('web_shop');
$shop->find(array('id'=>$pro->sid));
$new = new Model('web_art');
$lists = $new->where(array('tid'=>10))->limit('10')->order('sort desc,id desc')->list_all();