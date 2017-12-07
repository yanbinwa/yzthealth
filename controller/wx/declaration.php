<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/18
 * Time: 16:44
 */
$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
if (!empty($wid) && !empty($wxid)) {
    $goup=new Model('aboutus');
    $res=$goup->find(array('id'=>1));
}else{
    die('请从微信进入');
}