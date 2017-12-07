<?php
$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
if (!empty($wid) && !empty($wxid)) {
	$supplier_id = Request::get('supplier');
	if (Request::get(1) =='dropload') {
		$sta = intval(Request::get('page'));
		if (empty($sta)) {
			$sta = 0;
		}
		$sta = $sta*5;

		$db = DB::get_db();
		$sql = "SELECT id,unid,full_name,telephone,remark_content,remark_type,remark_time from orders WHERE status='1' and unid='$supplier_id' and remark_status='1' order by id desc limit $sta,5";

		$comment_array = $db->query($sql);
		foreach ($comment_array as $k => $v) {
			$full_name = mb_substr($v['full_name'], 0,1);
			$tel_q = substr($v['telephone'], 0,3);
			$tel_h = substr($v['telephone'], -3);
			$comment_array[$k]['full_name'] = $full_name.'**';
			$comment_array[$k]['telephone'] = $tel_q.'***'.$tel_h;
			if (empty($v['remark_content'])) {
				$comment_array[$k]['remark_content'] = '此用户没有填写评论!';
			}
			if($v['remark_type'] == 1){
				$comment_array[$k]['gendered'] = '好评';
			}elseif($v['remark_type'] == 2){
				$comment_array[$k]['gendered'] = '中评';
			}else{
				$comment_array[$k]['gendered'] = '差评';
			}

		}
		echo json_encode($comment_array);
		die;
	}
}else{
	die('请从微信进入');
}