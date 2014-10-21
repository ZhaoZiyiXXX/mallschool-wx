<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <title>喵校园-我的书籍</title>

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
         $.get($("#db_api_url").val()+"wsells?openid="+$("#openid").val(),
               {
                   dataType : "json",
               },
               function(data,status){
                   var bt=baidu.template;
                   var html=bt('t:search_result',data);
                   document.getElementById('result').innerHTML=html;
               });
     });
</script>
<?php
include('../config.php');
if(empty($_GET["openid"])){
	$openid = "";
}else{
	$openid = $_GET["openid"];
}
?>
</head>
<body>
    <div class="container-fluid">
    <div class="row">
        <input type="hidden" id="db_api_url" name="db_api_url" value="<?php echo constant('DB_API_URL') ?>">
        <div style="margin-top: 40px;margin-left:10px;margin-right:10px;">
            <input id="openid" type="hidden" value="<?php echo $openid ?>">
			<div class="bookbox" id="result">

			</div>
		  </div><!-- /.col-lg-6 -->
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
							<span class="search_now_price">&yen;<%=data[i].price%></span>
                        	<span class="search_pre_price">&yen;<%=data[i].fixedPrice%></span>
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
                    	<p class="seller_contact" > 
                        	<span>校区：</span><br/>
							<span><%=data[i].college%></span>
						</p>
                        <p class="seller_contact" > 
                        	<span>联系方式：</span><br/>
							<span><%=data[i].contact%></span>
						</p>
                        <p>
                        	<a href="managesellinfo.php?openid=<%=data[i].openid%>&sellinfoid=<%=data[i].sellinfoid%>" class="btn btn-default">点击修改</a>
        				</p>
        			</div>
				</div>
<%}%>
<!-- 模板结束 -->   
</script>
</body>
</html>