// JavaScript Document


      $(document).ready(function(){

            var $index=$("#index111"),
        $a=$index.find(".mod-index"),
        $main=$index.find(".index-main");
 //   setTimeout(function(){
  //      $a.addClass("on");
  //  },"2000s","ease-in");
    $main.click(function(){
		setTimeout(function(){
        $a.addClass("on");
    },"2000s","ease-in");
        $index.removeAttr("style");
        $a.removeAttr("style");
        if($a.hasClass("on")){
            $a.toggleClass("on");
            $index.css({"-webkit-transition-delay":"1s"}).toggleClass("move");
        }else{
            $index.toggleClass("move");
            $a.css({"-webkit-transition-delay":"2s"}).toggleClass("on");
        }
    });
        // 静态背景图
        $(".html").css("background-image", "url(images/index_bg.jpg)");


        showBtnUp(100);

        $("a.dev-prev").click(function(){
            history.back();
        });

        $("a.dev-next").click(function(){
          history.go(1);
        });

        $("a.dev-index").click(function(){
          //window.open("http://m.vcooline.com/11341?id=3563&amp;wxmuid=11222&amp;wxuid=1363614#mp.weixin.qq.com");
          location.href = "";
        });

        $("a.dev-refresh").click(function(){
          location.reload();
        });

        $("a.dev-tel").attr("href", "tel:电话号码未设置");

        $("a.dev-member").click(function(){
          $(this).attr("href", "")
        });

        $("a.dev-location").click(function(){
          $(this).attr("href", "")
        });
      });
