<?php
header("Content-type:text/html;charset=utf-8");
include_once "wxBizMsgCrypt.php";
$token='sdzzb110';
$encodingAesKey='sdzzb110sdzzb110sdzzb110sdzzb110sdzzb110232';
$appId='wx02560241e9071ed8';
$pc = new WXBizMsgCrypt($token, $encodingAesKey, $appId);
if($GLOBALS["HTTP_RAW_POST_DATA"] !=1){
	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

	$msg = '';
	if (!empty($postStr)){
		/*<xml>
		 <AppId>
		 <![CDATA[wx210c5ff63f00c188]]>
		 </AppId>
		 <CreateTime>1438756820</CreateTime>
		 <InfoType>
		 <![CDATA[component_verify_ticket]]></InfoType>
		 <ComponentVerifyTicket>
		 <![CDATA[ticket@@@RA7PfxJXlQa98EwEqIRH6owppmE0RHpzjyeTZOBilSCI8Bz4332ZZkPcirmLyfavrFu9-0vXr-3xqGNOfbtwOw]]>
		 </ComponentVerifyTicket>
		 </xml>

		 <xml><AppId><![CDATA[wx210c5ff63f00c188]]></AppId>
		 <CreateTime>1438757420</CreateTime>
		 <InfoType><![CDATA[component_verify_ticket]]></InfoType>
		 <ComponentVerifyTicket><![CDATA[ticket@@@RA7PfxJXlQa98EwEqIRH6owppmE0RHpzjyeTZOBilSCI8Bz4332ZZkPcirmLyfavrFu9-0vXr-3xqGNOfbtwOw]]></ComponentVerifyTicket>
		 </xml>
		 */
		$msg_sign = $_REQUEST['msg_signature'];
		$timeStamp= $_REQUEST['timestamp'];
		$nonce= $_REQUEST['nonce'];
		$errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $postStr, $msg);
		if ($errCode == 0) {
			$postObj = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
			/*
			 SimpleXMLElement Object
			 (
			 [AppId] => wx210c5ff63f00c188
			 [CreateTime] => 1439277138
			 [InfoType] => unauthorized
			 [AuthorizerAppid] => wx402cb360987b6026
			 )
			 ENDEVENT
			 */
			if($postObj->InfoType == 'unauthorized') //È¡ÏûÊÚÈ¨
			{
			   $pubs= new Model("pubs");
			   $ok=70;
			   $pubs->where(array('cusid'=>$postObj->AuthorizerAppid))->find();
			   if($pubs->has_id())
			   {
				   $ok=90;
				   $pubs->update(array('cusid'=>$postObj->AuthorizerAppid),array('isbind'=>0));
			   }
			}
			//file_put_contents('info.txt',$ok.'......'.print_r($postObj,true),FILE_APPEND);
			file_put_contents('ComponentVerifyTicket.json',$postObj->ComponentVerifyTicket);
			Cache::set('ComponentVerifyTicket',$postObj->ComponentVerifyTicket);
		} else {
			print($errCode . "\n");
		}
	}
}
?>