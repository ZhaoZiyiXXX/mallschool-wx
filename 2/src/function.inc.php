<?php


function Log($content){
    $mysql = new SaeMysql();
    $sql = "INSERT  INTO `log` ( `id`, `time`, `content`) VALUES (null ,'" . time() . "' ,'".$content."') ";
    $mysql->runSql($sql);
    $mysql->closeDb();
}