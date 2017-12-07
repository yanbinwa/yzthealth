$(function(){
	//加载后动画
	$("#cntbox").fadeIn(300,function(){
		$("#topbar").delay(300).animate({bottom:"0.2em"},300).animate({bottom:"0"},100);
	});
	//焦点图
	$('.focus').bxSlider({
		auto: true,
		controls: false
	});
	//弹登录面板
	$(".mohelogo, .loginbtn").click(function(){
		event.preventDefault();
		if($("#topbar").hasClass("on")){
			$("#topbar").animate({height:"9.5em"},300).removeClass("on");
			$(".footnav").slideDown();
			$(".mohelogo").removeClass("on");
		}else{
			$("#topbar").animate({height:"29.5em"},300).addClass("on");
			$(".footnav").slideUp();
			$(".mohelogo").addClass("on");
		}
	});
	//底部导航子菜单
	$(".hassub").click(function(){
		event.preventDefault();
		if($(this).hasClass("on")){
			$(this).removeClass("on");
			$(this).next(".subfnav").slideUp(200);
		}else{
			if ($(".hassub").hasClass("on")) {
				$(".hassub").removeClass("on");
				$(".hassub").next(".subfnav").slideUp(200);
			};
			$(this).addClass("on");
			$(this).next(".subfnav").slideDown(200);
		}
	});
	//触控内容时收起子菜单
	$("#cntbox").on('touchstart', function(){
		$(".hassub").removeClass("on");
		$(".hassub").next(".subfnav").hide();
	});
});