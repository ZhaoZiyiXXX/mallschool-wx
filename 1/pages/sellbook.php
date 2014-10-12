<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <title>喵校园-出售</title>

    <!-- Bootstrap -->
	<!-- 新 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="../src/bootstrap.min.css">
	
	<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
	<script src="../src/jquery.min.js"></script>
	
	<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
	<script src="../src/bootstrap.min.js"></script>

    <link href="../src/buybook.css" rel="stylesheet">
    <script>
         $(document).ready(function(){
			$("#submit").click(function(){
                if(!$("#price1").val()){
                    alert("请输入价格");
                    return;
                }
                if(isNaN($("#price1").val())){
                    alert("请输入有效数字");
                    return;
                }
                
				$.post("http://api.jige.olege.com/wsell",
				{
                    book_id:$("#bookid").val(),
                    openid:$("#wxopenid").val(),
                    price:$("#price1").val()
				},
				function(data,status){
                    if(0 == data.result){
                        alert("出售成功");
						WeixinJSBridge.call('closeWindow');
                    }else{
                        alert("出售失败");
                    }
                });
			});
         });
	</script>
</head>
<?php
if(empty($_GET["bookid"])){
echo "没有bookid，无法显示";
	exit;
}else{
	$bookid = $_GET["bookid"];
}

if(empty($_GET["openid"])){
	$wxopenid = "";
}else{
	$wxopenid = $_GET["openid"];
}

$url='http://api.jige.olege.com/book?type=id&q='.$bookid;
$html = file_get_contents($url);
$obj = json_decode($html);
if('{"result":0,"data":{"result":0}}' == $html){
    echo "获取图书信息异常";
    exit;
}
else{
    $imgpath = $obj->{"data"}->{"imgpath"};
    $name = $obj->{"data"}->{"name"};
    $price = $obj->{"data"}->{"fixedPrice"};
    $author = $obj->{"data"}->{"author"};
    $isbn = $obj->{"data"}->{"isbn"};
    $press = $obj->{"data"}->{"press"};
}
 ?>
<body>
    <div class="container-fluid">
    <div class="row" style="height:20px;">
    </div>
		<div class="row mbookinbox">
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
				<img src=<?php echo $imgpath?> alt="" class="pic" />
			</div>
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
				<p class="booktitle">
					<span ><?php echo $name?></span>
				</p>
				<p class="search_book_author" > 
				<span class="search_now_price">&yen;<?php echo $price?></span>
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
                <div class="form-group">
                    <label for="price1">请输入出售价格</label>
                    <input type="text" class="form-control" id="price1" name="price1">
                </div>
		            <input type="hidden" id="wxopenid" name="wxopenid" value="<?php echo $wxopenid ?>" >
		            <input type="hidden" id="bookid" name="bookid" value="<?php echo $bookid ?>" >
		            <p style="text-align:center;margin:3px auto;">
		            <button id = "submit" type="submit" class="btn btn-primary">确认出售</button>
		            </p>
		        <!--</form>-->
			</div>
		</div>
	</div>
</body>
</html>