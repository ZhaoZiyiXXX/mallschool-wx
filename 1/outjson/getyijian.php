<?php 
header("Content-Type: text/html; charset=UTF-8");
	$mysql = new SaeMysql();
	$sql = "SELECT * FROM `yijian`";
	$data = $mysql->getData( $sql );
    if ($mysql->errno() != 0)
    {
        die("Error:" . $mysql->errmsg());
    }
    $mysql->closeDb();
	echo json_encode(
        array(
        'data' => $data
            )
    );
?>