<?php
//获取系统设置
$index = "index";
$set = new Model('web_set');
$set->find();
$pics = json_decode($set->pics,true);
$i=0;

//最新动态
$art = new Model('web_art');
$list1 = $art->where("tid in(4,5,6,7,8) and is_home=1")->limit(1)->order('sort Desc,id desc')->list_all();
$list = $art->where("tid in(4,5,6,7,8) and is_home=1")->limit(1,2)->order('sort Desc,id desc')->list_all();

foreach ($list as $k => $v) {
			$introduction = strip_tags($v->introduction); // 去除 HTML、XML 以及 PHP 的标签。
			$title = strip_tags($v->title);
			$list[$k]->title = mb_substr( $title, 0, 16,'utf-8');
			$list[$k]->introduction = mb_substr( $introduction, 0, 40,'utf-8');
		}
// 案例
// dump($list1);
$anli = $art->where(array('tid'=>'3','is_home'=>1))->limit(1)->order('sort desc,id desc')->list_all();
// 视频展示
$shipin = $art->where(array('tid'=>'9','is_home'=>1))->limit(1)->order('sort desc,id desc')->list_all();
foreach ($shipin as $k => $v) {
			$introduction = strip_tags($v->introduction); // 去除 HTML、XML 以及 PHP 的标签。
			$title = strip_tags($v->title);
			$shipin[$k]->title = mb_substr( $title, 0, 16,'utf-8');
			$shipin[$k]->introduction = mb_substr( $introduction, 0, 32,'utf-8');
		}
// 产品信息
$chanp = new Model('web_pro');
$chanp->where(array('status'=>1))->limit(1)->order("rand()")->find();
// dump($chanp);
// 商噗
$shop = new Model('web_shop');
$list_shop = $shop->where(array('status'=>1))->limit(30)->list_all();
//公司简介
$d = new Model('web_danye');
$d->find(array('type'=>'企业介绍'));
$des = mb_substr( $d->des, 0, 32,'utf-8');
// dump($d);