<?php
/**
 *    @desc 添加角色
 * */
$uid = Session::get(mainuid);			//用户ID
$db = DB::get_db();  
$gongzhonghao = $db->query("select * from pubs where uid={$uid}"); 
include another('index');
$wid = Session::get('wid');
foreach($index as $key=>$value ){
	foreach($value['sub'] as $val){
	
	       
	}
}




if(Request::get(1)){
	$group = new Model('think_auth_group');
	$groups =$group->find(array('id'=>Request::get(1)));
	$rule = new Model('think_auth_rule');
	$rules =$rule->find(array('id'=>$groups->rules));
	$rules1 =explode(',',rtrim($rules->name,','));
	
	$gid =Request::get(1);
	
} 



//处理提交 

if(!empty($_POST['uid'])){
	
	$group = new Model('think_auth_group');
	$rule = new Model('think_auth_rule');

	$group->title =$_POST['name'];
	$group->wxid =$_POST['wxid'];
	$group->uid =$_POST['uid'];
	if(!empty($_POST['gid'])){
		$group->id =$_POST['gid'];
		//得到规则ID
		$db = DB::get_db();  
		$result11 = $db->query("select * from think_auth_group where id={$_POST['gid']}"); 
		$rule->id=$result11[0]['rules'];
	}
 
	
	//处理规则
	$rules1 ='';
	foreach($_POST['menusub'] as $val){
		$rules1.=$val.',';
	}
	
	$rule->name=$rules1;
	$rule->wxid=$_POST['wxid'];
	$rule->uid=$_POST['uid'];
	$rule->addtime=time();
	$rid =$rule->save();
	
	if($rid->id){
		$group->rules =$rid->id;
	}
	$group->save();			//
 
	tusi('提交成功');
	Redirect::to('role');


 
}

 