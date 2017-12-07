

//移除之前表格数据
function delTable()
{
    var testTable = document.getElementById("tablecount");
    while(testTable.hasChildNodes()){
     testTable.removeChild(testTable.lastChild);
    }
}

function loadyears(){
   loading('加载年份...');
   $.ajax({  
   type: 'POST',  
   dataType: 'JSON',  
   url: 'analysis_keliu-getYylist.html',  
   success : function(response){
        try{
			loading(false);
            var recordList = response.yylist;
            for(var i = 0 ;i<recordList.length;i++){
                $("#year").append("<option value="+recordList[i]+">"+recordList[i]+"</option>");    
            }
            var mo=$("#year").val();
            $("#year").val(mo);
        }catch(e){}    
   }
   });
}


function onyears(){
   loading('加载月份...');
   var para='year='+$('#year').val();
   xmlHttp = $.ajax({  
   type: 'POST',  
   data:para,
   dataType: 'JSON',  
   url: 'analysis_keliu-getMmlist.html', 
   success : function(response){
        try{
			loading(false);
            var recordList = response.mmlist;
            $("#mmonth").empty();
            $("#mmonth").append("<option value=''>--请选择--</option>");
            for(var i = 0 ;i<recordList.length;i++){
                $("#mmonth").append("<option value="+recordList[i]+">"+recordList[i]+"</option>");  
            }
            var mo=$("#month").val();
            $("#mmonth").val(mo);
        }catch(e){}
    
    }
   });
}


//选择月份后触发事件
function onmonth(){
    var year=$('#year').val();
    if(year==null||year==''){
        alert("请先选择年份！！");
    }else{
        if($('#mmonth').val()!=""){
            delTable();
            loadcharts();
        }
    }
}

function onclicka(type){
    delTable();
    loadcharts(type);
}


function loadcharts(type){
    loading('正在加载...');
    var year="";
    var month="";
    if(type==1){
        
    }else if(type==2){ 
      var date1=new Date;
      if(year=="" || mon==""){
            year=date1.getFullYear();
            month=date1.getMonth();
            /* month =(mon<10 ? "0"+mon:mon);  */
        }
    }else{
        var year=$('#year').val();
        var month=$('#mmonth').val();
    }


    if(year!="" && month==""){
        onyears();
    } 
        var para='year='+year+'&month='+month;
        xmlHttp = $.ajax({  
       type: 'POST',  
       data:para,
       dataType: 'JSON',  
       url: 'analysis_keliu-getKltjData.html', 
       success : function(response){
            try{
				loading(false);
                //   jQuery('#container_line').highcharts({ title: { text: 'Monthly Average Temperature', x: -20  }, subtitle: { text: 'Source: WorldClimate.com', x: -20 }, xAxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] }, yAxis: { title: { text: 'Temperature (°C)' }, plotLines: [{ value: 0, width: 1, color: '#808080' }] }, tooltip: { valueSuffix: '°C' }, legend: { layout: 'vertical', align: 'right', verticalAlign: 'middle', borderWidth: 0 }, series: [{ name: 'Tokyo', data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6] }, { name: 'New York', data: [-0.2, 0.8, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5] }, { name: 'Berlin', data: [-0.9, 0.6, 3.5, 8.4, 13.5, 17.0, 18.6, 17.9, 14.3, 9.0, 3.9, 1.0] }, { name: 'London', data: [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8] }] });
                $("#showdate1").html(response.yesterdaysum);
                $("#showdate2").html(response.thismonsum);
                $("#showdate3").html(response.lastmonsum);
                $("#showdate4").html(response.allsum);
                jQuery('#container_line').highcharts({
                chart: {
                    type: 'line'
                },
                title: {  //标题选项
                    text: '<b>'+response.yymm+'客流统计表</b>'
                },
                subtitle: {  //副标题选项
                    enabled:false
                },
                xAxis: {   //X轴选项
                    
                    categories: response.statDatelist,
                    tickmarkPlacement: 'on',
                    minTickInterval:2,
                    title: {
                        text: '日期（号）'
                    }
                },
                yAxis: {   //Y轴选项
                    min:0,
                    title: {
                        text: '客流数 (人次)'
                    }
                },
                tooltip: {  //数据点提示框
                    shared: true,
                    valueSuffix: ' 人次',
                    crosshairs:[{
                        width:1,
                        color:'#ccc'
                        }]
                },
                plotOptions: {  //数据点选项
                    line: {
                        lineWidth: 1,
                        marker: {
                            lineWidth: 2
                        }
                    }
                },
                credits:{  //版权信息
                    enabled:false
                    }, 
                series: [{
                    name: '总数',
                    data: response.sumCountlist
                }, {
                    name: '手机',
                    data: response.mobileCounttlist
                }, {
                    name: 'QQ',
                    data: response.qqCountlist
                }, {
                    name: '微博',
                    data: response.weiboCounttlist
                }, {
                    name: '微信',
                    data: response.weixinCounttlist
                }, {
                    name: '一键登录',
                    data: response.onkeyCountlist
                }]
            
        });
if(response.totalsum==0){
    $("#container_pie > *").hide();
}
if(response.totalsum!=0){
    $("#container_pie > *").show();
        jQuery('#container_pie').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
        },
        title: {
            text: '<b>登录方式饼图</b>'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
         plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                slicedOffset:5,
                dataLabels: {
                    enabled: true,
                    distance:1
                }
            }
        },
        credits:{  //版权信息
                enabled:false
                }, 
        legend: {
                enabled:false
            },      
        series: [{
            type: 'pie',
            name: '百分比',
            data: [
                ['手机',response.totalmo],
                ['QQ',response.totalqq],
                ['微博',response.totalwb],
                ['微信',response.totalwx],
                ['一键登录',response.totalon]
            ]
        }]
            });
        }else{
            $('#nullreson').show();
        }
        
        
         var date=new Date;
         mon=month;
         if(year=="" || month==""){
            year=date.getFullYear();
            mon=date.getMonth()+1;
            mon =(mon<10 ? "0"+mon:mon); 
        }

           var table =jQuery("#tablecount");
          
         
        for(var i=1;i<=response.statDatelist.length;i++){
            var date=year+'-'+mon+'-'+response.statDatelist[i-1];
            var sumCount=response.sumCountlist[i-1];
            var mobileCount=response.mobileCounttlist[i-1];
            var qqCount=response.qqCountlist[i-1];
            var weiboCount=response.weiboCounttlist[i-1];
            var weixinCount=response.weixinCounttlist[i-1];
            var onkeyCount=response.onkeyCountlist[i-1];
            table.append("<tr align='center'><td>"+date+"</td><td>"+sumCount+"</td><td>"+mobileCount+"</td><td>"+qqCount+"</td><td>"+weiboCount+"</td><td>"+weixinCount+"</td><td>"+onkeyCount+"</td><tr>");
//设置位置
        } 
        table.append("<tr align='center'><td>总数</td><td>"+response.totalsum+"</td><td>"+ response.totalmo+"</td><td>"+response.totalqq+"</td><td>"+response.totalwb+"</td><td>"+response.totalwx+"</td><td>"+response.totalon+"</td><tr>");

        }catch(e){}
     }// 
    });

}


jQuery(function () {
    loadyears();
    loadcharts();
});

