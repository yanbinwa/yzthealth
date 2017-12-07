<?php
access_control();
/**
 * 订单详情
 * 2016-8-6
 */
$type = (int)request::get('type');
$oid = (int)request::get(1);

$rebate = new Model('rebate');
$rebate->find(1);
if (!$type || !$oid) {
	die('订单信息不存在');
}

$db = DB::get_db();
$sql = "select o.id,o.total,o.created_at,o.updated_at,o.note,o.status,b.uname,m.shopname,m.id as mid,b.card_no,b.type from withdrawals_order as o ";
$sql.= "left join z02_zmd_tx_bank as b on b.id = o.bank_card_id ";
$sql.= "left join lgc_supplier_user as m on m.id = o.unid ";
$sql.= " where o.id=$oid"; 
$sql.= " order by o.id desc"; 

$p = new Pagination();
$rs = $p->sql_list($sql); 

$rs[0]['bank_name'] = $banktype[$rs[0]['type']];






$order = $rs[0];

// Array ( [id] => 1 [total] => 10000 [created_at] => 1470297766 [note] => 测试提现 [status] => 1 [name] => 测试姓名 [id_card] => 371301111111111111 [branch_name] => 临沂支行 [bank_name] => 中国农业银行 )
//  Array ( [id] => 4 [total] => 3000 [created_at] => 1470368414 [note] => 测试提现 [status] => 1 [name] => 222 [id_card] => kk [type] => 1111 ) 