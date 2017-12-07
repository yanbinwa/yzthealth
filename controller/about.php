<?php
//获取系统设置
$set = new Model('web_set');
$set->find();

//董事长的话
$art = new Model('web_art');

$list_dsz = $art->where('tid=1')->limit(5)->order('sort desc,id desc')->list_all();
$list_ry = $art->where('tid=2')->limit(5)->order('sort desc,id desc')->list_all();

//单页
$gk = new Model('web_danye');
$gk->find(array('type'=>'集团概况'));
$gkdes = mb_substr( $gk->des, 0, 120,'utf-8');

$jieshao = new Model('web_danye');
$jieshao->find(array('type'=>'企业介绍'));
$jieshaodes = mb_substr( $jieshao->des, 0, 120,'utf-8');
$wenhua = new Model('web_danye');
$wenhua->find(array('type'=>'企业文化'));
$wenhuades = mb_substr( $wenhua->des, 0, 120,'utf-8');
$linian = new Model('web_danye');
$linian->find(array('type'=>'健康理念'));
$liniandes = mb_substr( $linian->des, 0, 120,'utf-8');
