<?php
access_control();
$id = Request::get(1);

$fankui = array('1'=>'建议','2'=>'投诉','3'=>'其它');
if(!empty($id)){
	$complaint = new Model('complaint');
	$complaint->find(array('id'=>$id));

}
if($complaint->try_post()){
	$complaint->is_flag = 1;
	$complaint->load_from_post();
	$complaint->save();

	tusi('保存成功');
	Redirect::delay_to("complaint",1);
}