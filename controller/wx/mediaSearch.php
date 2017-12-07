<?php
$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
$wechatset = new Model('web_set');
$wechatset->find(array('id'=>2));
if (!empty($wid) && !empty($wxid)) {

	if ($_GET['action'] == 'searchType') {
		Session::set('searchName',$_GET['searchName']);
	}


	if (Request::get(1) =='dropload') {

		$sta = intval(Request::get('page'));
		$search = Session::get('searchName');
		if (empty($sta)) {
			$sta = 0;
		}
		$sta = $sta*10;
		$artice_array = array();
		$article = new Model('artice_list');
		if (!empty($search)) {
			$article_arr = $article->where(array('title@~'=>$search))->limit($sta,10)->list_all_array();
		}else{
			$article_arr = $article->where()->limit($sta,10)->list_all_array();
		}
		
		foreach ($article_arr as $key => $value) {
			$artice_array[$key]['id'] = $value['id'];
			$artice_array[$key]['title'] = mb_substr($value['title'],0,12,'utf-8');
			$artice_array[$key]['maketime'] = $value['maketime'];
		}
		echo json_encode($artice_array);
		die;
	}	

}else{
	die('请从微信进入');
}

	