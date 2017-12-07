<?php
$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
if (!empty($wid) && !empty($wxid)) {
    $aid = Request::get(1);
    $article_list = new Model('artice_list');
    $article = $article_list->find(array('id'=>$aid));
    $cata_list = new Model('cata_list');
    $cata_list->find(array('id'=>$article->cateid));

    $collect = new Model('collect_list');
    $collect->find(array('wxid'=>$wxid,'artId'=>$aid));


    if($_POST['action']=='collect'){
        $collect = new Model('collect_list');
        $collect->find(array('wxid'=>$wxid,'artId'=>$_POST['artId']));
        if(!$collect->has_id()){
            $collect->artId = $_POST['artId'];
            $collect->wxid = $wxid;
            $collect->create_time = time();
            $collect->save();
        }

    }
    if($_POST['action']=='cancel'){
        $collect = new Model('collect_list');
        $collect->find(array('wxid'=>$wxid,'artId'=>$_POST['artId']));
        if($collect->has_id()){
            $collect->remove();
        }

    }
}else{
    die('请从微信进入');
}


