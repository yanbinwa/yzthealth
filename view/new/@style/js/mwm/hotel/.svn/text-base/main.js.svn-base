

function confirm(title, content, fn1, fn2){
	var d = new iDialog();
	var args = {
		classList: "confirm",
		title:title,
		close:"",
		content:content
	};
	args.btns = [
		{id:"", name:"取消", onclick:"fn.call();", fn: function(self){
			fn1&&fn1.call(this);
			self.die();
		}}
	];
	fn2&&args.btns.push({id:"", name:"确定", onclick:"fn.call();", fn: function(self){
			fn2&&fn2.call(this);
			self.die();
		}});
	d.open(args);
}



function loading(type){
	if(type){
		window.loader = new iDialog();
		window.loader.open({
			classList: "loading",
			title:"",
			close:"",
			content:''
		});
	}else{
		//setTimeout(function(){
			window.loader.die();
			delete window.loader;
		//}, 100);
	}
	
}


$().ready(function(){
	$("input, textarea").on("focus blur", function(evt){
		console.log(evt.target);
		$(".nav_footer").css("display", "focus" == evt.type?"none":"inherit");
	});
});

/*document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
	WeixinJSBridge.call('hideOptionMenu');
});*/