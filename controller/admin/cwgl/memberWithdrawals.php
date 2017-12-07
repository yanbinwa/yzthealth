<?php
access_control();

$where = "o.mid>0";

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
	$where .=" and m.true_name like '%".$name."%'";
}
if(Request::get('bank') !='0'){
	$bid = Request::get('bank');
	$where .=" and o.bank_id =".$bid;
}

if(Request::get('time')){
	// echo ;
	$time = Request::get('time');
	$arr = explode("-",Request::get('time'));
	$star = strtotime($arr[0]);
	$end = strtotime($arr[1]);
	$where .= " and o.created_at between $star and $end";
}



$m = new Model('withdrawals_order');
$db = DB::get_db();
$sql = "select o.id,o.total,o.created_at,o.status,o.card_num,o.id_card,o.bank_id,o.branch_name,m.true_name,m.id as mid,m.true_name,o.updated_at from withdrawals_order as o ";
$sql.= "left join member as m on m.id = o.mid  ";
$sql.= "where ".$where; 
$sql.= " order by o.id desc"; 


$p = new Pagination(50);
$rs = $p->sql_list($sql); 

$rebate = new Model('rebate');
$rebate->find(1);
//统计信息
//审核通过
$where1 =$where." and o.status=1 ";
$sql1 = "select count(1) as count,sum(o.total) as total from withdrawals_order as o ";
$sql1.= "left join member as m on m.id = o.mid ";
$sql1.= "where ".$where1;
// echo $sql1;
$succ = $db->query($sql1);
$stotal = $succ[0][total];
// print_r($succ);
//待审核
$where2 =$where." and o.status=0 ";
$sql2 = "select count(1) as count,sum(o.total) as total from withdrawals_order as o ";
$sql2.= "left join member as m on m.id = o.mid ";
$sql2.= "where ".$where2;
// echo $sql2;
$falid = $db->query($sql2);
$ftotal = $falid[0][total];




//导出
$arr = $db->query($sql);
foreach($arr as $k=>$v){
	if($arr[$k]['status'] == 1){
		$arr[$k]['status'] = "已审核";
	}elseif($arr[$k]['status'] == 0){
		$arr[$k]['status'] = "未审核";
	}
	/*$arr[$k]['shuihou'] = $arr[$k]['total']-($arr[$k]['total']*$rebate->tx_rate/100);
	$arr[$k]['shouxufei'] = $arr[$k]['total']*$rebate->tx_rate/100;
	$arr[$k]['created_at'] = date("Y-m-d H:i:s",$arr[$k]['created_at']);
	$arr[$k]['updated_at'] = date("Y-m-d H:i:s",$arr[$k]['updated_at']);
	$arr[$k]['banktype'] = $banktype[$arr[$k]['bank_type']];
	$arr[$k]['bank_card'] = $arr[$k]['bank_card']." ";*/
	$arr[$k]['shuihou'] = $arr[$k]['total'];
	$arr[$k]['shouxufei'] = $arr[$k]['total'];
	$arr[$k]['created_at'] = date("Y-m-d H:i:s",$arr[$k]['created_at']);
	$arr[$k]['updated_at'] = date("Y-m-d H:i:s",$arr[$k]['updated_at']);
	$arr[$k]['banktype'] = $banktype[$arr[$k]['bank_type']];
	$arr[$k]['bank_card'] = $arr[$k]['bank_card']." ";	
}

$daochu = Request::get('daochu');
if($daochu == "daochu"){
	$keynames=array('编号','申请人','提现金额','银行卡号','银行类型','审核状态','创建时间','审核时间');
	$array_key=array('id','true_name','total','bank_card','banktype','status','created_at','updated_at');
	down_excel($arr, $keynames,$array_key, $name = "用户提现.xls",1);
	exit();
}



//用户提现多选处理
if('yonghutixian'==Request::post('types')){
	$db = DB::get_db();
	$ids = Request::post('ids');
	$deal_type = Request::post('deal_type');
	if($ids){
		foreach ($ids as $id){
		
			$orderModel = new Model('withdrawals_order');
			$orderModel->find(array('id'=>$id,'status'=>0));
			if($orderModel->has_id())
			{
			    deductionIntegral($orderModel);
				$orderModel->status = 1;
				$orderModel->updated_at = time();
				$orderModel->save();
			}
			//$sql_update = "UPDATE withdrawals_order SET status=$deal_type,updated_at=$updated_at WHERE id=".$id;
			//$rs = $db->query($sql_update);
			//deductionIntegral($orderModel);
		}
		$return["success"] = true;
		echo json_encode($return);exit;
	}else{
		$return["success"] = false;
		echo json_encode($return);exit;
	}
}



//扣除提现金额
function deductionIntegral($orderModel){
	if ($orderModel->mid) {
		$member = new Model('member');
		$member->find($orderModel->mid);
		if (!$member->id) return false;
		$member->total -= $orderModel->total;
		$member->save();
	}
}


