<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}

if(Session::has('wid')){
	$wid = Session::get('wid');
	$wxid = Session::get('wxid');
	$youhuiquan = new Model('youhuiquan');
	//待使用优惠券
	$youhuiquan_arr = $youhuiquan->where(array('wid'=>$wid,'state'=>0,'num@>'=>0,'endtime@>'=>date('Y-m-d H:i:s',time())))->list_all();

	if(!empty($_POST) && $_POST['action']=='obtain'){
		$coupan = $_POST['coupan'];
		$yhq = new Model('youhuiquan');
		$yhq->where(array('id'=>$coupan,'wid'=>$wid,'num@>'=>0,'state'=>0,'endtime@>'=>date('Y-m-d H:i:s',time())))->find();
		if($yhq->id){
			$myyhq = new Model('youhuiquan_list');
			$myyhq->where(array('yid'=>$yhq->id,'openid'=>$wxid,'state'=>'0'))->find();
			if ($myyhq->id) {
				$return = array(
				  'errno' =>'3',
				  'error' =>'此券已领还未使用'
				);
			}else{
				$myyhq->wid = $wid;
				$myyhq->openid = $wxid;
				$myyhq->yid = $yhq->id;
				$myyhq->yhjno= 'yhq'.rand6();
				$myyhq->receivetime = date('Y-m-d H:i:s',time());
				$myyhq->save();	

				$yhq->num = $yhq->num - 1;
	    		$yhq->save();

				$return = array(
				  'errno' =>'1',
				  'jine' =>$yhq->redcon,
				  'error' =>'恭喜你获得优惠券'
				  );				
			}
			
		}else{
			$return = array(
			  'errno' =>'2',
			  'error' =>'非法操作'
			  );			
		}
		
		echo json_encode($return);
		die();
	}

}else{
	die('请从微信进入');
}