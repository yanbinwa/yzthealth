<?php
$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
if (!empty($wid) && !empty($wxid)) {
	if (Request::get(1) =='dropload') {
		$sta = intval(Request::get('page'));
		if (empty($sta)) {
			$sta = 0;
		}
		$sta = $sta*5;

		$db = DB::get_db();
		$sql = "SELECT id,shopname,jianjie,dz,lxr,gendered,address from lgc_supplier_user WHERE is_lls='1' and status='1' order by id desc limit $sta,5";
		$artice_array = $db->query($sql);
		foreach ($artice_array as $k => $v) {
			$jianjie = strip_tags($v['jianjie']); // 去除 HTML、XML 以及 PHP 的标签。
			$artice_array[$k]['jianjie'] = mb_substr( $jianjie, 0, 40,'utf-8');

			if($v['gendered'] == 1){
				$artice_array[$k]['gendered'] = '男';
			}else{
				$artice_array[$k]['gendered'] = '女';
			}

		}
		echo json_encode($artice_array);
		die;
	}
}else{
	die('请从微信进入');
}
	