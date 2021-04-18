<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
if(isset($_POST['catId']) && isset($_POST['articleId'])){
	echo json_encode(array("error"=>"Please, send only one parametr"), JSON_UNESCAPED_UNICODE);
}
elseif(isset($_POST['catId'])){
	require_once("config.php");
	require_once("safemysql.class.php");
	$db = new SafeMySQL($config);
	$sql = "SELECT `id`,`parent_id`,`title`,`description` FROM `cms_category` WHERE `id` =?i";
	$result = $db->getAll($sql,$_POST['catId']);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
elseif(isset($_POST['articleId'])){
	require_once("config.php");
	require_once("safemysql.class.php");
	$db = new SafeMySQL($config);
	$sql ="SELECT c.id,c.parent_id,c.title,c.description FROM `cms_content` a INNER JOIN cms_category c ON a.category_id = c.id WHERE a.id = ?i";
	$result = $db->getAll($sql,$_POST['articleId']);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
else{
	echo json_encode($_POST, JSON_UNESCAPED_UNICODE);
}
