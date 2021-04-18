<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once("config.php");
require_once("safemysql.class.php");
function imageContent($content){
	$host = "https://elib.skolib.kz";
	$var = "/images/";
	//$pos = strripos($content, $var);
	
}
if(isset($_POST['articleId'])){
	$db = new SafeMySQL($config);
	$result = array();
	$sql = "SELECT `id`,`category_id`,`title`,`description`,`content`,`seolink` FROM `cms_content` WHERE `id` = ?i";
	$result = $db->getRow($sql,$_POST['articleId']);
	$host = "https://elib.skolib.kz/";
	$result1 = array(
				"id"=>$result['id'],
				"title"=>$result['title'],
				"category_id"=>$result['category_id'],
				"description"=>$result['description'],
				"content"=>$result['content'],
				"link"=>$host.$result['seolink'].".html");
	echo json_encode($result1, JSON_UNESCAPED_UNICODE);
}
else{
	echo json_encode($_POST, JSON_UNESCAPED_UNICODE);
}