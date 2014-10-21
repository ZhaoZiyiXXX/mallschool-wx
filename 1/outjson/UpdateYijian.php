<?php
header("Content-Type: text/html; charset=UTF-8");
if(empty($_POST['id'])| (!empty($_POST['content']))|(!empty($_POST['time']))){
    echo json_encode(
        array(
            'result' => '1000',
            'msg' => '必须含有id字段，必须不含有content\time字段'
        )
    );
    return;
}else{
    $mysql = new SaeMysql();
    $sql = "SELECT * FROM `yijian` WHERE id = '".$_POST['id']."'";
    sae_debug($sql);
    $data = $mysql->getData( $sql );
    if(0 == count($data)){
        echo json_encode(
            array(
                'result' => '1000',
                'msg' => 'unvalid id'
            )
        );
        return;
    }else{
        if(empty($_POST['staff'])){
            $staff = "null";
        }else{
            $staff = $_POST['staff'];
        }
        
        if(empty($_POST['stime'])){
            $stime = "null";
        }else{
            $stime = $_POST['stime'];
        }
        
        if(empty($_POST['mark'])){
            $mark = "null";
        }else{
            $mark = $_POST['mark'];
        }
        
        if(empty($_POST['status'])){
            $status = "0";
        }else{
            $status = $_POST['status'];
        }
        if(empty($_POST['staff'])){
            if(!empty($_POST['mark'])){
                $sql = "UPDATE `yijian` SET `mark` = '"  .$mark . "' WHERE id = '" .$_POST['id']  . "'";
            }else{
                $sql = "UPDATE `yijian` SET `status` = '"  .$status . "' WHERE id = '" .$_POST['id']  . "'";
            }
            
        }else{
            $sql = "UPDATE `yijian` SET `staff` = '".$staff."' ,`stime` =  '"  .$stime . 
                "' ,`mark` = '"  .$mark . "' ,`status`='" . $status . "' WHERE id = '" .$_POST['id']  . "'";
        }
        
    }
    sae_debug($sql);
    $mysql->runSql($sql);
    if ($mysql->errno() != 0)
    {
        die("Error:" . $mysql->errmsg());
    }
    $mysql->closeDb();
    echo json_encode(
        array(
            'result' => '0'
        )
    );           
}
?>
