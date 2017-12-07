<?php
Page::ignore_view();

$pubs = new Model('pubs');
$pubs->find(array('id'=>'1'));

$appid = $pubs->cusid;
$secret = $pubs->cussec;

$code = $_GET['code'];
$gurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $gurl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$output = curl_exec($ch);
curl_close($ch);

$rs = json_decode($output);
//openid
$openid = $rs->openid;

// access_token 应该全局存储与更新，以下代码以写入到文件中做示例
// $access_token = Cache::get('access_token_'.$appid);

$wid = '1';
$access_token = getAccessTokenByWid($wid);

$infourl = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $infourl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$output = curl_exec($ch);
curl_close($ch);
$rsinfo = json_decode($output);

//log::warn('output3333333333333333333333333333333:'.$output);

$headimgurl = $rsinfo->headimgurl;
$nickname = $rsinfo->nickname;


Session::set('headimgurl',$headimgurl);
Session::set('nickname',$nickname);
Session::set('wxid',$openid);


$tourl =  $_GET['tourl'];
Redirect::to($tourl);
	
	




