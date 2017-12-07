var path=$("#pathUrl").val();
var realpath="";

function activeOpenButtons(phone,qq,weibo,weixin,internet,login){
		//$(".approve_set_c div").addClass("hide");
		if (phone==1) {
			$(".phone_select .checkBox").addClass("active");
			$(".approve_set_c div").addClass("hide");
			$(".phone_set").removeClass("hide");

		}else{
			$(".phone_select .checkBox").removeClass("active");
			// $(".phone_set").addClass("hide");
		};
		if (qq==1) {
			$(".qq_select .checkBox").addClass("active");
			// $(".qq_set").removeClass("hide");
		}else{
			$(".qq_select .checkBox").removeClass("active");
			$(".qq_set").addClass("hide");
		};
		if (weibo==1) {
			$(".weibo_select .checkBox").addClass("active");
			// $(".weibo_set").removeClass("hide");
		}else{
			$(".weibo_select .checkBox").removeClass("active");
			$(".weibo_set").addClass("hide");
		};
		if (weixin==1) {
			$(".weixin_select .checkBox").addClass("active");
			// $(".weixin_set").removeClass("hide");
		}else{
			$(".weixin_select .checkBox").removeClass("active");
			$(".weixin_set").addClass("hide");
		};
		/*
		if (attention==1) {
			$(".attention_select").addClass("active");
			$(".attention_set").removeClass("hide");
		}else{
			$(".attention_select").removeClass("active");
			$(".attention_set").addClass("hide");
		};*/
		if (internet==1) {
			$(".internet_select .checkBox").addClass("active");
			$(".internet_set").removeClass("hide");
		}else{
			$(".internet_select .checkBox").removeClass("active");
			$(".internet_set").addClass("hide");
		};
		if (login==1) {
			$(".login_select .checkBox").addClass("active");
			$(".login_close").hide();
			$(".login_close").addClass("hide");
			$(".fweight_bold ").removeClass("hide");
		}else{
			$(".login_select .checkBox").removeClass("active");
			$(".login_close").addClass("hide");
		};
		/*
		if (whitelist==1) {
			$(".whitelist_select").addClass("active");
			$(".whitelist_set").removeClass("hide");
		}else{
			$(".whitelist_select").removeClass("active");
			$(".whitelist_set").addClass("hide");
		};
		if (blacklist==1) {
			$(".blacklist_select").addClass("active");
			$(".blacklist_set").removeClass("hide");
		}else{
			$(".blacklist_select").removeClass("active");
			$(".blacklist_set").addClass("hide");
		};
		if (qqs==1) {
			$(".qqs_select").addClass("active");
			$(".qqs_set").removeClass("hide");
		}else{
			$(".qqs_select").removeClass("active");
			$(".qqs_set").addClass("hide");
		};
		*/
	}
function getRzinfos(){ 
	loading('初始化...');
	$.ajax({
        type : "POST",
        url:"auth_set-getAttestationInfo.html",
        dataType:"json",
        success:function(data){
        	try{
				loading(false);
				var phone=data.phone_open;
	        	var qq=data.qq_open;
	        	var weibo=data.weibo_open;
	        	var weixin=data.wx_open;
	        	//var attention=data.weiboApprove.type;
	        	var internet=data.onekey_open;
	        	var login=data.url_open;
	        	//var whitelist=data.merchant.isWhiteList;
	        	//var blacklist=data.merchant.blackList;
	        	//var qos=data.merchant.qos;
	        	//activeOpenButtons(phone,qq,weibo,weixin,attention,internet,login,whitelist,blacklist,qos);
				activeOpenButtons(phone,qq,weibo,weixin,internet,login);
	        	/*$("select[id='timeout']").val(data.merchant.timeout);
	        	$("#adShowTimeInterval").val(data.merchant.adShowTimeInterval);
	        	$("#auth_addr").val(data.merchant.auth_addr);
	        	$("#wbuid").val(data.weiboApprove.wbuid);
	        	$("#wbFriends").val(data.weiboApprove.attentionList);
	        	$("#wbid").val(data.weiboApprove.wbid);

	        	$("#weixinAccount").val(data.merchantApprove.account);
	        	$("#weixinAccountId").val(data.merchantApprove.accountId);
	        	$("#wcid").val(data.merchantApprove.wcid);

	        	$("#upriver").val(data.qos.upriver);
	        	$("#downriver").val(data.qos.downriver);
	        	var bigimg = document.getElementById("imsiamge");
	        	if(data.merchantApprove.tdcodepath!="undefined"&&data.merchantApprove.tdcodepath!="null"){
	        		bigimg.src=data.merchantApprove.tdcodepath;
	        	}
	 			
	        	realpath=data.merchantApprove.tdcodepath;*/
			}catch(e){}	
        },
        error:function(data) {
        	alert("链接服务器错误");
		}
	});

}
$(function(){
	getRzinfos();
});
