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
 <script>
 $(document).ready(function(){
     var count1 = 10;//首次加载个数.也是当前已经加载个数的计数器
     var count2 = 4;//每次上拉加载的个数
	 $.ajax({
         type: "GET",
         url: "http://api.jige.olege.com/sells?",
         data: {
             type="latest",
             start : "0",
             count=count1,
             },
         dataType: "json",
         success: function(data){
        	 var bt=baidu.template;
             var html=bt('t:search_result',data);
             document.getElementById('result').innerHTML=html;
         }
     });
     
	$("#submit").click(function(){
			$.get("http://api.jige.olege.com/sells",
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
<body>
    <div class="container-fluid">
    <div class="row">
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