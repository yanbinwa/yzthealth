function g(o){
     return document.getElementById(o);
   }
 function HoverLi(m,n,c){
    /* var dw=$("#dw").val(n);
     for(var i=1;i>=counter;i++){
         g('tb_'+m+i).className='';
         g('tbc_'+m+i).className='tabbox_undis';
      }
      g('tbc_'+m+n).className='tabbox_dis';
      g('tb_'+m+n).className='on';
       */
       g('tbc_1'+m).className="tabbox_undis";
       g('tbc_1'+n).className="tabbox_undis";
       g('tbc_1'+c).className="tabbox_dis";
       g('tb_1'+c).className="on";
        g('tb_1'+m).className="";
       g('tb_1'+n).className="";
       
       
    }
jQuery(function(){
    jQuery("#tb_11").click(function(){
        jQuery("#tb_11>span>p").addClass("font20");
        jQuery("#tb_12>span>p").removeClass("font20");
        jQuery("#tb_13>span>p").removeClass("font20");
        });
    jQuery("#tb_12").click(function(){
        jQuery("#tb_12>span>p").addClass("font20");
        jQuery("#tb_11>span>p").removeClass("font20");
        jQuery("#tb_13>span>p").removeClass("font20");
        });
    jQuery("#tb_13").click(function(){
        jQuery("#tb_13>span>p").addClass("font20");
        jQuery("#tb_12>span>p").removeClass("font20");
        jQuery("#tb_11>span>p").removeClass("font20");
        });
    
    });


//选择月份后触发事件
function onmonth(){
    var year=$('#year').val();
    if(year==null||year==''){
        alert("请先选择年份！！");
    }else{
     if($('#mmonth').val()!=""){
         var month=$('#mmonth').val();
         delTable();
         /* document.searmonthe.submit(); */
         loadcharts(month);
     }
    }
}

//移除之前表格数据
function delTable()
{
    var testTable = document.getElementById("tablecount");
    while(testTable.hasChildNodes()){
        testTable.removeChild(testTable.lastChild);
    }
}

