<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once("config.php");
require_once("safemysql.class.php");
if($_POST){
	$host = "https://elib.skolib.kz";
	$db = new SafeMySQL($config);
	$catId = 17;
	$limit = 20;
	$offset = 0;
	if(isset($_POST['limit'])){
		$limit = $_POST['limit'];
	}
	if(isset($_POST['offset'])){
		$offset = $_POST['offset'];
	}
	
	$result = array();	
	$sql = "SELECT `id`,`title`,`description`,`content` FROM `cms_content` WHERE `category_id`=?i LIMIT ?i OFFSET ?i";
	$result = $db->getAll($sql,$catId,$limit,$offset);
	$result1 = array();
	for($i=0;$i<count($result);$i++){
		$content = $result[$i]["content"];
		$linkDirt = explode("=",$content);
		$link = explode("}",$linkDirt[1]);
		array_push($result1,array("id"=>$result[$i]["id"],"title"=>$result[$i]["title"],"description"=>$result[$i]["description"],"link"=>$host.$link[0]));
	}

	echo json_encode($result1, JSON_UNESCAPED_UNICODE);
}
else {
	echo json_encode(array("error"=>"please, use post method"), JSON_UNESCAPED_UNICODE);
}