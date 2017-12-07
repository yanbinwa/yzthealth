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

			if($wid!='38263' && $value['id']=='shop1')
			{
			   unset($index[$key]);
			}

			if($wid=='30705' && $value['id']=='nhyk')
			{
				unset($index[$key]);
			}

			if($wid!='30705' && $value['id']=='nhyk1')
			{
			   unset($index[$key]);
			}

			if($wid!='18127' && $value['id']=='qmjjr')
			{
			   unset($index[$key]);
			}

			if($wid!='15528' && $value['id']=='appset')
			{
			   unset($index[$key]);
			}

			if(($wid!='41415' && $wid !='7734' && $wid !='9014' && $wid != '17') && $value['id']=='wtp1')
			{
				unset($index[$key]);
			} 
			if($wid=='38263' && $value['id']=='shop2')
			{
			   unset($index[$key]);
			}


			//爱车去哪儿定制41860
			if($wid!='41860' && $value['id']=='acqn')
			{
				unset($index[$key]);
			}
			if($wid=='41860' && $value['id']=='wyy')
			{
				unset($index[$key]);
			}

			if($wid=='41860' && $value['id']=='shop2')
			{
				unset($index[$key]);
			}
			if($wid!='41860' && $value['id']=='shop3')
			{
			   unset($index[$key]);
			}
			//保利茉莉公馆定制49055
			if($wid!='49055' && $value['id']=='baoli')
			{
				unset($index[$key]);
			}

			//北京长为
			if($wid!='48752' && $wid!='5890' && $value['id']=='wfx')
			{
				unset($index[$key]);
			}

			if(($wid!='9014' && $wid != '17' && $wid != '41860') && $value['id']=='qhb')
			{
				unset($index[$key]);
			}
			if(($wid!='9014' && $wid != '17' && $wid != '37043') && $value['id']=='qp')
			{
				unset($index[$key]);
			}
			if(($wid!='9014' && $wid != '17' && $wid != '49055') && $value['id']=='baoli')
			{
				unset($index[$key]);
			}

			//无锡商城与所有子经销商登录时，显示无锡商城菜单 @haojie
			$sub_return = is_sub_seller();
			if(($wid!='9014' && $wid!='53982' && !in_array($wid,$sub_return['sub_wids'])) && $value['id']=='shop_wuxi')
			{
				unset($index[$key]);
			}
			if(($wid =='53982') && $value['id']=='shop2')
			{
				unset($index[$key]);
			}
			//陕西驾校
			if(($wid!='51688' && $wid != '9014' && $wid != '7734') && $value['id']=='jiaxiao')
			{
				unset($index[$key]);
			}
			//东海鲜
			if(($wid!='25752' && $wid != '9014') && $value['id']=='donghaixian')
			{
				unset($index[$key]);
			}
			//天目温泉
			if($wid!='38034' && $value['id']=='weijiud2')
			{
				unset($index[$key]);
			}								
		
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
	
	if($group->has_id()){
		tusi("成功");
	}

 
}

 