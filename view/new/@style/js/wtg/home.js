$(document).ready(function() { 
	
	var ind = 0;
	
    var nav = $(".nav");
    var init = $(".nav .m").eq(ind);
    var block = $(".nav .block");
    block.css({
        "left": init.position().left - 3
    });
    nav.hover(function() {},
    function() {
        block.stop().animate({
            "left": init.position().left - 3
        },
        100);
    });
    $(".nav").slide({
        type: "menu",
        titCell: ".m",
        targetCell: ".sub",
        delayTime: 300,
        triggerTime: 0,
        returnDefault: true,
        defaultIndex: ind,
        startFun: function(i, c, s, tit) {
            block.stop().animate({
                "left": tit.eq(i).position().left - 3
            },
            100);
        }
    });
	
	
	var numval = $("#amount-value").val(),
		num = numval.split(","),
        _html = [],
        spanObj = null,
        bool = false;
	
    for(var i=0;i<num.length;i++){
        _html.push('<span class="number-min"></span>');
    }
    _html.join("");
	
    $("#total").append(_html);
	spanObj = $("#total").find("span");
	
	//alert($(spanObj));
	
    $(spanObj).each(function(i){
		
		 $(this).stop().animate({backgroundPosition:"(0px -"+(num[i]*38)+"px)"}, {duration:3000})
    });

    if($("#iFocus ul li").length>1){
        bool = true;
    }
	
	//底部二维码切换
    $(".info-box").on("mouseover","a",function(){
        if($(this).hasClass("c-active")) return;

        var codeImgs = $(".info-code div.switch-code");
        var dataCode = $(this).data("code");
        $(this).closest("table").find("a").removeClass("c-active");
        $(this).addClass("c-active");
        for(var i=0;i<codeImgs.length;i++){
            if(dataCode == $(codeImgs[i]).data("index")){
                $(codeImgs[i]).siblings("div.switch-code").removeClass("code-active");
                $(codeImgs[i]).addClass("code-active");
                break;
            }
        }
    });
	
	 //滚动切换
    $("#iFocus").slide({ 
        titCell:".btn span", 
        mainCell:"ul",
        effect:"left", 
        autoPlay:bool
    }); 
	
	 
    //焦点图
    $(".picScroll").slide({ 
        mainCell:"ul",
        autoPlay:false,
        effect:"left",
        vis:4,
        scroll:2,
        autoPage:true,
        pnLoop:false 
    });

   
   
    //悬浮效果
    var offTop = $("#product-boxes").offset().top;
    var boxL =$('.product-box div.tabBoxLoading').length -1;
    var objHeight = $("#product-boxes").outerHeight();
    var lastBox = $('.product-box div.tabBoxLoading')[boxL];
    var lastBoxH = $(lastBox).offset().top + $(lastBox).outerHeight();
    var posHeight = lastBoxH - objHeight - $('.product-box').offset().top;
    var offLeft = $("#product-boxes").offset().left -16;

    var showLoad = function(){
        if( (offTop+objHeight) > lastBoxH) return
        var scrollTop = $(window).scrollTop(),
            totalHeiht = scrollTop + $(window).height(),
            winWidth = $(window).width();

         if(totalHeiht>=(offTop+objHeight)){
            $("#product-boxes").addClass("cateFixed");
            
            

            if(totalHeiht >= lastBoxH){
                 console.log(3)
                $("#product-boxes").removeClass("cateFixed");
                $("#product-boxes").addClass("posFixed");
                $("#product-boxes").removeAttr("style");
                $("#product-boxes").css("top",""+posHeight+"px")

            }else{
                 console.log(2)
                $("#product-boxes").removeClass("posFixed");
                $("#product-boxes").addClass("cateFixed");
                $("#product-boxes").removeAttr("style");
                $("#product-boxes").css("bottom","0px")
                $("#product-boxes").css("left",""+offLeft+"px");
                if(winWidth<=$(".container").outerWidth()){
                    console.log(1)
                    $("#product-boxes").removeAttr("style");
                    $("#product-boxes").css("right","0px");
                }
            }
         }
         else{
            $("#product-boxes").removeClass("cateFixed")
         }
    }

    $(window).scroll(function(){
       showLoad();
    })
    $(window).resize(function(){
       showLoad();
    })
})
 //跑马灯
    //上下滚动
    $("#txtMarqueeTop").slide({ 
        mainCell:"ul",
        autoPlay:true,
        effect:"topMarquee",
        interTime:70,vis:7
    });
	
	
	//只显示浏览范围内的图片
    $(".tabBoxLoading").slide({ 
        delayTime:0,
        trigger:"click" , 
        switchLoad:"_src" 
    });
    
	