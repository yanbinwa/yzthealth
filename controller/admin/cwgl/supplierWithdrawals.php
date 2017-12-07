<?php
access_control();

$where = "o.unid>0";

if(Request::get('status')){
	$order_status = Request::get('status');
	if($order_status == 1){
		$where .= " and o.status=0";
	}
	if($order_status == 2){
		$where .= " and o.status=1";
	}
	if($order_status == 3){
		$where .= " and o.status=2";
	}
}

if(Request::get('name')){
	$name = Request::get('name');
	$where .=" and b.uname like '%".$name."%'";
}
if(Request::get('bank') !='0'){
	$bid = Request::get('bank');
	$where .=" and b.type =".$bid;
}

if(Request::get('time')){
	// echo ;
	$time = Request::get('time');
	$arr = explode("-",Request::get('time'));
	$star = strtotime($arr[0]);
	$end = strtotime($arr[1]);
	$where .= " and o.created_at between $star and $end";
}

$db = DB::get_db();
$sql = "select o.id,o.total,o.created_at,o.updated_at,o.note,o.status,b.uname,m.shopname,m.id as mid,b.card_no,b.type from withdrawals_order as o ";
$sql.= "left join z02_zmd_tx_bank as b on b.id = o.bank_card_id ";
$sql.= "left join lgc_supplier_user as m on m.id = o.unid ";
$sql.= " where ".$where; 
$sql.= " order by o.id desc"; 

$p = new Pagination(50);
$rs = $p->sql_list($sql); 

$rebate = new Model('rebate');
$rebate->find(1);



//统计信息
//审核通过
$where1 =$where." and o.status=1 ";
$sql1 = "select count(1) as count,sum(o.total) as total from withdrawals_order as o ";
$sql1.= "left join z02_zmd_tx_bank as b on b.id = o.bank_card_id ";
$sql1.= "left join lgc_supplier_user as m on m.id = o.unid ";
$sql1.= "where ".$where1;
// echo $sql1;
$succ = $db->query($sql1);
$stotal = $succ[0][total];
// print_r($succ);
//待审核
$where2 =$where." and o.status=0 ";
$sql2 = "select count(1) as count,sum(o.total) as total from withdrawals_order as o ";
$sql2.= "left join z02_zmd_tx_bank as b on b.id = o.bank_card_id ";
$sql2.= "left join lgc_supplier_user as m on m.id = o.unid ";
$sql2.= "where ".$where2;
// echo $sql2;
$falid = $db->query($sql2);
$ftotal = $falid[0][total];


//导出
$arr = $db->query($sql);
//print_r($arr);exit;
foreach($arr as $k=>$v){
	if($arr[$k]['status'] == 1){
		$arr[$k]['status'] = "已审核";
	}elseif($arr[$k]['status'] == 0){
		$arr[$k]['status'] = "未审核";
	}
	$arr[$k]['created_at'] = date("Y-m-d H:i:s",$arr[$k]['created_at']);
	$arr[$k]['updated_at'] = date("Y-m-d H:i:s",$arr[$k]['updated_at']);
	$arr[$k]['banktype'] = $banktype[$arr[$k]['type']];
	$arr[$k]['card_no'] = $arr[$k]['card_no']." ";
}

$daochu = Request::get('daochu');
if($daochu == "daochu"){
	$keynames=array('编号','商户名','申请人','申请金额','卡号/账号','银行类型','审核状态','创建时间','处理时间');
	$array_key=array('id','shopname','uname','total','card_no','banktype','status','created_at','updated_at');
	down_excel($arr, $keynames,$array_key, $name = "商家提现.xls",1);
	exit();
}



//商家提现多选处理
if('shangjiatixian'==Request::post('types')){

	$db = DB::get_db();
	$ids = Request::post('ids');
	$deal_type = Request::post('deal_type');
	if($ids){
		foreach ($ids as $id){
		
		    /*
			$updated_at = time();
			$sql_update = "UPDATE withdrawals_order SET status=$deal_type,updated_at=$updated_at WHERE id=".$id;
			$rs = $db->query($sql_update);
			*/
			$orderModel = new Model('withdrawals_order');
			$orderModel->find(array('id'=>$id,'status'=>0));
			if($orderModel->has_id())
			{
				$orderModel->status = 1;
				$orderModel->updated_at = time();
				$orderModel->save();
			}
			
		}
		$return["success"] = true;
		echo json_encode($return);exit;
	}else{
		$return["success"] = false;
		echo json_encode($return);exit;
	}
}
