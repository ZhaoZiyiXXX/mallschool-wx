<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <title>喵校园-修改信息</title>

    <!-- Bootstrap -->
	<!-- 新 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="../src/bootstrap.min.css">
	
	<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
	<script src="../src/jquery.min.js"></script>
	
	<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
	<script src="../src/bootstrap.min.js"></script>
    <script>
        $(document).ready(function(){
            $.get("http://api.jige.olege.com/wuser?openid="+$("#openid").val(),
                  {
                      dataType : "json",
                  },
                  function(data,status){
                      $("#nickname").val(data.data.nickname);
                      $("#college").val(data.data.college);
                      $("#campus").val(data.data.campus);
                      $("#tel").val(data.data.tel);
                  });
            
            $("#submit").click(function(){
                $.post("http://api.jige.olege.com/wuser",
                       {
                           type:"update",
                           openid:$("#openid").val(),
                           nickname:$("#nickname").val(),
                           college:$("#college").val(),
                           campus:$("#campus").val(),
                           tel:$("#tel").val(),
                           dataType : "json",
                       },
                       function(data,status){
                           if(0 == data.result){
                               alert("修改成功");
                               WeixinJSBridge.call('closeWindow');
                           }else{
                               alert("修改失败");
                           }
                       });
            });
        });
    </script>
</head>
<?php 
if(empty($_GET["openid"])){
	$openid = "";
}else{
	$openid = $_GET["openid"];
}

?>
<body>
    <div class="container-fluid">
        <br/>
          <div class="form-group">
		    <label for="nickname">昵称</label>
		    <input type="text" class="form-control" id="nickname" name="nickname">
		  </div>
		  <div class="form-group">
		    <label for="college">学校</label>
		    <input type="text" class="form-control" id="college" name="college">
		  </div>
		  <div class="form-group">
		    <label for="campus">校区</label>
		    <input type="text" class="form-control" id="campus" name="campus">
		  </div>
		  <div class="form-group">
		    <label for="tel">手机</label>
		    <input type="text" class="form-control" id="tel" name="tel">
		  </div>
		  <input type="hidden" id="openid" name="openid" value="<?php echo $openid ?>">
		  <button id="submit" type="submit" class="btn btn-default">确认修改</button>
	</div>
</body>
</html>