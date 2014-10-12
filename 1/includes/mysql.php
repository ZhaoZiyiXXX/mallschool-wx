<?php

/**
 * 
 * @return unknown
 */
function _connect_mysql(){
	$dbURL = "";
	$dbname = "";
	$dbUsername = "";
	$dbPassword = "";
	try {
		$link = new PDO('mysql:host='.$dbURL.';dbname='.$dbname, $dbUsername,$dbPassword);
		$link->exec("set names utf8");
	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
	return $link;
}

/**
 * 
 * @param string $sql
 */
function _mysql_exec($sql){
	$link = _connect_mysql();
	try {
		$link->exec($sql);
 		//die(print_r($link->errorInfo(), true)); 
	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
}
/**
 * 
 * @param string $sql
 * @return result
 */
function _query_assoc($sql){
	$link = _connect_mysql();
	/* Select queries return a resultset */
	$result = $link->query($sql);
	@$result ->setFetchMode(PDO::FETCH_ASSOC);
	$ret = $result->fetchAll();
	return $ret;
}
/**
 * 
 * @param string $sql
 * @return unknown
 */
function _query_one_assoc($sql){
	$link = _connect_mysql();
	/* Select queries return a resultset */
	$result = $link->query($sql);
	if($result){
		$result ->setFetchMode(PDO::FETCH_ASSOC);
		$ret = $result->fetch();
	}else{
		$ret = null;
	}
	return $ret;
}
?>