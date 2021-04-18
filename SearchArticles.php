<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
if($_POST['queryString']){
	$result = array();
	require_once("config.php");
	require_once("safemysql.class.php");
	$db = new SafeMySQL($config);
	$limit = 20;
	$offset = 0;
	if(isset($_POST['limit'])){
		$limit = $_POST['limit'];
	}
	if(isset($_POST['offset'])){
		$offset = $_POST['offset'];
	}
	$query = '%'.$_POST['queryString'].'%';
	$sql = "SELECT `id`,`category_id`,`title` FROM `cms_content` WHERE `title` LIKE ?s LIMIT ?i OFFSET ?i";
	$result = $db->getAll($sql,$query,$limit,$offset);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
else{
	echo json_encode($_POST, JSON_UNESCAPED_UNICODE);
}