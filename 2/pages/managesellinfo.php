<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <title>喵校园-修改出售信息</title>

    <!-- Bootstrap -->
	<!-- 新 Bootstrap 核心 CSS 文件 -->
	
	<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <script src="../src/jquery.min.js"></script>
    <!-- Core files -->
    
    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <script src="../src/bootstrap.min.js"></script>
    
    <link rel="stylesheet" href="../src/bootstrap.min.css">
    <link rel="stylesheet" href="../src/buybook.css">
        
    <link rel="stylesheet" href="../src/jquery-ui.css">
    <script src="../src/jquery-ui.js"></script>
    <script>
        $(document).ready(function(){
            $("#unsaled").click(function(){
                $("#dropdownMenu1").text('未出售');
                $("#sellstatus").val("0");
            });
            
            $("#saled").click(function(){
                $("#dropdownMenu1").text('已出售');
                 $("#sellstatus").val("1");
            });
            
			$("#submit").click(function(){
                if(!$("#price1").val()){
                    $("#dialogprice").dialog({
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
                if(isNaN($("#price1").val())){
                    $("#dialognumber").dialog({
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
                
				$.post($("#db_api_url").val()+"wsell",
				{
                    type:"update",
                    sellinfoid:$("#sellinfoid").val(),
                    price:$("#price1").val(),
                    status: $("#sellstatus").val()
				},
				function(data,status){
                    if(0 == data.result){
                        $("#dialog").dialog({
                            resizable: false,
                            height:150,
                            modal: true,
                            buttons: {
                                "OK": function() {
                                    $( this ).dialog( "close" );
                                    WeixinJSBridge.call('closeWindow');
                                }
                            }
                        });
                    }else{
                         $("#dialogerror").dialog({
                             resizable: false,
                             height:150,
                             modal: true,
                             buttons: {
                                 "OK": function() {
                                     $( this ).dialog( "close" );
                                 }
                             }
                         });
                    }
                });
			});
         });
	</script>
</head>
<?php
include('../config.php');
if(empty($_GET["sellinfoid"])){
	echo "系统异常";
	exit;
}else{
	$sellinfoid = $_GET["sellinfoid"];
}

if(empty($_GET["openid"])){
	echo "系统异常";
	exit;
}else{
	$openid = $_GET["openid"];
}

$url=DB_API_URL . 'wsell?type=sellinfoid&sellinfoid='.$sellinfoid.'&openid='.$openid;
$html = file_get_contents($url);
$obj = json_decode($html);
if('{"result":0,"data":{"result":0}}' == $html){
    echo "获取图书信息异常";
    exit;
}
else{
    $imgpath = $obj->{"data"}[0]->{"imgpath"};
    $name = $obj->{"data"}[0]->{"name"};
    $fixedPrice = $obj->{"data"}[0]->{"fixedPrice"};
    $author = $obj->{"data"}[0]->{"author"};
    $isbn = $obj->{"data"}[0]->{"isbn"};
    $press = $obj->{"data"}[0]->{"press"};
    $price =$obj->{"data"}[0]->{"price"};
}
 ?>
<body>
    <div class="container-fluid">
    <div class="row" style="height:20px;">
    </div>
        <input type="hidden" id="db_api_url" name="db_api_url" value="<?php echo constant('DB_API_URL') ?>">
		<div class="row mbookinbox">
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
				<img src=<?php echo $imgpath?> alt="" class="pic" />
			</div>
            <input type="hidden" id="price" value="<?php echo $price?>">
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
				<p class="booktitle">
					<span ><?php echo $name?></span>
				</p>
				<p class="search_book_author" > 
				<span class="search_now_price">&yen;<?php echo $fixedPrice?></span>
				<span><?php echo $author?></span>
				</p>
				<p class="search_book_author" > 
				<span ><?php echo $isbn?></span>
                </p>
                <p class="search_book_author" >
				<span><?php echo $press?></span>
				</p>
			</div>
		</div>
        <div class="row">
            <div class="col-lg-10 col-lg-offset-1 col-md-10 col-md-offset-1  col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
                <!--<form method="POST">-->
                <br/>
                <label for="price1">出售状态</label>
                <div class="dropdown">
                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
                        未出售
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                        <li><a id="unsaled" role="menuitem" tabindex="-1" href="#">未出售</a></li>
                        <li><a id="saled" role="menuitem" tabindex="-1" href="#">已出售</a></li>
                    </ul>
                </div>
                <br/>
                <div class="form-group">
                    <label for="price1">价格</label>
                    <input type="text" class="form-control" id="price1" name="price1" value="<?php echo $price?>">
                </div>
                <input type="hidden" id="wxopenid" name="wxopenid" value="<?php echo $wxopenid ?>" >
                <input type="hidden" id="sellinfoid" name="sellinfoid" value="<?php echo $sellinfoid ?>" >
                <input type="hidden" id="sellstatus" value="0">
                <p style="text-align:center;margin:3px auto;">
                    <button id = "submit" type="submit" class="btn btn-primary">确认修改</button>
                </p>
                <!--</form>-->
            </div>
        </div>
	</div>
      <div id="dialog" style="display:none;">
        <p>修改成功！</p>
    </div>
    <div id="dialogerror" style="display:none;">
        <p>修改失败！请在网络良好的状态重试</p>
    </div>
    <div id="dialogprice" style="display:none;">
        <p>请输入价格</p>
    </div>
    <div id="dialognumber" style="display:none;">
        <p>请输入有效数字</p>
    </div>
</body>
</html>