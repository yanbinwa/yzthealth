<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');

$m = new Model('advert');
$p = new Pagination();
$r = $p->model_list($m->order('id desc'));


