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
$sql = "select o.id,o.total,o.created_at,o.refuse_data,o.bank_id,o.card_num,o.rename,o.status,o.id_card,o.branch_name from withdrawals_order as o ";
$sql.= "left join member as m on m.id = o.mid ";
$sql.= "where o.mid > 0 and o.id = $oid "; 

$p = new Pagination();
$rs = $p->sql_list($sql); 

$rs[0]['bank_name'] = $banktype[$rs[0]['bank_id']];






$order = $rs[0];

// Array ( [id] => 1 [total] => 10000 [created_at] => 1470297766 [note] => 测试提现 [status] => 1 [name] => 测试姓名 [id_card] => 371301111111111111 [branch_name] => 临沂支行 [bank_name] => 中国农业银行 )
//  Array ( [id] => 4 [total] => 3000 [created_at] => 1470368414 [note] => 测试提现 [status] => 1 [name] => 222 [id_card] => kk [type] => 1111 ) 