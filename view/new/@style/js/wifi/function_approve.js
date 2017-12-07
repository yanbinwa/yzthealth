var errorMap =   
    { 
        Phones_Empty:["phones","请填写手机号码"],
        Phones_Regular:["phones","请输入正确手机号码"]
}

var rule_num=/^[0-9]*$/;  //全为数字
var rule_phone=/^((1[3|4|5|6|8][0-9]\d{8})?(,|，))+$/;  //验证手机号码 数组
var rule_pwd=/^[A-Za-z0-9]+$/;  //英文、数字


$(document).ready(function(){
    var phones_arr=new Array();//黑白名单电话号码_处理后
    var repeatRes=false;//是否有重复
        //黑白名单
    $(".close_icon").click(function(){
        $(".uploadimg_dialogbox_main").hide();
    });
    $(".whitelist_set").click(function(){
        $("#list_type").val("1");
        loadWhiteAndBlackList(1,1);
        $("#phones").val("");
        $("#tips_err_phones").addClass("hide");
        $(".add_whitelist .isname").html("(白名单用户不限制流量且不需要认证即可上网)");
        $(".uploadimg_dialogbox_main").show();
    });
    $(".blacklist_set").click(function(){
        $("#list_type").val("2");
        loadWhiteAndBlackList(1,2);
        $("#phones").val("");
        $("#tips_err_phones").addClass("hide");
        $(".add_whitelist .isname").html("(黑名单用户不能认证上网)");
        $(".uploadimg_dialogbox_main").show();
    });

    var property={type:"text"};
    var isPhones=function(){
        return checkEmpty("Phones",property);
    }
    $("#add_phone").click(function(){
        $(this).attr("disabled",true);
        if(!isPhones()){
            $(this).attr("disabled",false);
            return;
        }
        var add_phone_res=false;
        var phones=$("#phones").val();
        var type= $("#list_type").val();
        param={"phones":phones,"type":type};
        $.ajax({
            type : "POST",
            url:"/whiteAndBlackList/save.do",
            dataType:"json",
            data:param,
            async:false,
            success:function(data){
                try{
                    var total=data.total;
                    var addNum=data.addNum;
                    var errorsList=data.errorsList;
                    var containsList=data.containsList;
                    var noLonginList=data.noLonginList;
                    var otherTypeContainsList=data.otherTypeContainsList;

                    var tips_err_content="共提交用户："+total+"条，添加成功："+addNum+"条";
                    if(noLonginList.length>0){
                        tips_err_content+="，未知用户："+noLonginList.length+"条";
                    }
                    if(containsList.length>0){
                        tips_err_content+="，重复添加："+containsList.length+"条";
                    }
                    if(otherTypeContainsList.length>0){
                        if(type=="1"){
                          tips_err_content+="，在黑名单中存在："+otherTypeContainsList.length+"条";  
                        }else{
                            tips_err_content+="，在白名单中存在："+otherTypeContainsList.length+"条";   
                        } 
                    }
                    if(errorsList.length>0){
                        tips_err_content+="，格式错误："+errorsList.length+"条";
                    }
                    $("#tips_err_phones").html(tips_err_content);
                    $("#tips_err_phones").removeClass("hide");
                    var phonesContent="";
                    for (var i=0;i<noLonginList.length;i++) {
                       phonesContent=phonesContent+noLonginList[i]+",";
                    };
                    for (var i=0;i<containsList.length;i++) {
                       phonesContent=phonesContent+containsList[i]+",";
                    };
                    for (var i=0;i<otherTypeContainsList.length;i++) {
                       phonesContent=phonesContent+otherTypeContainsList[i]+",";
                    };
                    for (var i=0;i<errorsList.length;i++) {
                       phonesContent=phonesContent+errorsList[i]+",";
                    };
                    if(phonesContent.charAt(phonesContent.length-1)==","){
                        phonesContent=phonesContent.substring(0,phonesContent.length-1);
                    }
                    $("#phones").val("");
                    $("#phones").val(phonesContent);
                     if(addNum>0){
                        add_phone_res=true;
                     }
                      if(add_phone_res){
                            loadWhiteAndBlackList(1,type);
                        }
                }catch(e){}
            }
        });
        $(this).attr("disabled",false);
    });


    
});

    function loadWhiteAndBlackList(pageNow,type){
        var param={"pageNow":pageNow,"type":type}
        $.ajax({
                type : "POST",
                url:"/whiteAndBlackList/pageList.do",
                dataType:"json",
                data:param,
                async:false,
                success:function(data){
                    try{
                        var page=data.page;
                        if(page!=null){
                            var whiteAndBlackList=page.list;
                            var totalPages=page.totalPages;
                            var pageNow=page.pageNow;
                            if(whiteAndBlackList!=null){
                                //填表格
                                $(".add_whitelist_list .table tbody tr").remove();
                                for(var i=0;i<5;i++){
                                    if(i<whiteAndBlackList.length){
                                        var whiteAndBlack=whiteAndBlackList[i];
                                        $(".add_whitelist_list .table").append("<tr><td>"+whiteAndBlack.listId+"</td><td>"+whiteAndBlack.phoneNum+"</td><td><a href='javascript:del("+pageNow+","+type+","+whiteAndBlack.listId+");'>删除</a></td></tr>");
                                    }else{
                                        $(".add_whitelist_list .table").append("<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>");
                                    }
                                }
                                //填页码
                                if(totalPages!=null&&totalPages>=0){
                                    if(totalPages==0){
                                        totalPages=1;
                                    }
                                    $(".paging").html("");
                                    if(pageNow==null||pageNow==0){
                                        pageNow=1;
                                    }
                                    var j=pageNow-2>0?pageNow-2:1;
                                    var anod="";
                                    if(pageNow==1){
                                         anod+="<a href='javascript:loadWhiteAndBlackList("+1+","+type+")'>上一页</a>";
                                    }else{
                                        anod+="<a href='javascript:loadWhiteAndBlackList("+(pageNow-1)+","+type+")'>上一页</a>"
                                    }
                                    for(;j<=totalPages;j++){
                                        if(j!=pageNow){
                                            anod+="<a href='javascript:loadWhiteAndBlackList("+j+","+type+")'>"+j+"</a>";
                                        }else{
                                            anod+="<a class='active' href='javascript:loadWhiteAndBlackList("+j+","+type+")'>"+j+"</a>";
                                        } 
                                    }
                                    if(pageNow==totalPages){
                                         anod+="<a href='javascript:loadWhiteAndBlackList("+totalPages+","+type+")'>下一页</a>";
                                    }else{
                                        anod+="<a href='javascript:loadWhiteAndBlackList("+(pageNow+1)+","+type+")'>下一页</a>"
                                    }

                                    anod+="&nbsp;&nbsp;共<span class='color_2f7ed8'>"+totalPages+"</span>页";
                                    $(".paging").html(anod);
                                }
                            }
                            
                        }
                    }catch(e){}
                    
                }
            });
    }

    function del(pageNow,type,id){
        var param={"id":id}
        $.ajax({
                type : "POST",
                url:"/whiteAndBlackList/delete.do",
                dataType:"json",
                data:param,
                async:false,
                success:function(data){
                    if(data.success==true){
                        alert(data.note);
                    }
                }
            });
        loadWhiteAndBlackList(pageNow,type);
    }
