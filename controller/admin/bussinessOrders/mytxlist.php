<?php
//提现类型
$wid = Session::get('wid');
$unid = Session::get('unid');

$where = "o.unid=".$unid;
if(Request::post('name')){
	$name = Request::post('name');
	$where .=" and b.uname like '%".$name."%'";
}
if(Request::post('bank') !='0'){
	$bid = Request::post('bank');
	$where .=" and b.type =".$bid;
}

if(Request::post('time')){
	// echo ;
	$time = Request::post('time');
	$arr = explode("-",Request::post('time'));
	$star = strtotime($arr[0]);
	$end = strtotime($arr[1]);
	$where .= " and o.created_at between $star and $end";
}

if(Request::post('status')){
	$status = Request::post('status');
	if($status == 1){
		$where .= " and o.status=0";
	}elseif($status == 2){
		$where .= " and o.status=1";
	}
	
}


$type = array(
    '1110'=>'微信钱包',
	'1111'=>'支付宝',
	'1112'=>'财付通',
	'1113'=>'中国工商银行',
	'1114'=>'中国建设银行',
	'1115'=>'中国银行',
	'1116'=>'中国农业银行',
	'1117'=>'交通银行',
	'1118'=>'招商银行',
	'1119'=>'民生银行',
	'1120'=>'平安银行'
	);

$db = DB::get_db();
$sql = "select o.total,o.created_at,o.note,o.type,o.status,o.refuse_data,b.type as btype,b.card_no,b.uname from withdrawals_order as o ";
$sql.= "left join z02_zmd_tx_bank as b on b.id = o.bank_card_id ";
$sql.= "where $where ";
$p = new Pagination();
$orders = $p->sql_list($sql); 

$sql_count = "select total,created_at,note,type,status from withdrawals_order where unid=$unid";
$countArr = $db->query($sql_count);
$is_deal = 0;
$no_deal = 0;
foreach ($countArr as $key => $value) {
	if($countArr[$key]["status"]==1){
		$is_deal = $is_deal+$countArr[$key]["total"];
	}
	if($countArr[$key]["status"]==0){
		$no_deal = $no_deal+$countArr[$key]["total"];
	}
}



