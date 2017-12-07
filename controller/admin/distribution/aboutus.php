<?php
$lbs = new Model('aboutus');

$lbs->find(array('id'=>1));
if($lbs->try_post()){
    $lbs->fabu_time=time();
    $lbs->save();
    tusi('发布成功');

}