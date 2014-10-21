<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <title>喵校园-我要买书</title>

    <!-- Bootstrap -->
	<link href="../src/bootstrap.min.css" rel="stylesheet">
    <link href="../src/buybook.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	
	
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="../src/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../src/bootstrap.min.js"></script>
    <script type="text/javascript" src="../src/baiduTemplate.js"></script>
    <link rel="stylesheet" href="../src/jquery-ui.css">
    <script src="../src/jquery-ui.js"></script>
 <script>
 $(document).ready(function(){
     var count1 = 6;//首次加载个数.也是当前已经加载个数的计数器
     var count2 = 4;//每次上拉加载的个数
	 $.ajax({
         type: "GET",
         url: $("#db_api_url").val()+"sells?type=latest&start=0&count=" + count1.toString(),
         dataType: "json",
         success: function(data){
        	 var bt=baidu.template;
             var html=bt('t:search_result',data);
             document.getElementById('result').innerHTML=html;
         }
     });
     //增加记录个数函数，直接写在回调函数中无效
     function SetCount(count){
         count1 = count1 + count;
     }
     
     $(window).bind("scroll", 
                    function (event) 
                    { 
                        var flag = 0;//正在获取ajax数据标记，避免重复请求
                        //滚动条到网页头部的 高度，兼容ie,ff,chrome 
                        var top = document.documentElement.scrollTop + document.body.scrollTop; 
                        //网页的高度 
                        var textheight = $(document).height(); 
                        // 网页高度-top-当前窗口高度 
                        var tmpheight = textheight - top - $(window).height();
                        //20就是距离页面底端的距离，相当于拉到里页面结束还有20px的时候触发加载动作
                        if (tmpheight < 20) { 
                            if( 0 == flag){
                                flag = 1;
                                $.ajax({
                                    type: "GET",
                                    url: $("#db_api_url").val()+"sells?type=latest&start="+count1.toString()+"&count=" + count2.toString(),
                                    dataType: "json",
                                    async:false,
                                    success: function(data){
                                        var bt=baidu.template;
                                        var html=bt('t:search_result',data);
                                        $("#result").append(html);
                                        if(0 == data.data.length){
                                            document.getElementById('below').innerHTML="<p>已经到底了</p>";
                                        }else{
                                            SetCount(data.data.length);
                                        }
                                    }
                                });
                            }
                        }
                    });
	$("#submit").click(function(){
			$.get($("#db_api_url").val()+"sells",
			{
				q:$("#search").val().replace(/ /g,"#"),
			    dataType : "json",
			},
			function(data,status){
                if(undefined == data.data[0]){
                    $("#dialog").dialog({
                        resizable: false,
                        height:150,
                        modal: true,
                        buttons: {
                            "OK": function() {
                                $( this ).dialog( "close" );
                            }
                        }
                    });
                    return;
                }
				var bt=baidu.template;
				var html=bt('t:search_result',data);
				document.getElementById('result').innerHTML=html;
			});
	});
});
</script>
</head>
    <?php 
        include('../config.php');
    ?>
<body>
    <div class="container-fluid">
    <div class="row">
        <input type="hidden" id="db_api_url" name="db_api_url" value="<?php echo constant('DB_API_URL') ?>">
        <div style="margin-top: 40px;margin-left:10px;margin-right:10px;">
		    <div class="input-group">
		      <input type="text" class="form-control" id="search">
                <span class="input-group-btn"> <button id="submit" type="button" class="btn btn-default">搜索</button></span>
		    </div><!-- /input-group -->
			<div class="bookbox" id="result">

			</div>
		  </div><!-- /.col-lg-6 -->
		  <div id="below" style="height:50px;text-align:center;padding:10px">
            	<p>正在为您加载更多内容...</p>
            </div>
    </div>	
<script id='t:search_result' type="text/template">
<!-- 模板部分 -->
<% for(var i = 0; i<data.length;i++){%>
				<div class="row mbookinbox">
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
						<img src="<%=data[i].imgpath%>" alt="" class="pic" />
					</div>
					<div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
						<p class="booktitle">
							<span ><%=data[i].name%></span>
						</p>
						<p class="search_book_author" > 
							<span class="search_now_price">￥<%=data[i].price%></span>
                        	<span class="search_pre_price">￥<%=data[i].fixedprice%></span>
                        	<span class="search_discount">(<%=data[i].off%>折)</span>
                        </p>
                        <p class="search_book_author">
                        	<span><%=data[i].author%></span>
						</p>
						<p class="search_book_author" > 
							<span><%=data[i].press%></span>
						</p>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    	<p class="seller_title">卖家信息</p>
                    	<p class="seller_campus" > 
                        	<span>校区：</span><br/>
							<span><%=data[i].college%></span>
						</p>
                        <p class="seller_contact" > 
                        	<span>联系方式：</span><br/>
							<a> <%=data[i].contact%></a>
						</p>
        			</div>
				</div>
<%}%>
<!-- 模板结束 -->   
</script>

        </div>
            <div id="dialog" style="display:none;">
            <p>没有搜索到相关出售信息</p>
        </div>
</body>
</html>