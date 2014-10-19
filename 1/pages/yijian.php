<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <title>喵校园-意见反馈</title>

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

</head>
    <?php
    if(isset($_POST["content"])){
          $mysql = new SaeMysql();
          $sql = "INSERT  INTO `yijian` ( `id`, `time`, `content`) VALUES (null ,'" . time() . "' ,'".$_POST["content"]."') ";
          $mysql->runSql($sql);
          $mysql->closeDb();
        	echo "\n\n\n我们已经收到了您的反馈，感谢您对喵校园的关注！";
        return;
    }
	?>
<body>
    <div class="container-fluid">
        <div class="row">
            <div style="margin-top: 40px;margin-left:10px;margin-right:10px;">
                <form method="post" style="margin-top:50px;">
                    <p>如果您的意见需要我们的反馈，请在填写完反馈信息后留下您的联系方式，我们承诺不会对您造成骚扰</p>
                    <p>您的宝贵意见是我们改进的动力</p>
                    <textarea id="content" name="content" class="form-control" rows="3"></textarea>
                    <button class="btn btn-info" style="margin-top:10px;width:100%;height:35px;line-height:35px;">确定提交</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>