<?php
access_control();
$wid = Session::get('wid');
$unid = Session::get('unid');
$orderModel = new Model('orders');

$where = "unid = $unid and status !=2 ";
if(Request::get('trade_no')){
	$trade_no = Request::get('trade_no');
	$where .=" and trade_no='".$trade_no."'";
}

if(Request::get('rename')){
	$rename = Request::get('rename');
	$where .= " and full_name like '%".$rename."%'";
}
if(Request::get('time')){
	$time = Request::get('time');
	$arr = explode("-",Request::get('time'));
	$star = strtotime($arr[0]);
	$end = strtotime($arr[1]);
	$where .= " and updated_at between $star and $end";
}
$db = DB::get_db();
$sql = "select id,total,amount,trade_no,created_at,pay_status,end_time,ll_time,status,telephone,full_name,good_name from orders as o ";
$sql.= "where ".$where." order by id desc ";

$p = new Pagination();
$rs = $p->sql_list($sql); 

$zcount = $orderModel->where(array('pay_status'=>1,'status'=>1,'unid'=>$unid))->count();
$orderModel_arr = $orderModel->where(array('pay_status'=>1,'status'=>1,'unid'=>$unid))->list_all_array();
$zamount = 0;
$shiji = 0;
foreach ($orderModel_arr as $key => $value) {
	$zamount+= $value['total'];
	$shiji+= $value['amount'];
}

if(Request::post() && 'd'==Request::get(1)){
	$id    = intval($_POST['id']);
	$supplier = new Model('orders');
	if(!empty($id)){
		$supplier->find($id);
		if ($supplier->id) {
			$supplier->status = 2;
			$supplier->save();
			$errno = 200;
			$error = '删除成功';			
		}else{
			$errno = 202;
			$error = '参数传递非法';			
		}
	}else{
		$errno = 203;
		$error = '参数传递错误请刷新后重试';
	}
	$rs = array(
	  'errno' =>$errno,
	  'error' =>$error 
	);
	response::json($rs);
}

$orderDateTrue = date('Y-m-d',time()-432000);
$orderAll = new Model('orders');
$order_arr = $orderAll->where(array('pay_status'=>1,'status'=>0,'pay_time@<'=>$orderDateTrue))->list_all_array();
foreach ($variable as $key => $value) {
	$amount = 0;
	if (!empty($value['fx_wxid'])) {
		$brokerage = new Model('brokerage_record');
		$member = new Model('member');
		$pay_set = new Model('ai9me_pay_set');
		$pay_set->find(array('id'=>1));
		$second_rate = $value['total']*$pay_set->second_rate/100; //总分销费用为使用优惠券之前费用
		$first_rate = $value['total']*$pay_set->first_rate/100;//总分销费用为使用优惠券之前费用
		
		$member->find(array('wxid'=>$value['fx_wxid'],'wid'=>$wid,'status'=>2));
		if ($member->id && !empty($member->fx_wxid)) {

			$memberfirst = new Model('member');
			$memberfirst->find(array('wxid'=>$member->fx_wxid,'wid'=>$wid,'status'=>2));
			if ($memberfirst->id) {
				$amount = $second_rate+$first_rate;
				$member->total += $second_rate; 
				$member->save();
				$brokerage->brokerage = $second_rate;
				$brokerage->member_id = $member->id;
				$brokerage->order_id = $value['id'];
				$brokerage->wid = $wid;
				$brokerage->create_time = date('Y-m-d H:i:s',time());
				$brokerage->save();						

				$memberfirst->total += $first_rate; 
				$memberfirst->save();
				$record = new Model('brokerage_record');
				$record->brokerage = $first_rate;
				$record->member_id = $memberfirst->id;
				$record->order_id = $value['id'];
				$record->wid = $wid;
				$record->create_time = date('Y-m-d H:i:s',time());
				$record->save();					
			}else{
				$amount = $first_rate;
				$member->total += $first_rate; 
				$member->save();
				$brokerage->brokerage = $first_rate;
				$brokerage->member_id = $member->id;
				$brokerage->order_id = $value['id'];
				$brokerage->wid = $wid;
				$brokerage->create_time = date('Y-m-d H:i:s',time());
				$brokerage->save();						

			}

		}elseif ($member->id && empty($member->fx_wxid)) {
			$amount = $first_rate;
			$member->total += $first_rate; 
			$member->save();
			$brokerage->brokerage = $first_rate;
			$brokerage->member_id = $member->id;
			$brokerage->order_id = $value['id'];
			$brokerage->wid = $wid;
			$brokerage->create_time = date('Y-m-d H:i:s',time());
			$brokerage->save();				
		}				
	}

	$llsupplier = new Model('lgc_supplier_user');
	$llsupplier->find(array('id'=>$value['llshopid'],'status'=>1));
	if($llsupplier->id){
		$dptc = $value['total']*$pay_set->dptc/100;
		$amount += $dptc;
		$llsupplier->total+= $dptc;
		$llsupplier->save();
		$supplier_record = new Model('brokerage_record');
		$supplier_record->brokerage = $dptc;
		$supplier_record->unid = $llsupplier->id;
		$supplier_record->order_id = $value['id'];
		$supplier_record->wid = $wid;
		$supplier_record->create_time = date('Y-m-d H:i:s',time());
		$supplier_record->save();
	}
	$orderstrue= new Model('orders');
	$orderstrue->find(array('id'=>$value['id']));
	$orderstrue->status = 1;
	$orderstrue->amount = $value['total']-$amount;
	$orderstrue->end_time = date('Y-m-d H:i:s',time());
	$orderstrue->save();

	$llsupplier->find(array('id'=>$value['unid'],'status'=>1));
	if ($llsupplier->has_id()) {
		$llsupplier->total+= $value['amount'];
		$llsupplier->save();
	}			

	
}
