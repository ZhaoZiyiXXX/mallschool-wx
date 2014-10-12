<?php

function _alert_back($msg) {
    echo '<script type="text/javascript">alert("' . $msg . '");history.back(-1);</script>';
}

function _alert_close($msg) {
    echo "<script type='text/javascript'>alert('" . $msg . "');WeixinJSBridge.call('closeWindow');</script>";
}