//选择年份,加载相应月份
function onyears(){
	loading('加载月份...');
    var para='year='+$('#year').val();
    xmlHttp = $.ajax({  
 type: 'POST',  
 data:para,
 dataType: 'JSON',  
 url: 'analysis_addata-getMmlist.html', 
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

//加载年份数据
function loadyears(){
	   loading('加载年份...');
       xmlHttp = $.ajax({  
       type: 'POST',  
       dataType: 'JSON',  
       url: 'analysis_addata-getYylist.html',  
       success : function(response){
		    loading(false);
            var recordList = response.yylist;
            for(var i = 0 ;i<recordList.length;i++){
                $("#year").append("<option value="+recordList[i]+">"+recordList[i]+"</option>");    
            }
            var mo=$("#year").val();
            $("#year").val(mo);
       }
       });
    }

jQuery(function() {
    loadyears();
    loadcharts();
});

    function onclicka(type){
        delTable();
        loadcharts(type);
    }

//加载highchart数据,以及表格数据
function loadcharts(type){
		loading('加载数据...');
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
        var dw=$('#dw').val();
        if(dw!=""){
            HoverLi(1,dw,3);
        }
        var para='mon='+$('#mon').val()+'&year='+year+'&month='+month;
        xmlHttp = $.ajax({  
     type: 'POST',  
       data:para,
     dataType: 'JSON',  
     url: 'analysis_addata-getGgtjData.html', 
     success : function(response){
       			loading(false);
                $("#showdate1").html(response.yesterdayshow);
                $("#showdate2").html(response.yesterdayclick);
                $("#showdate3").html(response.yesterdayrate);
                $("#showdate4").html(response.totalshow);
                $("#showdate5").html(response.totalclick);
                $("#showdate6").html(response.totalclickrate);
       
        var date=new Date;
            var mon=month;
             if(year=="" || month==""){
                year=date.getFullYear();
                mon=date.getMonth()+1;
                mon =(mon<10 ? "0"+mon:mon); 
            }

        var totalshow=0;
        var totalclick=0;
        var totalrate=0;
        var table=jQuery("#tablecount");
        for(i=1;i<=response.statDatelist.length;i++){
            var newCell1=year+'-'+mon+'-'+response.statDatelist[i-1];
            var newCell2=response.xtlist[i-1];
            var newCell3=response.dtlist[i-1];
            var newCell4=response.djltlist[i-1]+"%";
            table.append("<tr align='center'><td>"+newCell1+"</td><td>"+newCell2+"</td><td>"+newCell3+"</td><td>"+newCell4+"</td></tr>");
            totalshow+=response.xtlist[i-1];
            totalclick+=response.dtlist[i-1]; 
            }
        var dinjiel=totalshow==0?0:Math.round(totalclick*100/totalshow);
        table.append("<tr align='center'><td>总计</td><td>"+totalshow+"</td><td>"+totalclick+"</td><td>"+dinjiel+"%</td></tr>");
      jQuery('#container_show').highcharts({
          chart: {
              type: 'line',
          },
           title: {  //标题选项
                text: '<b>'+response.yymm+'广告展示统计表</b>'
          },
          subtitle: {  //副标题选项
                enabled:false
          },
          xAxis: {
              categories: response.statDatelist,
              tickmarkPlacement: 'on',
                minTickInterval:2,
              title: {
                  text: '日期（号）'
              }
          },
          yAxis: {
              title: {
                  text: '展示次数（次）'
              },
              labels: {
                  formatter: function() {
                      return this.value;
                  }
              }
          },
            legend: {
                enabled:false
            },
          tooltip: {
              shared: true,
              valueSuffix: '（次）',
                crosshairs:[{
                    width:1,
                    color:'#ccc'
                    }]
          },
        credits:{  //版权信息
                enabled:false
                }, 
          plotOptions: {
              area: {
                  stacking: 'normal',
                  lineColor: '#F8A31F',
                  lineWidth: 1,
                  marker: {
                      lineWidth: 1,
                      lineColor: '#F8A31F'
                  }
              }
          },
          series: [{
              name: '展示次数',
              data: response.xtlist
          }]
      });
        //展示次数
      jQuery('#container_click').highcharts({
          chart: {
              type: 'line',
          },
           title: {  //标题选项
                text: '<b>'+response.yymm+'广告点击统计表</b>'
          },
          subtitle: {  //副标题选项
                enabled:false
          },
          xAxis: {
              categories: response.statDatelist,
              tickmarkPlacement: 'on',
                minTickInterval:2,
              title: {
                  text: '日期（号）'
              }
          },
          yAxis: {
              title: {
                  text: '点击次数（次）'
              },
              labels: {
                  formatter: function() {
                      return this.value;
                  }
              }
          },
            legend: {
                enabled:false
            },
          tooltip: {
              shared: true,
              valueSuffix: '（次）',
                crosshairs:[{
                    width:1,
                    color:'#ccc'
                    }]
          },
        credits:{  //版权信息
                enabled:false
                }, 
          plotOptions: {
              area: {
                  stacking: 'normal',
                  lineColor: '#F8A31F',
                  lineWidth: 1,
                  marker: {
                      lineWidth: 1,
                      lineColor: '#F8A31F'
                  }
              }
          },
          series: [{
              name: '点击次数',
              data: response.dtlist
          }]
      });
        //点击次数
      jQuery('#container_rate').highcharts({
          chart: {
              type: 'line',
          },
           title: {  //标题选项
                text: '<b>'+response.yymm+'广告点击率统计表</b>'
          },
          subtitle: {  //副标题选项
                enabled:false
          },
          xAxis: {
              categories: response.statDatelist,
              tickmarkPlacement: 'on',
                minTickInterval:2,
              title: {
                  text: '日期（号）'
              }
          },
          yAxis: {
              title: {
                  text: '点击率（%）'
              },
              labels: {
                  formatter: function() {
                      return this.value;
                  }
              }
          },
            legend: {
                enabled:false
            },
          tooltip: {
              shared: true,
              valueSuffix: '（%）',
                crosshairs:[{
                    width:1,
                    color:'#ccc'
                    }]
          },
        credits:{  //版权信息
                enabled:false
                }, 
          plotOptions: {
              area: {
                  stacking: 'normal',
                  lineColor: '#F8A31F',
                  lineWidth: 1,
                  marker: {
                      lineWidth: 1,
                      lineColor: '#F8A31F'
                  }
              }
          },
          series: [{
              name: '点击率',
              data: response.djltlist
          }]
      });
       } 
    });
    
}


