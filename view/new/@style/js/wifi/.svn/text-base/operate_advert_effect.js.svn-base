$(document).ready(function(){
    // 广告排序
    $("#ul_imglist").dragsort({ 
        dragSelector: "div", 
        dragBetween: true, 
        dragEnd: saveOrder, 
        placeHolderTemplate: "<li class='placeHolder'><div></div></li>", 
        scrollSpeed: 5 
        }); 
    // 图片拖拽时方法
    function saveOrder() { 
    var data = $("#ul_imglist li").map(function(){ 
    return 
    $(this).children().html(); 
    }).get(); 
    $("input[name=listSortOrder]").val(data.join("|")); 
    }; 
	
	

	
	$("#btnOrder").click(function(){
		var objs = [],number=[],index=0;
		$("ul#ul_imglist>li").each(function(i) {
			objs.push(this.id);
			number.push(++index);
		});
		if (objs.length < 1) {
			alert("请先创建广告！");
			return;
		}
		var para = 'adIds=' + objs+"&number="+number;
		$.ajax({
			type : "POST",
			url : "analysis_ads-order.html",
			//dateType : "json",
			data : para,
			success : function(data) {
				try{
					alert(data);
					//if (data.success) {
						//alert("排序成功！");
					//} else {
						//alert("保存失败请重试！");
					//}
				}catch(e){}
				
			}
		});
	});
	
	var adsOn = $("#adsOn").val()*1;
	
	//启用广告
	$(".openBtn").click(function(){
		if(adsOn >= 3){
			alert("目前只能投放3个广告");
			return false;
		}
		var id = $(this).attr("res");
		var r = window.confirm("确定要投放此广告吗？");
		if(r){
			$.ajax({
				type : "POST",
				url : "analysis_ads-open.html",
				//dateType : "json",
				data : {"adId":id},
				success : function(data) {
					try{
						if (data=='success') {
							window.location.href = "analysis_ads.html";
						} else {
							alert(data);
						}
					}catch(e){}
				}
			});
		}
	});

	$("#btnSearch").click(function(){
		return true;
	});
	
	//停用广告
	$(".stopBtn").click(function(){
		var id = $(this).attr("res");
		var r = window.confirm("确定要停用此广告吗？");
		if(r){
			window.location.href = "analysis_ads-stop.html?adId="+id;
		}
	});
	
	
	//广告预览
	var browser={    
		versions:function(){            
		var u = navigator.userAgent, app = navigator.appVersion;            
		return {                
		trident: u.indexOf('Trident') > -1, //IE内核                
		presto: u.indexOf('Presto') > -1, //opera内核                
		webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核                
		gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1, //火狐内核                
		mobile: !!u.match(/AppleWebKit.*Mobile.*/)||!!u.match(/AppleWebKit/), //是否为移动终端                
		ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端                
		android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1, //android终端或者uc浏览器                
		iPhone: u.indexOf('iPhone') > -1 || u.indexOf('Mac') > -1, //是否为iPhone或者QQHD浏览器                
		iPad: u.indexOf('iPad') > -1, //是否iPad                
		webApp: u.indexOf('Safari') == -1 //是否web应该程序，没有头部与底部            
		};
		}()
    } 
	$(".btn_preview").click(function(){
		 if (browser.versions.webKit||browser.versions.gecko||browser.versions.trident){
		 	var preview_url=$("#preview_url").val();
		 	window.open (preview_url,'go', 'width='+ (screen.availWidth - 10) +',height='+ (screen.availHeight-50) +', toolbar=no, menubar=no, scrollbars=no,resizable=yes,location=no, status=no');
		}
		else{
			alert("不支持在该浏览器下预览，建议使用谷歌浏览器进行预览。");
			return false;
		}
	});

	$(".operate_advert_effect table td img").mouseover(function(){
		var src=$(this).parent().find("img").attr("src");
		$(this).parent().append("<div class='preview_img'><img src='"+src+"'></div>");
	});
	$(".operate_advert_effect table td img").mouseout(function(){
		$(".preview_img").remove();
	});
	$("select[name='Q_tfState_SN_EQ']").change(function(){
		$("form[name='search_form']").submit();
	});
});