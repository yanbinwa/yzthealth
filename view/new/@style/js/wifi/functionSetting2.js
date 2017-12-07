
	$(".approve_number .checkBox").click(function(){
		$(this).toggleClass("active");
		var name=$(this).parent();
		var is=$(this).hasClass("active");
		$(".approve_menu a").removeClass("active");
		if (name.hasClass("phone_select")) {
			$(".approve_menu a").eq(0).addClass("active");
			if (is==true) {
				$(".approve_set_c div").addClass("hide");
				$(".phone_set").removeClass("hide");
				$('#wifimohe_authensetphone_open').val(1);
			}else if(is==false){
				$(".approve_set_c div").addClass("hide");
				$(".set_alt").removeClass("hide");
				$('#wifimohe_authensetphone_open').val(0);
			};	
		}else if(name.hasClass("qq_select")) {
			$(".approve_menu a").eq(1).addClass("active");
			
			if (is==true) {
				$(".approve_set_c div").addClass("hide");
				$(".qq_set").removeClass("hide");
				$('#wifimohe_authensetqq_open').val(1);
			}else if(is==false){
				$(".approve_set_c div").addClass("hide");
				$(".set_alt").removeClass("hide");
				$('#wifimohe_authensetqq_open').val(0);
			};
		}else if(name.hasClass("weibo_select")) {
			$(".approve_menu a").eq(2).addClass("active");
			if (is==true) {
				$(".approve_set_c div").addClass("hide");
				$(".weibo_set").removeClass("hide");
				$('#wifimohe_authensetweibo_open').val(1);
			}else if(is==false){
				$(".approve_set_c div").addClass("hide");
				$(".set_alt").removeClass("hide");
				$('#wifimohe_authensetweibo_open').val(0);
			};
		}else if(name.hasClass("weixin_select")) {
			$(".approve_menu a").eq(3).addClass("active");
			if (is==true) {
				$(".approve_set_c div").addClass("hide");
				$(".weixin_set").removeClass("hide");
				$('#wifimohe_authensetwx_open').val(1);
			}else if(is==false){
				$(".approve_set_c div").addClass("hide");
				$(".set_alt").removeClass("hide");
				$('#wifimohe_authensetwx_open').val(0);
			};
		};
	});

	$(".phone_menu").click(function(){
				var isOpt=$(".phone_select .checkBox").hasClass("active");
				// alert(isOpt);
				if (isOpt==true) {
					$(".approve_set_c div").addClass("hide");
					$(".phone_set").removeClass("hide");
				}else if(isOpt==false){
					$(".approve_set_c div").addClass("hide");
					$(".set_alt").removeClass("hide");
				};
			});
	$(".qq_menu").click(function(){
				var isOpt=$(".qq_select .checkBox").hasClass("active");
				if (isOpt==true) {
					$(".approve_set_c div").addClass("hide");
					$(".qq_set").removeClass("hide");
				}else if(isOpt==false){
					$(".approve_set_c div").addClass("hide");
					$(".set_alt").removeClass("hide");
				};
			});
	$(".weibo_menu").click(function(){
				var isOpt=$(".weibo_select .checkBox").hasClass("active");
				if (isOpt==true) {
					$(".approve_set_c div").addClass("hide");
					$(".weibo_set").removeClass("hide");
				}else if(isOpt==false){
					$(".approve_set_c div").addClass("hide");
					$(".set_alt").removeClass("hide");
				};
			});
	$(".weixin_menu").click(function(){
				var isOpt=$(".weixin_select .checkBox").hasClass("active");
				if (isOpt==true) {
					$(".approve_set_c div").addClass("hide");
					$(".weixin_set").removeClass("hide");
				}else if(isOpt==false){
					$(".approve_set_c div").addClass("hide");
					$(".set_alt").removeClass("hide");
				};
			});
	$(".login_select").click(function(){
		var isLogin=$(".login_select").hasClass("active");
		if (isLogin==true) {
			$(".login_open").removeClass("hide");
			$(".login_close").addClass("hide");
			$('#wifimohe_authenseturl_open').val(1);
		}else if(isLogin==false) {
			$(".login_close").removeClass("hide");
			$(".login_open").addClass("hide");
			$('#wifimohe_authenseturl_open').val(0);
		}
	});
	
	
	
	$(".internet_select").click(function(){
		var isinternet=$(".internet_select").hasClass("active");
		if (isinternet==true) {
			$('#wifimohe_authensetonekey_open').val(1);
		}else if(isinternet==false) {
			$('#wifimohe_authensetonekey_open').val(0);
		}
	});
	
	
	
	$(".approve_menu a").click(function(){
		$(".approve_menu a").removeClass("active");
		$(this).addClass("active");
	});
	$(".approve_list .checkBox").click(function(){
		$(this).toggleClass("active");
	});
	$(".checkBox2").click(function(){
		$(this).toggleClass("active");
	});
	$(".whitelist_select").click(function(){
		var iswhite=$(".whitelist_select").hasClass("active");
		if (iswhite==true) {
			$(".whitelist_set").removeClass("hide");
		}else if (iswhite==false) {
			$(".whitelist_set").addClass("hide");
		};
	});
	$(".blacklist_select").click(function(){
		var iswhite=$(".blacklist_select").hasClass("active");
		if (iswhite==true) {
			$(".blacklist_set").removeClass("hide");
		}else if (iswhite==false) {
			$(".blacklist_set").addClass("hide");
		};
	});
	$(".qqs_select").click(function(){
		var iswhite=$(".qqs_select").hasClass("active");
		if (iswhite==true) {
			$(".qqs_set").removeClass("hide");
		}else if (iswhite==false) {
			$(".qqs_set").addClass("hide");
		};
	});
	$(".attention_select").click(function(){
		var iswhite=$(".attention_select").hasClass("active");
		if (iswhite==true) {
			$(".attention_set").removeClass("hide");
		}else if (iswhite==false) {
			$(".attention_set").addClass("hide");
		};
	});