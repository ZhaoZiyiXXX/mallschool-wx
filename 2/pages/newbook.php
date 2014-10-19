<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <title>喵校园-新增图书信息</title>

    <!-- Bootstrap -->
	<!-- 新 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="../src/bootstrap.min.css">
	
	<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
	<script src="../src/jquery.min.js"></script>
	
	<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
	<script src="../src/bootstrap.min.js"></script>
    <script>
        $(document).ready(function(){          
            $("#submit").click(function(){
                if(!$("#isbn").val()){
                    alert("ISBN不得为空");
                    return;
                }
                if(!$("#fixedPrice").val()){
                    alert("定价不得为空");
                    return;
                }
                if(!$("#name").val()){
                    alert("书名不得为空");
                    return;
                }
                if(!$("#press").val()){
                    alert("出版社不得为空");
                    return;
                }
                if(!$("#author").val()){
                    alert("作者不得为空");
                    return;
                }
                if(isNaN($("#fixedPrice").val())){
                    alert("价格必须为数字");
                    return;
                }
                $.post("http://api.jige.olege.com/book",
                       {
                           isbn:$("#isbn").val(),
                           fixedPrice:$("#fixedPrice").val(),
                           name:$("#name").val(),
                           press:$("#press").val(),
                           author:$("#author").val(),
                           dataType : "json",
                       },
                       function(data,status){
                           if(0 == data.result){
                               alert("提交成功，感谢您对喵校园的支持");
                               WeixinJSBridge.call('closeWindow');
                           }else{
                               alert("提交失败，请重试");
                           }
                       });
            });
        });
    </script>
</head>
    <body>
        <div class="container-fluid">
            <br/>
            <div class="form-group">
                <label for="name">书名</label>
                <input type="text" class="form-control" id="name" name="name">
            </div>
            <div class="form-group">
                <label for="author">作者</label>
                <input type="text" class="form-control" id="author" name="author">
            </div>
            <div class="form-group">
                <label for="press">出版社</label>
                <input type="text" class="form-control" id="press" name="press">
            </div>
            <div class="form-group">
                <label for="fixedPrice">定价</label>
                <input type="text" class="form-control" id="fixedPrice" name="fixedPrice">
            </div>
            <div class="form-group">
                <label for="isbn">ISBN</label>
                <input type="text" class="form-control" id="isbn" name="isbn">
            </div>
            <button id="submit" type="submit" class="btn btn-default">确认提交</button>
        </div>
    </body>
</html>