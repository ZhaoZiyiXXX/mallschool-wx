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
    
    <link rel="stylesheet" href="../src/jquery-ui.css">
    <script src="../src/jquery-ui.js"></script>
    <script>
        $(document).ready(function(){
            $.get($("#db_api_url").val()+"wuser?openid="+$("#openid").val(),
                  {
                      dataType : "json",
                  },
                  function(data,status){
                      $("#nickname").val(data.data.nickname);
                      //$("#college").val(data.data.college);
                      //$("#campus").val(data.data.campus);
                      $("#tel").val(data.data.tel);
                  });
            
            $( "#college" ).selectmenu({
                change: function( event, data ) {
                    if("同济大学" == $( "#college" ).val()){
                        $( "#campus" ).html("<option selected=\"selected\">四平路校区</option><option>嘉定校区</option>");
                    }else if("上海财经大学" ==  $( "#college" ).val()){
                        $( "#campus" ).html("<option selected=\"selected\">国定路校区</option><option>武东路校区</option>");
                    }
                }
            });
            
            $("#submit").click(function(){
                $.post($("#db_api_url").val()+"wuser",
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
if(empty($_GET["openid"])){
	$openid = "";
}else{
	$openid = $_GET["openid"];
}

$url = DB_API_URL . "wuser?openid=" . $openid;
$html = file_get_contents($url);
$obj = json_decode($html);
$college = $obj->{'data'}->{'college'};
$campus = $obj->{'data'}->{'campus'};
?>
    <body>
        <div class="container-fluid">
            <br/>
            <div class="form-group">
                <label for="nickname">昵称</label>
                <input type="text" class="form-control" id="nickname" name="nickname">
            </div>
            <!--<div class="form-group">
				<label for="college">学校</label>
				<input type="text" class="form-control" id="college" name="college">
			</div>-->
            <div class="form-group">
                <label for="college">学校</label>
                <select class="form-control" id="college" name="college" style="line-height:50px;">
                    <option>同济大学</option>
                    <option>上海财经大学</option>
                </select>
                <script>
                    $("#college").val("<?php echo $college ?>");
                </script>
            </div>

            
            <div class="form-group">
                <label for="campus">校区</label>
                <select class="form-control" id="campus" name="campus" style="line-height:50px;">
                </select>
                <script>
                    if ("同济大学" == $("#college").val()) {
                        $("#campus").append("<option>四平路校区</option>");
                        $("#campus").append("<option>嘉定校区</option>");
                    } else if ("上海财经大学" == $("#college").val()) {
                        $("#campus").append("<option>国定路校区</option>");
                        $("#campus").append("<option>武东路校区</option>");
                    }
                    $("#campus").val("<?php echo $campus ?>");
                </script>
            </div>
            <div class="form-group">
                <label for="tel">手机</label>
                <input type="text" class="form-control" id="tel" name="tel">
            </div>
            <input type="hidden" id="openid" name="openid" value="<?php echo $openid ?>">
            <button id="submit" type="submit" class="btn btn-default">确认修改</button>
            <div id="dialog" style="display:none;">
                <p>成功！</p>
            </div>
            <div id="dialogerror" style="display:none;">
                <p>修改失败！请在网络良好的状态重试</p>
            </div>
            <input type="hidden" id="db_api_url" name="db_api_url" value="<?php echo constant('DB_API_URL') ?>">
        </div>
    </body>
</html>