<!DOCTYPE html>
<html lang="zh-cn">
    <head>
        <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1">
        <link href="../src/jquery.mobile-1.4.4.min.css" rel="stylesheet">
        <script type="text/javascript" src="../src/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="../src/jquery.mobile-1.4.4.min.js"></script>
        <title>喵校园-修改信息</title>
        <script>
            function onSuccess(data, status)  
            {  
                //alert("修改成功");
                window.location.href="#dialogback";
                //WeixinJSBridge.call('closeWindow');
            }  
            
            function onError(data, status)  
            {  
                alert("修改失败");
            }    
            $(document).ready(function(){
                $.get($("#db_api_url").val()+"wuser?openid="+$("#openid").val(),
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
                    var formData = $("#userinfoform").serialize();

                    $.ajax({
                        type: "POST",
                        url: $("#db_api_url").val()+"wuser",
                        cache: false,
                        data: formData,
                        success: onSuccess,
                        error: onError
                    });
                    
                    return false;
                });
                $("#back").click(function(){
                    WeixinJSBridge.call('closeWindow');
                }); 
            });
        </script>
    </head>
    <?php 
		include('../config.php');
        $msg = "";
        if(empty($_GET["openid"])){
            $openid = "";
        }else{
            $openid = $_GET["openid"];
        }
    ?>
    <body>
        <div data-role="page">
            <div data-role="content">
                <form id="userinfoform">
                    <div data-role="fieldcontain">
                        <label for="nickname">昵称:</label><br/>
                        <input type="text" name="nickname" id="nickname">
                        <label for="college">学校:</label><br/>
                        <input type="text" name="college" id="college">
                        <label for="campus">校区:</label><br/>
                        <input type="text" name="campus" id="campus">
                        <label for="tel">手机:</label><br/>
                        <input type="text" name="tel" id="tel">
 						<input type="hidden" id="openid" name="openid" value="<?php echo $openid ?>">
                        <input type="hidden" id="type" name="type" value="update">
                        <input type="hidden" id="db_api_url" name="db_api_url" value="<?php echo constant('DB_API_URL') ?>">
                    </div>
                    <!-- <input id="submit" type="submit" data-inline="true" data-transition="slidedown" value="确认修改"> -->
                    <a href="#" id="submit" role="button" class="ui-shadow ui-btn ui-corner-all ui-btn-inline" data-transition="slidedown">确认修改</a>
                </form>
            </div>
        </div>
        
        <div data-role="page" data-dialog="true" data-close-btn="none" id="dialogback">
            <div data-role="content">
                <p>修改成功</p>
            </div>
            <div data-role="footer">
                <a href="#" id="back" class="ui-btn ui-shadow ui-corner-all ui-btn-b">OK</a>
            </div>
        </div>
    </body>
</html>