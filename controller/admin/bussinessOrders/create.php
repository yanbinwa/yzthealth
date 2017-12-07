<?php 

$unid = Session::get('unid');
if (!$unid) {
	Redirect::to('/bussiness/login');
}
// $bussiness = new Model('lgc_supplier_user');
// $bussiness->find($unid);


$belongs_dls = new Model('distribution');
$dailiarray = $belongs_dls->where(array('status'=>1))->map_array('id','name');
$dailiarray[''] = '--请选择--';


$m = new Model('orders');
// if ($id = (int)request::get(1)) {
// 	$m->find($id);
// }

$supperAdminUser = $unid;

$base = 1000;

if ($m->try_post()) {
	if($m->voucher && $m->voucher2){
		$m->unid = $unid;
		$m->created_at = time();
		$m->status = 1;
		$m->trade_no = 'AXLG'.time().rand(1000,9999);
		$m->total = 10*$m->total;
		$m->save();
		//用戶返點
		//addMemberPoint($m->id);
		//TODO 跳转在线支付
		Redirect::to('orders');
	}else{
		tusi("请上传凭证");
	}
	
}