(function() {
	
	//定义dom节点
	var user1 = $('#user1'),
	user2 = $('#user2'),
	earnIntegral = $('#earnIntegral'),
	earnIntegral2 = $('#earnIntegral2'),
	halveNum = $('#halveNum'),
	shengxia = $('#shengxia'),
	surplusNum = $('#surplusNum'),
	attendNum = $('#attendNum'),
	nurmalState = $('#nurmalState'),
	specialState = $('#specialState'),
	toGrabPanel = $('#toGrabPanel'),
	toSharePanel = $('#toSharePanel'),
	isLicensePlate = $('#isLicensePlate'),
	isLicensePlate2 = $('#isLicensePlate2'),
	grablicenseno = $('#grablicenseno'),
	flicenseno = $('#flicenseno'),
	rule_id = $('#rule_id'),
	share_winxin_panel = $('#share_winxin_panel'),
	grabphone = $('#grabphone');
	
	wid = $('#wid').val();
	wxid = $('#wxid').val();
	Cookie('carNO', '');
	Cookie('mobile', '');
		
	function validateDate(mobile, carNo, wid, buyDate, wxid, newCar) {
		var request = {};
		if(!mobile) {
			alert('请填写手机号码！');
			return false;
		}
		var phoneCheckResult = validateMobile(mobile);
		if(phoneCheckResult != true) {
			alert(phoneCheckResult);
			return false;
		}
		request['mobile'] = mobile;
		
		if(!newCar.hasClass('is_license_enabled')) {
			if(!carNo) {
				alert('请填写车牌号码！');
				return false;
			}
			if(!validateLicenseNo(carNo)) {
				alert('车牌号码格式不对！');
				return false;
			}
			request['carNo'] = carNo;
		}else {
			request['carNo'] = '*';
		}
		
		if(buyDate.indexOf('购车日期') != -1) {
			alert('请填写购车日期！');
			return false;
		}
		var selectTime = buyDate.split('-');
		var selectYear = selectTime[0];
		var selectMonth = selectTime[1];
		if(selectYear && selectMonth) {
			var now = new Date(), month = now.getMonth() + 1;
			if(selectYear === '2014') {
				if(selectMonth > month) {
					alert('购车日期不能大于当前日期！');
					return false;
				}
			} 
		}else {
			alert('请填写购车日期！');
			return false;
		}
		request['buyDate'] = buyDate;
		
		if(!wxid && !wid) {
			alert('没有获取到微信号！');
			return false;
		}
		request['wxid'] = $('#wxid').val();
		request['wid'] = $('#wid').val();
		return request;
	}
	
	function validateDateFromFirst(wxid, wid, enkeyValue) {
		var request = {};
		if(!wxid && !wid && !enkeyValue) {
			alert('没有获取到微信号！');
			return false;
		}
		request['wid'] = wid;
		request['wxid'] = wxid;
		request['enkeyValue'] = enkeyValue;
		return request;
	}
	/****
	 * 抢红包开始
	 */
	//去抢积分
	$('#toGrab').tap(function() {
		var rqgsj = parseInt($.trim($('#rqgsj').val()));
		var myDate = new Date();
		var hour = myDate.getHours();  
		var hour1 = parseInt(rqgsj) +5;
		if(hour < rqgsj || hour > hour1){
			tusi('抢红包还没开始，请耐心等待！');
			return false;
		}
		
		var hyid = $.trim($('#hyid').val());
		//判断是不是会员
		if(hyid == ''){
			tusi('对不起，此活动只有是会员才可以参加，请先领取会员卡！');
			return false;
		}
		
		var dd = $.trim($('#dd').val());
		if(dd != ''){
			tusi('您的机会已经用完！');
			return false;
		}
		//判断是否获奖
		var rjx = $('#rhdjx').val();
		if(rjx && rjx[0] != '0'){
			//获奖了,显示奖项，并且填写信息
			$('#toGrabPanel').css('display','block');
		}else{
			//显示不中奖信息
			var wid =  $('#wid').val();
			var wxid =  $('#wxid').val();
			var rjx =  0;
			var hid = $('#rhdid').val();
			var result = {};
			result['rjx'] = 0; 
			result['rhid'] = hid; 
			result['wid'] = wid;
			result['wxid'] = wxid;
			
			$.ajax({
	            url : 'redok.html',
	            data : result,
	            type : 'POST',
	            success : function(m) {
	            	}
	            });
			tusi('对不起，没有抢到红包哦，加油！');
			setTimeout(function(){
				location.reload(true);
			},1888);
		}
	});
	
	$('#grabbtn').tap(function() {
		
		var rmobile = $.trim($('#grabphone').val());
		var rcarNo = $.trim($('#grablicenseno').val());
		var rbuyDate = String($.trim($('#grab_year_sel').val())) + '-' + String($.trim($('#grab_month_sel').val()));
		var wid =  $('#wid').val();
		var wxid =  $('#wxid').val();
		var rjx =  $('#rhdjx').val();
		var rhid = $('#rhdid').val();
		var result;
		result = validateDate(rmobile, rcarNo, wid, rbuyDate, wxid, isLicensePlate);
		if(result === false) {
			return false;
		}
		
		result['rjx'] = rjx[0]; 
		result['rhid'] = rhid; 
		  	$.ajax({
            url : 'redok.html',
            data : result,
            type : 'POST',
            success : function(m){
            		$('#toGrabPanel').css('display','none');
            		window.location.href = "redok1.html";
            }
        });
	});
	/*****
	 * 抢红包结束
	 */
	
	/*****
	 * 抢免费券开始
	 */
	$('#toShare').tap(function() {
		var qgsj = $.trim($('#qgsj').val());
		var myDate = new Date();
		var hour = myDate.getHours(); 
		var hour1 = parseInt(qgsj) +5;
		if(hour < qgsj || hour > hour1){
			tusi('抢免费券还没开始，请耐心等待！');
			return false;
		}
		var d = $.trim($('#d').val());
		if(d != ''){
			tusi('您的机会已经用完！');
			return false;
		}
		//判断是否获奖
		var jx = $('#hdjx').val();
		if(jx && jx[0] != '0'){
			//获奖了,显示奖项，并且填写信息
			$('#toSharePanel').css('display','block');
		}else{
			//显示不中奖信息
			var wid =  $('#wid').val();
			var wxid =  $('#wxid').val();
			var jx =  0;
			var hid = $('#hdid').val();
			var result = {};
			result['jx'] = 0; 
			result['hid'] = hid; 
			result['wid'] = wid;
			result['wxid'] = wxid;
			
			$.ajax({
	            url : 'freeok.html',
	            data : result,
	            type : 'POST',
	            dataType : 'json',
	            success : function(m) {
	            	}
	            });
			tusi('对不起，没有抢到免费券哦，加油！');
			setTimeout(function(){
				location.reload(true);
			},1888);
		}
	});
	
	//抢免费券
	$('#ftogetbtn').tap(function() {
		
		var mobile = $.trim($('#fphone').val());
		var carNo = $.trim($('#flicenseno').val());
		var buyDate = String($.trim($('#share_year_sel').val())) + '-' + String($.trim($('#share_month_sel').val()));
		var wid =  $('#wid').val();
		var wxid =  $('#wxid').val();
		var jx =  $('#hdjx').val();
		var hid = $('#hdid').val();
		var result;
		result = validateDate(mobile, carNo, wid, buyDate, wxid, isLicensePlate2);
		if(result === false) {
			return false;
		}
		
		result['jx'] = jx[0]; 
		result['hid'] = hid; 
		  	$.ajax({
            url : 'freeok.html',
            data : result,
            type : 'POST',
            success : function(m){
            		$('#toShare').css('display','none');
            		$('#toSharePanel').css('display','none');
            		$('#sn').html(m);
            }
        });
		
	});
	/*****
	 * 抢免费券结束
	 */
	
	
	isLicensePlate.tap(function() {
		var self = $(this);
		if(self.hasClass('is_license_enabled')) {
			self.removeClass('is_license_enabled');
			grablicenseno.removeAttr('disabled');
		}else {
			self.addClass('is_license_enabled');
			grablicenseno.val('');
			grablicenseno.attr('disabled', 'true');
		}
	});
	
	isLicensePlate2.tap(function() {
		var self = $(this);
		if(self.hasClass('is_license_enabled')) {
			self.removeClass('is_license_enabled');
			flicenseno.removeAttr('disabled');
		}else {
			self.addClass('is_license_enabled');
			flicenseno.val('');
			flicenseno.attr('disabled', 'true');
		}
	});
	
	
	
	
	$('.close_panel').tap(function() {
		$('#back_rgba').hide();
		$(this).parent().hide();
	});
	
	$('.rule_btn_select').tap(function() {
		var target = $(this);
		if(target.hasClass('empty_class')) {
			target.removeClass('empty_class');
			target.css('background-image', '/res/tg/images/rule2.png');
		}else {
			target.addClass('empty_class');
			target.css('background-image', '/res/tg/images/rule.png');
		}
		var display = rule_id.css('display');
		if(display == 'none') {
			$(this).css('margin-bottom', '0px');
			rule_id.show();
		    window.scrollTo(0, target.offset().top);  
		}else {
			$(this).css('margin-bottom', '15px');
			rule_id.hide();
		}
	});
	
	share_winxin_panel.tap(function() {
		share_winxin_panel.hide();
	});
	
	$('.share_text_btn').tap(function() {
		var target = $('.pafa_back');
		var css = target.css('display');
		if (css === 'none') {
			target.show();
		}else {
			target.hide();
		}
	});
	
	$('#useJF1').tap(toUseJF);
	$('#useJF2').tap(toUseJF);
        
	function toUseJF() {
		var _url = changeUrl('/ebusiness/auto/mobile/upingan/insureOfferCustomer.html?mt=weixin&from=night');
		var mediasource =  getParams('mediasource') || null;
        if(mediasource != null){
            _url = _url+'&mediasource='+mediasource;
        }
        window.location.href = _url;
	}
	

})();
