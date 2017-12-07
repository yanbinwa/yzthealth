<?php
$unid = Session::get('unid');

$orderModel = new Model('withdrawals_order');

$p = new Pagination();
$orderArray = $p->model_list($orderModel->where(array('unid'=>$unid)));
