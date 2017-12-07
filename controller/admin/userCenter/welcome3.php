<?php
Conf::$session_prefix = 'sdzzbabcd';
$unid = Session::get('unid');

$pay_set = new Model('ai9me_pay_set');
$pay_set->find(array('id'=>1));

$supplier = new Model('lgc_supplier_user');

$sh_num = $supplier->where(array('belongs_id'=>$unid,'is_lls'=>0))->count();

$lls_num = $supplier->where(array('belongs_id'=>$unid,'is_lls'=>1))->count();

$shsh_num = $supplier->where(array('belongs_id'=>$unid,'status'=>1,'is_lls'=>0))->count();

$shlls_num = $supplier->where(array('belongs_id'=>$unid,'status'=>1,'is_lls'=>1))->count();

$momey_count =(($shsh_num*$pay_set->dianpu_fee*$pay_set->dp_dl)/100) + (($shlls_num*$pay_set->lls_fee*$pay_set->lls_dl)/100);
