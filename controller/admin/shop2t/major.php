<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');

$m = new Model('z02_major');
$p = new Pagination();
$major = $p->model_list($m->order('id desc'));


