<?php 
if("init" == $_GET['type']){
    $i = 0;
    for($i =0;$i<300;$i++){
        $sql = "INSERT INTO testdb (`value`) VALUES ('" . $i ."') ";
        $mysql = new SaeMysql();
        $mysql->runSql( $sql );
    }
    echo "init OK";
}else{
    $sql = "SELECT * FROM testdb LIMIT ".$_GET['start'].",".$_GET['count'];
    $mysql = new SaeMysql();
    $data = $mysql->getData( $sql );
    echo json_encode($data);
}
